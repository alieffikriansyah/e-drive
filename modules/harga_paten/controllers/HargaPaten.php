<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class HargaPaten — Mengelola daftar menu paten dengan harga jual tetap (pagu harga).
//
// Konsep utama:
//   - Setiap produk_jadi per cabang memiliki tepat SATU harga paten aktif.
//   - harga_jual_paten adalah PAGU harga — kasir tidak bisa mengubah harga saat transaksi.
//   - Saat harga paten dibuat/diubah, kolom harga_jual di tabel produk ikut tersinkron.
//   - HPP per porsi dihitung dinamis dari SUM(resep.jumlah_dibutuhkan × produk_bahan.harga_beli).
//
class HargaPaten extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('harga_paten/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman manajemen harga paten beserta dropdown untuk form.
    public function index() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        // Owner / admin melihat semua cabang; non-owner hanya cabang sendiri.
        if (in_array($user_role, ['owner', 'admin'])) {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE status != 8 ORDER BY nama_cabang ASC")->result();
        } else {
            $cabang = $this->db->query("SELECT id, nama_cabang FROM cabang WHERE id = ? AND status != 8", [$user_cabang])->result();
        }

        // Dropdown produk jadi — hanya produk dengan kategori produk_jadi.
        $produk_jadi = $this->db->query("
            SELECT p.id, p.nama_produk
            FROM produk p
            JOIN kategori k ON p.id_kategori = k.id AND k.jenis = 'produk_jadi'
            WHERE p.status != 8
            ORDER BY p.nama_produk ASC
        ")->result();

        $this->load->view('harga_paten/v_index', [
            'cabang'      => $cabang,
            'produk_jadi' => $produk_jadi,
        ]);
    }

    // Mengirim daftar harga paten via AJAX untuk DataTable.
    // Menampilkan HPP per porsi yang dihitung dari resep × produk.harga_beli bahan baku.
    public function load_data() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $filter_cabang = (int)($_GET['id_cabang'] ?? 0);

        if (in_array($user_role, ['owner', 'admin'])) {
            $where_cabang = $filter_cabang > 0 ? "AND hp.id_cabang = $filter_cabang" : '';
        } else {
            $where_cabang = "AND hp.id_cabang = $user_cabang";
        }

        $data = $this->db->query("
            SELECT
                hp.*,
                p.nama_produk,
                0 AS stok_sekarang,
                k.nama_kategori,
                s.nama_satuan,
                s.singkatan,
                c.nama_cabang,
                0 AS hpp_per_porsi,
                0 AS laba_per_porsi
            FROM harga_paten hp
            JOIN produk   p ON hp.id_produk   = p.id
            JOIN kategori k ON p.id_kategori  = k.id
            JOIN satuan   s ON p.id_satuan    = s.id
            JOIN cabang   c ON hp.id_cabang   = c.id AND c.status != 8
            WHERE hp.status != 8
              $where_cabang
            ORDER BY c.nama_cabang ASC, hp.nama_menu ASC
        ")->result();

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $data]);
        exit;
    }

    // Menyimpan harga paten baru.
    // Setelah insert, sinkronkan harga_jual di tabel produk.
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id     = $_SESSION['user_id']   ?? 0;
            $user_role   = strtolower($_SESSION['role_name'] ?? '');
            $user_cabang = $_SESSION['id_cabang'] ?? 0;

            $id_cabang = (in_array($user_role, ['owner', 'admin']))
                ? (int)($_POST['id_cabang'] ?? 0)
                : (int)$user_cabang;

            $id_produk        = (int)($_POST['id_produk'] ?? 0);
            $nama_menu        = $this->input->xss_clean($_POST['nama_menu']        ?? '');
            $harga_jual_paten = (float)($_POST['harga_jual_paten'] ?? 0);

            if ($id_cabang <= 0 || $id_produk <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Cabang dan produk wajib dipilih']);
                exit;
            }

            if (empty($nama_menu)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama menu tidak boleh kosong']);
                exit;
            }

            if ($harga_jual_paten <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Harga jual paten harus lebih dari 0']);
                exit;
            }

            // Cegah duplikat: satu produk per cabang hanya boleh punya satu harga paten.
            $cek = $this->db->query(
                "SELECT id FROM harga_paten WHERE id_cabang = ? AND id_produk = ? AND status != 8",
                [$id_cabang, $id_produk]
            )->fetch();

            if ($cek) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Produk ini sudah memiliki harga paten aktif di cabang tersebut. Gunakan Edit untuk mengubahnya.']);
                exit;
            }

            $data = [
                'id_cabang'        => $id_cabang,
                'id_produk'        => $id_produk,
                'nama_menu'        => $nama_menu,
                'harga_jual_paten' => $harga_jual_paten,
                'keterangan'       => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'created_by'       => $user_id,
                'updated_by'       => $user_id,
            ];

            $insert = $this->db->table('harga_paten')->insert($data);

            if ($insert) {

                header('Content-Type: application/json');
                echo json_encode(['status' => true, 'message' => 'Harga paten berhasil ditambahkan', 'id' => $insert]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Harga paten gagal ditambahkan']);
            }
            exit;
        }
    }

    // Mengambil satu data harga paten untuk form edit.
    public function get_harga_paten($id) {
        $data = $this->db->query("
            SELECT hp.*, p.nama_produk, c.nama_cabang
            FROM harga_paten hp
            JOIN produk p ON hp.id_produk = p.id
            JOIN cabang c ON hp.id_cabang = c.id
            WHERE hp.id = ?
        ", [$id])->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        if ($data) {
            echo json_encode(['status' => true, 'data' => $data]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    // Memperbarui harga paten dan sinkronkan ke produk.harga_jual.
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id          = $_SESSION['user_id']   ?? 0;
            $nama_menu        = $this->input->xss_clean($_POST['nama_menu']        ?? '');
            $harga_jual_paten = (float)($_POST['harga_jual_paten'] ?? 0);

            if (empty($nama_menu)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama menu tidak boleh kosong']);
                exit;
            }

            if ($harga_jual_paten <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Harga jual paten harus lebih dari 0']);
                exit;
            }

            // Ambil id_produk dari record yang akan diupdate (untuk sinkronisasi ke produk).
            $existing = $this->db->query(
                "SELECT id_produk FROM harga_paten WHERE id = ?", [$id]
            )->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
                exit;
            }

            $update = $this->db->table('harga_paten')->where('id', $id)->update([
                'nama_menu'        => $nama_menu,
                'harga_jual_paten' => $harga_jual_paten,
                'keterangan'       => $this->input->xss_clean($_POST['keterangan'] ?? ''),
                'updated_by'       => $user_id,
            ]);

            if ($update !== false) {

                header('Content-Type: application/json');
                echo json_encode(['status' => true, 'message' => 'Harga paten berhasil diperbarui']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Harga paten gagal diperbarui']);
            }
            exit;
        }
    }

    // Menghapus harga paten secara soft-delete.
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('harga_paten')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Harga paten berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Harga paten gagal dihapus']);
            }
            exit;
        }
    }

    // Mengambil daftar harga paten aktif per cabang untuk keperluan kasir (API endpoint).
    // Dipakai oleh Penjualan::index() via AJAX untuk memuat menu yang bisa dijual.
    public function get_menu_kasir() {
        $user_role   = strtolower($_SESSION['role_name'] ?? '');
        $user_cabang = $_SESSION['id_cabang'] ?? 0;

        $id_cabang = (in_array($user_role, ['owner', 'admin']))
            ? (int)($_GET['id_cabang'] ?? $user_cabang)
            : (int)$user_cabang;

        if ($id_cabang <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Cabang tidak valid']);
            exit;
        }

        $menu = $this->db->query("
            SELECT
                hp.id               AS id_harga_paten,
                hp.id_produk,
                hp.nama_menu,
                hp.harga_jual_paten,
                0 AS stok_sekarang,
                k.nama_kategori,
                s.nama_satuan,
                s.singkatan,
                0 AS hpp_per_porsi
            FROM harga_paten hp
            JOIN produk   p  ON hp.id_produk  = p.id AND p.status != 8
            JOIN kategori k  ON p.id_kategori = k.id AND k.jenis = 'produk_jadi'
            JOIN satuan   s  ON p.id_satuan   = s.id
            WHERE hp.id_cabang = ?
              AND hp.status    = 1
            ORDER BY k.nama_kategori ASC, hp.nama_menu ASC
        ", [$id_cabang])->result();

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'data' => $menu]);
        exit;
    }
}
