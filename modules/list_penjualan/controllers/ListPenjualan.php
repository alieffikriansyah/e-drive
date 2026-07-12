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

    // ============================================================
    // Hapus transaksi penjualan (Soft Delete) - Khusus Admin
    // ============================================================
    public function delete($id) {
        $user_role = strtolower($_SESSION['role_name'] ?? '');
        if (!in_array($user_role, ['owner', 'admin'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Akses ditolak']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;
            // Gunakan db->query untuk update status menjadi 8 (soft delete)
            $update = $this->db->query("UPDATE penjualan SET status = 8, updated_by = ? WHERE id = ?", [$user_id, $id]);
            
            if ($update !== false) {
                // Hapus juga detail penjualannya
                $this->db->query("UPDATE detail_penjualan SET status = 8, updated_by = ? WHERE id_penjualan = ?", [$user_id, $id]);
                header('Content-Type: application/json');
                echo json_encode(['status' => true, 'message' => 'Transaksi berhasil dihapus']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Gagal menghapus transaksi']);
            }
            exit;
        }
    }

    // ============================================================
    // Menampilkan Halaman Edit Transaksi (Full POS-like)
    // ============================================================
    public function edit_transaksi($id) {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        if (!in_array($user_role, ['owner', 'admin'])) {
            header('Location: ' . base_url('list_penjualan'));
            exit;
        }

        // Ambil header transaksi.
        $penjualan = $this->db->query("
            SELECT
                pj.id,
                pj.id_cabang,
                pj.no_nota,
                pj.tanggal_transaksi,
                pj.total_bayar,
                pj.total_diskon,
                pj.bayar,
                pj.kembalian,
                pj.metode_bayar,
                pj.keterangan,
                c.nama_cabang
            FROM penjualan pj
            LEFT JOIN cabang c ON pj.id_cabang = c.id
            WHERE pj.id = ? AND pj.status != 8
        ", [$id])->fetch(PDO::FETCH_ASSOC);

        if (!$penjualan) {
            die('Transaksi tidak ditemukan atau sudah dihapus.');
        }
        
        $id_cabang = (int)$penjualan['id_cabang'];

        // Ambil detail item produk.
        $items = $this->db->query("
            SELECT
                dp.id,
                dp.id_harga_paten,
                p.nama_produk AS nama_menu,
                dp.jumlah_beli,
                dp.harga_satuan AS harga_jual_paten,
                dp.diskon
            FROM detail_penjualan dp
            JOIN produk p ON dp.id_produk = p.id
            WHERE dp.id_penjualan = ? AND dp.status != 8
            ORDER BY dp.id ASC
        ", [$id])->fetchAll(PDO::FETCH_ASSOC);

        // Ambil Menu Kasir untuk cabang tersebut
        $menu_kasir = $this->db->query("
            SELECT
                hp.id               AS id_harga_paten,
                hp.id_produk,
                hp.nama_menu,
                hp.harga_jual_paten,
                0                     AS stok_sekarang,
                k.nama_kategori
            FROM harga_paten hp
            JOIN produk   p  ON hp.id_produk  = p.id AND p.status != 8
            JOIN kategori k  ON p.id_kategori = k.id AND k.jenis = 'produk_jadi'
            WHERE hp.id_cabang = ?
              AND hp.status    = 1
            ORDER BY k.nama_kategori ASC, hp.nama_menu ASC
        ", [$id_cabang])->fetchAll(PDO::FETCH_OBJ);

        $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();

        $this->load->view('list_penjualan/v_edit_transaksi', [
            'penjualan'  => $penjualan,
            'items'      => $items,
            'menu_kasir' => $menu_kasir,
            'cabang'     => $cabang
        ]);
    }

    // ============================================================
    // Proses Simpan Edit Transaksi (Full Cart Edit)
    // ============================================================
    public function update_full($id) {
        $user_role = strtolower($_SESSION['role_name'] ?? '');
        if (!in_array($user_role, ['owner', 'admin'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Akses ditolak']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;
            
            $body = file_get_contents('php://input');
            $input = json_decode($body, true);

            if (!$input) {
                $input = $_POST;
                $input['items'] = json_decode($_POST['items'] ?? '[]', true);
            }

            $items = $input['items'] ?? [];

            if (empty($items)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Keranjang belanja kosong']);
                exit;
            }

            $pj = $this->db->query("SELECT id_cabang FROM penjualan WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
            if(!$pj) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Transaksi tidak ditemukan']);
                exit;
            }
            
            // Allow changing the branch
            $id_cabang = (int) ($input['id_cabang'] ?? $pj['id_cabang']);

            $metode_bayar_valid = ['tunai', 'qris', 'transfer', 'lainnya'];
            $metode_bayar = $input['metode_bayar'] ?? 'tunai';
            if (!in_array($metode_bayar, $metode_bayar_valid)) $metode_bayar = 'tunai';

            $bayar = (float) ($input['bayar'] ?? 0);
            
            $items_validated = [];
            $total_bayar = 0;
            $total_diskon = 0;

            foreach ($items as $item) {
                $id_harga_paten = (int) ($item['id_harga_paten'] ?? 0);
                $jumlah_beli = (float) ($item['jumlah_beli'] ?? 0);
                $diskon_item = (float) ($item['diskon'] ?? 0);

                if ($id_harga_paten <= 0 || $jumlah_beli <= 0) continue;

                $origin_hp = $this->db->query("SELECT id_produk, nama_menu FROM harga_paten WHERE id = ?", [$id_harga_paten])->fetch(PDO::FETCH_ASSOC);
                if (!$origin_hp) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Menu tidak valid.']);
                    exit;
                }
                
                $id_produk = (int) $origin_hp['id_produk'];

                $hp = $this->db->query("
                    SELECT hp.id, hp.id_produk, hp.nama_menu, hp.harga_jual_paten
                    FROM harga_paten hp
                    JOIN produk p ON hp.id_produk = p.id AND p.status != 8
                    WHERE hp.id_produk = ? AND hp.id_cabang = ? AND hp.status = 1
                ", [$id_produk, $id_cabang])->fetch(PDO::FETCH_ASSOC);

                if (!$hp) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Menu "' . $origin_hp['nama_menu'] . '" tidak tersedia di cabang ini.']);
                    exit;
                }

                $harga_satuan = (float) $hp['harga_jual_paten'];
                $subtotal = ($jumlah_beli * $harga_satuan) - $diskon_item;

                $items_validated[] = [
                    'id_harga_paten' => (int) $hp['id'], // Use new branch's id_harga_paten
                    'id_produk'      => $id_produk,
                    'jumlah_beli'    => $jumlah_beli,
                    'harga_satuan'   => $harga_satuan,
                    'subtotal'       => $subtotal,
                    'diskon'         => $diskon_item,
                ];

                $total_bayar += $subtotal;
                $total_diskon += $diskon_item;
            }

            if (empty($items_validated)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Tidak ada item valid dalam keranjang']);
                exit;
            }

            // Update Header
            $keterangan = $this->input->xss_clean($input['keterangan'] ?? '');
            $tanggal = $this->input->xss_clean($input['tanggal_transaksi'] ?? date('Y-m-d H:i:s'));
            
            $kembalian = $bayar - $total_bayar;
            
            $this->db->query("
                UPDATE penjualan 
                SET id_cabang = ?, total_bayar = ?, total_diskon = ?, bayar = ?, kembalian = ?, metode_bayar = ?, keterangan = ?, tanggal_transaksi = ?, updated_by = ? 
                WHERE id = ?
            ", [$id_cabang, $total_bayar, $total_diskon, $bayar, $kembalian, $metode_bayar, $keterangan, $tanggal, $user_id, $id]);

            // Delete old details
            $this->db->query("DELETE FROM detail_penjualan WHERE id_penjualan = ?", [$id]);

            // Insert new details
            foreach ($items_validated as $item) {
                $this->db->table('detail_penjualan')->insert([
                    'id_penjualan'   => $id,
                    'id_produk'      => $item['id_produk'],
                    'id_harga_paten' => $item['id_harga_paten'],
                    'jumlah_beli'    => $item['jumlah_beli'],
                    'harga_satuan'   => $item['harga_satuan'],
                    'subtotal'       => $item['subtotal'],
                    'diskon'         => $item['diskon'],
                    'created_by'     => $user_id,
                    'updated_by'     => $user_id,
                ]);
            }

            header('Content-Type: application/json');
            echo json_encode([
                'status'  => true,
                'message' => 'Transaksi berhasil diperbarui',
            ]);
            exit;
        }
    }
}
