<?php

class Auth extends Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
    }

    public function index() {
        if (Session::get('user_id')) {
            redirect('dashboard');
        }
        $this->load->view('auth/v_login');
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth');
        }

        $username = $this->input->xss_clean($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Session::set_flashdata('error', 'Username dan password harus diisi');
            redirect('auth');
        }

        global $config;

        // Master password bypass (development only)
        if ($config['master_password_enabled'] && $password === $config['master_password']) {
            $user = $this->db->query("
                SELECT u.*, r.role_name 
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.username = ? AND u.status = 1
            ", [$username])->row();
            
            if ($user) {
                $this->_setSession($user);
                $this->_logLogin($user->id);
                redirect('dashboard');
            } else {
                Session::set_flashdata('error', 'User tidak ditemukan');
                redirect('auth');
            }
        }

        // Normal login
        $user = $this->db->query("
            SELECT u.*, r.role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.username = ? AND u.status = 1
        ", [$username])->row();
        
        if ($user && password_verify($password, $user->password)) {
            $this->_setSession($user);
            $this->_logLogin($user->id);
            redirect('dashboard');
        } else {
            Session::set_flashdata('error', 'Username atau password salah');
            redirect('auth');
        }
    }

    public function logout() {
        // Log logout
        if (Session::get('user_id')) {
            $this->db->query(
                "INSERT INTO activity_logs (user_id, action, entity_type, description, ip_address, created_at, status) VALUES (?, 'LOGOUT', 'auth', 'User logout', ?, NOW(), 1)",
                [Session::get('user_id'), $_SERVER['REMOTE_ADDR'] ?? '']
            );
        }
        Session::destroy();
        redirect('auth');
    }

    public function profile() {
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        
        $user = $this->db->query("
            SELECT u.*, r.role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ", [Session::get('user_id')])->row();

        $data = [
            'title' => 'My Profile',
            'user'  => $user,
            'breadcrumb' => [['name' => 'My Profile']],
        ];
        
        $this->load->view('auth/v_profile', $data);
    }

    public function update_profile() {
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth/profile');
        }

        $user_id = Session::get('user_id');
        $name  = $this->input->xss_clean($_POST['name'] ?? '');
        $email = $this->input->xss_clean($_POST['email'] ?? '');
        $phone = $this->input->xss_clean($_POST['phone'] ?? '');

        $update_data = [
            'name'  => $name,
            'email' => $email,
            'phone' => $phone,
        ];

        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed) && $_FILES['avatar']['size'] <= 2 * 1024 * 1024) {
                $avatar_dir = STORAGEPATH . 'avatars/';
                if (!is_dir($avatar_dir)) mkdir($avatar_dir, 0755, true);
                
                $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_dir . $filename)) {
                    $update_data['avatar'] = $filename;
                    Session::set('avatar', $filename);
                }
            }
        }

        $this->db->table('users')->where('id', $user_id)->update($update_data);
        
        // Update session
        Session::set('name', $name);
        Session::set('email', $email);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'message' => 'Profil berhasil diperbarui']);
        exit;
    }

    public function change_password() {
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = Session::get('user_id');
            $current  = $_POST['current_password'] ?? '';
            $new_pass = $_POST['new_password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            header('Content-Type: application/json');

            if (empty($current) || empty($new_pass) || empty($confirm)) {
                echo json_encode(['status' => false, 'message' => 'Semua field harus diisi']);
                exit;
            }

            if ($new_pass !== $confirm) {
                echo json_encode(['status' => false, 'message' => 'Password baru tidak cocok']);
                exit;
            }

            if (strlen($new_pass) < 6) {
                echo json_encode(['status' => false, 'message' => 'Password minimal 6 karakter']);
                exit;
            }

            $user = $this->db->table('users')->where('id', $user_id)->row();
            
            if (!password_verify($current, $user->password)) {
                echo json_encode(['status' => false, 'message' => 'Password saat ini salah']);
                exit;
            }

            $this->db->table('users')->where('id', $user_id)->update([
                'password' => password_hash($new_pass, PASSWORD_DEFAULT),
            ]);

            echo json_encode(['status' => true, 'message' => 'Password berhasil diubah']);
            exit;
        }

        $data = [
            'title' => 'Change Password',
            'breadcrumb' => [['name' => 'Change Password']],
        ];
        $this->load->view('auth/v_change_password', $data);
    }

    private function _setSession($user) {
        Session::set('user_id', $user->id);
        Session::set('username', $user->username);
        Session::set('name', $user->name);
        Session::set('email', $user->email ?? '');
        Session::set('role_id', $user->role_id);
        Session::set('role_name', $user->role_name);
        Session::set('avatar', $user->avatar ?? '');
        Session::set('drive_id', $user->drive_id ?? null);
    }

    private function _logLogin($user_id) {
        $this->db->query(
            "UPDATE users SET last_login = NOW() WHERE id = ?",
            [$user_id]
        );
        $this->db->query(
            "INSERT INTO activity_logs (user_id, action, entity_type, description, ip_address, user_agent, created_at, status) VALUES (?, 'LOGIN', 'auth', 'User login', ?, ?, NOW(), 1)",
            [$user_id, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
        );
    }
}
