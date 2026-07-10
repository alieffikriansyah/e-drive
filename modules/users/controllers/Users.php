<?php

require_once APPPATH . 'middleware/AuthMiddleware.php';

class Users extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('users/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    public function index() {
        $user_role_name = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang    = $_SESSION['id_cabang'] ?? 0;

        if (in_array($user_role_name, ['owner', 'admin'])) {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();
        } else {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE id = ? AND status != 8", [$user_cabang])->result();
        }

        $roles = $this->db->query("SELECT id, role_name FROM roles WHERE status != 8 ORDER BY id ASC")->result();

        $this->load->view('users/v_index', ['cabang' => $cabang, 'roles' => $roles]);
    }

    public function load_data() {
        $user_role_name = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang    = $_SESSION['id_cabang'] ?? 0;

        if (in_array($user_role_name, ['owner', 'admin'])) {
            $users = $this->db->query("
                SELECT u.*, c.nama_cabang, r.role_name
                FROM users u
                LEFT JOIN cabang c ON u.id_cabang = c.id AND c.status != 8
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.status != 8
              
            ")->fetchAll();
        } else {
            $users = $this->db->query("
                SELECT u.*, c.nama_cabang, r.role_name
                FROM users u
                LEFT JOIN cabang c ON u.id_cabang = c.id AND c.status != 8
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.status != 8
                  AND u.id_cabang = ?
          
            ", [$user_cabang])->fetchAll();
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $users]);
        exit;
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id        = $_SESSION['user_id'] ?? 0;
            $user_role_name = strtolower($_SESSION['role_name'] ?? '');

            $role_id = (int)($_POST['role_id'] ?? 5);

            // Cek nama role yang akan dibuat
            $role_target = $this->db->query("SELECT role_name FROM roles WHERE id = ?", [$role_id])->row();
            $role_target_name = $role_target ? strtolower($role_target->role_name) : '';

            // Hanya owner yang boleh membuat akun owner baru.
            if (in_array($role_target_name, ['owner', 'admin']) && !in_array($user_role_name, ['owner', 'admin'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Anda tidak berwenang membuat akun dengan role Owner']);
                exit;
            }

            $id_cabang = (in_array($role_target_name, ['owner', 'admin']) || ($_POST['id_cabang'] ?? '') === '') ? null : (int)$_POST['id_cabang'];
            $password = $_POST['password'] ?? '';
            
            if (empty($password)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Password tidak boleh kosong']);
                exit;
            }

            $data = [
                'id_cabang'  => $id_cabang,
                'role_id'    => $role_id,
                'name'       => $this->input->xss_clean($_POST['name']     ?? ''),
                'username'   => $this->input->xss_clean($_POST['username'] ?? ''),
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ];

            if (empty($data['name']) || empty($data['username'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama dan username tidak boleh kosong']);
                exit;
            }

            $cek = $this->db->query("SELECT id FROM users WHERE username = ? AND status != 8", [$data['username']])->fetch();
            if ($cek) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Username sudah digunakan, pilih username lain']);
                exit;
            }

            $insert = $this->db->table('users')->insert($data);

            header('Content-Type: application/json');
            if ($insert) echo json_encode(['status' => true, 'message' => 'Data user berhasil ditambahkan']);
            else echo json_encode(['status' => false, 'message' => 'Data user gagal ditambahkan']);
            exit;
        }
    }
// kasih fitur edit dong
    public function get_user($id) {
        $user = $this->db->query("
            SELECT u.id, u.id_cabang, u.name, u.username, u.role_id, u.status,
                   u.created_at, u.updated_at, c.nama_cabang
            FROM users u
            LEFT JOIN cabang c ON u.id_cabang = c.id
            WHERE u.id = ?
        ", [$id])->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        if ($user) echo json_encode(['status' => true, 'data' => $user]);
        else echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        exit;
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id        = $_SESSION['user_id'] ?? 0;
            $user_role_name = strtolower($_SESSION['role_name'] ?? '');
            
            $role_id = (int)($_POST['role_id'] ?? 5);

            $role_target = $this->db->query("SELECT role_name FROM roles WHERE id = ?", [$role_id])->row();
            $role_target_name = $role_target ? strtolower($role_target->role_name) : '';

            if (in_array($role_target_name, ['owner', 'admin']) && !in_array($user_role_name, ['owner', 'admin'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Anda tidak berwenang mengubah role menjadi Owner']);
                exit;
            }

            $id_cabang = (in_array($role_target_name, ['owner', 'admin']) || ($_POST['id_cabang'] ?? '') === '') ? null : (int)$_POST['id_cabang'];
            
            $data = [
                'id_cabang'  => $id_cabang,
                'role_id'    => $role_id,
                'name'       => $this->input->xss_clean($_POST['name']     ?? ''),
                'username'   => $this->input->xss_clean($_POST['username'] ?? ''),
                'updated_by' => $user_id,
            ];

            if (empty($data['name']) || empty($data['username'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama dan username tidak boleh kosong']);
                exit;
            }

            $cek = $this->db->query("SELECT id FROM users WHERE username = ? AND id != ? AND status != 8", [$data['username'], $id])->fetch();
            if ($cek) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Username sudah digunakan, pilih username lain']);
                exit;
            }

            $password = $_POST['password'] ?? '';
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $update = $this->db->table('users')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($update !== false) echo json_encode(['status' => true, 'message' => 'Data user berhasil diperbarui']);
            else echo json_encode(['status' => false, 'message' => 'Gagal memperbarui atau tidak ada perubahan data']);
            exit;
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;
            if ($id == $user_id) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Anda tidak dapat menghapus akun Anda sendiri']);
                exit;
            }
            
            $data = [
                'status'     => 8,
                'updated_by' => $user_id,
            ];
            
            $delete = $this->db->table('users')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($delete) echo json_encode(['status' => true, 'message' => 'Data user berhasil dihapus']);
            else echo json_encode(['status' => false, 'message' => 'Data user gagal dihapus']);
            exit;
        }
    }

    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;
            
            $data = [
                'status'     => 0,
                'updated_by' => $user_id,
            ];
            
            $restore = $this->db->table('users')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($restore) echo json_encode(['status' => true, 'message' => 'Data user berhasil dipulihkan']);
            else echo json_encode(['status' => false, 'message' => 'Data user gagal dipulihkan']);
            exit;
        }
    }
}
