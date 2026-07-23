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

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');
        
        $folder_id = isset($_POST['folder_id']) ? (int)$_POST['folder_id'] : 0;
        
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
