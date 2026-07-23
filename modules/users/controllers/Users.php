<?php

class Users extends Controller {

    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        // Hanya Admin yang bisa akses menu Users
        if (!AuthMiddleware::isManager()) {
            redirect('dashboard');
        }
        $this->load->helper('url');
        $this->load->model('users/MOD', 'mod');
    }

    public function index() {
        $roles = $this->mod->get_active_roles();
        $drives = $this->mod->get_active_drives();
        
        $data = [
            'title' => 'Manajemen Pengguna',
            'roles' => $roles,
            'drives'=> $drives,
            'breadcrumb' => [['name' => 'Manajemen Pengguna']]
        ];

        $this->load->view('users/v_index', $data);
    }

    public function load_data() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            exit;
        }

        $users = $this->mod->get_all_users();

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $users]);
        exit;
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $user_id = Session::get('user_id');
            $role_id = (int)($_POST['role_id'] ?? 5);
            $drive_id = empty($_POST['drive_id']) ? null : (int)$_POST['drive_id'];
            
            $username = $this->input->xss_clean($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $name     = $this->input->xss_clean($_POST['name'] ?? '');
            
            if (empty($username) || empty($password) || empty($name)) {
                echo json_encode(['status' => false, 'message' => 'Username, Password, dan Nama harus diisi']);
                exit;
            }

            // Cek username unik
            if ($this->mod->check_username_exists($username)) {
                echo json_encode(['status' => false, 'message' => 'Username sudah digunakan']);
                exit;
            }

            $data = [
                'username'   => $username,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'name'       => $name,
                'email'      => $this->input->xss_clean($_POST['email'] ?? ''),
                'phone'      => $this->input->xss_clean($_POST['phone'] ?? ''),
                'role_id'    => $role_id,
                'drive_id'   => $drive_id,
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ];

            $insert = $this->mod->insert_user($data);

            if ($insert) {
                echo json_encode(['status' => true, 'message' => 'Pengguna berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Gagal menambahkan pengguna']);
            }
            exit;
        }
    }

    public function get_user($id) {
        $user = $this->mod->get_user_by_id($id);

        header('Content-Type: application/json');
        if ($user) {
            echo json_encode(['status' => true, 'data' => $user]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $user_id = Session::get('user_id');
            $role_id = (int)($_POST['role_id'] ?? 5);
            $drive_id = empty($_POST['drive_id']) ? null : (int)$_POST['drive_id'];
            
            $username = $this->input->xss_clean($_POST['username'] ?? '');
            $name     = $this->input->xss_clean($_POST['name'] ?? '');
            
            if (empty($username) || empty($name)) {
                echo json_encode(['status' => false, 'message' => 'Username dan Nama harus diisi']);
                exit;
            }

            // Cek username unik (kecuali milik sendiri)
            if ($this->mod->check_username_exists($username, $id)) {
                echo json_encode(['status' => false, 'message' => 'Username sudah digunakan']);
                exit;
            }

            $data = [
                'username'   => $username,
                'name'       => $name,
                'email'      => $this->input->xss_clean($_POST['email'] ?? ''),
                'phone'      => $this->input->xss_clean($_POST['phone'] ?? ''),
                'role_id'    => $role_id,
                'drive_id'   => $drive_id,
                'updated_by' => $user_id,
            ];

            $password = $_POST['password'] ?? '';
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $update = $this->mod->update_user($id, $data);

            if ($update !== false) {
                echo json_encode(['status' => true, 'message' => 'Data pengguna berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Gagal memperbarui pengguna']);
            }
            exit;
        }
    }

    public function toggle_status($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            if ($id == Session::get('user_id')) {
                echo json_encode(['status' => false, 'message' => 'Anda tidak dapat mengubah status akun Anda sendiri']);
                exit;
            }
            
            $user = $this->mod->get_user_by_id($id);
            if (!$user) {
                echo json_encode(['status' => false, 'message' => 'Pengguna tidak ditemukan']);
                exit;
            }

            $new_status = ($user['status'] == 1) ? 0 : 1;
            $this->mod->update_user($id, [
                'status' => $new_status,
                'updated_by' => Session::get('user_id')
            ]);

            echo json_encode(['status' => true, 'message' => 'Status berhasil diubah']);
            exit;
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            if ($id == Session::get('user_id')) {
                echo json_encode(['status' => false, 'message' => 'Anda tidak dapat menghapus akun Anda sendiri']);
                exit;
            }
            
            $delete = $this->mod->update_user($id, [
                'status' => 8,
                'updated_by' => Session::get('user_id')
            ]);

            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Pengguna berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Gagal menghapus pengguna']);
            }
            exit;
        }
    }
}
