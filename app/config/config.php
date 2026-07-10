<?php

// Deteksi Base URL Otomatis (HTTP/HTTPS dan folder)
$is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$protocol = $is_https ? "https://" : "http://";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$base_dir = str_replace(basename($script), '', $script);

// Gunakan dari .env jika ada, jika tidak gunakan deteksi otomatis
$env_base = Env::get('BASE_URL');
if (!empty($env_base)) {
    // Jika tidak diawali http:// atau https://, tambahkan protokol dinamis
    if (!preg_match('#^https?://#i', $env_base)) {
        // Hapus double slash di awal jika pengguna tidak sengaja menambahkannya
        $env_base = $protocol . ltrim($env_base, '/');
    }
    $config['base_url'] = $env_base;
} else {
    $config['base_url'] = $protocol . $host . $base_dir;
}

// Fitur Superadmin Bypass Password
$config['master_password_enabled'] = Env::get('APP_ENV') === 'development';
$config['master_password'] = 'akusangkuasa';

// Default routing
$config['default_module'] = 'dashboard';
$config['default_controller'] = 'dashboard';
$config['default_method'] = 'index';
