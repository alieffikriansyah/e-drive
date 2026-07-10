<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class PengeluaranOperasional — Mengelola transaksi biaya operasional per cabang.
// (Gaji karyawan, tagihan listrik, air, sewa tempat, gas LPG, dll)
class PengeluaranOperasional extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('pengeluaran_operasional/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman input pengeluaran operasional.
    public function index() {
        $user_cabang = $_SESSION['id_cabang'] ?? 0;
        $user_role   = strtolower($_SESSION['role_name'] ?? '');

        if (in_array($user_role, ['owner', 'admin'])) {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();
        } else {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE id = ? AND status != 8", [$user_cabang])->result();
        }

        $kat_ops = $this->db->query("SELECT id, nama_kategori FROM kategori_operasional WHERE status != 8 ORDER BY nama_kategori ASC")->result();

        $this->load->view('pengeluaran_operasional/v_index', [
            'cabang'  => $cabang,
            'kat_ops' => $kat_ops,
        ]);
    }

    // Mengirim daftar pengeluaran operasional via AJAX dengan filter tanggal.
    public function load_data() {
        $user_cabang = $_SESSION['id_cabang'] ?? 0;
        $user_role   = strtolower($_SESSION['role_name'] ?? '');

        $where_cabang = (!in_array($user_role, ['owner', 'admin'])) ? "AND po.id_cabang = $user_cabang" : '';

        $tgl_awal  = $this->input->xss_clean($_GET['tgl_awal']  ?? date('Y-m-01'));
        $tgl_akhir = $this->input->xss_clean($_GET['tgl_akhir'] ?? date('Y-m-d'));

        $data = $this->db->query("
            SELECT
                po.*,
                ko.nama_kategori,
                c.nama_cabang,
                u.name AS nama_penginput
            FROM pengeluaran_operasional po
            JOIN kategori_operasional ko ON po.id_kat_operasional = ko.id
            JOIN cabang               c  ON po.id_cabang          = c.id
            LEFT JOIN users           u  ON po.created_by          = u.id
            WHERE po.status != 8
              AND po.tanggal BETWEEN ? AND ?
              $where_cabang
            ORDER BY po.tanggal DESC, po.id DESC
        ", [$tgl_awal, $tgl_akhir])->result();

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }

    // Menyimpan pengeluaran operasional baru.
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id     = $_SESSION['user_id']   ?? 0;
            $user_role   = strtolower($_SESSION['role_name'] ?? '');
            $user_cabang = $_SESSION['id_cabang'] ?? 0;

            $id_cabang = (in_array($user_role, ['owner', 'admin']))
                ? (int)($_POST['id_cabang'] ?? 0)
                : (int)$user_cabang;

            $data = [
                'id_cabang'          => $id_cabang,
                'id_kat_operasional' => (int)($_POST['id_kat_operasional'] ?? 0),
                'tanggal'            => $this->input->xss_clean($_POST['tanggal'] ?? date('Y-m-d')),
                'nama_pengeluaran'   => $this->input->xss_clean($_POST['nama_pengeluaran'] ?? ''),
                'total_biaya'        => (float)($_POST['total_biaya'] ?? 0),
                'bukti_pembayaran'   => $this->input->xss_clean($_POST['bukti_pembayaran'] ?? ''),
                'keterangan'         => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'created_by'         => $user_id,
                'updated_by'         => $user_id,
            ];

            if ($id_cabang <= 0 || $data['id_kat_operasional'] <= 0 || empty($data['nama_pengeluaran']) || $data['total_biaya'] <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Semua field wajib diisi dengan benar']);
                exit;
            }

            $insert = $this->db->table('pengeluaran_operasional')->insert($data);

            header('Content-Type: application/json');
            if ($insert) {
                echo json_encode(['status' => true, 'message' => 'Pengeluaran berhasil dicatat']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Pengeluaran gagal dicatat']);
            }
            exit;
        }
    }

    // Mengambil satu data pengeluaran untuk form edit.
    public function get_pengeluaran($id) {
        $po = $this->db->query("
            SELECT po.*, ko.nama_kategori, c.nama_cabang
            FROM pengeluaran_operasional po
            JOIN kategori_operasional ko ON po.id_kat_operasional = ko.id
            JOIN cabang               c  ON po.id_cabang          = c.id
            WHERE po.id = ?
        ", [$id])->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        if ($po) {
            echo json_encode(['status' => true, 'data' => $po]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    // Memperbarui data pengeluaran operasional.
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id     = $_SESSION['user_id']   ?? 0;
            $user_role   = strtolower($_SESSION['role_name'] ?? '');
            $user_cabang = $_SESSION['id_cabang'] ?? 0;

            $id_cabang = (in_array($user_role, ['owner', 'admin']))
                ? (int)($_POST['id_cabang'] ?? 0)
                : (int)$user_cabang;

            $data = [
                'id_cabang'          => $id_cabang,
                'id_kat_operasional' => (int)($_POST['id_kat_operasional'] ?? 0),
                'tanggal'            => $this->input->xss_clean($_POST['tanggal'] ?? date('Y-m-d')),
                'nama_pengeluaran'   => $this->input->xss_clean($_POST['nama_pengeluaran'] ?? ''),
                'total_biaya'        => (float)($_POST['total_biaya'] ?? 0),
                'bukti_pembayaran'   => $this->input->xss_clean($_POST['bukti_pembayaran'] ?? ''),
                'keterangan'         => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'updated_by'         => $user_id,
            ];

            $update = $this->db->table('pengeluaran_operasional')->where('id', $id)->update($data);

            header('Content-Type: application/json');
            if ($update !== false) {
                echo json_encode(['status' => true, 'message' => 'Pengeluaran berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Pengeluaran gagal diperbarui']);
            }
            exit;
        }
    }

    // Menghapus pengeluaran secara soft-delete.
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('pengeluaran_operasional')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Pengeluaran berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Pengeluaran gagal dihapus']);
            }
            exit;
        }
    }

    // Memulihkan pengeluaran yang di-soft-delete.
    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restore = $this->db->table('pengeluaran_operasional')->where('id', $id)->restore();

            header('Content-Type: application/json');
            if ($restore) {
                echo json_encode(['status' => true, 'message' => 'Pengeluaran berhasil dipulihkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Pengeluaran gagal dipulihkan']);
            }
            exit;
        }
    }
}
