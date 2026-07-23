<?php

class Search extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->model('search/MOD', 'mod');
    }

    public function index() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $results = [];

        if (!empty($query)) {
            $user_id = Session::get('user_id');
            $role_id = Session::get('role_id');
            $is_admin = AuthMiddleware::isManager();

            // Dapatkan ID drive yang bisa diakses user ini
            $drives_sql = "SELECT id FROM drives WHERE status = 1";
            if (!$is_admin) {
                $drives_sql .= " AND (owner_role_id = $role_id OR is_shared = 1 
                               OR id IN (SELECT entity_id FROM shared_access WHERE entity_type = 'drive' AND user_id = $user_id AND status = 1))";
            }
            $allowed_drives = $this->db->query($drives_sql)->fetchAll();
            $drive_ids = array_column($allowed_drives, 'id');

            $results = $this->mod->search_documents($query, $drive_ids);
        }

        $data = [
            'title'      => 'Pencarian',
            'query'      => $query,
            'results'    => $results,
            'breadcrumb' => [['name' => 'Pencarian']]
        ];

        $this->load->view('search/v_index', $data);
    }
}
