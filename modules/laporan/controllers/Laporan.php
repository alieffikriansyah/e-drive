<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class Laporan — Dashboard Laba-Rugi Bersih dengan fitur Export Excel.
// Menyajikan kalkulasi laba kotor dan bersih per cabang,
// harian maupun bulanan, dari tabel penjualan & pengeluaran_operasional.
class Laporan extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('laporan/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman dashboard laporan.
    public function index() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        if (in_array($user_role, ['owner', 'admin'])) {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();
        } else {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE id = ? AND status != 8", [$user_cabang])->result();
        }

        $this->load->view('laporan/v_index', ['cabang' => $cabang]);
    }

    // ============================================================
    // LAPORAN LABA-RUGI BULANAN per Cabang
    // ============================================================
    // GET params: ?tahun=2025&bulan=6&id_cabang=0 (0=semua, khusus owner)
    public function laba_rugi_bulanan() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $tahun            = (int)($_GET['tahun']     ?? date('Y'));
        $bulan            = (int)($_GET['bulan']     ?? date('n'));
        $id_cabang_filter = (int)($_GET['id_cabang'] ?? 0);

        if (!in_array($user_role, ['owner', 'admin'])) {
            $id_cabang_filter = (int)$user_cabang;
        }

        $where_cabang_pj  = $id_cabang_filter > 0 ? "AND pj.id_cabang = $id_cabang_filter" : '';
        $where_cabang_ops = $id_cabang_filter > 0 ? "AND id_cabang = $id_cabang_filter"     : '';
        $where_cabang_c   = $id_cabang_filter > 0 ? "AND c.id = $id_cabang_filter"           : '';

        $data = $this->db->query("
            SELECT
                c.id                                       AS id_cabang,
                c.nama_cabang,
                COALESCE(pj_agg.total_transaksi,  0)       AS total_transaksi,
                COALESCE(pj_agg.total_pendapatan, 0)       AS total_pendapatan,
                0                                          AS total_hpp,
                0                                          AS laba_kotor,
                COALESCE(ops_agg.total_biaya_ops, 0)       AS total_biaya_operasional,
                (
                    COALESCE(pj_agg.total_pendapatan, 0)
                    - COALESCE(ops_agg.total_biaya_ops, 0)
                )                                          AS laba_bersih
            FROM cabang c
            LEFT JOIN (
                SELECT id_cabang,
                       COUNT(id)        AS total_transaksi,
                       SUM(total_bayar) AS total_pendapatan,
                       0                AS total_hpp
                FROM penjualan
                WHERE status != 8
                  AND YEAR(tanggal_transaksi)  = ?
                  AND MONTH(tanggal_transaksi) = ?
                  $where_cabang_pj
                GROUP BY id_cabang
            ) pj_agg ON pj_agg.id_cabang = c.id
            LEFT JOIN (
                SELECT id_cabang,
                       SUM(total_biaya) AS total_biaya_ops
                FROM pengeluaran_operasional
                WHERE status != 8
                  AND YEAR(tanggal)  = ?
                  AND MONTH(tanggal) = ?
                  $where_cabang_ops
                GROUP BY id_cabang
            ) ops_agg ON ops_agg.id_cabang = c.id
            WHERE c.status != 8
              $where_cabang_c
            ORDER BY c.id ASC
        ", [$tahun, $bulan, $tahun, $bulan])->result();

        $nama_bulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];

        header('Content-Type: application/json');
        echo json_encode([
            'status'  => true,
            'periode' => ($nama_bulan[$bulan] ?? $bulan) . ' ' . $tahun,
            'tahun'   => $tahun,
            'bulan'   => $bulan,
            'data'    => $data,
        ]);
        exit;
    }

    // ============================================================
    // LAPORAN LABA-RUGI HARIAN (detail per hari dalam satu bulan)
    // ============================================================
    // GET params: ?tahun=2025&bulan=6&id_cabang=1
    public function laba_rugi_harian() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $tahun     = (int)($_GET['tahun']     ?? date('Y'));
        $bulan     = (int)($_GET['bulan']     ?? date('n'));
        $id_cabang = (int)($_GET['id_cabang'] ?? 0);

        if (!in_array($user_role, ['owner', 'admin'])) {
            $id_cabang = (int)$user_cabang;
        }

        if ($id_cabang <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Pilih cabang terlebih dahulu']);
            exit;
        }

        $data = $this->db->query("
            SELECT
                all_dates.tanggal,
                COALESCE(pj_day.total_transaksi,  0) AS total_transaksi,
                COALESCE(pj_day.total_pendapatan, 0) AS total_pendapatan,
                0                                    AS total_hpp,
                0                                    AS laba_kotor,
                COALESCE(ops_day.total_biaya,     0) AS total_biaya_operasional,
                (
                    COALESCE(pj_day.total_pendapatan, 0)
                    - COALESCE(ops_day.total_biaya, 0)
                )                                    AS laba_bersih
            FROM (
                SELECT DATE(CONCAT(?, '-', LPAD(?, 2,'0'), '-01') + INTERVAL (seq.n - 1) DAY) AS tanggal
                FROM (
                    SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
                    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
                    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
                    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
                    UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25
                    UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30
                    UNION SELECT 31
                ) seq
                WHERE DATE(CONCAT(?, '-', LPAD(?, 2,'0'), '-01') + INTERVAL (seq.n - 1) DAY)
                    <= LAST_DAY(CONCAT(?, '-', LPAD(?, 2,'0'), '-01'))
            ) all_dates
            LEFT JOIN (
                SELECT DATE(tanggal_transaksi) AS tgl,
                       COUNT(id)               AS total_transaksi,
                       SUM(total_bayar)        AS total_pendapatan,
                       0                       AS total_hpp
                FROM penjualan
                WHERE status != 8 AND id_cabang = ?
                  AND YEAR(tanggal_transaksi) = ? AND MONTH(tanggal_transaksi) = ?
                GROUP BY DATE(tanggal_transaksi)
            ) pj_day ON pj_day.tgl = all_dates.tanggal
            LEFT JOIN (
                SELECT tanggal AS tgl, SUM(total_biaya) AS total_biaya
                FROM pengeluaran_operasional
                WHERE status != 8 AND id_cabang = ?
                  AND YEAR(tanggal) = ? AND MONTH(tanggal) = ?
                GROUP BY tanggal
            ) ops_day ON ops_day.tgl = all_dates.tanggal
            ORDER BY all_dates.tanggal ASC
        ", [
            $tahun, $bulan, $tahun, $bulan, $tahun, $bulan,
            $id_cabang, $tahun, $bulan,
            $id_cabang, $tahun, $bulan,
        ])->result();

        $total_pendapatan = array_sum(array_column((array)$data, 'total_pendapatan'));
        $total_hpp        = array_sum(array_column((array)$data, 'total_hpp'));
        $total_biaya_ops  = array_sum(array_column((array)$data, 'total_biaya_operasional'));

        header('Content-Type: application/json');
        echo json_encode([
            'status'    => true,
            'id_cabang' => $id_cabang,
            'tahun'     => $tahun,
            'bulan'     => $bulan,
            'data'      => $data,
            'ringkasan' => [
                'total_transaksi'         => array_sum(array_column((array)$data, 'total_transaksi')),
                'total_pendapatan'        => $total_pendapatan,
                'total_hpp'               => 0,
                'laba_kotor'              => $total_pendapatan,
                'total_biaya_operasional' => $total_biaya_ops,
                'laba_bersih'             => $total_pendapatan - $total_biaya_ops,
            ],
        ]);
        exit;
    }

    // ============================================================
    // LAPORAN PRODUK TERLARIS per Cabang dan Periode
    // ============================================================
    // GET params: ?tgl_awal=2025-06-01&tgl_akhir=2025-06-30&id_cabang=1&limit=10
    public function produk_terlaris() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $tgl_awal  = $this->input->xss_clean($_GET['tgl_awal']  ?? date('Y-m-01'));
        $tgl_akhir = $this->input->xss_clean($_GET['tgl_akhir'] ?? date('Y-m-d'));
        $limit     = max(1, min(50, (int)($_GET['limit'] ?? 10)));
        $id_cabang = (int)($_GET['id_cabang'] ?? 0);

        if (!in_array($user_role, ['owner', 'admin'])) {
            $id_cabang = (int)$user_cabang;
        }

        $where_cabang = $id_cabang > 0 ? "AND pj.id_cabang = $id_cabang" : '';

        $data = $this->db->query("
            SELECT
                p.id                AS id_produk,
                p.nama_produk,
                s.nama_satuan,
                SUM(dp.jumlah_beli)  AS total_terjual,
                SUM(dp.subtotal)     AS total_pendapatan,
                0                    AS total_hpp,
                SUM(dp.subtotal)     AS kontribusi_laba
            FROM detail_penjualan dp
            JOIN penjualan pj ON dp.id_penjualan = pj.id
            JOIN produk    p  ON dp.id_produk    = p.id
            JOIN satuan    s  ON p.id_satuan     = s.id
            WHERE dp.status != 8 AND pj.status != 8
              AND DATE(pj.tanggal_transaksi) BETWEEN ? AND ?
              $where_cabang
            GROUP BY p.id, p.nama_produk, s.nama_satuan
            ORDER BY total_terjual DESC
            LIMIT $limit
        ", [$tgl_awal, $tgl_akhir])->result();

        header('Content-Type: application/json');
        echo json_encode([
            'status'    => true,
            'tgl_awal'  => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'data'      => $data,
        ]);
        exit;
    }

    // ============================================================
    // EXPORT EXCEL — Laba-Rugi Bulanan (semua cabang)
    // ============================================================
    // GET params: ?tahun=2025&bulan=6&id_cabang=0
    // Menghasilkan file .xls yang langsung diunduh oleh browser.
    // Menggunakan format HTML Table yang dirender sebagai Excel
    // (tidak perlu library tambahan, berjalan native di PHP).
    public function export_excel_bulanan() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $tahun            = (int)($_GET['tahun']     ?? date('Y'));
        $bulan            = (int)($_GET['bulan']     ?? date('n'));
        $id_cabang_filter = (int)($_GET['id_cabang'] ?? 0);

        if (!in_array($user_role, ['owner', 'admin'])) {
            $id_cabang_filter = (int)$user_cabang;
        }

        $nama_bulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $periode = ($nama_bulan[$bulan] ?? $bulan) . ' ' . $tahun;

        // Ambil data sama persis seperti laba_rugi_bulanan().
        $where_cabang_pj  = $id_cabang_filter > 0 ? "AND pj.id_cabang = $id_cabang_filter" : '';
        $where_cabang_ops = $id_cabang_filter > 0 ? "AND id_cabang = $id_cabang_filter"     : '';
        $where_cabang_c   = $id_cabang_filter > 0 ? "AND c.id = $id_cabang_filter"           : '';

        $rows = $this->db->query("
            SELECT
                c.nama_cabang,
                COALESCE(pj_agg.total_transaksi,  0)       AS total_transaksi,
                COALESCE(pj_agg.total_pendapatan, 0)       AS total_pendapatan,
                0                                          AS total_hpp,
                0                                          AS laba_kotor,
                COALESCE(ops_agg.total_biaya_ops, 0)       AS total_biaya_operasional,
                (
                    COALESCE(pj_agg.total_pendapatan, 0)
                    - COALESCE(ops_agg.total_biaya_ops, 0)
                )                                          AS laba_bersih
            FROM cabang c
            LEFT JOIN (
                SELECT id_cabang,
                       COUNT(id) AS total_transaksi,
                       SUM(total_bayar) AS total_pendapatan,
                       0                AS total_hpp
                FROM penjualan
                WHERE status != 8
                  AND YEAR(tanggal_transaksi) = ?
                  AND MONTH(tanggal_transaksi) = ?
                  $where_cabang_pj
                GROUP BY id_cabang
            ) pj_agg ON pj_agg.id_cabang = c.id
            LEFT JOIN (
                SELECT id_cabang, SUM(total_biaya) AS total_biaya_ops
                FROM pengeluaran_operasional
                WHERE status != 8
                  AND YEAR(tanggal) = ? AND MONTH(tanggal) = ?
                  $where_cabang_ops
                GROUP BY id_cabang
            ) ops_agg ON ops_agg.id_cabang = c.id
            WHERE c.status != 8 $where_cabang_c
            ORDER BY c.id ASC
        ", [$tahun, $bulan, $tahun, $bulan])->result();

        // Hitung baris TOTAL.
        $grand_transaksi  = 0;
        $grand_pendapatan = 0;
        $grand_hpp        = 0;
        $grand_kotor      = 0;
        $grand_biaya      = 0;
        $grand_bersih     = 0;

        foreach ($rows as $r) {
            $grand_transaksi  += $r->total_transaksi;
            $grand_pendapatan += $r->total_pendapatan;
            $grand_hpp        += $r->total_hpp;
            $grand_kotor      += $r->laba_kotor;
            $grand_biaya      += $r->total_biaya_operasional;
            $grand_bersih     += $r->laba_bersih;
        }

        // Helper format angka ke Rupiah tanpa simbol.
        $rp = function($n) { return number_format($n, 0, ',', '.'); };

        // --- Set header HTTP untuk memaksa browser mengunduh sebagai file Excel ---
        $filename = 'LaporanLabaRugiBulanan_' . $periode . '_' . date('Ymd_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        // --- Render konten HTML Table yang akan dibaca sebagai Excel ---
        ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body      { font-family: Arial, sans-serif; font-size: 11pt; }
    table     { border-collapse: collapse; width: 100%; }
    th, td    { border: 1px solid #aaa; padding: 5px 8px; }
    th        { background-color: #2D6A4F; color: white; text-align: center; font-weight: bold; }
    .judul    { font-size: 14pt; font-weight: bold; text-align: center; }
    .subj     { font-size: 11pt; text-align: center; margin-bottom: 10px; }
    .right    { text-align: right; }
    .center   { text-align: center; }
    .positif  { color: #1B4332; font-weight: bold; }
    .negatif  { color: #C1121F; font-weight: bold; }
    .total-row { background-color: #D8F3DC; font-weight: bold; }
</style>
</head>
<body>
<table>
    <tr><td colspan="8" class="judul">LAPORAN LABA-RUGI BULANAN</td></tr>
    <tr><td colspan="8" class="subj">Periode: <?= htmlspecialchars($periode) ?></td></tr>
    <tr><td colspan="8" class="subj">Dicetak: <?= date('d/m/Y H:i:s') ?></td></tr>
    <tr><td colspan="8"></td></tr>
    <tr>
        <th>No</th>
        <th>Nama Cabang</th>
        <th>Jumlah Transaksi</th>
        <th>Total Pendapatan (Rp)</th>
        <th>Total HPP (Rp)</th>
        <th>Laba Kotor (Rp)</th>
        <th>Biaya Operasional (Rp)</th>
        <th>Laba Bersih (Rp)</th>
    </tr>
    <?php $no = 1; foreach ($rows as $r): ?>
    <tr>
        <td class="center"><?= $no++ ?></td>
        <td><?= htmlspecialchars($r->nama_cabang) ?></td>
        <td class="center"><?= $rp($r->total_transaksi) ?></td>
        <td class="right"><?= $rp($r->total_pendapatan) ?></td>
        <td class="right"><?= $rp($r->total_hpp) ?></td>
        <td class="right <?= $r->laba_kotor >= 0 ? 'positif' : 'negatif' ?>"><?= $rp($r->laba_kotor) ?></td>
        <td class="right"><?= $rp($r->total_biaya_operasional) ?></td>
        <td class="right <?= $r->laba_bersih >= 0 ? 'positif' : 'negatif' ?>"><?= $rp($r->laba_bersih) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td colspan="2" class="center">TOTAL KESELURUHAN</td>
        <td class="center"><?= $rp($grand_transaksi) ?></td>
        <td class="right"><?= $rp($grand_pendapatan) ?></td>
        <td class="right"><?= $rp($grand_hpp) ?></td>
        <td class="right <?= $grand_kotor >= 0 ? 'positif' : 'negatif' ?>"><?= $rp($grand_kotor) ?></td>
        <td class="right"><?= $rp($grand_biaya) ?></td>
        <td class="right <?= $grand_bersih >= 0 ? 'positif' : 'negatif' ?>"><?= $rp($grand_bersih) ?></td>
    </tr>
</table>
</body>
</html>
        <?php
        exit;
    }

    // ============================================================
    // EXPORT EXCEL — Laba-Rugi Harian (detail per hari, satu cabang)
    // ============================================================
    // GET params: ?tahun=2025&bulan=6&id_cabang=1
    public function export_excel_harian() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $tahun     = (int)($_GET['tahun']     ?? date('Y'));
        $bulan     = (int)($_GET['bulan']     ?? date('n'));
        $id_cabang = (int)($_GET['id_cabang'] ?? 0);

        if (!in_array($user_role, ['owner', 'admin'])) {
            $id_cabang = (int)$user_cabang;
        }

        if ($id_cabang <= 0) {
            echo 'Pilih cabang terlebih dahulu.'; exit;
        }

        $nama_bulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $periode = ($nama_bulan[$bulan] ?? $bulan) . ' ' . $tahun;

        // Ambil info cabang untuk header laporan.
        $info_cabang = $this->db->query("SELECT nama_cabang FROM cabang WHERE id = ?", [$id_cabang])->fetch();
        $nama_cabang = $info_cabang ? $info_cabang->nama_cabang : 'Cabang #' . $id_cabang;

        // Ambil data harian.
        $rows = $this->db->query("
            SELECT
                all_dates.tanggal,
                COALESCE(pj_day.total_transaksi,  0) AS total_transaksi,
                COALESCE(pj_day.total_pendapatan, 0) AS total_pendapatan,
                0                                    AS total_hpp,
                0                                    AS laba_kotor,
                COALESCE(ops_day.total_biaya,     0) AS total_biaya_operasional,
                (
                    COALESCE(pj_day.total_pendapatan, 0)
                    - COALESCE(ops_day.total_biaya, 0)
                )                                    AS laba_bersih
            FROM (
                SELECT DATE(CONCAT(?, '-', LPAD(?, 2,'0'), '-01') + INTERVAL (seq.n - 1) DAY) AS tanggal
                FROM (
                    SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
                    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
                    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
                    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
                    UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25
                    UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30
                    UNION SELECT 31
                ) seq
                WHERE DATE(CONCAT(?, '-', LPAD(?, 2,'0'), '-01') + INTERVAL (seq.n - 1) DAY)
                    <= LAST_DAY(CONCAT(?, '-', LPAD(?, 2,'0'), '-01'))
            ) all_dates
            LEFT JOIN (
                SELECT DATE(tanggal_transaksi) AS tgl,
                       COUNT(id) AS total_transaksi,
                       SUM(total_bayar) AS total_pendapatan,
                       0                AS total_hpp
                FROM penjualan
                WHERE status != 8 AND id_cabang = ?
                  AND YEAR(tanggal_transaksi) = ? AND MONTH(tanggal_transaksi) = ?
                GROUP BY DATE(tanggal_transaksi)
            ) pj_day ON pj_day.tgl = all_dates.tanggal
            LEFT JOIN (
                SELECT tanggal AS tgl, SUM(total_biaya) AS total_biaya
                FROM pengeluaran_operasional
                WHERE status != 8 AND id_cabang = ?
                  AND YEAR(tanggal) = ? AND MONTH(tanggal) = ?
                GROUP BY tanggal
            ) ops_day ON ops_day.tgl = all_dates.tanggal
            ORDER BY all_dates.tanggal ASC
        ", [
            $tahun,$bulan,$tahun,$bulan,$tahun,$bulan,
            $id_cabang,$tahun,$bulan,
            $id_cabang,$tahun,$bulan,
        ])->result();

        // Hitung grand total.
        $grand_transaksi  = array_sum(array_column((array)$rows, 'total_transaksi'));
        $grand_pendapatan = array_sum(array_column((array)$rows, 'total_pendapatan'));
        $grand_hpp        = array_sum(array_column((array)$rows, 'total_hpp'));
        $grand_kotor      = array_sum(array_column((array)$rows, 'laba_kotor'));
        $grand_biaya      = array_sum(array_column((array)$rows, 'total_biaya_operasional'));
        $grand_bersih     = array_sum(array_column((array)$rows, 'laba_bersih'));

        $rp = function($n) { return number_format($n, 0, ',', '.'); };

        $filename = 'LaporanHarian_' . str_replace(' ', '_', $nama_cabang) . '_' . $periode . '_' . date('Ymd_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body      { font-family: Arial, sans-serif; font-size: 11pt; }
    table     { border-collapse: collapse; width: 100%; }
    th, td    { border: 1px solid #aaa; padding: 5px 8px; }
    th        { background-color: #1B4332; color: white; text-align: center; font-weight: bold; }
    .judul    { font-size: 14pt; font-weight: bold; text-align: center; }
    .subj     { font-size: 11pt; text-align: center; }
    .right    { text-align: right; }
    .center   { text-align: center; }
    .positif  { color: #1B4332; font-weight: bold; }
    .negatif  { color: #C1121F; font-weight: bold; }
    .total-row { background-color: #D8F3DC; font-weight: bold; }
    .zero-row { color: #aaa; }
</style>
</head>
<body>
<table>
    <tr><td colspan="8" class="judul">LAPORAN LABA-RUGI HARIAN</td></tr>
    <tr><td colspan="8" class="subj">Cabang: <?= htmlspecialchars($nama_cabang) ?></td></tr>
    <tr><td colspan="8" class="subj">Periode: <?= htmlspecialchars($periode) ?></td></tr>
    <tr><td colspan="8" class="subj">Dicetak: <?= date('d/m/Y H:i:s') ?></td></tr>
    <tr><td colspan="8"></td></tr>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Jumlah Transaksi</th>
        <th>Total Pendapatan (Rp)</th>
        <th>Total HPP (Rp)</th>
        <th>Laba Kotor (Rp)</th>
        <th>Biaya Operasional (Rp)</th>
        <th>Laba Bersih (Rp)</th>
    </tr>
    <?php $no = 1; foreach ($rows as $r):
        $is_zero = ($r->total_transaksi == 0 && $r->total_biaya_operasional == 0);
        $row_class = $is_zero ? 'zero-row' : '';
    ?>
    <tr class="<?= $row_class ?>">
        <td class="center"><?= $no++ ?></td>
        <td class="center"><?= date('d/m/Y', strtotime($r->tanggal)) ?></td>
        <td class="center"><?= $rp($r->total_transaksi) ?></td>
        <td class="right"><?= $rp($r->total_pendapatan) ?></td>
        <td class="right"><?= $rp($r->total_hpp) ?></td>
        <td class="right <?= !$is_zero ? ($r->laba_kotor >= 0 ? 'positif' : 'negatif') : '' ?>"><?= $rp($r->laba_kotor) ?></td>
        <td class="right"><?= $rp($r->total_biaya_operasional) ?></td>
        <td class="right <?= !$is_zero ? ($r->laba_bersih >= 0 ? 'positif' : 'negatif') : '' ?>"><?= $rp($r->laba_bersih) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
        <td colspan="2" class="center">TOTAL BULAN INI</td>
        <td class="center"><?= $rp($grand_transaksi) ?></td>
        <td class="right"><?= $rp($grand_pendapatan) ?></td>
        <td class="right"><?= $rp($grand_hpp) ?></td>
        <td class="right <?= $grand_kotor >= 0 ? 'positif' : 'negatif' ?>"><?= $rp($grand_kotor) ?></td>
        <td class="right"><?= $rp($grand_biaya) ?></td>
        <td class="right <?= $grand_bersih >= 0 ? 'positif' : 'negatif' ?>"><?= $rp($grand_bersih) ?></td>
    </tr>
</table>
</body>
</html>
        <?php
        exit;
    }

    // ============================================================
    // EXPORT EXCEL — Produk Terlaris
    // ============================================================
    // GET params: ?tgl_awal=2025-06-01&tgl_akhir=2025-06-30&id_cabang=1&limit=20
    public function export_excel_produk_terlaris() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $tgl_awal  = $this->input->xss_clean($_GET['tgl_awal']  ?? date('Y-m-01'));
        $tgl_akhir = $this->input->xss_clean($_GET['tgl_akhir'] ?? date('Y-m-d'));
        $limit     = max(1, min(100, (int)($_GET['limit'] ?? 20)));
        $id_cabang = (int)($_GET['id_cabang'] ?? 0);

        if (!in_array($user_role, ['owner', 'admin'])) {
            $id_cabang = (int)$user_cabang;
        }

        $where_cabang = $id_cabang > 0 ? "AND pj.id_cabang = $id_cabang" : '';

        $info_cabang = ($id_cabang > 0)
            ? $this->db->query("SELECT nama_cabang FROM cabang WHERE id = ?", [$id_cabang])->fetch()
            : null;
        $nama_cabang = $info_cabang ? $info_cabang->nama_cabang : 'Semua Cabang';

        $rows = $this->db->query("
            SELECT
                p.id               AS id_produk,
                p.nama_produk,
                s.nama_satuan,
                SUM(dp.jumlah_beli)  AS total_terjual,
                SUM(dp.subtotal)     AS total_pendapatan,
                0                    AS total_hpp,
                SUM(dp.subtotal)     AS kontribusi_laba
            FROM detail_penjualan dp
            JOIN penjualan pj ON dp.id_penjualan = pj.id
            JOIN produk    p  ON dp.id_produk    = p.id
            JOIN satuan    s  ON p.id_satuan     = s.id
            WHERE dp.status != 8 AND pj.status != 8
              AND DATE(pj.tanggal_transaksi) BETWEEN ? AND ?
              $where_cabang
            GROUP BY p.id, p.nama_produk, s.nama_satuan
            ORDER BY total_terjual DESC
            LIMIT $limit
        ", [$tgl_awal, $tgl_akhir])->result();

        $rp = function($n) { return number_format($n, 0, ',', '.'); };

        $filename = 'ProdukTerlaris_' . str_replace(' ','_', $nama_cabang) . '_' . $tgl_awal . '_sd_' . $tgl_akhir . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body      { font-family: Arial, sans-serif; font-size: 11pt; }
    table     { border-collapse: collapse; width: 100%; }
    th, td    { border: 1px solid #aaa; padding: 5px 8px; }
    th        { background-color: #1D3557; color: white; text-align: center; font-weight: bold; }
    .judul    { font-size: 14pt; font-weight: bold; text-align: center; }
    .subj     { font-size: 11pt; text-align: center; }
    .right    { text-align: right; }
    .center   { text-align: center; }
    .top3     { background-color: #FFD700; font-weight: bold; }
</style>
</head>
<body>
<table>
    <tr><td colspan="7" class="judul">LAPORAN PRODUK TERLARIS</td></tr>
    <tr><td colspan="7" class="subj">Cabang: <?= htmlspecialchars($nama_cabang) ?></td></tr>
    <tr><td colspan="7" class="subj">Periode: <?= $tgl_awal ?> s/d <?= $tgl_akhir ?></td></tr>
    <tr><td colspan="7" class="subj">Dicetak: <?= date('d/m/Y H:i:s') ?></td></tr>
    <tr><td colspan="7"></td></tr>
    <tr>
        <th>Peringkat</th>
        <th>Nama Produk</th>
        <th>Satuan</th>
        <th>Total Terjual</th>
        <th>Total Pendapatan (Rp)</th>
        <th>Total HPP (Rp)</th>
        <th>Kontribusi Laba (Rp)</th>
    </tr>
    <?php $rank = 1; foreach ($rows as $r): ?>
    <tr class="<?= $rank <= 3 ? 'top3' : '' ?>">
        <td class="center">#<?= $rank++ ?></td>
        <td><?= htmlspecialchars($r->nama_produk) ?></td>
        <td class="center"><?= htmlspecialchars($r->nama_satuan) ?></td>
        <td class="center"><?= $rp($r->total_terjual) ?></td>
        <td class="right"><?= $rp($r->total_pendapatan) ?></td>
        <td class="right"><?= $rp($r->total_hpp) ?></td>
        <td class="right"><?= $rp($r->kontribusi_laba) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
        <?php
        exit;
    }
}
