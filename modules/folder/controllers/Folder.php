<?php

class Folder extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->model('folder/MOD', 'mod');
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        $drive_id = isset($_POST['drive_id']) ? (int)$_POST['drive_id'] : 0;
        $parent_id = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $name = trim($_POST['name'] ?? '');

        if (!$drive_id || empty($name)) {
            echo json_encode(['status' => false, 'message' => 'Nama folder dan ID Drive harus diisi.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Anda tidak memiliki akses ke Drive ini.']);
            return;
        }

        // Slug generation logic (simple)
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        // Path logic
        $path = '/' . $name;
        if ($parent_id) {
            $parent = $this->mod->get_folder_by_id($parent_id);
            if ($parent) {
                $path = $parent->path . '/' . $name;
            }
        }

        // Create physical folder
        $role_id = Session::get('role_id') ?? 0;
        $year = date('Y');
        $path_parts = array_filter(explode('/', $path));
        $safe_path = implode('/', array_map(function($p) {
            return preg_replace('/[^a-zA-Z0-9_ -]/', '_', $p);
        }, $path_parts));
        
        $physical_dir = STORAGEPATH . 'drives/' . $role_id . '/' . $year . '/' . $safe_path;
        if (!is_dir($physical_dir)) {
            mkdir($physical_dir, 0755, true);
        }

        $data = [
            'drive_id'   => $drive_id,
            'parent_id'  => $parent_id,
            'name'       => $name,
            'slug'       => $slug,
            'path'       => $path,
            'color'      => '#F59E0B', // Default amber color
        ];

        try {
            $id = $this->mod->insert_folder($data);
            
            // Log activity
            $this->db->query(
                "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'CREATE', 'folder', ?, ?, ?, NOW(), 1)",
                [Session::get('user_id'), $id, "Membuat folder baru: $name", $_SERVER['REMOTE_ADDR'] ?? '']
            );

            echo json_encode(['status' => true, 'message' => 'Folder berhasil dibuat', 'folder_id' => $id]);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Gagal membuat folder.']);
        }
    }

    public function rename() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $folder_id = isset($input['folder_id']) ? (int)$input['folder_id'] : 0;
        $new_name = isset($input['new_name']) ? trim($input['new_name']) : '';

        if (empty($new_name)) {
            echo json_encode(['status' => false, 'message' => 'Nama folder tidak boleh kosong.']);
            return;
        }

        $folder = $this->mod->get_folder_by_id($folder_id);
        if (!$folder) {
            echo json_encode(['status' => false, 'message' => 'Folder tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($folder->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        // Slug generation logic (simple)
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $new_name)));

        // Path logic
        $new_path = '/' . $slug;
        if ($folder->parent_id) {
            $parent = $this->mod->get_folder_by_id($folder->parent_id);
            if ($parent) {
                $new_path = $parent->path . '/' . $slug;
            }
        }
        
        $old_path = $folder->path;
        
        // Update DB
        $this->db->table('folders')->where('id', $folder_id)->update([
            'name' => $new_name,
            'slug' => $slug,
            'path' => $new_path,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Session::get('user_id')
        ]);
        
        // Find physical folder
        $year = date('Y', strtotime($folder->created_at));
        $owner_role = $folder->created_by; // Assuming owner role is the creator
        // In e-drive, it was Session::get('role_id') during creation
        
        // We will just find ANY documents inside this folder and replace their file_path in DB.
        // It's safer to just do a SQL REPLACE for documents directly under this folder.
        // Also rename physical dir if it exists
        $docs = $this->db->table('documents')->where('folder_id', $folder_id)->get();
        foreach ($docs as $doc) {
            $old_file_path = $doc->file_path;
            
            // Recompute paths by replacing old folder slug with new slug in the directory string
            $old_slug_segment = '/' . $folder->slug . '/';
            $new_slug_segment = '/' . $slug . '/';
            
            // Note: Since e-drive has flat file_paths relative to storage: drives/1/2026/gdgdgd/file.png
            // We find the physical directory of the document and rename it if we haven't already.
            $doc_physical_dir = STORAGEPATH . dirname($old_file_path);
            if (is_dir($doc_physical_dir) && basename($doc_physical_dir) === $folder->slug) {
                $new_physical_dir = dirname($doc_physical_dir) . '/' . $slug;
                if (!is_dir($new_physical_dir)) {
                    @rename($doc_physical_dir, $new_physical_dir);
                }
            }
            
            // Update document DB
            $new_file_path = preg_replace('#/' . preg_quote($folder->slug, '#') . '/#', '/' . $slug . '/', $old_file_path, 1);
            if ($new_file_path !== $old_file_path) {
                $this->db->table('documents')->where('id', $doc->id)->update(['file_path' => $new_file_path]);
            }
        }
        
        // Also try to rename empty folder directory if no docs exist but directory was created
        $empty_physical_dir = STORAGEPATH . 'drives/' . $folder->created_by . '/' . $year . $old_path;
        if (is_dir($empty_physical_dir)) {
            $empty_new_dir = STORAGEPATH . 'drives/' . $folder->created_by . '/' . $year . $new_path;
            @rename($empty_physical_dir, $empty_new_dir);
        }

        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'UPDATE', 'folder', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $folder_id, "Mengubah nama folder dari '{$folder->name}' menjadi '{$new_name}'", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Nama folder berhasil diubah.']);
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $folder_id = isset($input['folder_id']) ? (int)$input['folder_id'] : 0;
        
        $folder = $this->mod->get_folder_by_id($folder_id);
        if (!$folder) {
            echo json_encode(['status' => false, 'message' => 'Folder tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($folder->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        // Soft delete folder
        $this->mod->delete_folder($folder_id);
        
        // Log activity
        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'DELETE', 'folder', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $folder_id, "Memindahkan folder ke Recycle Bin: {$folder->name}", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Folder dipindahkan ke Recycle Bin.']);
    }
}
