<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class Kategori — Mengelola kategori produk (Bahan Baku, Setengah Jadi, Produk Jadi).
class Kategori extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('kategori/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman manajemen kategori.
    public function index() {
        $this->load->view('kategori/v_index');
    }

    // Mengirim daftar semua kategori aktif dalam format JSON.
    public function load_data() {
        $kategori = $this->db->query("
            SELECT *
            FROM kategori
            WHERE status != 8
            ORDER BY jenis ASC, nama_kategori ASC
        ")->result();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => true,
            'data'   => $kategori
        ]);
        exit;
    }

    // Menyimpan data kategori baru.
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            // Validasi nilai enum jenis kategori.
            $jenis_valid = ['bahan_baku', 'setengah_jadi', 'produk_jadi'];
            $jenis       = $_POST['jenis'] ?? 'bahan_baku';
            if (!in_array($jenis, $jenis_valid)) $jenis = 'bahan_baku';

            $data = [
                'nama_kategori' => $this->input->xss_clean($_POST['nama_kategori'] ?? ''),
                'jenis'         => $jenis,
                'keterangan'    => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'created_by'    => $user_id,
                'updated_by'    => $user_id,
            ];

            if (empty($data['nama_kategori'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama kategori tidak boleh kosong']);
                exit;
            }

            $insert = $this->db->table('kategori')->insert($data);

            header('Content-Type: application/json');
            if ($insert) {
                echo json_encode(['status' => true, 'message' => 'Kategori berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Kategori gagal ditambahkan']);
            }
            exit;
        }
    }

    // Mengambil satu data kategori untuk form edit.
    public function get_kategori($id) {
        $kategori = $this->db->table('kategori')->where('id', $id)->get()->row_array();

        header('Content-Type: application/json');
        if ($kategori) {
            echo json_encode(['status' => true, 'data' => $kategori]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    // Memperbarui data kategori.
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $jenis_valid = ['bahan_baku', 'setengah_jadi', 'produk_jadi'];
            $jenis       = $_POST['jenis'] ?? 'bahan_baku';
            if (!in_array($jenis, $jenis_valid)) $jenis = 'bahan_baku';

            $data = [
                'nama_kategori' => $this->input->xss_clean($_POST['nama_kategori'] ?? ''),
                'jenis'         => $jenis,
                'keterangan'    => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'updated_by'    => $user_id,
            ];

            if (empty($data['nama_kategori'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama kategori tidak boleh kosong']);
                exit;
            }

            $update = $this->db->table('kategori')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($update !== false) {
                echo json_encode(['status' => true, 'message' => 'Kategori berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Kategori gagal diperbarui']);
            }
            exit;
        }
    }

    // Menghapus kategori secara soft-delete.
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('kategori')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Kategori berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Kategori gagal dihapus']);
            }
            exit;
        }
    }

    // Memulihkan kategori yang di-soft-delete.
    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restore = $this->db->table('kategori')->where('id', $id)->restore();

            header('Content-Type: application/json');
            if ($restore) {
                echo json_encode(['status' => true, 'message' => 'Kategori berhasil dipulihkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Kategori gagal dipulihkan']);
            }
            exit;
        }
    }
}
