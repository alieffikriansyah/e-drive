<?php
require_once 'app/core/Env.php';
Env::load('.env');

$host = Env::get('DB_HOST') ?? 'localhost';
$db   = Env::get('DB_NAME') ?? 'pos_warung';
$user = Env::get('DB_USER') ?? 'root';
$pass = Env::get('DB_PASS') ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);

// Insert Log Aktivitas
$stmt = $pdo->query("SELECT id FROM system_menus WHERE url = 'log_record_users'");
if (!$stmt->fetch()) {
    $pdo->query("INSERT INTO system_menus (name, url, icon, order_num, parent_id, is_active, status) VALUES ('Log Aktivitas Users', 'log_record_users', 'fa-history', 3, 16, 1, 0)");
    $log_id = $pdo->lastInsertId();
    $pdo->query("INSERT INTO role_menu_access (role_id, menu_id) VALUES (1, $log_id)");
    echo "Log Aktivitas Users inserted.\n";
} else {
    echo "Log Aktivitas Users already exists.\n";
}

// Insert Hapus Semua Data
$stmt = $pdo->query("SELECT id FROM system_menus WHERE url = 'hapus_keseluruhan_data'");
if (!$stmt->fetch()) {
    $pdo->query("INSERT INTO system_menus (name, url, icon, order_num, parent_id, is_active, status) VALUES ('Hapus Semua Data', 'hapus_keseluruhan_data', 'fa-trash-alt', 4, 16, 1, 0)");
    $hapus_id = $pdo->lastInsertId();
    $pdo->query("INSERT INTO role_menu_access (role_id, menu_id) VALUES (1, $hapus_id)");
    echo "Hapus Semua Data inserted.\n";
} else {
    echo "Hapus Semua Data already exists.\n";
}

// Ensure log_record_users table exists
$stmt = $pdo->query("SHOW TABLES LIKE 'log_record_users'");
if (!$stmt->fetch()) {
    $pdo->query("CREATE TABLE `log_record_users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `id_user` int(11) NOT NULL,
      `action` varchar(50) NOT NULL,
      `keterangan` text NOT NULL,
      `ip_address` varchar(45) DEFAULT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    echo "Table log_record_users created.\n";
} else {
    echo "Table log_record_users already exists.\n";
}

echo "All done!\n";
