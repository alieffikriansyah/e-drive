<?php

class MOD extends Model {

    public function truncate_semua() {
        try {
            // Kita nonaktifkan sementara foreign key checks agar truncate tidak error
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

            $tables = [
                'cabang',
                'satuan',
                'kategori',
                'kategori_operasional',
                'produk',
                'harga_paten',
                'pengeluaran_operasional',
                'penjualan',
                'detail_penjualan'
            ];

            foreach ($tables as $table) {
                // Periksa apakah tabelnya ada sebelum di-truncate
                $check = $this->db->query("SHOW TABLES LIKE '$table'")->fetch();
                if ($check) {
                    $this->db->query("TRUNCATE TABLE `$table`");
                }
            }

            // Aktifkan kembali foreign key checks
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
