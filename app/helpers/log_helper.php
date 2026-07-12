<?php

if (!function_exists('record_log')) {
    /**
     * Mencatat log aktivitas user ke tabel log_record_users
     *
     * @param string $action     Nama aksi (misal: 'INSERT_PENJUALAN', 'UPDATE_PRODUK', dll)
     * @param string $keterangan Detail keterangan aksi (misal: 'User menambahkan penjualan baru dengan No. Nota TRX-123')
     * @return bool
     */
    function record_log($action, $keterangan) {
        $CI =& Controller::get_instance();
        
        $user_id = $_SESSION['user_id'] ?? 0;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        $sql = "INSERT INTO log_record_users (id_user, action, keterangan, ip_address, created_at)
                VALUES (?, ?, ?, ?, NOW())";
                
        return $CI->db->query($sql, [$user_id, strtoupper($action), $keterangan, $ip_address]);
    }
}
