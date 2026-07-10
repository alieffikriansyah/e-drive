<?php

require_once APPPATH . 'middleware/AuthMiddleware.php';

class Dashboard extends Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    public function index() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        if (in_array($user_role, ['owner', 'admin'])) {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();
        } else {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE id = ? AND status != 8", [$user_cabang])->result();
        }

        $data = [
            'title'  => 'Dashboard',
            'cabang' => $cabang,
        ];
        $this->load->view('dashboard/v_index', $data);
    }

    public function get_data() {
        $id_cabang_session = $_SESSION['id_cabang'] ?? 0;
        $role              = strtolower($_SESSION['role_name'] ?? '');
        $is_admin          = in_array($role, ['owner', 'admin']);
        $today             = date('Y-m-d');
        $bulan             = date('Y-m');

        // Filter cabang: admin bisa pilih via GET, selain admin pakai cabang session
        $id_cabang = $is_admin
            ? (int)($_GET['id_cabang'] ?? 0)
            : (int)$id_cabang_session;

        // Omzet & transaksi hari ini
        $where_penjualan = ($id_cabang > 0) ? "AND id_cabang = $id_cabang" : '';
        $hari = $this->db->query("
            SELECT
                COUNT(id)         AS total_transaksi,
                COALESCE(SUM(total_bayar), 0) AS omzet,
                COALESCE(SUM(total_bayar), 0) AS laba_kotor
            FROM penjualan
            WHERE status != 8
              AND DATE(tanggal_transaksi) = ?
              $where_penjualan
        ", [$today])->fetch(PDO::FETCH_ASSOC);

        // Pengeluaran operasional hari ini
        $where_ops = ($id_cabang > 0) ? "AND id_cabang = $id_cabang" : '';
        $ops_hari = $this->db->query("
            SELECT COALESCE(SUM(total_biaya), 0) AS total_ops
            FROM pengeluaran_operasional
            WHERE status != 8 AND tanggal = ?
              $where_ops
        ", [$today])->fetch(PDO::FETCH_ASSOC);

        $laba_bersih_hari = ($hari['laba_kotor'] ?? 0) - ($ops_hari['total_ops'] ?? 0);

        // Omzet bulan ini
        $bulan_data = $this->db->query("
            SELECT COALESCE(SUM(total_bayar), 0) AS omzet_bulan
            FROM penjualan
            WHERE status != 8
              AND DATE_FORMAT(tanggal_transaksi, '%Y-%m') = ?
              $where_penjualan
        ", [$bulan])->fetch(PDO::FETCH_ASSOC);

        // Produk stok menipis dinonaktifkan (kasir-saja)
        $stok_menipis = ['jumlah' => 0];
        $produk_menipis = [];

        // Transaksi terakhir (limit 5)
        $where_pj_alias = ($id_cabang > 0) ? "AND pj.id_cabang = $id_cabang" : '';
        $transaksi_terakhir = $this->db->query("
            SELECT pj.no_nota, pj.total_bayar, pj.tanggal_transaksi,
                   pj.metode_bayar, u.name AS kasir, c.nama_cabang
            FROM penjualan pj
            LEFT JOIN users  u ON pj.id_user   = u.id
            LEFT JOIN cabang c ON pj.id_cabang = c.id
            WHERE pj.status != 8
              $where_pj_alias
            ORDER BY pj.tanggal_transaksi DESC
            LIMIT 5
        ")->result();

        $data = [
            'omzet_hari'         => $hari['omzet']           ?? 0,
            'total_transaksi'    => $hari['total_transaksi']  ?? 0,
            'laba_bersih_hari'   => $laba_bersih_hari,
            'omzet_bulan'        => $bulan_data['omzet_bulan'] ?? 0,
            'stok_menipis'       => $stok_menipis['jumlah']   ?? 0,
            'produk_menipis'     => $produk_menipis,
            'transaksi_terakhir' => $transaksi_terakhir,
        ];

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }
}
