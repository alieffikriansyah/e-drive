<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class KategoriOperasional — Mengelola jenis pengeluaran operasional.
class KategoriOperasional extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('kategori_operasional/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman manajemen kategori operasional.
    public function index() {
        $this->load->view('kategori_operasional/v_index');
    }

    // Mengirim daftar kategori operasional via AJAX.
    public function load_data() {
        $data = $this->db->query("
            SELECT *
            FROM kategori_operasional
            WHERE status != 8
            ORDER BY nama_kategori ASC
        ")->result();

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }

    // Menyimpan kategori operasional baru.
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $data = [
                'nama_kategori' => $this->input->xss_clean($_POST['nama_kategori'] ?? ''),
                'keterangan'    => $this->input->xss_clean($_POST['keterangan']    ?? ''),
                'created_by'    => $user_id,
                'updated_by'    => $user_id,
            ];

            if (empty($data['nama_kategori'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama kategori tidak boleh kosong']);
                exit;
            }

            $insert = $this->db->table('kategori_operasional')->insert($data);

            header('Content-Type: application/json');
            if ($insert) {
                echo json_encode(['status' => true, 'message' => 'Kategori operasional berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Kategori operasional gagal ditambahkan']);
            }
            exit;
        }
    }

    // Mengambil satu data kategori operasional untuk form edit.
    public function get_kat_ops($id) {
        $kat = $this->db->table('kategori_operasional')->where('id', $id)->get()->row_array();

        header('Content-Type: application/json');
        if ($kat) {
            echo json_encode(['status' => true, 'data' => $kat]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    // Memperbarui data kategori operasional.
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $data = [
                'nama_kategori' => $this->input->xss_clean($_POST['nama_kategori'] ?? ''),
                'keterangan'    => $this->input->xss_clean($_POST['keterangan']    ?? ''),
                'updated_by'    => $user_id,
            ];

            if (empty($data['nama_kategori'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama kategori tidak boleh kosong']);
                exit;
            }

            $update = $this->db->table('kategori_operasional')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($update !== false) {
                echo json_encode(['status' => true, 'message' => 'Kategori operasional berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Kategori operasional gagal diperbarui']);
            }
            exit;
        }
    }

    // Menghapus kategori operasional secara soft-delete.
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('kategori_operasional')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Kategori berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Kategori gagal dihapus']);
            }
            exit;
        }
    }

    // Memulihkan kategori operasional yang di-soft-delete.
    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restore = $this->db->table('kategori_operasional')->where('id', $id)->restore();

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
