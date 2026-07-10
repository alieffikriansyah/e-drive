<?php

// Memuat middleware untuk pengecekan akses.
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class Produk — Master data item GLOBAL (bahan baku, setengah jadi, produk jadi).
//
// ARSITEKTUR FINAL:
//   produk         = master global (id, kategori, satuan, nama, kode, stok_minimum, keterangan)
//   log_stok       = stok per (id_produk, id_cabang) — sumber kebenaran stok
//   v_stok         = VIEW stok real-time: entri log_stok terakhir per (id_produk, id_cabang)
//   resep.harga_hpp= HPP per bahan baku (diupdate weighted average saat BarangMasuk::create)
//   harga_paten    = harga jual per (id_produk, id_cabang)
//
class Produk extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('produk/MOD', 'mod');
    }

    public function _middleware() {
        AuthMiddleware::check();
    }

    // Menampilkan halaman manajemen produk.
    public function index() {
        $kategori = $this->db->query("SELECT id, nama_kategori, jenis FROM kategori WHERE status != 8 ORDER BY jenis ASC, nama_kategori ASC")->result();
        $satuan   = $this->db->query("SELECT id, nama_satuan, singkatan FROM satuan WHERE status != 8 ORDER BY nama_satuan ASC")->result();

        $this->load->view('produk/v_index', [
            'kategori' => $kategori,
            'satuan'   => $satuan,
        ]);
    }

    // Mengirim semua produk via AJAX — produk bersifat GLOBAL, tidak difilter per cabang.
    // Stok per cabang ditampilkan terpisah via modul Log Stok atau Ringkasan Stok.
    public function load_data() {
        ob_start(); // Buffer output agar PHP notice/warning tidak mencemari JSON
        try {
            $produk = $this->db->query("
                SELECT
                    p.id,
                    p.id_kategori,
                    p.id_satuan,
                    p.nama_produk,
                    p.kode_produk,
                    p.stok_minimum,
                    p.keterangan,
                    p.status,
                    p.created_at,
                    k.nama_kategori,
                    k.jenis     AS jenis_kategori,
                    s.nama_satuan,
                    s.singkatan AS satuan_singkatan
                FROM produk p
                LEFT JOIN kategori k ON p.id_kategori = k.id AND k.status != 8
                LEFT JOIN satuan   s ON p.id_satuan   = s.id AND s.status != 8
                WHERE p.status != 8
                
            ")->result();

            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => true, 'data' => $produk]);
        } catch (\Exception $e) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }


    // Menyimpan produk baru (master global).
    // Jika kategori = produk_jadi, harga_jual wajib diisi dan otomatis sync ke harga_paten.
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $nama_produk = $this->input->xss_clean($_POST['nama_produk'] ?? '');
            $id_kategori = (int)($_POST['id_kategori'] ?? 0);
            $id_satuan   = (int)($_POST['id_satuan']   ?? 0);

            if (empty($nama_produk) || $id_kategori <= 0 || $id_satuan <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama produk, kategori, dan satuan wajib diisi']);
                exit;
            }

            // Cek apakah kategori ini adalah produk_jadi
            $kat = $this->db->query("SELECT jenis FROM kategori WHERE id = ?", [$id_kategori])->fetch(PDO::FETCH_ASSOC);
            $is_produk_jadi = ($kat && $kat['jenis'] === 'produk_jadi');

            $harga_jual = $is_produk_jadi ? (float)($_POST['harga_jual'] ?? 0) : 0;

            $data = [
                'id_kategori' => $id_kategori,
                'id_satuan'   => $id_satuan,
                'nama_produk' => $nama_produk,
                'kode_produk' => $this->input->xss_clean($_POST['kode_produk'] ?? ''),
                'stok_minimum'=> (float)($_POST['stok_minimum'] ?? 0),
                'keterangan'  => $this->input->xss_clean($_POST['keterangan']  ?? ''),
                'created_by'  => $user_id,
                'updated_by'  => $user_id,
            ];

            $insert = $this->db->table('produk')->insert($data);

            if ($insert && $is_produk_jadi) {
                $this->_sync_harga_paten($insert, $nama_produk, $harga_jual, $user_id);
            }

            header('Content-Type: application/json');
            if ($insert) {
                echo json_encode(['status' => true, 'message' => 'Produk berhasil ditambahkan', 'id' => $insert]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Produk gagal ditambahkan']);
            }
            exit;
        }
    }

    // Mengambil satu data produk untuk form edit.
    // Jika produk_jadi, ambil juga harga dari harga_paten (gabungan semua cabang, ambil pertama).
    public function get_produk($id) {
        $produk = $this->db->query("
            SELECT p.id, p.id_kategori, p.id_satuan,
                   p.nama_produk, p.kode_produk, p.stok_minimum, p.keterangan, p.status,
                   k.nama_kategori, k.jenis AS jenis_kategori, s.nama_satuan,
                   COALESCE(hp.harga_jual_paten, 0) AS harga_jual
            FROM produk p
            LEFT JOIN kategori k ON p.id_kategori = k.id
            LEFT JOIN satuan   s ON p.id_satuan   = s.id
            LEFT JOIN harga_paten hp ON hp.id_produk = p.id AND hp.status = 1
            WHERE p.id = ?
            LIMIT 1
        ", [$id])->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        if ($produk) {
            echo json_encode(['status' => true, 'data' => $produk]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        }
        exit;
    }

    // Memperbarui master data produk.
    // Jika kategori = produk_jadi, sync harga_jual ke harga_paten.
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'] ?? 0;

            $nama_produk = $this->input->xss_clean($_POST['nama_produk'] ?? '');
            if (empty($nama_produk)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Nama produk tidak boleh kosong']);
                exit;
            }

            $id_kategori = (int)($_POST['id_kategori'] ?? 0);

            // Cek apakah kategori ini adalah produk_jadi
            $kat = $this->db->query("SELECT jenis FROM kategori WHERE id = ?", [$id_kategori])->fetch(PDO::FETCH_ASSOC);
            $is_produk_jadi = ($kat && $kat['jenis'] === 'produk_jadi');

            $harga_jual = $is_produk_jadi ? (float)($_POST['harga_jual'] ?? 0) : 0;

            $data = [
                'id_kategori' => $id_kategori,
                'id_satuan'   => (int)($_POST['id_satuan']   ?? 0),
                'nama_produk' => $nama_produk,
                'kode_produk' => $this->input->xss_clean($_POST['kode_produk'] ?? ''),
                'stok_minimum'=> (float)($_POST['stok_minimum'] ?? 0),
                'keterangan'  => $this->input->xss_clean($_POST['keterangan']  ?? ''),
                'updated_by'  => $user_id,
            ];

            $update = $this->db->table('produk')->where('id', $id)->update($data);

            if ($update !== false && $is_produk_jadi) {
                $this->_sync_harga_paten((int)$id, $nama_produk, $harga_jual, $user_id);
            }

            header('Content-Type: application/json');
            if ($update !== false) {
                echo json_encode(['status' => true, 'message' => 'Produk berhasil diperbarui']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Produk gagal diperbarui']);
            }
            exit;
        }
    }

    // Menghapus produk secara soft-delete.
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('produk')->where('id', $id)->delete();

            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode(['status' => true, 'message' => 'Produk berhasil dihapus']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Produk gagal dihapus']);
            }
            exit;
        }
    }

    // Memulihkan produk yang di-soft-delete.
    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restore = $this->db->table('produk')->where('id', $id)->restore();

            header('Content-Type: application/json');
            if ($restore) {
                echo json_encode(['status' => true, 'message' => 'Produk berhasil dipulihkan']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Produk gagal dipulihkan']);
            }
            exit;
        }
    }

    // ============================================================
    // PRIVATE: Auto-sync harga_paten dari produk_jadi
    // ============================================================
    // Untuk setiap cabang aktif, upsert record di harga_paten:
    //   - Jika sudah ada (id_produk + id_cabang) → update harga_jual_paten & nama_menu
    //   - Jika belum ada → insert baru
    // Ini memastikan kasir selalu melihat harga terbaru dari master produk.
    private function _sync_harga_paten($id_produk, $nama_produk, $harga_jual, $user_id) {
        $cabang_list = $this->db->query("SELECT id FROM cabang WHERE status != 8")->result();

        foreach ($cabang_list as $cabang) {
            $exists = $this->db->query(
                "SELECT id FROM harga_paten WHERE id_produk = ? AND id_cabang = ?",
                [$id_produk, $cabang->id]
            )->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                // Update harga dan nama saja, jangan ubah status
                $this->db->table('harga_paten')
                    ->where('id_produk', $id_produk)
                    ->where('id_cabang', $cabang->id)
                    ->update([
                        'nama_menu'        => $nama_produk,
                        'harga_jual_paten' => $harga_jual,
                        'updated_by'       => $user_id,
                    ]);
            } else {
                $this->db->table('harga_paten')->insert([
                    'id_cabang'        => $cabang->id,
                    'id_produk'        => $id_produk,
                    'nama_menu'        => $nama_produk,
                    'harga_jual_paten' => $harga_jual,
                    'status'           => 1,
                    'created_by'       => $user_id,
                    'updated_by'       => $user_id,
                ]);
            }
        }
    }
}
