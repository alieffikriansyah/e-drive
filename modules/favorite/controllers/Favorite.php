<?php

class Favorite extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->model('favorite/MOD', 'mod');
    }

    public function index() {
        $user_id = Session::get('user_id');
        $favorites = $this->mod->get_user_favorites($user_id);

        $data = [
            'title'      => 'Favorit Saya',
            'favorites'  => $favorites,
            'breadcrumb' => [['name' => 'Favorit Saya']]
        ];

        $this->load->view('favorite/v_index', $data);
    }

    public function toggle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        header('Content-Type: application/json');

        $entity_id = isset($_POST['entity_id']) ? (int)$_POST['entity_id'] : 0;
        $entity_type = $_POST['entity_type'] ?? 'document';
        $user_id = Session::get('user_id');

        if (!$entity_id) {
            echo json_encode(['status' => false, 'message' => 'ID tidak valid.']);
            return;
        }

        $existing = $this->mod->check_favorite($user_id, $entity_type, $entity_id);

        if ($existing) {
            $new_status = $existing->status == 1 ? 0 : 1;
            $this->mod->update_favorite($existing->id, ['status' => $new_status, 'updated_at' => date('Y-m-d H:i:s')]);
            $is_favorite = $new_status == 1;
        } else {
            $this->mod->insert_favorite([
                'user_id' => $user_id,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
            ]);
            $is_favorite = true;
        }

        echo json_encode([
            'status' => true, 
            'is_favorite' => $is_favorite,
            'message' => $is_favorite ? 'Ditambahkan ke Favorit' : 'Dihapus dari Favorit'
        ]);
    }
}
