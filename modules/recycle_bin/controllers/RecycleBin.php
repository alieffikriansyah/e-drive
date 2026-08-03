<?php

class RecycleBin extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->model('recycle_bin/MOD', 'mod');
    }

    public function index() {
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');
        $is_admin = AuthMiddleware::isManager();

        $drives_sql = "SELECT id FROM drives WHERE status = 1";
        if (!$is_admin) {
            $drives_sql .= " AND (owner_role_id = $role_id OR is_shared = 1 
                           OR id IN (SELECT entity_id FROM shared_access WHERE entity_type = 'drive' AND user_id = $user_id AND status = 1))";
        }
        $allowed_drives = $this->db->query($drives_sql)->fetchAll();
        $drive_ids = array_column($allowed_drives, 'id');

        $deleted_docs = $this->mod->get_deleted_documents($drive_ids, $is_admin);
        $deleted_folders = $this->mod->get_deleted_folders($drive_ids, $is_admin);

        $data = [
            'title'        => 'Recycle Bin',
            'documents'    => $deleted_docs,
            'folders'      => $deleted_folders,
            'breadcrumb'   => [['name' => 'Recycle Bin']]
        ];

        $this->load->view('recycle_bin/v_index', $data);
    }

    public function restore() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');
        
        $doc_id = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
        
        $doc = $this->db->table('documents')->with_trashed()->where('id', $doc_id)->where('status', 8)->row();
        if (!$doc) {
            echo json_encode(['status' => false, 'message' => "Dokumen tidak ditemukan di Recycle Bin (ID: $doc_id)."]);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $this->mod->restore_document($doc_id);

        if ($doc->folder_id) {
            $this->db->query("UPDATE folders SET total_files = total_files + 1, total_size = total_size + ? WHERE id = ?", [$doc->file_size, $doc->folder_id]);
        }
        $this->db->query("UPDATE drives SET total_size = total_size + ? WHERE id = ?", [$doc->file_size, $doc->drive_id]);

        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'RESTORE', 'document', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $doc_id, "Memulihkan file: {$doc->name}", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Dokumen berhasil dipulihkan.']);
    }

    public function permanent_delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');
        
        $doc_id = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
        
        $doc = $this->db->table('documents')->with_trashed()->where('id', $doc_id)->where('status', 8)->row();
        if (!$doc) {
            echo json_encode(['status' => false, 'message' => 'Dokumen tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $file_path = STORAGEPATH . $doc->file_path;
        if (!file_exists($file_path) && file_exists(STORAGEPATH . 'documents/' . $doc->file_path)) {
            $file_path = STORAGEPATH . 'documents/' . $doc->file_path;
        }

        if (file_exists($file_path)) {
            @unlink($file_path);
        }

        $this->mod->permanent_delete($doc_id);

        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'DELETE_PERMANENT', 'document', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $doc_id, "Menghapus permanen file: {$doc->name}", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Dokumen dihapus secara permanen.']);
    }

    public function restore_folder() {
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
        
        $folder = $this->db->table('folders')->with_trashed()->where('id', $folder_id)->where('status', 8)->row();
        if (!$folder) {
            echo json_encode(['status' => false, 'message' => "Folder tidak ditemukan di Recycle Bin (ID: $folder_id)."]);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($folder->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        $this->mod->restore_folder($folder_id);

        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'RESTORE', 'folder', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $folder_id, "Memulihkan folder: {$folder->name}", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Folder berhasil dipulihkan.']);
    }

    public function permanent_delete_folder() {
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
        
        $folder = $this->db->table('folders')->with_trashed()->where('id', $folder_id)->where('status', 8)->row();
        if (!$folder) {
            echo json_encode(['status' => false, 'message' => 'Folder tidak ditemukan.']);
            return;
        }

        if (!AuthMiddleware::canAccessDrive($folder->drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }

        // Helper to recursively delete directory
        $deleteDir = function($dirPath) use (&$deleteDir) {
            if (!is_dir($dirPath)) return;
            if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') $dirPath .= '/';
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $deleteDir($file);
                } else {
                    @unlink($file);
                }
            }
            @rmdir($dirPath);
        };

        // Find and delete physical folder
        $year = date('Y', strtotime($folder->created_at));
        $physical_dir = STORAGEPATH . 'drives/' . $folder->created_by . '/' . $year . $folder->path;
        $deleteDir($physical_dir);

        // Delete documents under this folder in DB
        $docs = $this->db->table('documents')->where('folder_id', $folder_id)->get();
        foreach ($docs as $doc) {
            $this->mod->permanent_delete($doc->id);
        }

        $this->mod->permanent_delete_folder($folder_id);

        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at, status) VALUES (?, 'DELETE_PERMANENT', 'folder', ?, ?, ?, NOW(), 1)",
            [Session::get('user_id'), $folder_id, "Menghapus permanen folder: {$folder->name}", $_SERVER['REMOTE_ADDR'] ?? '']
        );

        echo json_encode(['status' => true, 'message' => 'Folder dihapus secara permanen.']);
    }
}
