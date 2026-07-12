<?php

require_once APPPATH . 'middleware/AuthMiddleware.php';

class LogRecordUsers extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        require_once MODULESPATH . 'log_record_users/models/MOD.php';
        $this->MOD = new MOD();
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

            global $config;
            $master_password = $config['master_password'] ?? 'akusangkuasa';

            if ($password === $master_password) {
                $_SESSION['log_record_authenticated'] = true;
                header('Content-Type: application/json');
                echo json_encode(['status' => true, 'message' => 'Berhasil autentikasi.']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Password salah!']);
            }
            exit;
        }
    }

    public function load_data()
    {
        // Harus sudah autentikasi
        if (empty($_SESSION['log_record_authenticated'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Silakan masukkan password terlebih dahulu.']);
            exit;
        }

        $tgl_awal = $this->input->xss_clean($_GET['tgl_awal'] ?? '');
        $tgl_akhir = $this->input->xss_clean($_GET['tgl_akhir'] ?? '');

        $logs = $this->MOD->get_logs($tgl_awal, $tgl_akhir);

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $logs]);
        exit;
    }
}
