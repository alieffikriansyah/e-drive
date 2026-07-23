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
    if (!preg_match('#^https?://#i', $env_base)) {
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

// Application
$config['app_name'] = 'E-Drive';
$config['app_version'] = '1.0.0';
$config['app_description'] = 'Enterprise Document Management System';

// File Upload Configuration
$config['max_file_size'] = 100 * 1024 * 1024; // 100MB
$config['allowed_file_types'] = [
    // Documents
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp',
    'txt', 'rtf', 'csv',
    // Images
    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico',
    // Archives
    'zip', 'rar', '7z', 'tar', 'gz',
    // Media
    'mp4', 'mp3', 'avi', 'mov', 'wmv', 'wav', 'flac',
    // Code
    'html', 'css', 'js', 'json', 'xml', 'php', 'sql', 'md',
    // Others
    'dwg', 'dxf', 'ai', 'psd', 'eps',
];

// Previewable MIME types
$config['previewable_types'] = [
    'application/pdf',
    'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp', 'image/bmp',
    'text/plain', 'text/html', 'text/css', 'text/csv',
    'application/json', 'application/xml',
    'video/mp4', 'video/webm',
    'audio/mpeg', 'audio/wav', 'audio/ogg',
    // Office (via online preview or LibreOffice)
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
];
