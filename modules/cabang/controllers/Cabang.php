<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class Cabang — Mengelola master data gerai / outlet.
class Cabang extends Controller {

    // Fungsi konstruktor: menyiapkan helper dan model yang dibutuhkan.
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('cabang/MOD', 'mod');
    }

    // Middleware: menahan akses jika belum login.
    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman manajemen cabang.
    public function index() {
        $this->load->view('cabang/v_index');
    }

    // Mengirim daftar semua cabang aktif dalam format JSON (dipanggil AJAX).
    public function load_data() {
        $cabang = $this->db->query("
            SELECT *
            FROM cabang
            WHERE status != 8
            ORDER BY id ASC
        ")->result();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => true,
            'data'   => $cabang
        ]);
        exit;
    }

    // Menyimpan data cabang baru ke database.
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $data = [
                'nama_cabang' => $this->input->xss_clean($_POST['nama_cabang'] ?? ''),
                'alamat'      => $this->input->xss_clean($_POST['alamat'] ?? ''),
                'telepon'     => $this->input->xss_clean($_POST['telepon'] ?? ''),
                'keterangan'  => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'created_by'  => $user_id,
                'updated_by'  => $user_id,
            ];

            // Validasi dasar: nama cabang tidak boleh kosong.
            if (empty($data['nama_cabang'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama cabang tidak boleh kosong']);
                exit;
            }

            $insert = $this->db->table('cabang')->insert($data);

            header('Content-Type: application/json');
            if ($insert) {
                echo json_encode(['status' => true, 'message' => 'Data cabang berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Data cabang gagal ditambahkan']);
            }
            exit;
        }
    }

    // Mengambil satu data cabang berdasarkan ID (untuk mengisi form edit).
    public function get_cabang($id) {
        $cabang = $this->db->table('cabang')->where('id', $id)->get()->row_array();

        header('Content-Type: application/json');
        if ($cabang) {
            echo json_encode(['status' => true, 'data' => $cabang]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    // Memperbarui data cabang yang sudah ada.
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $data = [
                'nama_cabang' => $this->input->xss_clean($_POST['nama_cabang'] ?? ''),
                'alamat'      => $this->input->xss_clean($_POST['alamat'] ?? ''),
                'telepon'     => $this->input->xss_clean($_POST['telepon'] ?? ''),
                'keterangan'  => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'updated_by'  => $user_id,
            ];

            if (empty($data['nama_cabang'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama cabang tidak boleh kosong']);
                exit;
            }

            $update = $this->db->table('cabang')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($update !== false) {
                echo json_encode(['status' => true, 'message' => 'Data cabang berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Data cabang gagal diperbarui']);
            }
            exit;
        }
    }

    // Menghapus cabang secara soft-delete (mengubah status menjadi 8).
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('cabang')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Data cabang berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Data cabang gagal dihapus']);
            }
            exit;
        }
    }

    // Memulihkan cabang yang sebelumnya di-soft-delete.
    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restore = $this->db->table('cabang')->where('id', $id)->restore();

            header('Content-Type: application/json');
            if ($restore) {
                echo json_encode(['status' => true, 'message' => 'Data cabang berhasil dipulihkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Data cabang gagal dipulihkan']);
            }
            exit;
        }
    }
}
