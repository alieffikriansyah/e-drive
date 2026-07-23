<?php

class Activity_log extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->model('activity_log/MOD', 'mod');
    }

    public function index() {
        $user_id = Session::get('user_id');
        $is_admin = AuthMiddleware::isManager();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $logs = $this->mod->get_logs($is_admin, $user_id, $limit, $offset);
        $total_rows = $this->mod->get_total_count($is_admin, $user_id);
        $total_pages = ceil($total_rows / $limit);

        $data = [
            'title'       => 'Activity Log',
            'logs'        => $logs,
            'is_admin'    => $is_admin,
            'current_page'=> $page,
            'total_pages' => $total_pages,
            'breadcrumb'  => [['name' => 'Activity Log']]
        ];

        $this->load->view('activity_log/v_index', $data);
    }
}
