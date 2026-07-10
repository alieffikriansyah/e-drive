<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class Satuan — Mengelola master satuan ukuran (Gram, Pcs, Porsi, dll).
class Satuan extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('satuan/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman manajemen satuan.
    public function index() {
        $this->load->view('satuan/v_index');
    }

    // Mengirim daftar semua satuan aktif dalam format JSON.
    public function load_data() {
        $satuan = $this->db->query("
            SELECT *
            FROM satuan
            WHERE status != 8
            ORDER BY nama_satuan ASC
        ")->result();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => true,
            'data'   => $satuan
        ]);
        exit;
    }

    // Menyimpan data satuan baru.
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $data = [
                'nama_satuan' => $this->input->xss_clean($_POST['nama_satuan'] ?? ''),
                'singkatan'   => $this->input->xss_clean($_POST['singkatan'] ?? ''),
                'created_by'  => $user_id,
                'updated_by'  => $user_id,
            ];

            if (empty($data['nama_satuan'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama satuan tidak boleh kosong']);
                exit;
            }

            $insert = $this->db->table('satuan')->insert($data);

            header('Content-Type: application/json');
            if ($insert) {
                echo json_encode(['status' => true, 'message' => 'Satuan berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Satuan gagal ditambahkan']);
            }
            exit;
        }
    }

    // Mengambil satu data satuan untuk form edit.
    public function get_satuan($id) {
        $satuan = $this->db->table('satuan')->where('id', $id)->get()->row_array();

        header('Content-Type: application/json');
        if ($satuan) {
            echo json_encode(['status' => true, 'data' => $satuan]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    // Memperbarui data satuan.
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $data = [
                'nama_satuan' => $this->input->xss_clean($_POST['nama_satuan'] ?? ''),
                'singkatan'   => $this->input->xss_clean($_POST['singkatan'] ?? ''),
                'updated_by'  => $user_id,
            ];

            if (empty($data['nama_satuan'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama satuan tidak boleh kosong']);
                exit;
            }

            $update = $this->db->table('satuan')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($update !== false) {
                echo json_encode(['status' => true, 'message' => 'Satuan berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Satuan gagal diperbarui']);
            }
            exit;
        }
    }

    // Menghapus satuan secara soft-delete.
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('satuan')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Satuan berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Satuan gagal dihapus']);
            }
            exit;
        }
    }

    // Memulihkan satuan yang di-soft-delete.
    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restore = $this->db->table('satuan')->where('id', $id)->restore();

            header('Content-Type: application/json');
            if ($restore) {
                echo json_encode(['status' => true, 'message' => 'Satuan berhasil dipulihkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Satuan gagal dipulihkan']);
            }
            exit;
        }
    }
}
