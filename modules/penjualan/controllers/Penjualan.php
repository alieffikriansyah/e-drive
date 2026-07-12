<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class Penjualan — Mengelola transaksi kasir (POS).
//
// KONSEP HARGA:
//   - Harga jual WAJIB diambil dari tabel `harga_paten` (pagu harga).
//   - Kasir tidak bisa input harga manual; frontend hanya mengirim id_harga_paten.
//   - HPP per porsi = SUM(resep.jumlah_dibutuhkan × produk_bahan.harga_beli)
//     di mana produk_bahan.harga_beli adalah HPP rata-rata (weighted average)
//     yang diupdate otomatis setiap ada barang masuk baru.
//
// WORKFLOW UTAMA saat Kasir menekan "Bayar":
//   1. Generate nomor nota otomatis.
//   2. Insert header ke tabel `penjualan`.
//   3. Loop setiap item di keranjang:
//      a. Validasi & ambil harga_jual_paten dari harga_paten (server-side).
//      b. Hitung HPP per porsi dari SUM resep × produk.harga_beli.
//      c. Insert ke `detail_penjualan` (snapshot harga + id_harga_paten).
//      d. Query `resep` → potong stok bahan baku → catat `log_stok` (jenis: 'penjualan').
//   4. Update `total_hpp` di header `penjualan`.
//
class Penjualan extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        require_once APPPATH . 'helpers/log_helper.php';
        $this->load->model('penjualan/MOD', 'mod');
    }

    public function _middleware()
    {
        AuthMiddleware::check();
    }

    // Menampilkan halaman kasir / point-of-sale.
    // Menu dari harga_paten, stok dari v_stok, HPP dari resep.harga_hpp.
    public function index()
    {
        $user_cabang = $_SESSION['id_cabang'] ?? 0;
        $user_role = strtolower($_SESSION['role_name'] ?? '');
        $is_owner = in_array($user_role, ['owner', 'admin']);

        $id_cabang = $is_owner
            ? (int) ($_GET['id_cabang'] ?? $user_cabang)
            : (int) $user_cabang;

        // Daftar cabang untuk dropdown di modal pembayaran.
        if ($is_owner) {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();
        } else {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE id = ? AND status != 8", [$user_cabang])->result();
        }

        // Menu kasir:
        // - Jika owner tanpa filter cabang (id_cabang=0): tampilkan semua produk_jadi dari harga_paten
        //   (GROUP BY id_produk untuk hindari duplikat, ambil harga tertinggi/terbaru)
        // - Jika kasir / owner dengan filter cabang: tampilkan sesuai cabang
        if ($is_owner && $id_cabang <= 0) {
            $menu_kasir = $this->db->query("
                SELECT
                    MAX(hp.id)               AS id_harga_paten,
                    hp.id_produk,
                    hp.nama_menu,
                    MAX(hp.harga_jual_paten) AS harga_jual_paten,
                    0                     AS stok_sekarang,
                    0                     AS has_stok_record,
                    k.nama_kategori,
                    s.nama_satuan,
                    s.singkatan,
                    0                     AS hpp_per_porsi
                FROM harga_paten hp
                JOIN produk   p  ON hp.id_produk  = p.id AND p.status != 8
                JOIN kategori k  ON p.id_kategori = k.id AND k.jenis = 'produk_jadi'
                JOIN satuan   s  ON p.id_satuan   = s.id
                WHERE hp.status = 1
                GROUP BY hp.id_produk, hp.nama_menu, k.nama_kategori, s.nama_satuan, s.singkatan
                ORDER BY k.nama_kategori ASC, hp.nama_menu ASC
            ")->result();
        } else {
            $menu_kasir = $this->db->query("
                SELECT
                    hp.id               AS id_harga_paten,
                    hp.id_produk,
                    hp.nama_menu,
                    hp.harga_jual_paten,
                    0                     AS stok_sekarang,
                    0                     AS has_stok_record,
                    k.nama_kategori,
                    s.nama_satuan,
                    s.singkatan,
                    0                     AS hpp_per_porsi
                FROM harga_paten hp
                JOIN produk   p  ON hp.id_produk  = p.id AND p.status != 8
                JOIN kategori k  ON p.id_kategori = k.id AND k.jenis = 'produk_jadi'
                JOIN satuan   s  ON p.id_satuan   = s.id
                WHERE hp.id_cabang = ?
                  AND hp.status    = 1
                ORDER BY k.nama_kategori ASC, hp.nama_menu ASC
            ", [$id_cabang])->result();
        }

        $this->load->view('penjualan/v_index', [
            'menu_kasir' => $menu_kasir,
            'cabang' => $cabang,
        ]);
    }


    // Mengirim daftar riwayat transaksi via AJAX.
    public function load_data()
    {
        $user_cabang = $_SESSION['id_cabang'] ?? 0;
        $user_role = strtolower($_SESSION['role_name'] ?? '');

        $where_cabang = (!in_array($user_role, ['owner', 'admin'])) ? "AND pj.id_cabang = $user_cabang" : '';

        $tgl_awal = $this->input->xss_clean($_GET['tgl_awal'] ?? date('Y-m-d'));
        $tgl_akhir = $this->input->xss_clean($_GET['tgl_akhir'] ?? date('Y-m-d'));

        $data = $this->db->query("
            SELECT
                pj.*,
                c.nama_cabang,
                u.name AS nama_kasir,
                (pj.total_bayar) AS laba_kotor
            FROM penjualan pj
            JOIN cabang c ON pj.id_cabang = c.id
            JOIN users  u ON pj.id_user   = u.id
            WHERE pj.status != 8
              AND DATE(pj.tanggal_transaksi) BETWEEN ? AND ?
              $where_cabang
            ORDER BY pj.tanggal_transaksi DESC
        ", [$tgl_awal, $tgl_akhir])->result();

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }

    // FUNGSI UTAMA: Memproses transaksi penjualan.
    // Menerima data keranjang dalam format JSON via POST body.
    //
    // Format data POST yang diharapkan:
    // {
    //   "metode_bayar": "tunai",
    //   "bayar": 50000,
    //   "keterangan": "",
    //   "items": [
    //     { "id_harga_paten": 3, "jumlah_beli": 2, "diskon": 0 },
    //     { "id_harga_paten": 5, "jumlah_beli": 1, "diskon": 0 }
    //   ]
    // }
    // PENTING: Frontend hanya mengirim id_harga_paten (bukan harga_satuan manual).
    //          Harga diambil server-side dari harga_paten.harga_jual_paten (pagu harga).
    //          HPP dihitung server-side dari SUM(resep × produk.harga_beli).
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;
            $user_role = strtolower($_SESSION['role_name'] ?? '');
            $user_cabang = $_SESSION['id_cabang'] ?? 0;

            // Baca body JSON yang dikirim dari frontend (aplikasi kasir).
            $body = file_get_contents('php://input');
            $input = json_decode($body, true);

            // Fallback ke $_POST biasa jika bukan JSON.
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

            // Kasir tidak bisa mengubah id_cabang; selalu pakai cabang dari session.
            $id_cabang = (in_array($user_role, ['owner', 'admin']))
                ? (int) ($input['id_cabang'] ?? $user_cabang)
                : (int) $user_cabang;

            if ($id_cabang <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Cabang tidak valid. Harap hubungi administrator.']);
                exit;
            }

            // --- LANGKAH 1: Generate nomor nota unik ---
            $no_nota = $this->_generate_no_nota($id_cabang);

            $metode_bayar_valid = ['tunai', 'qris', 'transfer', 'lainnya'];
            $metode_bayar = $input['metode_bayar'] ?? 'tunai';
            if (!in_array($metode_bayar, $metode_bayar_valid))
                $metode_bayar = 'tunai';

            $bayar = (float) ($input['bayar'] ?? 0);

            // Pra-validasi semua item: ambil harga paten & HPP server-side sebelum insert.
            // Ini mencegah transaksi setengah jalan jika ada item yang tidak valid.
            $items_validated = [];
            $total_bayar = 0;
            $total_diskon = 0;

            foreach ($items as $item) {
                $id_harga_paten = (int) ($item['id_harga_paten'] ?? 0);
                $jumlah_beli = (float) ($item['jumlah_beli'] ?? 0);
                $diskon_item = (float) ($item['diskon'] ?? 0);

                if ($id_harga_paten <= 0 || $jumlah_beli <= 0)
                    continue;

                // 1. Dapatkan id_produk asal dari id_harga_paten yang dikirim
                $origin_hp = $this->db->query("
                    SELECT id_produk, nama_menu FROM harga_paten WHERE id = ?
                ", [$id_harga_paten])->fetch(PDO::FETCH_ASSOC);

                if (!$origin_hp) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Menu tidak valid atau tidak ditemukan.']);
                    exit;
                }
                $id_produk = (int) $origin_hp['id_produk'];

                // 2. Cari harga_paten aktif untuk id_produk tersebut pada cabang tujuan transaksi
                $hp = $this->db->query("
                    SELECT hp.id, hp.id_produk, hp.nama_menu, hp.harga_jual_paten,
                           p.nama_produk
                    FROM harga_paten hp
                    JOIN produk p ON hp.id_produk = p.id AND p.status != 8
                    WHERE hp.id_produk = ? AND hp.id_cabang = ? AND hp.status = 1
                ", [$id_produk, $id_cabang])->fetch(PDO::FETCH_ASSOC);

                if (!$hp) {
                    // Beri error informatif jika cabang yang dipilih belum diset harga patennya
                    $cabang_name_row = $this->db->query("SELECT nama_cabang FROM cabang WHERE id = ?", [$id_cabang])->fetch(PDO::FETCH_ASSOC);
                    $cabang_name = $cabang_name_row['nama_cabang'] ?? 'Cabang Terpilih';

                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => false,
                        'message' => 'Menu "' . ($origin_hp['nama_menu'] ?? 'Produk') . '" belum diset harga patennya di ' . $cabang_name
                    ]);
                    exit;
                }

                $hpp_per_porsi = 0;
                $harga_satuan = (float) $hp['harga_jual_paten']; // Pagu harga dari harga_paten
                $subtotal = ($jumlah_beli * $harga_satuan) - $diskon_item;
             

                $items_validated[] = [
                    'id_harga_paten' => $id_harga_paten,
                    'id_produk' => (int) $hp['id_produk'],
                    'nama_produk' => $hp['nama_menu'],
                    'jumlah_beli' => $jumlah_beli,
                    'harga_satuan' => $harga_satuan,
                    'harga_hpp' => 0,
                    'subtotal' => $subtotal,
                  
                    'diskon' => $diskon_item,
                ];

                $total_bayar += $subtotal;
                $total_diskon += $diskon_item;
            }

            if (empty($items_validated)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Tidak ada item valid dalam keranjang']);
                exit;
            }

            // --- LANGKAH 2: Insert header penjualan ---
            $data_header = [
                'id_cabang' => $id_cabang,
                'id_user' => $user_id,
                'no_nota' => $no_nota,
                'tanggal_transaksi' => date('Y-m-d H:i:s'),
                'total_bayar' => $total_bayar,
               
                'total_diskon' => $total_diskon,
                'bayar' => $bayar,
                'kembalian' => $bayar - $total_bayar,
                'metode_bayar' => $metode_bayar,
                'keterangan' => $this->input->xss_clean($input['keterangan'] ?? ''),
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ];

            $id_penjualan = $this->db->table('penjualan')->insert($data_header);

            if (!$id_penjualan) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Gagal membuat transaksi. Silakan coba lagi.']);
                exit;
            }

            // --- LANGKAH 3: Proses setiap item yang sudah tervalidasi ---
            foreach ($items_validated as $item) {
                $id_produk = $item['id_produk'];
                $jumlah_beli = $item['jumlah_beli'];

                // 3a. Insert ke detail_penjualan (snapshot harga + referensi harga_paten).
                $this->db->table('detail_penjualan')->insert([
                    'id_penjualan' => $id_penjualan,
                    'id_produk' => $id_produk,
                    'id_harga_paten' => $item['id_harga_paten'],
                    'jumlah_beli' => $jumlah_beli,
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                    'diskon' => $item['diskon'],
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                ]);
            }

            // HPP dan stok log diabaikan secara total
            $this->db->table('penjualan')->where('id', $id_penjualan)->update([
           
                'updated_by' => $user_id,
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'status' => true,
                'message' => 'Transaksi berhasil disimpan',
                'id_penjualan' => $id_penjualan,
                'no_nota' => $no_nota,
                'total_bayar' => $total_bayar,
                'kembalian' => $bayar - $total_bayar,
            ]);
            exit;
        }
    }

    // Mengambil detail nota transaksi beserta item-itemnya (untuk struk / invoice).
    public function get_nota($id)
    {
        // Ambil header nota.
        $nota = $this->db->query("
            SELECT pj.*, c.nama_cabang, u.name AS nama_kasir
            FROM penjualan pj
            JOIN cabang c ON pj.id_cabang = c.id
            JOIN users  u ON pj.id_user   = u.id
            WHERE pj.id = ?
        ", [$id])->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        if (!$nota) {
            echo json_encode(['status' => false, 'message' => 'Nota tidak ditemukan']);
            exit;
        }

        // Ambil detail item dalam nota.
        $items = $this->db->query("
            SELECT
                dp.*,
                p.nama_produk,
                s.nama_satuan
            FROM detail_penjualan dp
            JOIN produk p ON dp.id_produk  = p.id
            JOIN satuan s ON p.id_satuan   = s.id
            WHERE dp.id_penjualan = ?
              AND dp.status != 8
        ", [$id])->result();

        $nota['items'] = $items;

        echo json_encode(['status' => true, 'data' => $nota]);
        exit;
    }

    // Membatalkan / void transaksi (soft-delete header penjualan).
    // CATATAN PENTING: Void TIDAK otomatis mengembalikan stok.
    // Lakukan koreksi stok manual via log_stok jika diperlukan.
    public function void($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            // Get nota info before deleting for logging
            $nota = $this->db->query("SELECT no_nota, total_bayar FROM penjualan WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);

            $delete = $this->db->table('penjualan')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                // Update updated_by untuk audit trail.
                $this->db->table('penjualan')->where('id', $id)->update(['updated_by' => $user_id]);
                
                echo json_encode(['status' => true, 'message' => 'Transaksi berhasil di-void. Koreksi stok jika diperlukan.']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Gagal mem-void transaksi']);
            }
            exit;
        }
    }

    // Fungsi helper: Generate nomor nota unik per cabang per hari.
    // Format: TRX-[ID_CABANG]-[YYYYMMDD]-[URUTAN 4 DIGIT]
    // Contoh: TRX-1-20250604-0001
    private function _generate_no_nota($id_cabang)
    {
        $today = date('Ymd');
        $prefix = "TRX-{$id_cabang}-{$today}-";

        // Cari nomor urut terakhir hari ini untuk cabang ini.
        $last = $this->db->query("
            SELECT no_nota
            FROM penjualan
            WHERE no_nota LIKE ?
            ORDER BY id DESC
            LIMIT 1
        ", [$prefix . '%'])->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            // Ambil bagian urutan di akhir nomor nota dan tambahkan 1.
            $parts = explode('-', $last['no_nota']);
            $urutan = (int) end($parts) + 1;
        } else {
            $urutan = 1;
        }

        return $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
    }
}
