<?php

require_once APPPATH . 'middleware/AuthMiddleware.php';

class Hapus_keseluruhan_data extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('hapus_keseluruhan_data/MOD', 'mod');
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
        $this->load->view('hapus_keseluruhan_data/v_index');
    }

    public function proses_hapus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $this->input->xss_clean($_POST['password'] ?? '');

            global $config;
            $master_password = $config['master_password'] ?? 'akusangkuasa';

            // Autentikasi master password
            if ($password !== $master_password) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Password salah!']);
                exit;
            }

            $success = $this->mod->truncate_semua();

            header('Content-Type: application/json');
            if ($success) {
                // Catat ke log
                $user_id = $_SESSION['user_id'] ?? 0;
                $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                
                $this->db->query("
                    INSERT INTO log_record_users (id_user, action, keterangan, ip_address, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ", [$user_id, 'HAPUS_SEMUA_DATA', 'Admin mengeksekusi penghapusan keseluruhan data transaksi dan master', $ip_address]);

                echo json_encode(['status' => true, 'message' => 'Seluruh data berhasil dihapus permanen.']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Terjadi kesalahan saat menghapus data.']);
            }
            exit;
        }
    }
}
