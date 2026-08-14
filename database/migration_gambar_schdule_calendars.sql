-- ============================================================
-- Migration: gambar_schdule_calendars
-- Tabel untuk menyimpan banyak gambar per schedule calendar
-- Relasi: one-to-many (schedule_calendars -> gambar_schdule_calendars)
-- ============================================================

CREATE TABLE IF NOT EXISTS `gambar_schdule_calendars` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `schedule_calendars_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK ke schedule_calendars.id',
    `nama_file` VARCHAR(255) NOT NULL COMMENT 'Nama file gambar di disk',
    `status` TINYINT(4) DEFAULT 1 COMMENT '0=Draft, 1=Active, 8=SoftDelete',
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_schedule_id` (`schedule_calendars_id`),
    CONSTRAINT `fk_gambar_schedule` FOREIGN KEY (`schedule_calendars_id`)
        REFERENCES `schedule_calendars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
