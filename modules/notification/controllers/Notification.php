<?php

class Notification extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->model('notification/MOD', 'mod');
    }

    public function index() {
        $user_id = Session::get('user_id');
        
        $notifications = $this->mod->get_user_notifications($user_id, 50);

        // Mark all as read when page is opened
        $this->mod->mark_all_read($user_id);

        $data = [
            'title'         => 'Notifikasi',
            'notifications' => $notifications,
            'breadcrumb'    => [['name' => 'Notifikasi']]
        ];

        $this->load->view('notification/v_index', $data);
    }
    
    public function get_unread_count() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');
        
        $user_id = Session::get('user_id');
        $this->load->helper('notification');
        
        $count = get_unread_notification_count($user_id);
        
        echo json_encode(['status' => true, 'count' => $count]);
    }
}
