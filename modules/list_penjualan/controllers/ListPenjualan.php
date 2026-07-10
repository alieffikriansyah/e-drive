<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class ListPenjualan — Menampilkan daftar transaksi penjualan dengan filter cabang & tanggal,
// serta fitur cetak nota detail per transaksi.
class ListPenjualan extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // ============================================================
    // Menampilkan halaman daftar penjualan.
    // ============================================================
    public function index() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        if (in_array($user_role, ['owner', 'admin'])) {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();
        } else {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE id = ? AND status != 8", [$user_cabang])->result();
        }

        $this->load->view('list_penjualan/v_index', ['cabang' => $cabang]);
    }

    // ============================================================
    // Mengembalikan daftar transaksi penjualan dalam format JSON.
    // GET params: ?tgl_awal=YYYY-MM-DD&tgl_akhir=YYYY-MM-DD&id_cabang=0
    // ============================================================
    public function load_data() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $tgl_awal  = $this->input->xss_clean($_GET['tgl_awal']  ?? date('Y-m-01'));
        $tgl_akhir = $this->input->xss_clean($_GET['tgl_akhir'] ?? date('Y-m-d'));
        $id_cabang = (int)($_GET['id_cabang'] ?? 0);

        // Non-admin/owner hanya bisa lihat data cabangnya sendiri.
        if (!in_array($user_role, ['owner', 'admin'])) {
            $id_cabang = (int)$user_cabang;
        }

        $params       = [$tgl_awal, $tgl_akhir];
        $where_cabang = '';

        if ($id_cabang > 0) {
            $where_cabang = 'AND pj.id_cabang = ?';
            $params[]     = $id_cabang;
        }

        $data = $this->db->query("
            SELECT
                pj.id,
                pj.no_nota,
                pj.tanggal_transaksi,
                pj.total_bayar,
                pj.total_diskon,
                pj.bayar,
                pj.kembalian,
                pj.metode_bayar,
                pj.keterangan,
                pj.status,
                c.nama_cabang,
                u.name AS nama_kasir,
                (SELECT COUNT(*) FROM detail_penjualan dp WHERE dp.id_penjualan = pj.id AND dp.status != 8) AS jumlah_item
            FROM penjualan pj
            LEFT JOIN cabang c ON pj.id_cabang = c.id
            LEFT JOIN users  u ON pj.id_user   = u.id
            WHERE pj.status != 8
              AND DATE(pj.tanggal_transaksi) BETWEEN ? AND ?
              $where_cabang
            ORDER BY pj.tanggal_transaksi DESC
        ", $params)->fetchAll();

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }

    // ============================================================
    // Mengembalikan detail 1 transaksi beserta semua item produk.
    // Digunakan untuk modal Nota.
    // ============================================================
    public function get_nota($id) {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        // Ambil header transaksi.
        $penjualan = $this->db->query("
            SELECT
                pj.id,
                pj.no_nota,
                pj.tanggal_transaksi,
                pj.total_bayar,
                pj.total_diskon,
                pj.bayar,
                pj.kembalian,
                pj.metode_bayar,
                pj.keterangan,
                c.nama_cabang,
                u.name AS nama_kasir
            FROM penjualan pj
            LEFT JOIN cabang c ON pj.id_cabang = c.id
            LEFT JOIN users  u ON pj.id_user   = u.id
            WHERE pj.id = ? AND pj.status != 8
        ", [$id])->fetch(PDO::FETCH_ASSOC);

        if (!$penjualan) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Transaksi tidak ditemukan']);
            exit;
        }

        // Proteksi: non-admin hanya bisa lihat nota cabangnya sendiri.
        if (!in_array($user_role, ['owner', 'admin'])) {
            $owner_pj = $this->db->query("SELECT id_cabang FROM penjualan WHERE id = ?", [$id])->fetch();
            if (!$owner_pj || (int)$owner_pj->id_cabang !== (int)$user_cabang) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Akses ditolak']);
                exit;
            }
        }

        // Ambil detail item produk.
        $items = $this->db->query("
            SELECT
                dp.id,
                p.nama_produk,
                s.nama_satuan,
                dp.jumlah_beli,
                dp.harga_satuan,
                dp.diskon,
                dp.subtotal,
                dp.keterangan AS ket_item
            FROM detail_penjualan dp
            JOIN produk p ON dp.id_produk = p.id
            JOIN satuan s ON p.id_satuan  = s.id
            WHERE dp.id_penjualan = ? AND dp.status != 8
            ORDER BY dp.id ASC
        ", [$id])->fetchAll();

        header('Content-Type: application/json');
        echo json_encode([
            'status'    => true,
            'penjualan' => $penjualan,
            'items'     => $items,
        ]);
        exit;
    }
}
