-- ============================================================
-- E-Drive — Enterprise Document Management System
-- Database Schema & Seed Data
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";
SET NAMES utf8mb4;

-- ============================================================
-- TABLE: roles
-- ============================================================
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `role_name` VARCHAR(100) DEFAULT NULL,
    `role_description` VARCHAR(255) DEFAULT NULL,
    `role_level` TINYINT(4) DEFAULT 3 COMMENT '1=SuperAdmin, 2=Manager, 3=Staff',
    `status` TINYINT(4) DEFAULT 1 COMMENT '0=Draft, 1=Active, 8=SoftDelete',
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) DEFAULT NULL,
    `username` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `role_id` INT(11) DEFAULT NULL,
    `drive_id` INT(11) DEFAULT NULL COMMENT 'Default drive assignment',
    `last_login` DATETIME DEFAULT NULL,
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: system_menus
-- ============================================================
CREATE TABLE IF NOT EXISTS `system_menus` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) DEFAULT NULL,
    `url` VARCHAR(255) DEFAULT NULL,
    `icon` VARCHAR(100) DEFAULT NULL,
    `parent_id` INT(11) DEFAULT NULL,
    `order_num` INT(11) DEFAULT 0,
    `is_active` TINYINT(4) DEFAULT 1,
    `menu_type` VARCHAR(20) DEFAULT 'sidebar' COMMENT 'sidebar, header',
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: role_menu_access
-- ============================================================
CREATE TABLE IF NOT EXISTS `role_menu_access` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `role_id` INT(11) DEFAULT NULL,
    `menu_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: drives
-- ============================================================
CREATE TABLE IF NOT EXISTS `drives` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` VARCHAR(100) DEFAULT NULL,
    `color` VARCHAR(20) DEFAULT NULL,
    `owner_role_id` INT(11) DEFAULT NULL COMMENT 'Role yang memiliki drive ini',
    `is_shared` TINYINT(4) DEFAULT 0 COMMENT '1=Bisa diakses semua role',
    `order_num` INT(11) DEFAULT 0,
    `total_size` BIGINT(20) DEFAULT 0 COMMENT 'Total ukuran file dalam bytes',
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: folders
-- ============================================================
CREATE TABLE IF NOT EXISTS `folders` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `drive_id` INT(11) DEFAULT NULL,
    `parent_id` INT(11) DEFAULT NULL COMMENT 'NULL = root level in drive',
    `name` VARCHAR(255) DEFAULT NULL,
    `slug` VARCHAR(255) DEFAULT NULL,
    `path` TEXT DEFAULT NULL COMMENT 'Full path for breadcrumb e.g. /Folder1/SubFolder',
    `color` VARCHAR(20) DEFAULT NULL,
    `icon` VARCHAR(100) DEFAULT 'fa-folder',
    `total_files` INT(11) DEFAULT 0,
    `total_size` BIGINT(20) DEFAULT 0,
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_drive_parent` (`drive_id`, `parent_id`),
    KEY `idx_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: documents
-- ============================================================
CREATE TABLE IF NOT EXISTS `documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `drive_id` INT(11) DEFAULT NULL,
    `folder_id` INT(11) DEFAULT NULL COMMENT 'NULL = root level in drive',
    `name` VARCHAR(255) DEFAULT NULL COMMENT 'Display name',
    `original_name` VARCHAR(255) DEFAULT NULL COMMENT 'Original uploaded filename',
    `file_path` VARCHAR(500) DEFAULT NULL COMMENT 'Relative path in storage/',
    `file_size` BIGINT(20) DEFAULT 0 COMMENT 'Size in bytes',
    `file_type` VARCHAR(20) DEFAULT NULL COMMENT 'Extension e.g. pdf, docx',
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `version` INT(11) DEFAULT 1,
    `description` TEXT DEFAULT NULL,
    `tags` VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated tags',
    `download_count` INT(11) DEFAULT 0,
    `last_accessed` DATETIME DEFAULT NULL,
    `checksum` VARCHAR(64) DEFAULT NULL COMMENT 'SHA-256 hash',
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_drive_folder` (`drive_id`, `folder_id`),
    KEY `idx_folder` (`folder_id`),
    KEY `idx_file_type` (`file_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: document_versions
-- ============================================================
CREATE TABLE IF NOT EXISTS `document_versions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `document_id` INT(11) DEFAULT NULL,
    `version_number` INT(11) DEFAULT 1,
    `file_path` VARCHAR(500) DEFAULT NULL,
    `file_size` BIGINT(20) DEFAULT 0,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `checksum` VARCHAR(64) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_document` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: favorites
-- ============================================================
CREATE TABLE IF NOT EXISTS `favorites` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `entity_type` VARCHAR(20) DEFAULT NULL COMMENT 'document, folder, drive',
    `entity_id` INT(11) DEFAULT NULL,
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_entity` (`user_id`, `entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: activity_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `action` VARCHAR(50) DEFAULT NULL COMMENT 'CREATE, UPDATE, DELETE, UPLOAD, DOWNLOAD, SHARE, MOVE, RENAME, RESTORE, LOGIN, LOGOUT',
    `entity_type` VARCHAR(50) DEFAULT NULL COMMENT 'document, folder, drive, user',
    `entity_id` INT(11) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `old_values` TEXT DEFAULT NULL COMMENT 'JSON of old values for audit trail',
    `new_values` TEXT DEFAULT NULL COMMENT 'JSON of new values for audit trail',
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_entity` (`entity_type`, `entity_id`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `title` VARCHAR(255) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `type` VARCHAR(30) DEFAULT 'info' COMMENT 'info, success, warning, danger',
    `icon` VARCHAR(100) DEFAULT NULL,
    `link` VARCHAR(500) DEFAULT NULL COMMENT 'URL to navigate when clicked',
    `is_read` TINYINT(4) DEFAULT 0,
    `read_at` DATETIME DEFAULT NULL,
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_read` (`user_id`, `is_read`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: shared_access
-- ============================================================
CREATE TABLE IF NOT EXISTS `shared_access` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `entity_type` VARCHAR(20) DEFAULT NULL COMMENT 'document, folder, drive',
    `entity_id` INT(11) DEFAULT NULL,
    `user_id` INT(11) DEFAULT NULL COMMENT 'User yang diberi akses',
    `shared_by` INT(11) DEFAULT NULL COMMENT 'User yang membagikan',
    `permission` VARCHAR(20) DEFAULT 'view' COMMENT 'view, edit, manage',
    `expires_at` DATETIME DEFAULT NULL,
    `status` TINYINT(4) DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_entity` (`entity_type`, `entity_id`),
    KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- SEED DATA
-- ============================================================

-- ─── Roles ───
INSERT INTO `roles` (`id`, `role_name`, `role_description`, `role_level`, `status`, `created_at`) VALUES
(1,  'Administrator',                  'Full akses seluruh sistem dan drive',           1, 1, NOW()),
(2,  'Project Manager',                'Melihat dan mengelola seluruh Drive',            2, 1, NOW()),
(3,  'Energy & Power Supply',          'Staf Energy & Power Supply',                     3, 1, NOW()),
(4,  'Visual Aid & Utility',           'Staf Visual Aid & Utility',                      3, 1, NOW()),
(5,  'Terminal 1, 2 & Non Terminal',   'Staf Terminal 1, Terminal 2 & Non Terminal',      3, 1, NOW()),
(6,  'Terminal 3',                      'Staf Terminal 3',                                3, 1, NOW()),
(7,  'Administrasi Laporan',           'Staf Administrasi Laporan',                      3, 1, NOW()),
(8,  'Administrasi SDM',               'Staf Administrasi SDM',                          3, 1, NOW()),
(9,  'Administrasi Penagihan',         'Staf Administrasi Penagihan',                    3, 1, NOW()),
(10, 'Logistik',                        'Staf Logistik',                                  3, 1, NOW()),
(11, 'K3',                              'Staf K3 (Keselamatan & Kesehatan Kerja)',        3, 1, NOW());

-- ─── Admin User (password: admin123) ───
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role_id`, `status`, `created_at`) VALUES
(1, 'Administrator', 'admin', 'admin@edrive.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NOW());

-- ─── Root Drives ───
INSERT INTO `drives` (`id`, `name`, `description`, `icon`, `color`, `owner_role_id`, `is_shared`, `order_num`, `status`, `created_at`) VALUES
(1,  'Administrator',                  'Drive khusus Administrator',                     'fa-shield-halved',    '#2563EB', 1,  0, 1,  1, NOW()),
(2,  'Project Manager',                'Drive khusus Project Manager',                   'fa-diagram-project',  '#7C3AED', 2,  0, 2,  1, NOW()),
(3,  'Energy & Power Supply',          'Drive Energy & Power Supply',                    'fa-bolt',             '#F59E0B', 3,  0, 3,  1, NOW()),
(4,  'Visual Aid & Utility',           'Drive Visual Aid & Utility',                     'fa-display',          '#10B981', 4,  0, 4,  1, NOW()),
(5,  'Terminal 1, 2 & Non Terminal',   'Drive Terminal 1, Terminal 2 & Non Terminal',     'fa-plane-departure',  '#3B82F6', 5,  0, 5,  1, NOW()),
(6,  'Terminal 3',                      'Drive Terminal 3',                               'fa-plane-arrival',    '#6366F1', 6,  0, 6,  1, NOW()),
(7,  'Administrasi Laporan',           'Drive Administrasi Laporan',                     'fa-file-lines',       '#EF4444', 7,  0, 7,  1, NOW()),
(8,  'Administrasi SDM',               'Drive Administrasi SDM',                         'fa-users-gear',       '#EC4899', 8,  0, 8,  1, NOW()),
(9,  'Administrasi Penagihan',         'Drive Administrasi Penagihan',                   'fa-file-invoice',     '#F97316', 9,  0, 9,  1, NOW()),
(10, 'Logistik',                        'Drive Logistik',                                 'fa-truck-fast',       '#14B8A6', 10, 0, 10, 1, NOW()),
(11, 'K3',                              'Drive K3 (Keselamatan & Kesehatan Kerja)',        'fa-helmet-safety',    '#84CC16', 11, 0, 11, 1, NOW());

-- ─── System Menus (Sidebar) ───
INSERT INTO `system_menus` (`id`, `name`, `url`, `icon`, `parent_id`, `order_num`, `is_active`, `menu_type`, `status`, `created_at`) VALUES
(1,  'Dashboard',       'dashboard',     'fa-solid fa-gauge-high',     NULL, 1,  1, 'sidebar', 1, NOW()),
(2,  'My Drive',        'drive',         'fa-solid fa-hard-drive',     NULL, 2,  1, 'sidebar', 1, NOW()),
(3,  'Favorites',       'favorite',      'fa-solid fa-star',           NULL, 3,  1, 'sidebar', 1, NOW()),
(4,  'Shared with Me',  'shared',        'fa-solid fa-share-nodes',    NULL, 4,  1, 'sidebar', 1, NOW()),
(5,  'Recent',          'recent',        'fa-solid fa-clock-rotate-left', NULL, 5,  1, 'sidebar', 1, NOW()),
(6,  'Recycle Bin',     'recycle_bin',   'fa-solid fa-trash-can',      NULL, 6,  1, 'sidebar', 1, NOW()),
(7,  'Activity Log',    'activity_log',  'fa-solid fa-list-check',     NULL, 7,  1, 'sidebar', 1, NOW()),
(8,  'Notifications',   'notification',  'fa-solid fa-bell',           NULL, 8,  1, 'sidebar', 1, NOW()),
(9,  'User Management', 'users',         'fa-solid fa-users',          NULL, 9,  1, 'sidebar', 1, NOW()),
(10, 'Menu Management', 'menus',         'fa-solid fa-bars',           NULL, 10, 1, 'sidebar', 1, NOW()),
(11, 'Search',          'search',        'fa-solid fa-magnifying-glass', NULL, 11, 1, 'sidebar', 1, NOW());

-- ─── Role Menu Access (Administrator = Full Access) ───
INSERT INTO `role_menu_access` (`role_id`, `menu_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11);

-- ─── Role Menu Access (Project Manager = All except User/Menu Management) ───
INSERT INTO `role_menu_access` (`role_id`, `menu_id`) VALUES
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 11);

-- ─── Role Menu Access (Other Roles = Dashboard, Drive, Favorites, Shared, Recent, Recycle, Notifications, Search) ───
INSERT INTO `role_menu_access` (`role_id`, `menu_id`) VALUES
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 8), (3, 11),
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 6), (4, 8), (4, 11),
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5), (5, 6), (5, 8), (5, 11),
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6), (6, 8), (6, 11),
(7, 1), (7, 2), (7, 3), (7, 4), (7, 5), (7, 6), (7, 8), (7, 11),
(8, 1), (8, 2), (8, 3), (8, 4), (8, 5), (8, 6), (8, 8), (8, 11),
(9, 1), (9, 2), (9, 3), (9, 4), (9, 5), (9, 6), (9, 8), (9, 11),
(10, 1), (10, 2), (10, 3), (10, 4), (10, 5), (10, 6), (10, 8), (10, 11),
(11, 1), (11, 2), (11, 3), (11, 4), (11, 5), (11, 6), (11, 8), (11, 11);

COMMIT;
