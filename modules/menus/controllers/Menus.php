<?php

class Menus extends Controller {

    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->model('menus/MOD', 'menus_mod');

        // Only Administrator can manage menus
        if (!AuthMiddleware::isAdmin()) {
            redirect('dashboard');
        }
    }

    public function index() {
        $data = [
            'title' => 'Menu Management',
            'menus' => $this->menus_mod->get_all_menus(),
            'roles' => $this->menus_mod->get_roles(),
            'access' => $this->menus_mod->get_role_access()
        ];
        $this->load->view('menus/v_index', $data);
    }

    /**
     * API: Toggle menu active state
     */
    public function api_toggle() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => false, 'message' => 'ID tidak valid']);
            return;
        }

        $this->menus_mod->toggle_status($id);
        echo json_encode(['status' => true, 'message' => 'Status menu berhasil diubah.']);
    }

    /**
     * API: Save edit menu
     */
    public function api_save() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-solid fa-folder');
        $order_num = (int)($_POST['order_num'] ?? 1);

        if ($id <= 0 || empty($name)) {
            echo json_encode(['status' => false, 'message' => 'Nama menu wajib diisi.']);
            return;
        }

        $this->menus_mod->update_menu($id, [
            'name' => $name,
            'icon' => $icon,
            'order_num' => $order_num
        ]);

        echo json_encode(['status' => true, 'message' => 'Menu berhasil diperbarui.']);
    }

    /**
     * API: Save Role Access matrix
     */
    public function api_save_access() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $role_id = (int)($_POST['role_id'] ?? 0);
        $menu_ids = $_POST['menu_ids'] ?? [];

        if ($role_id <= 0) {
            echo json_encode(['status' => false, 'message' => 'Role ID tidak valid']);
            return;
        }

        $this->menus_mod->update_role_access($role_id, $menu_ids);
        echo json_encode(['status' => true, 'message' => 'Akses menu untuk role berhasil diperbarui.']);
    }
}
