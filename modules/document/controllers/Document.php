<?php

class Document extends Controller
{
    public function __construct()
    {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('file');
        $this->load->model('document/MOD', 'mod');
    }

    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        $drive_id = isset($_POST['drive_id']) ? (int) $_POST['drive_id'] : 0;
        $folder_id = isset($_POST['folder_id']) && !empty($_POST['folder_id']) ? (int) $_POST['folder_id'] : null;

        if (!$drive_id) {
            echo json_encode(['status' => false, 'message' => 'ID Drive tidak valid.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Anda tidak memiliki akses unggah ke Drive ini.']);
            return;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => false, 'message' => 'Gagal mengunggah file. Kode error: ' . ($_FILES['file']['error'] ?? 'Unknown')]);
            return;
        }

        global $config;
        $file = $_FILES['file'];

        $original_name = $file['name'];
        $file_size = $file['size'];
        $ext = get_file_extension($original_name);

        // 1. Validation
        if ($file_size > $config['max_file_size']) {
            echo json_encode(['status' => false, 'message' => 'Ukuran file melebihi batas maksimal (' . format_file_size($config['max_file_size']) . ').']);
            return;
        }

        if (!in_array($ext, $config['allowed_file_types'])) {
            echo json_encode(['status' => false, 'message' => 'Ekstensi file tidak diizinkan.']);
            return;
        }

        // 2. Prepare Storage Directory based on Role ID and Year
        $role_id = Session::get('role_id') ?? 0;
        $year = date('Y');
        
        $safe_path = '';
        if ($folder_id) {
            $folder = $this->db->table('folders')->where('id', $folder_id)->row();
            $path_parts = array_filter(explode('/', $folder->path));
            $safe_path = implode('/', array_map(function($p) {
                return preg_replace('/[^a-zA-Z0-9_ -]/', '_', $p);
            }, $path_parts));
            $safe_path .= '/';
        }
        
        $target_dir = STORAGEPATH . 'drives/' . $role_id . '/' . $year . '/' . $safe_path;
        $relative_dir = 'drives/' . $role_id . '/' . $year . '/' . $safe_path;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // 3. Keep original filename (sanitize spaces and special chars)
        $base_name = pathinfo($original_name, PATHINFO_FILENAME);
        $safe_base = preg_replace('/[^a-zA-Z0-9_ -]/', '_', $base_name); // Keep spaces, letters, numbers, dash, underscore
        
        $new_filename = $safe_base . '.' . $ext;
        $target_file = $target_dir . $new_filename;
        
        // Anti-overwrite (append _1, _2 if file exists)
        $counter = 1;
        $display_name = pathinfo($original_name, PATHINFO_FILENAME);
        while (file_exists($target_file)) {
            $new_filename = $safe_base . '_' . $counter . '.' . $ext;
            $target_file = $target_dir . $new_filename;
            $display_name = pathinfo($original_name, PATHINFO_FILENAME) . '_' . $counter;
            $counter++;
        }

        $relative_path = $relative_dir . $new_filename;

        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            echo json_encode(['status' => false, 'message' => 'Gagal menyimpan file secara fisik.']);
            return;
        }

        $now = date('Y-m-d H:i:s');


        $mime_type = get_mime_type($target_file);
        $checksum = hash_file('sha256', $target_file);

        // 4. Save to Database
        $db_folder_id = !empty($folder_id) ? (int)$folder_id : null;
        $data = [
            'drive_id' => $drive_id,
            'folder_id' => $db_folder_id,
            'name' => $display_name,
            'original_name' => $original_name,
            'file_path' => $relative_path,
            'file_size' => $file_size,
            'file_type' => $ext,
            'mime_type' => $mime_type,
            'version' => 1,
            'checksum' => $checksum,
            'created_by' => Session::get('user_id'),
            'updated_by' => Session::get('user_id'),
            'created_at' => $now,
            'updated_at' => $now
        ];

        try {
            $doc_id = $this->mod->insert_document($data);

            // Save first version
            $version_data = [
                'document_id' => $doc_id,
                'version_number' => 1,
                'file_path' => $relative_path,
                'file_size' => $file_size,
                'mime_type' => $mime_type,
                'checksum' => $checksum,
                'notes' => 'Initial upload'
            ];
            $this->mod->insert_version($version_data);

            // Update folder & drive stats
            if ($db_folder_id) {
                $this->db->query("UPDATE folders SET total_files = total_files + 1, total_size = total_size + ? WHERE id = ?", [$file_size, $db_folder_id]);
            }
            $this->db->query("UPDATE drives SET total_size = total_size + ? WHERE id = ?", [$file_size, $drive_id]);

            // Log activity
            $this->db->query(
                "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'UPLOAD', 'document', ?, ?, ?, NOW(), 1)",
                [Session::get('user_id'), $doc_id, "Mengunggah file: $original_name", $_SERVER['REMOTE_ADDR'] ?? '']
            );

            echo json_encode(['status' => true, 'message' => 'File berhasil diunggah', 'document_id' => $doc_id]);
        } catch (Exception $e) {
            // Rollback physically
            @unlink($target_file);
            echo json_encode(['status' => false, 'message' => 'Gagal menyimpan ke database.']);
        }
    }

    /**
     * Extract a ZIP/RAR archive document into folder hierarchy (manual extraction from UI)
     */
    public function extract($doc_id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        if (!$doc_id) {
            $doc_id = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
        }
        $doc_id = (int)$doc_id;

        $doc = $this->mod->get_document_by_id($doc_id);
        if (!$doc) {
            echo json_encode(['status' => false, 'message' => 'Dokumen tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $ext = strtolower($doc->file_type);
        if (!in_array($ext, ['zip', 'rar'])) {
            echo json_encode(['status' => false, 'message' => 'File ini bukan arsip ZIP/RAR.']);
            return;
        }

        if (!class_exists('ZipArchive')) {
            echo json_encode(['status' => false, 'message' => 'Ekstensi ZipArchive tidak tersedia di server.']);
            return;
        }

        $archive_path = STORAGEPATH . $doc->file_path;
        if (!file_exists($archive_path)) {
            echo json_encode(['status' => false, 'message' => 'File arsip tidak ditemukan di server.']);
            return;
        }

        // Determine role_id and year from existing file path
        $path_parts_arr = explode('/', $doc->file_path);
        // Expected: drives/{role_id}/{year}/...
        $role_id = isset($path_parts_arr[1]) ? $path_parts_arr[1] : (Session::get('role_id') ?? 0);
        $year = isset($path_parts_arr[2]) ? $path_parts_arr[2] : date('Y');

        $now = date('Y-m-d H:i:s');
        $display_name = $doc->name; // e.g. "ijzah"

        try {
            $extracted = $this->_process_zip_extract($archive_path, $doc->drive_id, $doc->folder_id, $display_name, $doc->original_name, $role_id, $year, $now);
            
            if ($extracted === false) {
                echo json_encode(['status' => false, 'message' => 'Gagal mengekstrak arsip. File mungkin rusak.']);
                return;
            }

            // Log activity
            $this->db->query(
                "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'EXTRACT', 'folder', ?, ?, ?, NOW(), 1)",
                [Session::get('user_id'), $extracted['root_folder_id'], "Mengekstrak arsip {$doc->original_name} menjadi folder {$display_name} ({$extracted['files_count']} file)", $_SERVER['REMOTE_ADDR'] ?? '']
            );

            echo json_encode([
                'status' => true,
                'message' => "Arsip '{$doc->original_name}' berhasil diekstrak menjadi folder '{$display_name}' ({$extracted['files_count']} file).",
                'folder_id' => $extracted['root_folder_id'],
                'files_count' => $extracted['files_count']
            ]);
        } catch (Exception $e) {
            error_log("ZIP Extract Error: " . $e->getMessage() . " | File: " . $doc->original_name);
            echo json_encode(['status' => false, 'message' => 'Gagal mengekstrak: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper: Extract ZIP Archive into Folders & Documents using extractTo()
     * Uses extractTo() for reliable extraction of ALL files, then scans the filesystem
     */
    private function _process_zip_extract($archive_path, $drive_id, $folder_id, $display_name, $original_name, $role_id, $year, $now) {
        $zip = new ZipArchive();
        $res = $zip->open($archive_path);
        if ($res !== TRUE) {
            error_log("ZipArchive::open failed for $archive_path - error code: $res");
            return false;
        }

        $user_id = Session::get('user_id');

        // 1. Extract to a temporary directory
        $temp_dir = STORAGEPATH . 'temp_extract_' . uniqid() . '/';
        if (!mkdir($temp_dir, 0755, true)) {
            $zip->close();
            return false;
        }

        if (!$zip->extractTo($temp_dir)) {
            $zip->close();
            $this->_remove_dir($temp_dir);
            return false;
        }
        $zip->close();

        // 2. Get parent folder path
        $parent_path = '/';
        if ($folder_id) {
            $pf = $this->db->table('folders')->where('id', $folder_id)->row();
            if ($pf) {
                $parent_path = $pf->path;
            }
        }

        // 3. Create Root Folder for extracted archive
        $safe_root_name = preg_replace('/[^a-zA-Z0-9_ -]/', '_', $display_name);
        $root_folder_path = rtrim($parent_path, '/') . '/' . $safe_root_name . '/';

        $root_folder_data = [
            'drive_id' => $drive_id,
            'parent_id' => !empty($folder_id) ? (int)$folder_id : null,
            'name' => $display_name,
            'path' => $root_folder_path,
            'total_files' => 0,
            'total_size' => 0,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'created_at' => $now,
            'updated_at' => $now,
            'status' => 1
        ];

        $root_folder_id = $this->mod->insert_folder($root_folder_data);

        // 4. Scan extracted files using RecursiveDirectoryIterator
        $folder_cache = ['' => $root_folder_id]; // relative_path => folder_id
        $extracted_files_count = 0;
        $extracted_total_bytes = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($temp_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            // Get path relative to temp_dir
            $relative = str_replace('\\', '/', substr($item->getPathname(), strlen($temp_dir)));
            
            // Skip OS metadata
            if (strpos($relative, '__MACOSX') !== false || 
                basename($relative) === '.DS_Store' || 
                basename($relative) === 'Thumbs.db' ||
                basename($relative)[0] === '.') {
                continue;
            }

            if ($item->isDir()) {
                // Create folder in DB
                $dir_parts = array_values(array_filter(explode('/', $relative)));
                $curr_subpath = '';
                $curr_parent_id = $root_folder_id;

                foreach ($dir_parts as $p) {
                    $curr_subpath .= ($curr_subpath ? '/' : '') . $p;
                    if (!isset($folder_cache[$curr_subpath])) {
                        $p_folder = $this->db->table('folders')->where('id', $curr_parent_id)->row();
                        $p_path = $p_folder ? $p_folder->path : '/';
                        $safe_p = preg_replace('/[^a-zA-Z0-9_ -]/', '_', $p);
                        $new_path = rtrim($p_path, '/') . '/' . $safe_p . '/';

                        $sub_folder_data = [
                            'drive_id' => $drive_id,
                            'parent_id' => $curr_parent_id,
                            'name' => $p,
                            'path' => $new_path,
                            'total_files' => 0,
                            'total_size' => 0,
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                            'status' => 1
                        ];
                        $folder_cache[$curr_subpath] = $this->mod->insert_folder($sub_folder_data);
                    }
                    $curr_parent_id = $folder_cache[$curr_subpath];
                }
            } else {
                // It's a file — determine its parent folder
                $file_name = basename($relative);
                $dir_relative = dirname($relative);
                $dir_relative = ($dir_relative === '.' || $dir_relative === '') ? '' : str_replace('\\', '/', $dir_relative);

                $target_sub_folder_id = $root_folder_id;
                if (!empty($dir_relative) && isset($folder_cache[$dir_relative])) {
                    $target_sub_folder_id = $folder_cache[$dir_relative];
                } elseif (!empty($dir_relative)) {
                    // Create folder if not yet created (edge case: file before dir entry)
                    $dir_parts = array_values(array_filter(explode('/', $dir_relative)));
                    $curr_subpath = '';
                    $curr_parent_id = $root_folder_id;
                    foreach ($dir_parts as $p) {
                        $curr_subpath .= ($curr_subpath ? '/' : '') . $p;
                        if (!isset($folder_cache[$curr_subpath])) {
                            $p_folder = $this->db->table('folders')->where('id', $curr_parent_id)->row();
                            $p_path = $p_folder ? $p_folder->path : '/';
                            $safe_p = preg_replace('/[^a-zA-Z0-9_ -]/', '_', $p);
                            $new_path = rtrim($p_path, '/') . '/' . $safe_p . '/';

                            $sub_folder_data = [
                                'drive_id' => $drive_id,
                                'parent_id' => $curr_parent_id,
                                'name' => $p,
                                'path' => $new_path,
                                'total_files' => 0,
                                'total_size' => 0,
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                                'created_at' => $now,
                                'updated_at' => $now,
                                'status' => 1
                            ];
                            $folder_cache[$curr_subpath] = $this->mod->insert_folder($sub_folder_data);
                        }
                        $curr_parent_id = $folder_cache[$curr_subpath];
                    }
                    $target_sub_folder_id = $folder_cache[$dir_relative];
                }

                // Copy file from temp to permanent storage
                $inner_ext = get_file_extension($file_name);
                $inner_base = pathinfo($file_name, PATHINFO_FILENAME);
                $inner_safe = preg_replace('/[^a-zA-Z0-9_ -]/', '_', $inner_base);
                $inner_size = $item->getSize();

                // Build target directory
                $target_folder_obj = $this->db->table('folders')->where('id', $target_sub_folder_id)->row();
                $safe_sub_path = '';
                if ($target_folder_obj) {
                    $pparts = array_filter(explode('/', $target_folder_obj->path));
                    $safe_sub_path = implode('/', array_map(function($p) {
                        return preg_replace('/[^a-zA-Z0-9_ -]/', '_', $p);
                    }, $pparts)) . '/';
                }

                $inner_target_dir = STORAGEPATH . 'drives/' . $role_id . '/' . $year . '/' . $safe_sub_path;
                $inner_relative_dir = 'drives/' . $role_id . '/' . $year . '/' . $safe_sub_path;

                if (!is_dir($inner_target_dir)) {
                    mkdir($inner_target_dir, 0755, true);
                }

                $inner_filename = $inner_safe . '.' . $inner_ext;
                $inner_file_full = $inner_target_dir . $inner_filename;
                $c = 1;
                while (file_exists($inner_file_full)) {
                    $inner_filename = $inner_safe . '_' . $c . '.' . $inner_ext;
                    $inner_file_full = $inner_target_dir . $inner_filename;
                    $c++;
                }

                // Copy from temp to final location
                copy($item->getPathname(), $inner_file_full);

                $inner_relative_path = $inner_relative_dir . $inner_filename;
                $inner_mime = get_mime_type($inner_file_full);
                $inner_checksum = hash_file('sha256', $inner_file_full);

                $doc_data = [
                    'drive_id' => $drive_id,
                    'folder_id' => $target_sub_folder_id,
                    'name' => $inner_base,
                    'original_name' => $file_name,
                    'file_path' => $inner_relative_path,
                    'file_size' => $inner_size,
                    'file_type' => $inner_ext,
                    'mime_type' => $inner_mime,
                    'version' => 1,
                    'checksum' => $inner_checksum,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                $extracted_doc_id = $this->mod->insert_document($doc_data);
                $this->mod->insert_version([
                    'document_id' => $extracted_doc_id,
                    'version_number' => 1,
                    'file_path' => $inner_relative_path,
                    'file_size' => $inner_size,
                    'mime_type' => $inner_mime,
                    'checksum' => $inner_checksum,
                    'notes' => 'Extracted from archive: ' . $original_name
                ]);

                // Update folder stats
                $this->db->query("UPDATE folders SET total_files = total_files + 1, total_size = total_size + ? WHERE id = ?", [$inner_size, $target_sub_folder_id]);
                $extracted_files_count++;
                $extracted_total_bytes += $inner_size;
            }
        }

        // 5. Clean up temp directory
        $this->_remove_dir($temp_dir);

        // 6. Update root folder stats & drive stats
        $this->db->query("UPDATE folders SET total_files = ?, total_size = ? WHERE id = ?", [$extracted_files_count, $extracted_total_bytes, $root_folder_id]);
        $this->db->query("UPDATE drives SET total_size = total_size + ? WHERE id = ?", [$extracted_total_bytes, $drive_id]);

        return [
            'root_folder_id' => $root_folder_id,
            'files_count' => $extracted_files_count,
            'total_bytes' => $extracted_total_bytes
        ];
    }

    /**
     * Helper: Recursively remove a directory
     */
    private function _remove_dir($dir) {
        if (!is_dir($dir)) return;
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($dir);
    }

    public function download($id)
    {
        $doc = $this->mod->get_document_by_id($id);

        if (!$doc) {
            die('Dokumen tidak ditemukan atau sudah dihapus.');
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            die('Akses ditolak.');
        }

        $file_path = STORAGEPATH . $doc->file_path;
        
        // Backward compatibility for old files (if any were uploaded before the change)
        if (!file_exists($file_path) && file_exists(STORAGEPATH . 'documents/' . $doc->file_path)) {
            $file_path = STORAGEPATH . 'documents/' . $doc->file_path;
        }

        if (!file_exists($file_path)) {
            die('File fisik tidak ditemukan di server.');
        }

        // Update download count & access time
        $this->db->query("UPDATE documents SET download_count = download_count + 1, last_accessed = NOW() WHERE id = ?", [$id]);

        // Log activity
        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'DOWNLOAD', 'document', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $id, "Mengunduh file: {$doc->name}.{$doc->file_type}", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        // Force download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $doc->mime_type);
        header('Content-Disposition: attachment; filename="' . $doc->name . '.' . $doc->file_type . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
        exit;
    }

    public function preview($id) {
        $doc = $this->mod->get_document_by_id($id);
        
        if (!$doc) {
            die('Dokumen tidak ditemukan atau sudah dihapus.');
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            die('Akses ditolak.');
        }

        $file_path = STORAGEPATH . $doc->file_path;
        
        // Backward compatibility for old files (if any were uploaded before the change)
        if (!file_exists($file_path) && file_exists(STORAGEPATH . 'documents/' . $doc->file_path)) {
            $file_path = STORAGEPATH . 'documents/' . $doc->file_path;
        }
        
        if (!file_exists($file_path)) {
            die('File fisik tidak ditemukan di server.');
        }

        // Inline display
        header('Content-Type: ' . $doc->mime_type);
        header('Content-Disposition: inline; filename="' . $doc->name . '.' . $doc->file_type . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        readfile($file_path);
        exit;
    }

    public function rename()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $doc_id = isset($input['document_id']) ? (int) $input['document_id'] : 0;
        $new_name = isset($input['new_name']) ? trim($input['new_name']) : '';

        if (empty($new_name)) {
            echo json_encode(['status' => false, 'message' => 'Nama file tidak boleh kosong.']);
            return;
        }

        $doc = $this->mod->get_document_by_id($doc_id);
        if (!$doc) {
            echo json_encode(['status' => false, 'message' => 'Dokumen tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        // Update name
        $this->mod->update_document($doc_id, [
            'name' => $new_name,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Session::get('user_id')
        ]);

        // Log activity
        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'UPDATE', 'document', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $doc_id, "Mengubah nama file dari '{$doc->name}' menjadi '{$new_name}'", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Nama file berhasil diubah.']);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        $doc_id = isset($_POST['document_id']) ? (int) $_POST['document_id'] : 0;

        $doc = $this->mod->get_document_by_id($doc_id);
        if (!$doc) {
            echo json_encode(['status' => false, 'message' => 'Dokumen tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        // Soft delete
        $this->mod->delete_document($doc_id);

        // Update stats
        if ($doc->folder_id) {
            $this->db->query("UPDATE folders SET total_files = GREATEST(0, total_files - 1), total_size = GREATEST(0, total_size - ?) WHERE id = ?", [$doc->file_size, $doc->folder_id]);
        }
        $this->db->query("UPDATE drives SET total_size = GREATEST(0, total_size - ?) WHERE id = ?", [$doc->file_size, $doc->drive_id]);

        // Log activity
        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'DELETE', 'document', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $doc_id, "Memindahkan file ke Recycle Bin: {$doc->name}", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Dokumen dipindahkan ke Recycle Bin.']);
    }

    public function move()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        $doc_id = isset($_POST['document_id']) ? (int) $_POST['document_id'] : 0;
        $target_folder_id = isset($_POST['target_folder_id']) && !empty($_POST['target_folder_id']) ? (int) $_POST['target_folder_id'] : null;

        $doc = $this->mod->get_document_by_id($doc_id);
        if (!$doc) {
            echo json_encode(['status' => false, 'message' => 'Dokumen tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        // Validate target folder if not root
        $folder_name = 'Root Drive';
        if ($target_folder_id) {
            $target_folder = $this->db->table('folders')->where('id', $target_folder_id)->where('status', 1)->row();
            if (!$target_folder) {
                echo json_encode(['status' => false, 'message' => 'Folder tujuan tidak valid atau sudah dihapus.']);
                return;
            }
            if ($target_folder->drive_id != $doc->drive_id) {
                echo json_encode(['status' => false, 'message' => 'Tidak dapat memindahkan file ke drive yang berbeda.']);
                return;
            }
            $folder_name = $target_folder->name;
        }

        if ($doc->folder_id === $target_folder_id) {
            echo json_encode(['status' => false, 'message' => 'File sudah berada di folder tujuan.']);
            return;
        }

        $old_folder_id = $doc->folder_id;

        // Update document
        $this->mod->update_document($doc_id, [
            'folder_id' => $target_folder_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Session::get('user_id')
        ]);

        // Update stats
        if ($old_folder_id) {
            $this->db->query("UPDATE folders SET total_files = GREATEST(0, total_files - 1), total_size = GREATEST(0, total_size - ?) WHERE id = ?", [$doc->file_size, $old_folder_id]);
        }
        if ($target_folder_id) {
            $this->db->query("UPDATE folders SET total_files = total_files + 1, total_size = total_size + ? WHERE id = ?", [$doc->file_size, $target_folder_id]);
        }

        // Log activity
        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'MOVE', 'document', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $doc_id, "Memindahkan file '{$doc->name}' ke '{$folder_name}'", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'File berhasil dipindahkan.']);
    }
}
