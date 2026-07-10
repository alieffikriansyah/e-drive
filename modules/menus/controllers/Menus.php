<?php

// Memuat middleware untuk pengecekan akses (Analogi: Memerintahkan satpam bersiap di pintu masuk).
require_once APPPATH . 'middleware/AuthMiddleware.php';

// Class Menus turunan dari Controller (Analogi: Departemen khusus yang mengelola daftar menu di restoran/aplikasi).
class Menus extends Controller {
    
    // Fungsi konstruktor (Analogi: Menyiapkan alat kerja sebelum mulai bertugas).
    public function __construct() {
        // Menjalankan persiapan dasar dari kantor pusat (induk Controller).
        parent::__construct();
        // Memuat helper URL agar bisa membuat link dengan mudah (Analogi: Mengambil buku panduan alamat).
        $this->load->helper('url');
        // Memuat model untuk berinteraksi dengan tabel database terkait menu (Analogi: Mempekerjakan staf spesialis data menu).
        $this->load->model('menus/MOD', 'mod');
    }

    // Fungsi middleware penahan akses tanpa login.
    public function _middleware() {
        // Cek login (Analogi: Satpam memeriksa karcis pengunjung sebelum masuk ruangan ini).
        AuthMiddleware::check();
    }

    // Fungsi index untuk memuat halaman awal (Analogi: Membuka pintu ruang lobi departemen menu).
    public function index() {
        // Ambil data jabatan/roles (Analogi: Mengambil daftar siapa saja yang boleh melihat menu tertentu).
        $roles = $this->db->table('roles')->get()->result();
        // Ambil menu-menu utama (yang tidak punya parent) untuk opsi dropdown (Analogi: Mengambil daftar kategori utama menu masakan).
        $parent_menus = $this->db->query("SELECT * FROM system_menus WHERE parent_id IS NULL AND status != 8")->result();
        
        // Menampilkan view dengan membawa data tersebut (Analogi: Menyerahkan daftar kategori dan peran kepada dekorator ruangan).
        $this->load->view('menus/v_index', [
            'roles' => $roles, 
            'parent_menus' => $parent_menus
        ]);
    }

    // Fungsi untuk mengirim data daftar menu via AJAX (Analogi: Menyiapkan laporan inventaris menu dalam bentuk digital).
    public function load_data() {
        // Melakukan query untuk mengambil semua menu beserta nama menu induknya (Analogi: Mencatat semua menu beserta kategori utamanya dari lemari arsip).
        $menus = $this->db->query("
            SELECT m1.*, m2.name as parent_name 
            FROM system_menus m1 
            LEFT JOIN system_menus m2 ON m1.parent_id = m2.id
            WHERE m1.status != 8
            ORDER BY m1.status ASC, m1.parent_id ASC, m1.order_num ASC
        ")->result();
        
        // Memberi tahu peminta data bahwa balasan berformat JSON (Analogi: Menempelkan stiker 'Data Digital JSON' pada paket).
        header('Content-Type: application/json');
        
        // Mencetak data ke format JSON (Analogi: Memasukkan daftar tadi ke kargo JSON lalu mengirimnya ke Javascript).
        echo json_encode([
            'status' => true,
            'data' => $menus
        ]);
        
        // Menghentikan script (Analogi: Menutup gerbang agar tidak ada data HTML lain yang terselip).
        exit;
    }
    
    // Fungsi untuk membuat/menyimpan menu baru (Analogi: Menerima resep menu baru untuk dimasukkan ke daftar).
    public function create() {
        // Hanya memproses request yang bertipe POST (Analogi: Memastikan koki menyerahkan dokumen tertulis, bukan cuma bertanya).
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Mengumpulkan data dari form input dan membersihkannya dari injeksi script jahat.
            $data = [
                'name' => $this->input->xss_clean($_POST['name'] ?? ''),
                'url' => $this->input->xss_clean($_POST['url'] ?? ''),
                'icon' => $this->input->xss_clean($_POST['icon'] ?? ''),
                'order_num' => (int)($_POST['order_num'] ?? 0),
                // Jika parent_id kosong, set menjadi null (menu utama).
                'parent_id' => empty($_POST['parent_id']) ? null : (int)$_POST['parent_id'],
                // Jika checkbox is_active dicentang valuenya 1, jika tidak 0.
                'is_active' => isset($_POST['is_active']) && $_POST['is_active'] === '1' ? 1 : 0
            ];
            
            // Menyimpan ke database dan mendapatkan ID dari menu yang baru disimpan (Analogi: Memasukkan menu ke daftar dan mendapatkan nomor urutnya).
            $menu_id = $this->db->table('system_menus')->insert($data);
            
            // Siapkan header json
            header('Content-Type: application/json');
            
            // Jika sukses menyimpan menu
            if ($menu_id) {
                // Proses data checkbox 'roles' (jabatan mana saja yang boleh melihat menu ini).
                if (!empty($_POST['roles'])) {
                    $roles = is_array($_POST['roles']) ? $_POST['roles'] : explode(',', $_POST['roles']);
                    foreach ($roles as $role_id) {
                        if (empty($role_id)) continue;
                        // Simpan akses menu untuk setiap role ke tabel relasi (Analogi: Menandai buku izin akses untuk masing-masing jabatan).
                        $sql = "INSERT INTO role_menu_access (role_id, menu_id) VALUES (?, ?)";
                        $this->db->query($sql, [(int)$role_id, $menu_id]);
                    }
                }
                
                // Balasan berhasil
                echo json_encode([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan'
                ]);
            } else {
                // Balasan gagal
                echo json_encode([
                    'status' => false,
                    'message' => 'Data gagal ditambahkan'
                ]);
            }
            exit;
        }
    }
    
    // Fungsi untuk mengambil satu data spesifik untuk keperluan Edit di Form (Analogi: Meminta detail resep satu menu untuk diperbaiki).
    public function get_menu($id) {
        // Ambil satu baris data menu dari database.
        $menu = $this->db->table('system_menus')->where('id', $id)->get()->row_array();
        
        header('Content-Type: application/json');
        // Jika datanya ketemu
        if ($menu) {
            // Ambil juga data jabatan mana saja yang punya akses ke menu ini (untuk men-centang checkbox secara otomatis di layar edit).
            $menu_roles = $this->db->query("SELECT role_id FROM role_menu_access WHERE menu_id = ?", [$id])->result_array();
            // Ubah menjadi array sederhana [1, 2, 3] dst.
            $menu['role_ids'] = array_column($menu_roles, 'role_id');
            
            // Kirim balasan beserta data menu-nya
            echo json_encode([
                'status' => true,
                'data' => $menu
            ]);
        } else {
            // Jika ID tidak ditemukan
            echo json_encode([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        exit;
    }
    
    // Fungsi untuk menyimpan perubahan data edit menu (Analogi: Mengganti dokumen resep lama dengan versi revisi).
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sama seperti saat create, kumpulkan data baru yang disubmit.
            $data = [
                'name' => $this->input->xss_clean($_POST['name'] ?? ''),
                'url' => $this->input->xss_clean($_POST['url'] ?? ''),
                'icon' => $this->input->xss_clean($_POST['icon'] ?? ''),
                'order_num' => (int)($_POST['order_num'] ?? 0),
                'parent_id' => empty($_POST['parent_id']) ? null : (int)$_POST['parent_id'],
                'is_active' => isset($_POST['is_active']) && $_POST['is_active'] === '1' ? 1 : 0
            ];
            
            // Lakukan perintah UPDATE ke database berdasarkan ID.
            $update = $this->db->table('system_menus')->where('id', $id)->update($data);
            
            header('Content-Type: application/json');
            if ($update !== false) {
                // Trik mengupdate hak akses: Hapus SEMUA hak akses lama untuk menu ini, lalu masukkan ulang yang baru dari form (Analogi: Merobek kertas izin lama, lalu membuat daftar izin baru dari nol).
                $this->db->query("DELETE FROM role_menu_access WHERE menu_id = ?", [$id]);
                if (!empty($_POST['roles'])) {
                    $roles = is_array($_POST['roles']) ? $_POST['roles'] : explode(',', $_POST['roles']);
                    foreach ($roles as $role_id) {
                        if (empty($role_id)) continue;
                        $sql = "INSERT INTO role_menu_access (role_id, menu_id) VALUES (?, ?)";
                        $this->db->query($sql, [(int)$role_id, $id]);
                    }
                }
                
                echo json_encode([
                    'status' => true,
                    'message' => 'Data berhasil diperbarui'
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => 'Data gagal diperbarui'
                ]);
            }
            exit;
        }
    }
    
    // Fungsi hapus (soft delete) sama seperti di modul User.
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $delete = $this->db->table('system_menus')->where('id', $id)->delete();
            
            header('Content-Type: application/json');
            if ($delete) {
                echo json_encode([
                    'status' => true,
                    'message' => 'Data berhasil dihapus (soft delete)'
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => 'Data gagal dihapus'
                ]);
            }
            exit;
        }
    }

    // Fungsi restore data terhapus.
    public function restore($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restore = $this->db->table('system_menus')->where('id', $id)->restore();
            
            header('Content-Type: application/json');
            if ($restore) {
                echo json_encode([
                    'status' => true,
                    'message' => 'Data berhasil dipulihkan'
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => 'Data gagal dipulihkan'
                ]);
            }
            exit;
        }
    }
}
