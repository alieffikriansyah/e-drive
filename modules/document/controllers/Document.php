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

        $mime_type = get_mime_type($target_file);
        $checksum = hash_file('sha256', $target_file);

        // 4. Save to Database
        $now = date('Y-m-d H:i:s');
        $data = [
            'drive_id' => $drive_id,
            'folder_id' => $folder_id,
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
            if ($folder_id) {
                $this->db->query("UPDATE folders SET total_files = total_files + 1, total_size = total_size + ? WHERE id = ?", [$file_size, $folder_id]);
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
}
