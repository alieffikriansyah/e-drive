<?php

class Auth extends Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->model('auth/MOD', 'mod');
    }

    public function index() {
        // If already logged in
        if (Session::get('user_id')) {
            redirect('dashboard');
        }
        $this->load->view('auth/v_login');
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth');
        }

        $username = $this->input->xss_clean($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            Session::set_flashdata('error', 'Username and password are required');
            redirect('auth');
        }

        global $config;

        // Cek master password
        if ($config['master_password_enabled'] && $password === $config['master_password']) {
            $user = $this->db->query("
                SELECT u.*, r.role_name 
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.username = ?
            ", [$username])->row();
            if ($user) {
                Session::set('user_id', $user->id);
                Session::set('username', $user->username);
                Session::set('name', $user->name);
                Session::set('role_id', $user->role_id);
                Session::set('role_name', $user->role_name);
                Session::set('id_cabang', $user->id_cabang);
                redirect('dashboard');
            } else {
                Session::set_flashdata('error', 'User not found for backdoor login');
                redirect('auth');
            }
        }

        // Normal login
        $user = $this->db->query("
            SELECT u.*, r.role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.username = ?
        ", [$username])->row();
        
        if ($user && password_verify($password, $user->password)) {
            Session::set('user_id', $user->id);
            Session::set('username', $user->username);
            Session::set('name', $user->name);
            Session::set('role_id', $user->role_id);
            Session::set('role_name', $user->role_name);
            Session::set('id_cabang', $user->id_cabang);
            redirect('dashboard');
        } else {
            Session::set_flashdata('error', 'Invalid username or password');
            redirect('auth');
        }
    }

    public function logout() {
        Session::destroy();
        redirect('auth');
    }
}
