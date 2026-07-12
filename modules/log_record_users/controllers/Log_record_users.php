<?php

require_once APPPATH . 'middleware/AuthMiddleware.php';

class Log_record_users extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('log_record_users/MOD', 'mod');
    }

    public function _middleware()
    {
        AuthMiddleware::check();
        
        // Cek Role, hanya admin yang boleh akses
        $user_role = strtolower($_SESSION['role_name'] ?? '');
        if ($user_role !== 'admin') {
            die('Akses ditolak. Hanya Admin yang diizinkan mengakses halaman ini.');
        }
    }

    public function index()
    {
        $this->load->view('log_record_users/v_index');
    }

    public function auth()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $this->input->xss_clean($_POST['password'] ?? '');

            if ($password === 'akusangkuasa') {
                $_SESSION['log_record_authenticated'] = true;
                header('Content-Type: application/json');
                echo json_encode(['status' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Password salah!']);
            }
            exit;
        }
    }

    public function load_data()
    {
        if (empty($_SESSION['log_record_authenticated'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Sesi habis, silakan masukkan password lagi.']);
            exit;
        }

        $tgl_awal = $this->input->xss_clean($_GET['tgl_awal'] ?? date('Y-m-d'));
        $tgl_akhir = $this->input->xss_clean($_GET['tgl_akhir'] ?? date('Y-m-d'));

        $data = $this->mod->get_logs($tgl_awal, $tgl_akhir);

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }
}
