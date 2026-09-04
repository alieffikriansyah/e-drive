-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2026 at 12:30 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_drive`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CREATE, UPDATE, DELETE, UPLOAD, DOWNLOAD, SHARE, MOVE, RENAME, RESTORE, LOGIN, LOGOUT',
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'document, folder, drive, user',
  `entity_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `old_values` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON of old values for audit trail',
  `new_values` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON of new values for audit trail',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `old_values`, `new_values`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 15:15:03', NULL, NULL, NULL, NULL),
(2, 1, 'DELETE', 'system_menus', NULL, 'Menghapus data di tabel system_menus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 15:21:39', NULL, NULL, NULL, NULL),
(3, 1, 'DELETE', 'system_menus', NULL, 'Menghapus data di tabel system_menus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 15:21:57', NULL, NULL, NULL, NULL),
(4, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:36:16', NULL, NULL, NULL, NULL),
(5, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:36:16', NULL, NULL, NULL, NULL),
(6, 1, 'UPLOAD', 'document', 1, 'Mengunggah file: Laporan Progres Dashboard.pdf', '::1', NULL, NULL, NULL, 1, '2026-07-22 18:36:16', NULL, NULL, NULL, NULL),
(7, 1, 'CREATE', 'users', NULL, 'Menambah data ke tabel users. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:47:40', NULL, NULL, NULL, NULL),
(8, 1, 'CREATE', 'users', NULL, 'Menambah data ke tabel users. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:48:03', NULL, NULL, NULL, NULL),
(9, 1, 'CREATE', 'users', NULL, 'Menambah data ke tabel users. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:48:23', NULL, NULL, NULL, NULL),
(10, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-22 18:48:48', NULL, NULL, NULL, NULL),
(11, 4, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:49:03', NULL, NULL, NULL, NULL),
(12, 4, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-22 18:49:28', NULL, NULL, NULL, NULL),
(13, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:49:35', NULL, NULL, NULL, NULL),
(14, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:58:02', NULL, NULL, NULL, NULL),
(15, 1, 'CREATE', 'folder', 1, 'Membuat folder baru: tes', '::1', NULL, NULL, NULL, 1, '2026-07-22 18:58:02', NULL, NULL, NULL, NULL),
(16, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:59:12', NULL, NULL, NULL, NULL),
(17, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 18:59:12', NULL, NULL, NULL, NULL),
(18, 1, 'UPLOAD', 'document', 2, 'Mengunggah file: Data Perkembangan Unit.xlsx', '::1', NULL, NULL, NULL, 1, '2026-07-22 18:59:12', NULL, NULL, NULL, NULL),
(19, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:04:14', NULL, NULL, NULL, NULL),
(20, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:04:14', NULL, NULL, NULL, NULL),
(21, 1, 'UPLOAD', 'document', 1, 'Mengunggah file: Cara_Penggunaan_Framework.txt', '::1', NULL, NULL, NULL, 1, '2026-07-22 19:04:14', NULL, NULL, NULL, NULL),
(22, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:07:39', NULL, NULL, NULL, NULL),
(23, 1, 'CREATE', 'folder', 1, 'Membuat folder baru: apasaj', '::1', NULL, NULL, NULL, 1, '2026-07-22 19:07:39', NULL, NULL, NULL, NULL),
(24, 1, 'DOWNLOAD', 'document', 1, 'Mengunduh file: Cara_Penggunaan_Framework.txt', '::1', NULL, NULL, NULL, 1, '2026-07-22 19:16:24', NULL, NULL, NULL, NULL),
(25, 1, 'DELETE', 'documents', NULL, 'Menghapus data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:16:28', NULL, NULL, NULL, NULL),
(26, 1, 'DELETE', 'document', 1, 'Memindahkan file ke Recycle Bin: Cara_Penggunaan_Framework', '::1', NULL, NULL, NULL, 1, '2026-07-22 19:16:28', NULL, NULL, NULL, NULL),
(27, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-22 19:43:42', NULL, NULL, NULL, NULL),
(28, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:43:51', NULL, NULL, NULL, NULL),
(29, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:45:22', NULL, NULL, NULL, NULL),
(30, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:45:22', NULL, NULL, NULL, NULL),
(31, 1, 'UPLOAD', 'document', 2, 'Mengunggah file: MR-(google drive, Hosting, dan Domain).xlsx', '::1', NULL, NULL, NULL, 1, '2026-07-22 19:45:22', NULL, NULL, NULL, NULL),
(32, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 19:45:32', NULL, NULL, NULL, NULL),
(33, 1, 'DELETE', 'document', 2, 'Memindahkan file ke Recycle Bin: MR-(google drive, Hosting, dan Domain)', '::1', NULL, NULL, NULL, 1, '2026-07-22 19:45:32', NULL, NULL, NULL, NULL),
(34, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 20:01:24', NULL, NULL, NULL, NULL),
(35, 1, 'RESTORE', 'document', 1, 'Memulihkan file: Cara_Penggunaan_Framework', '::1', NULL, NULL, NULL, 1, '2026-07-22 20:01:24', NULL, NULL, NULL, NULL),
(36, 1, 'DELETE_PERMANENT', 'document', 2, 'Menghapus permanen file: MR-(google drive, Hosting, dan Domain)', '::1', NULL, NULL, NULL, 1, '2026-07-22 20:01:30', NULL, NULL, NULL, NULL),
(37, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 20:03:38', NULL, NULL, NULL, NULL),
(38, 1, 'DELETE', 'document', 1, 'Memindahkan file ke Recycle Bin: Cara_Penggunaan_Framework', '::1', NULL, NULL, NULL, 1, '2026-07-22 20:03:38', NULL, NULL, NULL, NULL),
(39, 1, 'DELETE_PERMANENT', 'document', 1, 'Menghapus permanen file: Cara_Penggunaan_Framework', '::1', NULL, NULL, NULL, 1, '2026-07-22 20:03:43', NULL, NULL, NULL, NULL),
(40, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 20:04:59', NULL, NULL, NULL, NULL),
(41, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 20:04:59', NULL, NULL, NULL, NULL),
(42, 1, 'UPLOAD', 'document', 3, 'Mengunggah file: Screenshot (1).png', '::1', NULL, NULL, NULL, 1, '2026-07-22 20:04:59', NULL, NULL, NULL, NULL),
(43, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 20:05:23', NULL, NULL, NULL, NULL),
(44, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-22 20:05:23', NULL, NULL, NULL, NULL),
(45, 1, 'UPLOAD', 'document', 4, 'Mengunggah file: LAPORAN PEKERJAAN BANDARA SOEKARNO HATTA JIAC 3.docx', '::1', NULL, NULL, NULL, 1, '2026-07-22 20:05:23', NULL, NULL, NULL, NULL),
(46, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 00:12:25', NULL, NULL, NULL, NULL),
(47, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 00:57:30', NULL, NULL, NULL, NULL),
(48, 1, 'DOWNLOAD', 'document', 4, 'Mengunduh file: LAPORAN PEKERJAAN BANDARA SOEKARNO HATTA JIAC 3.docx', '::1', NULL, NULL, NULL, 1, '2026-07-23 01:03:44', NULL, NULL, NULL, NULL),
(49, 1, 'DOWNLOAD', 'document', 4, 'Mengunduh file: LAPORAN PEKERJAAN BANDARA SOEKARNO HATTA JIAC 3.docx', '::1', NULL, NULL, NULL, 1, '2026-07-23 01:17:15', NULL, NULL, NULL, NULL),
(50, 1, 'DOWNLOAD', 'document', 4, 'Mengunduh file: LAPORAN PEKERJAAN BANDARA SOEKARNO HATTA JIAC 3.docx', '::1', NULL, NULL, NULL, 1, '2026-07-23 01:19:04', NULL, NULL, NULL, NULL),
(51, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 01:23:15', NULL, NULL, NULL, NULL),
(52, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:12:12', NULL, NULL, NULL, NULL),
(53, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:15:21', NULL, NULL, NULL, NULL),
(54, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:15:21', NULL, NULL, NULL, NULL),
(55, 1, 'UPLOAD', 'document', 5, 'Mengunggah file: Data Perkembangan Unit.xlsx', '::1', NULL, NULL, NULL, 1, '2026-07-23 11:15:21', NULL, NULL, NULL, NULL),
(56, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:17:20', NULL, NULL, NULL, NULL),
(57, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:17:20', NULL, NULL, NULL, NULL),
(58, 1, 'UPLOAD', 'document', 6, 'Mengunggah file: Laporan Progres Dashboard.pdf', '::1', NULL, NULL, NULL, 1, '2026-07-23 11:17:20', NULL, NULL, NULL, NULL),
(59, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:17:26', NULL, NULL, NULL, NULL),
(60, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:17:26', NULL, NULL, NULL, NULL),
(61, 1, 'UPLOAD', 'document', 7, 'Mengunggah file: Laporan Progres Dashboard.pdf', '::1', NULL, NULL, NULL, 1, '2026-07-23 11:17:26', NULL, NULL, NULL, NULL),
(62, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:25:09', NULL, NULL, NULL, NULL),
(63, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:25:09', NULL, NULL, NULL, NULL),
(64, 1, 'UPLOAD', 'document', 8, 'Mengunggah file: Laporan Progres Dashboard.pdf', '::1', NULL, NULL, NULL, 1, '2026-07-23 11:25:09', NULL, NULL, NULL, NULL),
(65, 1, 'DELETE', 'system_menus', NULL, 'Menghapus data di tabel system_menus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:29:49', NULL, NULL, NULL, NULL),
(66, 1, 'DELETE', 'system_menus', NULL, 'Menghapus data di tabel system_menus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:29:57', NULL, NULL, NULL, NULL),
(67, 1, 'UPDATE', 'system_menus', NULL, 'Mengubah data di tabel system_menus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:30:36', NULL, NULL, NULL, NULL),
(68, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 11:33:15', NULL, NULL, NULL, NULL),
(69, 3, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 11:33:32', NULL, NULL, NULL, NULL),
(70, 3, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 12:07:45', NULL, NULL, NULL, NULL),
(71, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:09:07', NULL, NULL, NULL, NULL),
(72, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:10:05', NULL, NULL, NULL, NULL),
(73, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:10:06', NULL, NULL, NULL, NULL),
(74, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:10:12', NULL, NULL, NULL, NULL),
(75, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:14:28', NULL, NULL, NULL, NULL),
(76, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 12:19:05', NULL, NULL, NULL, NULL),
(77, 3, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:19:17', NULL, NULL, NULL, NULL),
(78, 3, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 12:29:46', NULL, NULL, NULL, NULL),
(79, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:29:54', NULL, NULL, NULL, NULL),
(80, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 12:35:31', NULL, NULL, NULL, NULL),
(81, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 12:35:39', NULL, NULL, NULL, NULL),
(82, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 14:30:06', NULL, NULL, NULL, NULL),
(83, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 15:50:17', NULL, NULL, NULL, NULL),
(84, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 15:50:52', NULL, NULL, NULL, NULL),
(85, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-23 17:18:38', NULL, NULL, NULL, NULL),
(86, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-23 17:18:44', NULL, NULL, NULL, NULL),
(87, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-24 09:16:49', NULL, NULL, NULL, NULL),
(88, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-24 10:12:13', NULL, NULL, NULL, NULL),
(89, 4, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-24 10:13:07', NULL, NULL, NULL, NULL),
(90, 4, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-24 10:13:16', NULL, NULL, NULL, NULL),
(91, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-24 10:13:22', NULL, NULL, NULL, NULL),
(92, 1, 'UPDATE', 'system_menus', NULL, 'Mengubah data di tabel system_menus', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-24 10:13:33', NULL, NULL, NULL, NULL),
(93, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-24 10:13:35', NULL, NULL, NULL, NULL),
(94, 4, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-24 10:13:44', NULL, NULL, NULL, NULL),
(95, 4, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-24 10:14:23', NULL, NULL, NULL, NULL),
(96, 4, 'UPDATE', 'schedule_calendars', NULL, 'Mengubah data di tabel schedule_calendars', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1', NULL, NULL, 1, '2026-07-24 10:38:37', NULL, NULL, NULL, NULL),
(97, 4, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-24 14:49:29', NULL, NULL, NULL, NULL),
(98, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-29 09:59:33', NULL, NULL, NULL, NULL),
(99, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-29 11:13:19', NULL, NULL, NULL, NULL),
(100, 1, 'UPDATE', 'document', 8, 'Mengubah nama file dari \'Laporan Progres Dashboard_1\' menjadi \'Laporan Progres Dashboard_10\'', '::1', NULL, NULL, NULL, 1, '2026-07-29 11:13:19', NULL, NULL, NULL, NULL),
(101, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 08:16:15', NULL, NULL, NULL, NULL),
(102, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 08:34:33', NULL, NULL, NULL, NULL),
(103, 1, 'CREATE', 'folder', 2, 'Membuat folder baru: gdgdgd', '::1', NULL, NULL, NULL, 1, '2026-07-31 08:34:33', NULL, NULL, NULL, NULL),
(104, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 08:34:44', NULL, NULL, NULL, NULL),
(105, 1, 'UPDATE', 'folder', 2, 'Mengubah nama folder dari \'gdgdgd\' menjadi \'123\'', '::1', NULL, NULL, NULL, 1, '2026-07-31 08:34:44', NULL, NULL, NULL, NULL),
(106, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 08:35:07', NULL, NULL, NULL, NULL),
(107, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 08:35:07', NULL, NULL, NULL, NULL),
(108, 1, 'UPLOAD', 'document', 9, 'Mengunggah file: db_didgitalreport.txt', '::1', NULL, NULL, NULL, 1, '2026-07-31 08:35:07', NULL, NULL, NULL, NULL),
(109, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 08:35:33', NULL, NULL, NULL, NULL),
(110, 1, 'DELETE', 'folder', 2, 'Memindahkan folder ke Recycle Bin: 123', '::1', NULL, NULL, NULL, 1, '2026-07-31 08:35:33', NULL, NULL, NULL, NULL),
(111, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:08:33', NULL, NULL, NULL, NULL),
(112, 1, 'CREATE', 'folder', 3, 'Membuat folder baru: r', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:08:33', NULL, NULL, NULL, NULL),
(113, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:08:57', NULL, NULL, NULL, NULL),
(114, 1, 'UPDATE', 'folder', 3, 'Mengubah nama folder dari \'r\' menjadi \'r1\'', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:08:57', NULL, NULL, NULL, NULL),
(115, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:12:34', NULL, NULL, NULL, NULL),
(116, 1, 'RESTORE', 'folder', 2, 'Memulihkan folder: 123', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:12:34', NULL, NULL, NULL, NULL),
(117, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:12:54', NULL, NULL, NULL, NULL),
(118, 1, 'DELETE', 'folder', 2, 'Memindahkan folder ke Recycle Bin: 123', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:12:54', NULL, NULL, NULL, NULL),
(119, 1, 'DELETE_PERMANENT', 'folder', 2, 'Menghapus permanen folder: 123', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:13:00', NULL, NULL, NULL, NULL),
(120, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:17:24', NULL, NULL, NULL, NULL),
(121, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:17:24', NULL, NULL, NULL, NULL),
(122, 1, 'UPLOAD', 'document', 10, 'Mengunggah file: WIN_20251028_09_47_22_Pro.mp4', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:17:24', NULL, NULL, NULL, NULL),
(123, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:17:49', NULL, NULL, NULL, NULL),
(124, 1, 'CREATE', 'folder', 4, 'Membuat folder baru: tes', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:17:49', NULL, NULL, NULL, NULL),
(125, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:18:27', NULL, NULL, NULL, NULL),
(126, 1, 'UPDATE', 'folder', 4, 'Mengubah nama folder dari \'tes\' menjadi \'tes1\'', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:18:27', NULL, NULL, NULL, NULL),
(127, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 09:18:34', NULL, NULL, NULL, NULL),
(128, 1, 'DELETE', 'folder', 4, 'Memindahkan folder ke Recycle Bin: tes1', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:18:34', NULL, NULL, NULL, NULL),
(129, 1, 'DELETE_PERMANENT', 'folder', 4, 'Menghapus permanen folder: tes1', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:18:43', NULL, NULL, NULL, NULL),
(130, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-31 09:59:59', NULL, NULL, NULL, NULL),
(131, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:14:31', NULL, NULL, NULL, NULL),
(132, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:14:44', NULL, NULL, NULL, NULL),
(133, 1, 'DELETE', 'document', 8, 'Memindahkan file ke Recycle Bin: Laporan Progres Dashboard_10', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:14:44', NULL, NULL, NULL, NULL),
(134, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:14:47', NULL, NULL, NULL, NULL),
(135, 1, 'DELETE', 'document', 6, 'Memindahkan file ke Recycle Bin: Laporan Progres Dashboard', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:14:47', NULL, NULL, NULL, NULL),
(136, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:14:50', NULL, NULL, NULL, NULL),
(137, 1, 'DELETE', 'document', 5, 'Memindahkan file ke Recycle Bin: Data Perkembangan Unit', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:14:50', NULL, NULL, NULL, NULL),
(138, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:14:55', NULL, NULL, NULL, NULL),
(139, 1, 'DELETE', 'document', 4, 'Memindahkan file ke Recycle Bin: LAPORAN PEKERJAAN BANDARA SOEKARNO HATTA JIAC 3', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:14:55', NULL, NULL, NULL, NULL),
(140, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:14:58', NULL, NULL, NULL, NULL),
(141, 1, 'DELETE', 'document', 3, 'Memindahkan file ke Recycle Bin: Screenshot (1)', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:14:58', NULL, NULL, NULL, NULL),
(142, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:15:03', NULL, NULL, NULL, NULL),
(143, 1, 'RESTORE', 'document', 5, 'Memulihkan file: Data Perkembangan Unit', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:03', NULL, NULL, NULL, NULL),
(144, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:15:12', NULL, NULL, NULL, NULL),
(145, 1, 'DELETE', 'folder', 3, 'Memindahkan folder ke Recycle Bin: r1', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:12', NULL, NULL, NULL, NULL),
(146, 1, 'UPDATE', 'folders', NULL, 'Mengubah data di tabel folders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:15:29', NULL, NULL, NULL, NULL),
(147, 1, 'UPDATE', 'folder', 1, 'Mengubah nama folder dari \'apasaj\' menjadi \'Data 1\'', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:29', NULL, NULL, NULL, NULL),
(148, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:15:37', NULL, NULL, NULL, NULL),
(149, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:15:37', NULL, NULL, NULL, NULL),
(150, 1, 'UPLOAD', 'document', 11, 'Mengunggah file: db_didgitalreport.txt', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:37', NULL, NULL, NULL, NULL),
(151, 1, 'DELETE_PERMANENT', 'folder', 3, 'Menghapus permanen folder: r1', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:43', NULL, NULL, NULL, NULL),
(152, 1, 'DELETE_PERMANENT', 'document', 3, 'Menghapus permanen file: Screenshot (1)', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:45', NULL, NULL, NULL, NULL),
(153, 1, 'DELETE_PERMANENT', 'document', 4, 'Menghapus permanen file: LAPORAN PEKERJAAN BANDARA SOEKARNO HATTA JIAC 3', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:47', NULL, NULL, NULL, NULL),
(154, 1, 'DELETE_PERMANENT', 'document', 6, 'Menghapus permanen file: Laporan Progres Dashboard', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:49', NULL, NULL, NULL, NULL),
(155, 1, 'DELETE_PERMANENT', 'document', 8, 'Menghapus permanen file: Laporan Progres Dashboard_10', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:15:51', NULL, NULL, NULL, NULL),
(156, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:16:01', NULL, NULL, NULL, NULL),
(157, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:16:01', NULL, NULL, NULL, NULL),
(158, 1, 'UPLOAD', 'document', 12, 'Mengunggah file: db_didgitalreport.txt', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:16:01', NULL, NULL, NULL, NULL),
(159, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-07-31 10:16:21', NULL, NULL, NULL, NULL),
(160, 1, 'DELETE', 'document', 11, 'Memindahkan file ke Recycle Bin: db_didgitalreport', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:16:21', NULL, NULL, NULL, NULL),
(161, 1, 'DELETE_PERMANENT', 'document', 11, 'Menghapus permanen file: db_didgitalreport', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:16:27', NULL, NULL, NULL, NULL),
(162, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-07-31 10:20:33', NULL, NULL, NULL, NULL),
(163, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-03 11:20:58', NULL, NULL, NULL, NULL),
(164, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-03 11:47:28', NULL, NULL, NULL, NULL),
(165, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-03 11:48:38', NULL, NULL, NULL, NULL),
(166, 4, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-03 11:49:37', NULL, NULL, NULL, NULL),
(167, 4, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-03 11:50:59', NULL, NULL, NULL, NULL),
(168, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-03 12:58:52', NULL, NULL, NULL, NULL),
(169, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-03 14:38:40', NULL, NULL, NULL, NULL),
(170, 4, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-03 14:38:52', NULL, NULL, NULL, NULL),
(171, 4, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-03 14:43:07', NULL, NULL, NULL, NULL),
(172, 4, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-03 14:44:15', NULL, NULL, NULL, NULL),
(173, 4, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-03 15:01:16', NULL, NULL, NULL, NULL),
(174, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:02:59', NULL, NULL, NULL, NULL),
(175, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:22:22', NULL, NULL, NULL, NULL),
(176, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:22:22', NULL, NULL, NULL, NULL),
(177, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:22:22', NULL, NULL, NULL, NULL),
(178, 1, 'EXTRACT_UPLOAD', 'folder', 2, 'Mengunggah dan mengekstrak arsip ijzah.zip menjadi folder ijzah (1 file)', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:22:22', NULL, NULL, NULL, NULL),
(179, 1, 'DELETE', 'folder', 2, 'Memindahkan folder ke Recycle Bin: ijzah', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:27:09', NULL, NULL, NULL, NULL),
(180, 1, 'DELETE_PERMANENT', 'folder', 2, 'Menghapus permanen folder: ijzah', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:27:18', NULL, NULL, NULL, NULL),
(181, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:31:57', NULL, NULL, NULL, NULL),
(182, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:31:57', NULL, NULL, NULL, NULL),
(183, 1, 'UPLOAD', 'document', 14, 'Mengunggah file: ijzah.zip', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:31:57', NULL, NULL, NULL, NULL),
(184, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:32:10', NULL, NULL, NULL, NULL),
(185, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:32:10', NULL, NULL, NULL, NULL),
(186, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:32:10', NULL, NULL, NULL, NULL),
(187, 1, 'EXTRACT', 'folder', 3, 'Mengekstrak arsip ijzah.zip menjadi folder ijzah (1 file)', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:32:10', NULL, NULL, NULL, NULL),
(188, 1, 'DELETE', 'folder', 3, 'Memindahkan folder ke Recycle Bin: ijzah', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:32:37', NULL, NULL, NULL, NULL),
(189, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:32:41', NULL, NULL, NULL, NULL),
(190, 1, 'DELETE', 'document', 14, 'Memindahkan file ke Recycle Bin: ijzah', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:32:41', NULL, NULL, NULL, NULL),
(191, 1, 'DELETE_PERMANENT', 'folder', 3, 'Menghapus permanen folder: ijzah', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:32:49', NULL, NULL, NULL, NULL),
(192, 1, 'DELETE_PERMANENT', 'document', 14, 'Menghapus permanen file: ijzah', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:32:52', NULL, NULL, NULL, NULL),
(193, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:36:54', NULL, NULL, NULL, NULL),
(194, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:36:54', NULL, NULL, NULL, NULL),
(195, 1, 'UPLOAD', 'document', 1, 'Mengunggah file: test.zip', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:36:54', NULL, NULL, NULL, NULL),
(196, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(197, 1, 'CREATE', 'folders', NULL, 'Menambah data ke tabel folders. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(198, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(199, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(200, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(201, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(202, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(203, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(204, 1, 'EXTRACT', 'folder', 1, 'Mengekstrak arsip test.zip menjadi folder test (3 file)', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:37:01', NULL, NULL, NULL, NULL),
(205, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:51:11', NULL, NULL, NULL, NULL),
(206, 1, 'MOVE', 'document', 2, 'Memindahkan file \'1784648560239-dc372108-ba1e-48a3-be5d-18f558211b81_1\' ke \'test\'', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:51:11', NULL, NULL, NULL, NULL),
(207, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:51:17', NULL, NULL, NULL, NULL),
(208, 1, 'MOVE', 'document', 3, 'Memindahkan file \'FOTO FAHRI\' ke \'test\'', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:51:17', NULL, NULL, NULL, NULL),
(209, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:51:25', NULL, NULL, NULL, NULL),
(210, 1, 'MOVE', 'document', 4, 'Memindahkan file \'KAK_O&M_2026-2027[1]\' ke \'test\'', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:51:25', NULL, NULL, NULL, NULL),
(211, 1, 'DELETE', 'folder', 2, 'Memindahkan folder ke Recycle Bin: test', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:51:37', NULL, NULL, NULL, NULL),
(212, 1, 'DELETE_PERMANENT', 'folder', 2, 'Menghapus permanen folder: test', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:51:46', NULL, NULL, NULL, NULL),
(213, 1, 'UPDATE', 'documents', NULL, 'Mengubah data di tabel documents', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-04 11:52:06', NULL, NULL, NULL, NULL),
(214, 1, 'DELETE', 'document', 1, 'Memindahkan file ke Recycle Bin: test', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:52:06', NULL, NULL, NULL, NULL),
(215, 1, 'DELETE_PERMANENT', 'document', 1, 'Menghapus permanen file: test', '::1', NULL, NULL, NULL, 1, '2026-08-04 11:52:12', NULL, NULL, NULL, NULL),
(216, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-04 14:41:58', NULL, NULL, NULL, NULL),
(217, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 09:55:33', NULL, NULL, NULL, NULL),
(218, 1, 'CREATE', 'users', NULL, 'Menambah data ke tabel users. ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 09:56:07', NULL, NULL, NULL, NULL),
(219, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 10:38:22', NULL, NULL, NULL, NULL),
(220, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 10:39:14', NULL, NULL, NULL, NULL),
(221, 1, 'UPDATE', 'schedule_calendars', NULL, 'Mengubah data di tabel schedule_calendars', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 10:42:57', NULL, NULL, NULL, NULL),
(222, 1, 'CREATE', 'gambar_schdule_calendars', NULL, 'Menambah data ke tabel gambar_schdule_calendars. ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 10:42:57', NULL, NULL, NULL, NULL),
(223, 1, 'CREATE', 'gambar_schdule_calendars', NULL, 'Menambah data ke tabel gambar_schdule_calendars. ID: 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 10:42:57', NULL, NULL, NULL, NULL),
(224, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-07 10:58:45', NULL, NULL, NULL, NULL),
(225, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-07 13:56:09', NULL, NULL, NULL, NULL),
(226, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-07 13:58:16', NULL, NULL, NULL, NULL),
(227, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-12 16:04:24', NULL, NULL, NULL, NULL),
(228, 1, 'CREATE', 'documents', NULL, 'Menambah data ke tabel documents. ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-12 16:05:49', NULL, NULL, NULL, NULL),
(229, 1, 'CREATE', 'document_versions', NULL, 'Menambah data ke tabel document_versions. ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-12 16:05:49', NULL, NULL, NULL, NULL),
(230, 1, 'UPLOAD', 'document', 5, 'Mengunggah file: Tes1.docx', '::1', NULL, NULL, NULL, 1, '2026-08-12 16:05:50', NULL, NULL, NULL, NULL),
(231, 1, 'DOWNLOAD', 'document', 5, 'Mengunduh file: Tes1.docx', '::1', NULL, NULL, NULL, 1, '2026-08-12 16:06:23', NULL, NULL, NULL, NULL),
(232, 1, 'DOWNLOAD', 'document', 5, 'Mengunduh file: Tes1.docx', '::1', NULL, NULL, NULL, 1, '2026-08-12 16:06:54', NULL, NULL, NULL, NULL),
(233, 1, 'LOGIN', 'auth', NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-14 10:49:12', NULL, NULL, NULL, NULL),
(234, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-14 11:35:45', NULL, NULL, NULL, NULL),
(235, 1, 'CREATE', 'gambar_schdule_calendars', NULL, 'Menambah data ke tabel gambar_schdule_calendars. ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-14 11:35:45', NULL, NULL, NULL, NULL);
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `old_values`, `new_values`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(236, 1, 'UPDATE', 'gambar_schdule_calendars', NULL, 'Mengubah data di tabel gambar_schdule_calendars', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-14 11:35:59', NULL, NULL, NULL, NULL),
(237, 1, 'UPDATE', 'schedule_calendars', NULL, 'Mengubah data di tabel schedule_calendars', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-14 11:35:59', NULL, NULL, NULL, NULL),
(238, 1, 'CREATE', 'schedule_calendars', NULL, 'Menambah data ke tabel schedule_calendars. ID: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', NULL, NULL, 1, '2026-08-14 11:36:31', NULL, NULL, NULL, NULL),
(239, 1, 'LOGOUT', 'auth', NULL, 'User logout', '::1', NULL, NULL, NULL, 1, '2026-08-14 11:37:06', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ai_conversations`
--

CREATE TABLE `ai_conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'New Chat',
  `assistant_type` enum('general','edrive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `model_used` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'qwen3',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=active, 8=deleted',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_conversations`
--

INSERT INTO `ai_conversations` (`id`, `user_id`, `title`, `assistant_type`, `model_used`, `is_pinned`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'buatkan format surat u8ntuk 17 agustus undangan', 'general', 'qwen3', 0, 8, '2026-08-03 13:09:24', '2026-08-03 13:41:03'),
(2, 1, 'tuorial membuat folder di aplikasi ini bagaimana', 'edrive', 'qwen3', 0, 8, '2026-08-03 13:13:32', '2026-08-03 14:13:43'),
(3, 1, 'buatkan format cv ats', 'general', 'qwen3', 0, 8, '2026-08-03 13:41:20', '2026-08-03 14:30:08'),
(4, 1, 'buatkan resep untuk memasak rawon', 'general', 'qwen3', 0, 8, '2026-08-03 14:13:09', '2026-08-03 14:30:06'),
(5, 1, 'bagaimana saya menjelakan aplikasi e-drive ini dan apa saja ...', 'edrive', 'qwen3', 0, 8, '2026-08-03 14:14:05', '2026-08-03 14:17:35'),
(6, 1, 'ada berapa role yang ada di aplikasi ini', 'edrive', 'qwen3', 0, 1, '2026-08-03 14:17:48', '2026-08-03 14:24:44'),
(7, 1, 'coba jelaskan kenapa terjadinya engine slubber pada genset', 'general', 'qwen3', 0, 8, '2026-08-03 14:26:25', '2026-08-03 14:30:03'),
(8, 1, 'coba jelaskan kenapa terjadinya engine Slobber pada genset', 'general', 'qwen3', 0, 1, '2026-08-03 14:30:25', '2026-08-03 14:31:35'),
(9, 1, 'jelaskan code ini , <?php\r\n\r\nclass Dashboard extends Control...', 'general', 'qwen3', 0, 1, '2026-08-03 14:32:29', '2026-08-03 14:35:42'),
(10, 4, 'buatkan menu masakan gudeg jogja', 'general', 'qwen3', 0, 1, '2026-08-03 14:42:05', '2026-08-03 14:42:07'),
(11, 4, 'drive mana yang paling banyak menyimpan data', 'edrive', 'qwen3', 0, 1, '2026-08-03 14:42:33', '2026-08-03 14:42:56'),
(12, 1, 'berapa banyak role drive di aplikasi ini n', 'edrive', 'qwen3', 0, 1, '2026-08-04 14:40:24', '2026-08-04 14:40:24'),
(13, 1, 'apa itu kabel to poor', 'general', 'qwen3', 0, 1, '2026-08-07 10:03:55', '2026-08-07 10:04:25'),
(14, 1, 'berapa banyak role drive di aplikasi ini', 'edrive', 'qwen3', 0, 1, '2026-08-07 10:06:09', '2026-08-07 10:06:50'),
(15, 1, 'buatkan format cv ats berikan file word atau .docx apakah bi...', 'general', 'qwen3', 0, 1, '2026-08-07 10:12:01', '2026-08-07 10:23:10'),
(16, 1, 'hello', 'general', 'qwen3', 0, 1, '2026-08-07 10:56:42', '2026-08-07 10:56:43'),
(17, 1, 'buatkan', 'general', 'qwen3', 0, 1, '2026-08-07 11:24:06', '2026-08-07 11:24:09'),
(18, 1, 'a', 'general', 'qwen3', 0, 1, '2026-08-07 11:26:57', '2026-08-07 11:26:58');

-- --------------------------------------------------------

--
-- Table structure for table `ai_documents`
--

CREATE TABLE `ai_documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint(20) NOT NULL DEFAULT '0',
  `chunk_count` int(11) NOT NULL DEFAULT '0',
  `embedding_status` enum('pending','processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `collection_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'edrive_knowledge',
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SOP, Tutorial, FAQ, etc.',
  `uploaded_by` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_embeddings`
--

CREATE TABLE `ai_embeddings` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `chunk_index` int(11) NOT NULL DEFAULT '0',
  `chunk_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vector_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID in vector DB',
  `token_count` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_feedback`
--

CREATE TABLE `ai_feedback` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL COMMENT '1=thumbs_down, 2=thumbs_up',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_messages`
--

CREATE TABLE `ai_messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `role` enum('user','assistant','system') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokens_in` int(11) NOT NULL DEFAULT '0',
  `tokens_out` int(11) NOT NULL DEFAULT '0',
  `model_used` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_time_ms` int(11) DEFAULT NULL,
  `metadata` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Tool calls, sources, citations',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_messages`
--

INSERT INTO `ai_messages` (`id`, `conversation_id`, `role`, `content`, `tokens_in`, `tokens_out`, `model_used`, `response_time_ms`, `metadata`, `status`, `created_at`) VALUES
(1, 1, 'user', 'buatkan format surat u8ntuk 17 agustus undangan', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 13:09:24'),
(2, 1, 'assistant', '⚠️ **[AI Assistant Service Offline]**\n\nMaaf, koneksi ke service AI (Flowise / Ollama) saat ini belum dikonfigurasi atau sedang offline.\n\n*Silakan atur koneksi di menu **AI Settings** atau hubungi Administrator.*', 0, 0, 'system-offline', 4252, '{\"offline\":true}', 1, '2026-08-03 13:09:28'),
(3, 1, 'user', 'buatkan surat undangan rapat', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 13:13:04'),
(4, 1, 'assistant', '### 📜 **FORMAT SURAT UNDANGAN PERINGATAN HUT RI KE-81 (17 AGUSTUS)**\n\n**PANITIA PERINGATAN HUT KEMERDEKAAN RI KE-81**  \n**RT 05 / RW 03 KELURAHAN SUKAMAJU**  \n____________________________________________________________________\n\n**Nomor:** 017/PAN-HUT-RI/VIII/2026  \n**Hal:** Undangan Rapat & Peringatan HUT RI ke-81  \n**Lampiran:** -  \n\n**Kepada Yth.**  \nBapak/Ibu/Saudara/i warga RT 05 / RW 03  \nDi Tempat\n\n*Assalamu\'alaikum Wr. Wb. / Salam Sejahtera,* \n\nDalam rangka memeriahkan Hari Ulang Tahun (HUT) Kemerdekaan Republik Indonesia yang ke-81, kami mengundang Bapak/Ibu/Saudara/i untuk hadir pada acara Malam Puncak Peringatan 17 Agustus yang akan dilaksanakan pada:\n\n- 🗓️ **Hari / Tanggal:** Sabtu, 17 Agustus 2026  \n- ⏰ **Waktu:** 19.30 WIB s.d. Selesai  \n- 📍 **Tempat:** Lapangan Serbaguna RT 05  \n- 👕 **Pakaian:** Nuansa Merah Putih  \n\n**Susunan Acara:**\n1. Pembukaan & Menyanyikan Lagu Indonesia Raya\n2. Sambutan Ketua Panitia & Ketua RT\n3. Penyerahan Hadiah Lomba 17 Agustus\n4. Pentas Seni Warga & Ramah Tamah\n\nDemikian surat undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu/Saudara/i, kami ucapkan terima kasih.\n\n*Wassalamu\'alaikum Wr. Wb.*\n\n**Hormat Kami,**  \n**Ketua Panitia HUT RI**\n\n*( Nama Ketua Panitia )*', 7, 315, 'edrive-smart-fallback', 1213, '{\"mode\":\"smart_fallback\"}', 1, '2026-08-03 13:13:05'),
(5, 2, 'user', 'tuorial membuat folder di aplikasi ini bagaimana', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 13:13:32'),
(6, 2, 'assistant', 'Terima kasih atas pertanyaan Anda mengenai **\"tuorial membuat folder di aplikasi ini bagaimana\"**.\n\nSaya dapat membantu Anda membuat dokumen, draft surat, ringkasan teks, maupun menganalisis data penyimpanan E-Drive.\n\n> 💡 *Catatan: Server Ollama/Flowise saat ini belum berjalan di localhost. Setelah layanan Flowise/Ollama Anda diaktifkan di menu **AI Settings**, jawaban akan dihasilkan secara real-time dari model LLM (Qwen3/Llama3).*', 12, 110, 'edrive-smart-fallback', 1210, '{\"mode\":\"smart_fallback\"}', 1, '2026-08-03 13:13:33'),
(7, 3, 'user', 'buatkan format cv ats', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 13:41:20'),
(8, 3, 'assistant', 'Terima kasih atas pertanyaan Anda mengenai **\"buatkan format cv ats\"**.\n\nSaya dapat membantu Anda membuat dokumen, draft surat, ringkasan teks, memberikan panduan E-Drive, maupun menganalisis data penyimpanan.\n\n> 💡 *Catatan: Server Ollama/Flowise saat ini belum berjalan di localhost. Setelah layanan Flowise/Ollama Anda diaktifkan di menu **AI Settings**, jawaban akan dihasilkan secara real-time dari model LLM (Qwen3/Llama3).*', 6, 108, 'edrive-smart-fallback', 1227, '{\"mode\":\"smart_fallback\"}', 1, '2026-08-03 13:41:21'),
(9, 3, 'user', 'berikan format cv dengan mode ats', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:02:16'),
(10, 3, 'assistant', 'Terima kasih atas pertanyaan Anda mengenai **\"berikan format cv dengan mode ats\"**.\n\nSaya dapat membantu Anda membuat dokumen, draft surat, ringkasan teks, memberikan panduan E-Drive, maupun menganalisis data penyimpanan.\n\n> 💡 *Catatan: Server Ollama/Flowise saat ini belum berjalan di localhost. Setelah layanan Flowise/Ollama Anda diaktifkan di menu **AI Settings**, jawaban akan dihasilkan secara real-time dari model LLM (Qwen3/Llama3).*', 9, 111, 'edrive-smart-fallback', 718, '{\"mode\":\"smart_fallback\"}', 1, '2026-08-03 14:02:17'),
(11, 3, 'user', 'tes', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:06:58'),
(12, 3, 'assistant', 'Terima kasih atas pertanyaan Anda mengenai **\"tes\"**.\n\nSaya dapat membantu Anda membuat dokumen, draft surat, ringkasan teks, memberikan panduan E-Drive, maupun menganalisis data penyimpanan.\n\n> 💡 *Catatan: Server Ollama/Flowise saat ini belum berjalan di localhost. Setelah layanan Flowise/Ollama Anda diaktifkan di menu **AI Settings**, jawaban akan dihasilkan secara real-time dari model LLM (Qwen3/Llama3).*', 1, 104, 'edrive-smart-fallback', 1312, '{\"mode\":\"smart_fallback\"}', 1, '2026-08-03 14:06:59'),
(13, 3, 'user', 'berikan format cv dengan mode ats', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:10:15'),
(14, 3, 'assistant', '⚠️ **[Cloud LLM API Error]**\n\nCloud LLM error (400): `max_tokens` must be positive\n\n*Silakan periksa API Key atau Model Name Anda di menu **AI Settings**.*', 0, 0, 'cloud-error', 752, '{\"error\":\"Cloud LLM error (400): `max_tokens` must be positive\"}', 1, '2026-08-03 14:10:16'),
(15, 4, 'user', 'buatkan resep untuk memasak rawon', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:13:09'),
(16, 4, 'assistant', '### Resep Rawon\n#### Bahan-bahan:\n- 500 gram daging sapi, potong kecil-kecil\n- 2 liter air\n- 2 sendok makan minyak goreng\n- 4 siung bawang putih, iris\n- 6 siung bawang merah, iris\n- 2 batang serai, memarkan\n- 2 lembar daun salam\n- 1 lembar daun jeruk\n- 1/2 sendok teh garam\n- 1/2 sendok teh gula pasir\n- 1/2 sendok teh merica bubuk\n- 2 sendok makan kecap manis (opsional)\n- Bumbu halus:\n  - 4 butir kemiri\n  - 2 sendok makan ketumbar\n  - 1 sendok makan jinten\n  - 1/2 sendok teh kunyit bubuk\n  - 1/2 sendok teh lada bubuk\n  - 2 buah kluwek, ambil isinya dan rendam dengan sedikit air panas\n- Bahan pelengkap:\n  - Tauge\n  - Bawang goreng\n  - Sambal\n  - Kerupuk\n\n#### Cara Memasak:\n1. **Membuat Bumbu Halus:**\n   - Siapkan bahan-bahan untuk bumbu halus.\n   - Blender atau ulek kemiri, ketumbar, jinten, kunyit bubuk, lada bubuk, dan isi kluwek yang telah direndam dengan air panas hingga halus.\n2. **Menumis Bumbu:**\n   - Panaskan minyak goreng dalam wajan besar.\n   - Tumis bawang putih dan bawang merah hingga harum.\n   - Masukkan serai, daun salam, dan daun jeruk, tumis sebentar.\n   - Masukkan bumbu halus, tumis hingga bumbu matang dan harum.\n3. **Merebus Daging:**\n   - Pindahkan bumbu yang telah ditumis ke dalam panci besar.\n   - Tambahkan air, garam, gula pasir, merica bubuk, dan kecap manis (jika menggunakan).\n   - Masukkan daging sapi, rebus dengan api sedang hingga daging empuk.\n4. **Menyajikan:**\n   - Sajikan rawon dalam mangkuk, tambahkan tauge, bawang goreng, sambal, dan kerupuk sesuai selera.\n   - Rawon siap disajikan dan dinikmati.\n\n#### Tips:\n- Pastikan untuk menggunakan daging sapi yang berkualitas agar rawon lebih lezat.\n- Jika ingin rawon lebih kental, bisa mengurangi jumlah air atau menambahkan sedikit tepung maizena yang dilarutkan dengan air.\n- Rawon bisa disajikan dengan nasi putih hangat atau sebagai pendamping untuk hidangan lain.', 96, 672, 'llama-3.3-70b-versatile', 3335, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:13:12'),
(17, 5, 'user', 'bagaimana saya menjelakan aplikasi e-drive ini dan apa saja fitur di dalam aplikasi ini', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:14:05'),
(18, 5, 'assistant', '### Pengenalan Aplikasi E-Drive\nAplikasi E-Drive adalah sebuah sistem manajemen dokumen elektronik yang dirancang untuk membantu organisasi dalam mengelola dan menyimpan dokumen secara efektif dan efisien. Dengan menggunakan E-Drive, pengguna dapat dengan mudah mengupload, mengunduh, dan mengakses dokumen dari mana saja dan kapan saja.\n\n### Fitur Utama Aplikasi E-Drive\nBerikut adalah beberapa fitur utama yang tersedia di dalam aplikasi E-Drive:\n\n1. **Upload dan Download Dokumen**: Pengguna dapat mengupload dokumen dari perangkat mereka ke aplikasi E-Drive dan mengunduh dokumen yang telah diupload sebelumnya.\n2. **Pengelolaan Folder**: Pengguna dapat membuat, mengedit, dan menghapus folder untuk mengorganisir dokumen mereka.\n3. **Pengelolaan Drive**: Pengguna dapat mengakses dan mengelola drive mereka, termasuk membuat, mengedit, dan menghapus file dan folder.\n4. **Pencarian Dokumen**: Pengguna dapat mencari dokumen berdasarkan nama, tanggal, dan kata kunci.\n5. **Izin Akses**: Pengguna dapat mengatur izin akses untuk dokumen dan folder, sehingga hanya pengguna yang berwenang yang dapat mengaksesnya.\n6. **Riwayat Aktivitas**: Pengguna dapat melihat riwayat aktivitas dokumen, termasuk tanggal dan waktu upload, download, dan perubahan.\n7. **Notifikasi**: Pengguna dapat menerima notifikasi ketika dokumen diupload, diunduh, atau diubah.\n8. **Integrasi dengan Aplikasi Lain**: E-Drive dapat diintegrasikan dengan aplikasi lain, seperti Microsoft Office dan Google Drive.\n\n### Manfaat Menggunakan Aplikasi E-Drive\nDengan menggunakan aplikasi E-Drive, organisasi dapat:\n\n* Mengurangi biaya penyimpanan dokumen fisik\n* Meningkatkan efisiensi dan produktivitas\n* Meningkatkan keamanan dan integritas dokumen\n* Meningkatkan kemampuan kolaborasi dan berbagi dokumen\n* Meningkatkan kemampuan pencarian dan pengelolaan dokumen\n\nDengan demikian, aplikasi E-Drive dapat membantu organisasi dalam mengelola dokumen secara efektif dan efisien, sehingga dapat meningkatkan produktivitas dan keamanan dokumen.', 166, 550, 'llama-3.3-70b-versatile', 2306, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:14:07'),
(19, 5, 'user', 'ada berapa role di aplikasi ini', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:14:49'),
(20, 5, 'assistant', '### Role di Aplikasi E-Drive\nDi aplikasi E-Drive, terdapat beberapa role yang dapat diberikan kepada pengguna, yaitu:\n\n1. **Admin**: Role admin memiliki akses penuh ke aplikasi E-Drive, termasuk mengelola pengguna, mengatur izin akses, dan mengkonfigurasi pengaturan aplikasi.\n2. **Manager**: Role manager memiliki akses ke dokumen dan folder yang terkait dengan departemen atau tim mereka, serta dapat mengelola izin akses untuk pengguna lain.\n3. **User**: Role user memiliki akses ke dokumen dan folder yang terkait dengan mereka, serta dapat mengupload, mengunduh, dan mengedit dokumen.\n4. **Guest**: Role guest memiliki akses terbatas ke dokumen dan folder yang telah diizinkan oleh admin atau manager, serta tidak dapat mengupload atau mengedit dokumen.\n5. **Viewer**: Role viewer memiliki akses ke dokumen dan folder yang telah diizinkan oleh admin atau manager, serta dapat melihat dokumen tetapi tidak dapat mengunduh atau mengedit.\n6. **Editor**: Role editor memiliki akses ke dokumen dan folder yang terkait dengan mereka, serta dapat mengedit dokumen tetapi tidak dapat menghapus atau mengupload dokumen baru.\n7. **Uploader**: Role uploader memiliki akses ke dokumen dan folder yang terkait dengan mereka, serta dapat mengupload dokumen tetapi tidak dapat mengedit atau menghapus dokumen.\n\n### Deskripsi Role\nBerikut adalah deskripsi lebih lanjut tentang setiap role:\n\n* **Admin**: Mengelola pengguna, mengatur izin akses, mengkonfigurasi pengaturan aplikasi, dan memiliki akses penuh ke aplikasi E-Drive.\n* **Manager**: Mengelola dokumen dan folder, mengatur izin akses untuk pengguna lain, dan memiliki akses ke dokumen dan folder yang terkait dengan departemen atau tim mereka.\n* **User**: Mengupload, mengunduh, dan mengedit dokumen, serta memiliki akses ke dokumen dan folder yang terkait dengan mereka.\n* **Guest**: Melihat dokumen yang telah diizinkan oleh admin atau manager, tetapi tidak dapat mengupload atau mengedit dokumen.\n* **Viewer**: Melihat dokumen yang telah diizinkan oleh admin atau manager, tetapi tidak dapat mengunduh atau mengedit dokumen.\n* **Editor**: Mengedit dokumen yang terkait dengan mereka, tetapi tidak dapat menghapus atau mengupload dokumen baru.\n* **Uploader**: Mengupload dokumen yang terkait dengan mereka, tetapi tidak dapat mengedit atau menghapus dokumen.\n\nDengan demikian, setiap role memiliki akses dan kemampuan yang berbeda-beda, sehingga dapat membantu dalam mengelola dokumen dan folder di aplikasi E-Drive.', 733, 610, 'llama-3.3-70b-versatile', 1948, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:14:51'),
(21, 6, 'user', 'ada berapa role yang ada di aplikasi ini', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:17:48'),
(22, 6, 'assistant', '### Jumlah Role di Aplikasi E-Drive\n\nTerdapat **11 Role** yang ada di aplikasi E-Drive, yaitu:\n\n1. **Administrator**\n2. **Project Manager**\n3. **Energy & Power Supply**\n4. **Visual Aid & Utility**\n5. **Terminal 1, 2 & Non Terminal**\n6. **Terminal 3**\n7. **Administrasi Laporan**\n8. **Administrasi SDM**\n9. **Administrasi Penagihan**\n10. **Logistik**\n11. **K3** (Keselamatan & Kesehatan Kerja)', 454, 131, 'llama-3.3-70b-versatile', 1241, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:17:49'),
(23, 6, 'user', 'fitur  nya apa saja ?', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:18:00'),
(24, 6, 'assistant', '### Fitur Aplikasi E-Drive\n\nAplikasi E-Drive memiliki beberapa fitur utama, yaitu:\n\n* **Upload**: Mengupload dokumen ke dalam sistem E-Drive\n* **Download**: Mengunduh dokumen yang telah diupload ke dalam sistem E-Drive\n* **Folder**: Membuat dan mengelola folder untuk menyimpan dokumen\n* **Drive**: Mengakses dan mengelola drive yang telah ditentukan untuk setiap role\n\nSelain itu, aplikasi E-Drive juga memiliki fitur lain seperti:\n\n* **Manajemen Dokumen**: Mengelola dokumen, termasuk membuat, mengedit, dan menghapus dokumen\n* **Pengaturan Akses**: Mengatur akses untuk setiap role, sehingga hanya role yang berwenang yang dapat mengakses dokumen tertentu\n* **Pencarian Dokumen**: Mencari dokumen berdasarkan kata kunci, tanggal, dan lain-lain\n* **Informasi Storage**: Menampilkan informasi tentang kapasitas penyimpanan yang tersedia dan yang telah digunakan\n* **Jadwal**: Menampilkan jadwal untuk setiap role, sehingga mereka dapat mengakses dokumen yang relevan dengan jadwal mereka\n* **Troubleshooting**: Membantu mengatasi masalah yang umum terjadi dalam penggunaan aplikasi E-Drive\n\nNamun, perlu diingat bahwa fitur yang tersedia dapat berbeda-beda tergantung pada role yang digunakan.', 601, 331, 'llama-3.3-70b-versatile', 1669, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:18:02'),
(25, 6, 'user', 'apakah ada fitur schedule ?', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:18:28'),
(26, 6, 'assistant', '### Fitur Jadwal (Schedule) di Aplikasi E-Drive\n\nYa, aplikasi E-Drive memiliki fitur **Jadwal (Schedule)**. Fitur ini memungkinkan pengguna untuk melihat jadwal yang telah ditentukan untuk setiap role, sehingga mereka dapat mengakses dokumen yang relevan dengan jadwal mereka.\n\nDengan fitur Jadwal, pengguna dapat:\n\n* Melihat jadwal yang telah ditentukan untuk setiap role\n* Mengakses dokumen yang relevan dengan jadwal mereka\n* Mengelola jadwal untuk setiap role, jika mereka memiliki akses yang cukup\n\nNamun, perlu diingat bahwa fitur Jadwal dapat berbeda-beda tergantung pada role yang digunakan dan konfigurasi sistem E-Drive.', 948, 180, 'llama-3.3-70b-versatile', 1303, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:18:30'),
(27, 6, 'user', 'di menu apa saya membuat drive ?', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:19:02'),
(28, 6, 'assistant', '### Membuat Drive di Aplikasi E-Drive\n\nUntuk membuat drive di aplikasi E-Drive, Anda dapat melakukannya di menu **Folder** atau **Drive**. Namun, perlu diingat bahwa hanya role tertentu yang memiliki akses untuk membuat drive, seperti **Administrator** dan **Project Manager**.\n\nBerikut adalah langkah-langkah umum untuk membuat drive di aplikasi E-Drive:\n\n1. Masuk ke aplikasi E-Drive dengan menggunakan akun Anda\n2. Klik pada menu **Folder** atau **Drive**\n3. Klik pada tombol **Buat Folder** atau **Buat Drive**\n4. Masukkan nama drive dan deskripsi (jika diperlukan)\n5. Tentukan akses untuk drive tersebut (siapa yang dapat mengakses drive)\n6. Klik pada tombol **Simpan** untuk membuat drive\n\nPerlu diingat bahwa langkah-langkah di atas dapat berbeda-beda tergantung pada konfigurasi sistem E-Drive dan role yang Anda gunakan.', 1144, 228, 'llama-3.3-70b-versatile', 1674, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:19:04'),
(29, 6, 'user', 'role mana yang paling banyak menyimpan data di aplikasi e- drive ini ?', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:19:56'),
(30, 6, 'assistant', '### Role yang Paling Banyak Menyimpan Data di Aplikasi E-Drive\n\nBerdasarkan data yang tersedia, role **Administrasi Laporan**, **Administrasi Penagihan**, dan **Administrasi SDM** kemungkinan besar memiliki akses untuk menyimpan data di aplikasi E-Drive. Namun, role yang paling banyak menyimpan data di aplikasi E-Drive ini adalah **Project Manager**.\n\nRole **Project Manager** memiliki akses untuk mengelola seluruh drive dan dokumen di aplikasi E-Drive, sehingga mereka kemungkinan besar memiliki jumlah data yang paling banyak disimpan di aplikasi ini.\n\nBerikut adalah daftar role yang memiliki akses untuk menyimpan data di aplikasi E-Drive, beserta kemungkinan jumlah data yang disimpan:\n\n* **Project Manager**: Sangat banyak\n* **Administrasi Laporan**: Banyak\n* **Administrasi Penagihan**: Banyak\n* **Administrasi SDM**: Banyak\n* **Energy & Power Supply**: Sedang\n* **Visual Aid & Utility**: Sedang\n* **Terminal 1, 2 & Non Terminal**: Sedang\n* **Terminal 3**: Sedang\n* **Logistik**: Sedang\n* **K3**: Sedang\n* **Administrator**: Sangat banyak (karena memiliki akses penuh ke aplikasi E-Drive)\n\nPerlu diingat bahwa jumlah data yang disimpan dapat berbeda-beda tergantung pada kebutuhan dan aktivitas masing-masing role.', 1398, 329, 'llama-3.3-70b-versatile', 1464, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:19:58'),
(31, 6, 'user', 'sebuitkan nilai kapastitas penyimpanan sekarang', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:21:01'),
(32, 6, 'assistant', '### Kapasitas Penyimpanan Aplikasi E-Drive\n\nSayangnya, saya tidak memiliki informasi tentang nilai kapasitas penyimpanan aplikasi E-Drive saat ini. Data yang saya miliki hanya mencakup daftar role dan drive yang tersedia, tetapi tidak mencakup informasi tentang kapasitas penyimpanan.\n\nJika Anda ingin mengetahui nilai kapasitas penyimpanan aplikasi E-Drive, saya sarankan Anda untuk memeriksa menu **Informasi Storage** atau **Kapasitas Penyimpanan** di dalam aplikasi E-Drive. Di sana, Anda dapat melihat informasi tentang kapasitas penyimpanan yang tersedia dan yang telah digunakan.', 1735, 151, 'llama-3.3-70b-versatile', 1206, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:21:02'),
(33, 6, 'user', 'Drive mana yang memiliki kapasitas penyimpanan terbesar dan berapa nilainya?', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:24:24'),
(34, 6, 'assistant', '### Drive dengan Kapasitas Penyimpanan Terbesar\n\nBerdasarkan data yang tersedia, drive yang memiliki kapasitas penyimpanan terbesar adalah **Drive Administrator**.\n\nNilai kapasitas penyimpanan drive Administrator adalah **7.63 MB**. Ini berarti bahwa drive Administrator telah menggunakan sekitar 7.63 MB dari kapasitas penyimpanan yang tersedia.\n\nPerlu diingat bahwa nilai ini dapat berubah-ubah tergantung pada aktivitas pengguna dan jumlah data yang disimpan di drive Administrator.', 1982, 116, 'llama-3.3-70b-versatile', 1480, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:24:26'),
(35, 6, 'user', 'Rincikan kapasitas penyimpanan per drive saat ini', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:24:42'),
(36, 6, 'assistant', '### Rincian Kapasitas Penyimpanan per Drive\n\nBerdasarkan data yang tersedia, berikut adalah rincian kapasitas penyimpanan per drive saat ini:\n\n* **Drive Administrator**: 7.63 MB (4 file dokumen)\n* **Drive Administrasi Laporan**: 0 B (0 file dokumen)\n* **Drive Administrasi Penagihan**: 0 B (0 file dokumen)\n* **Drive Administrasi SDM**: 0 B (0 file dokumen)\n* **Drive Energy & Power Supply**: 0 B (0 file dokumen)\n* **Drive K3**: 0 B (0 file dokumen)\n* **Drive Logistik**: 0 B (0 file dokumen)\n* **Drive Project Manager**: 0 B (0 file dokumen)\n* **Drive Terminal 1, 2 & Non Terminal**: 0 B (0 file dokumen)\n* **Drive Terminal 3**: 0 B (0 file dokumen)\n* **Drive Visual Aid & Utility**: 0 B (0 file dokumen)\n\nDari data di atas, dapat dilihat bahwa hanya **Drive Administrator** yang memiliki kapasitas penyimpanan yang digunakan, yaitu sebesar 7.63 MB. Sementara itu, drive lainnya belum memiliki data yang disimpan.', 1772, 279, 'llama-3.3-70b-versatile', 1325, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:24:44'),
(37, 7, 'user', 'coba jelaskan kenapa terjadinya engine slubber pada genset', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:26:25'),
(38, 7, 'assistant', '### Engine Slubber pada Genset: Penyebab dan Penjelasan\n\nEngine slubber adalah kondisi yang terjadi pada mesin genset (generator set) di mana mesin mengalami penurunan kinerja dan efisiensi, disertai dengan peningkatan konsumsi bahan bakar dan emisi gas buang. Berikut adalah beberapa penyebab utama terjadinya engine slubber pada genset:\n\n#### 1. **Kotoran dan Kerak pada Sistem Bahan Bakar**\n * Kotoran dan kerak pada sistem bahan bakar dapat menyebabkan penyumbatan pada nozzle, injector, dan komponen lainnya, sehingga mengganggu proses pembakaran yang efisien.\n * Hal ini dapat menyebabkan mesin menjadi kurang responsif, menghasilkan lebih sedikit tenaga, dan meningkatkan konsumsi bahan bakar.\n\n#### 2. **Kondisi Oli yang Tidak Baik**\n * Oli yang kotor, encer, atau tidak sesuai dengan spesifikasi dapat menyebabkan gesekan yang berlebihan pada komponen mesin, sehingga meningkatkan keausan dan panas.\n * Kondisi oli yang tidak baik juga dapat menyebabkan penurunan kinerja mesin dan efisiensi bahan bakar.\n\n#### 3. **Kerusakan atau Kotoran pada Sistem Udara**\n * Kotoran dan debu pada filter udara dapat mengganggu aliran udara yang masuk ke mesin, sehingga mengurangi kinerja dan efisiensi mesin.\n * Kerusakan pada sistem udara, seperti kebocoran atau penyumbatan, juga dapat menyebabkan penurunan kinerja mesin.\n\n#### 4. **Penggunaan Bahan Bakar yang Tidak Sesuai**\n * Penggunaan bahan bakar yang tidak sesuai dengan spesifikasi mesin dapat menyebabkan penurunan kinerja dan efisiensi mesin.\n * Bahan bakar yang tidak sesuai juga dapat menyebabkan kerusakan pada komponen mesin dan sistem bahan bakar.\n\n#### 5. **Kerusakan atau Kegagalan Komponen Mesin**\n * Kerusakan atau kegagalan komponen mesin, seperti piston, ring, atau valve, dapat menyebabkan penurunan kinerja dan efisiensi mesin.\n * Kerusakan komponen mesin juga dapat menyebabkan peningkatan konsumsi bahan bakar dan emisi gas buang.\n\n#### 6. **Pemeliharaan yang Tidak Teratur**\n * Pemeliharaan yang tidak teratur dapat menyebabkan penumpukan kotoran dan kerak pada sistem bahan bakar, oli, dan komponen mesin lainnya.\n * Pemeliharaan yang tidak teratur juga dapat menyebabkan kerusakan pada komponen mesin dan sistem bahan bakar.\n\nDengan memahami penyebab-penyebab di atas, Anda dapat mengambil langkah-langkah untuk mencegah terjadinya engine slubber pada genset, seperti melakukan pemeliharaan teratur, menggunakan bahan bakar yang sesuai, dan memantau kondisi mesin secara berkala.', 101, 730, 'llama-3.3-70b-versatile', 3455, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:26:28'),
(39, 7, 'user', 'apakah engine slubber dapat dikaitkan dengan kekurangan beban yang kecil', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:28:01'),
(40, 7, 'assistant', '### Kekurangan Beban dan Engine Slubber: Hubungan yang Erat\n\nYa, engine slubber dapat dikaitkan dengan kekurangan beban yang kecil. Kekurangan beban yang kecil dapat menyebabkan mesin genset mengalami kondisi yang tidak optimal, sehingga meningkatkan risiko terjadinya engine slubber.\n\nBerikut adalah beberapa cara kekurangan beban yang kecil dapat menyebabkan engine slubber:\n\n#### 1. **Peningkatan Suhu Mesin**\n * Ketika beban mesin genset terlalu kecil, mesin akan menghasilkan lebih sedikit panas dan tekanan, sehingga suhu mesin akan meningkat.\n * Peningkatan suhu mesin dapat menyebabkan keausan komponen mesin, seperti piston, ring, dan valve, sehingga meningkatkan risiko terjadinya engine slubber.\n\n#### 2. **Peningkatan Konsumsi Bahan Bakar**\n * Kekurangan beban yang kecil dapat menyebabkan mesin genset mengkonsumsi lebih banyak bahan bakar untuk menghasilkan daya yang sama.\n * Peningkatan konsumsi bahan bakar dapat menyebabkan peningkatan emisi gas buang dan keausan komponen mesin, sehingga meningkatkan risiko terjadinya engine slubber.\n\n#### 3. **Peningkatan Keausan Komponen Mesin**\n * Kekurangan beban yang kecil dapat menyebabkan komponen mesin, seperti piston, ring, dan valve, mengalami keausan yang lebih cepat.\n * Keausan komponen mesin dapat menyebabkan penurunan kinerja mesin dan efisiensi bahan bakar, sehingga meningkatkan risiko terjadinya engine slubber.\n\n#### 4. **Peningkatan Risiko Kerusakan Mesin**\n * Kekurangan beban yang kecil dapat menyebabkan mesin genset mengalami kerusakan yang lebih cepat, seperti kerusakan pada sistem bahan bakar, oli, dan komponen mesin lainnya.\n * Kerusakan mesin dapat menyebabkan penurunan kinerja mesin dan efisiensi bahan bakar, sehingga meningkatkan risiko terjadinya engine slubber.\n\nUntuk mencegah terjadinya engine slubber akibat kekurangan beban yang kecil, Anda dapat melakukan beberapa hal berikut:\n\n* Pastikan beban mesin genset sesuai dengan kapasitas mesin.\n* Gunakan sistem pengatur beban untuk mengatur beban mesin genset.\n* Lakukan pemeliharaan teratur untuk memastikan kondisi mesin genset yang optimal.\n* Pantau kondisi mesin genset secara berkala untuk mendeteksi kerusakan atau keausan komponen mesin.', 858, 641, 'llama-3.3-70b-versatile', 2118, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:28:04'),
(41, 8, 'user', 'coba jelaskan kenapa terjadinya engine Slobber pada genset', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:30:25'),
(42, 8, 'assistant', '### Engine Slobber pada Genset: Penyebab dan Penjelasan\n\nEngine slobber adalah fenomena yang terjadi pada mesin genset, di mana mesin mengeluarkan cairan yang mirip dengan lendir atau slobber. Berikut adalah beberapa penyebab utama terjadinya engine slobber pada genset:\n\n#### 1. **Kondensasi**\nKondensasi terjadi ketika udara yang panas dan lembab masuk ke dalam mesin dan kemudian mendingin, menyebabkan air mengembun di dalam mesin. Hal ini dapat menyebabkan terbentuknya cairan yang mirip dengan lendir.\n\n#### 2. **Kerusakan Sistem Pendingin**\nSistem pendingin yang tidak berfungsi dengan baik dapat menyebabkan mesin menjadi terlalu panas, sehingga menyebabkan terjadinya kondensasi dan pembentukan cairan yang mirip dengan lendir.\n\n#### 3. **Kualitas Bahan Bakar**\nBahan bakar yang tidak sesuai dengan spesifikasi mesin dapat menyebabkan terjadinya engine slobber. Bahan bakar yang memiliki kandungan air yang tinggi atau kandungan lain yang tidak sesuai dapat menyebabkan terbentuknya cairan yang mirip dengan lendir.\n\n#### 4. **Kerusakan Sistem Pelumasan**\nSistem pelumasan yang tidak berfungsi dengan baik dapat menyebabkan mesin menjadi terlalu panas, sehingga menyebabkan terjadinya kondensasi dan pembentukan cairan yang mirip dengan lendir.\n\n#### 5. **Umur Mesin**\nMesin yang sudah tua dapat mengalami penurunan kinerja dan efisiensi, sehingga menyebabkan terjadinya engine slobber.\n\n### Cara Mengatasi Engine Slobber\n\nUntuk mengatasi engine slobber, beberapa langkah yang dapat dilakukan adalah:\n\n* Periksa dan perbaiki sistem pendingin dan sistem pelumasan\n* Ganti bahan bakar dengan yang sesuai dengan spesifikasi mesin\n* Lakukan perawatan rutin pada mesin\n* Periksa dan perbaiki kerusakan pada mesin\n* Ganti mesin jika sudah tua dan tidak dapat diperbaiki lagi\n\nDengan memahami penyebab dan cara mengatasi engine slobber, Anda dapat menghindari kerusakan pada genset dan memastikan kinerja yang optimal.', 101, 547, 'llama-3.3-70b-versatile', 2863, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:30:28'),
(43, 8, 'user', 'apakah engine Slobber ada kaitannya kurang beban', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:31:33'),
(44, 8, 'assistant', '### Kaitan Engine Slobber dengan Kurang Beban\n\nYa, engine slobber dapat terkait dengan kurang beban pada genset. Berikut adalah beberapa penjelasan tentang kaitan antara engine slobber dan kurang beban:\n\n#### 1. **Kurang Beban menyebabkan Mesin Berjalan pada Putaran Rendah**\nKetika genset berjalan pada kurang beban, mesin akan berjalan pada putaran rendah. Hal ini dapat menyebabkan mesin menjadi terlalu panas dan tidak dapat mengeluarkan panas dengan efektif, sehingga menyebabkan terjadinya kondensasi dan pembentukan cairan yang mirip dengan lendir.\n\n#### 2. **Kurang Beban menyebabkan Sistem Pendingin Tidak Berfungsi dengan Baik**\nSistem pendingin pada genset dirancang untuk berfungsi dengan baik ketika mesin berjalan pada beban penuh. Ketika genset berjalan pada kurang beban, sistem pendingin tidak dapat berfungsi dengan baik, sehingga menyebabkan mesin menjadi terlalu panas dan terjadinya engine slobber.\n\n#### 3. **Kurang Beban menyebabkan Pembentukan Karbon**\nKetika genset berjalan pada kurang beban, bahan bakar tidak dapat terbakar dengan sempurna, sehingga menyebabkan pembentukan karbon. Karbon ini dapat menyebabkan terjadinya engine slobber dan kerusakan pada mesin.\n\n#### 4. **Kurang Beban menyebabkan Penurunan Kinerja Mesin**\nKetika genset berjalan pada kurang beban, kinerja mesin dapat menurun. Hal ini dapat menyebabkan mesin menjadi terlalu panas, terjadinya engine slobber, dan kerusakan pada mesin.\n\n### Cara Mengatasi Kurang Beban pada Genset\n\nUntuk mengatasi kurang beban pada genset, beberapa langkah yang dapat dilakukan adalah:\n\n* Pastikan genset berjalan pada beban yang sesuai dengan spesifikasi mesin\n* Gunakan genset pada aplikasi yang sesuai dengan kemampuan mesin\n* Lakukan perawatan rutin pada genset untuk memastikan kinerja yang optimal\n* Periksa dan perbaiki sistem pendingin dan sistem pelumasan untuk memastikan kinerja yang optimal\n* Pertimbangkan untuk menggunakan genset dengan kemampuan yang lebih sesuai dengan kebutuhan Anda\n\nDengan memahami kaitan antara engine slobber dan kurang beban, Anda dapat menghindari kerusakan pada genset dan memastikan kinerja yang optimal.', 670, 605, 'llama-3.3-70b-versatile', 2047, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:31:35'),
(45, 9, 'user', 'jelaskan code ini , <?php\r\n\r\nclass Dashboard extends Controller {\r\n    public function __construct() {\r\n        parent::__construct();\r\n        require_once APPPATH . \'middleware/AuthMiddleware.php\';\r\n        AuthMiddleware::check();\r\n        $this->load->helper(\'url\');\r\n        $this->load->helper(\'file\');\r\n    }\r\n\r\n    public function index() {\r\n        $user_id = Session::get(\'user_id\');\r\n        $role_id = Session::get(\'role_id\');\r\n        $is_admin = AuthMiddleware::isManager();\r\n\r\n        // 1. Storage Stats\r\n        if ($is_admin) {\r\n            $total_docs = $this->db->query(\"SELECT COUNT(*) as cnt FROM documents WHERE status = 1\")->row()->cnt;\r\n            $total_size = $this->db->query(\"SELECT SUM(file_size) as total FROM documents WHERE status = 1\")->row()->total ?? 0;\r\n            $total_drives = $this->db->query(\"SELECT COUNT(*) as cnt FROM drives WHERE status = 1\")->row()->cnt;\r\n            $total_users = $this->db->query(\"SELECT COUNT(*) as cnt FROM users WHERE status = 1\")->row()->cnt;\r\n        } else {\r\n            // Stats based on accessible drives\r\n            $drives = $this->db->query(\"\r\n                SELECT id FROM drives \r\n                WHERE (owner_role_id = ? OR is_shared = 1) AND status = 1\r\n                UNION\r\n                SELECT entity_id as id FROM shared_access \r\n                WHERE entity_type = \'drive\' AND user_id = ? AND status = 1\r\n            \", [$role_id, $user_id])->fetchAll();\r\n            \r\n            $drive_ids = array_column($drives, \'id\');\r\n            if (empty($drive_ids)) {\r\n                $total_docs = 0; $total_size = 0; $total_drives = 0;\r\n            } else {\r\n                $in_drives = implode(\',\', $drive_ids);\r\n                $total_docs = $this->db->query(\"SELECT COUNT(*) as cnt FROM documents WHERE drive_id IN ($in_drives) AND status = 1\")->row()->cnt;\r\n                $total_size = $this->db->query(\"SELECT SUM(file_size) as total FROM documents WHERE drive_id IN ($in_drives) AND status = 1\")->row()->total ?? 0;\r\n                $total_drives = count($drive_ids);\r\n            }\r\n            $total_users = 0; // Not relevant for non-admins\r\n        }\r\n\r\n        // 2. Recent Documents\r\n        $recent_docs_sql = \"\r\n            SELECT d.*, f.name as folder_name, dr.name as drive_name, u.name as uploader_name\r\n            FROM documents d\r\n            LEFT JOIN folders f ON d.folder_id = f.id\r\n            LEFT JOIN drives dr ON d.drive_id = dr.id\r\n            LEFT JOIN users u ON d.created_by = u.id\r\n            WHERE d.status = 1\r\n        \";\r\n        \r\n        if (!$is_admin && !empty($drive_ids)) {\r\n            $recent_docs_sql .= \" AND d.drive_id IN (\" . implode(\',\', $drive_ids) . \")\";\r\n        } elseif (!$is_admin && empty($drive_ids)) {\r\n            $recent_docs_sql .= \" AND 1=0\"; // Return empty\r\n        }\r\n        \r\n        $recent_docs_sql .= \" ORDER BY d.created_at DESC LIMIT 5\";\r\n        $recent_docs = $this->db->query($recent_docs_sql)->fetchAll();\r\n\r\n        // 3. Activity Log\r\n        $activities = $this->db->query(\"\r\n            SELECT a.*, u.name as user_name, u.avatar\r\n            FROM activity_logs a\r\n            JOIN users u ON a.user_id = u.id\r\n            ORDER BY a.created_at DESC LIMIT 6\r\n        \")->fetchAll();\r\n\r\n        // 4. Chart Data (Storage per Drive)\r\n        $chart_sql = \"\r\n            SELECT dr.name, SUM(d.file_size) as total_size\r\n            FROM drives dr\r\n            LEFT JOIN documents d ON dr.id = d.drive_id AND d.status = 1\r\n            WHERE dr.status = 1\r\n        \";\r\n        if (!$is_admin) {\r\n            $chart_sql .= \" AND dr.owner_role_id = \" . intval($role_id);\r\n        }\r\n        $chart_sql .= \" GROUP BY dr.id ORDER BY total_size DESC\";\r\n        $chart_data = $this->db->query($chart_sql)->fetchAll();\r\n\r\n        $data = [\r\n            \'title\'        => \'Dashboard\',\r\n            \'total_docs\'   => $total_docs,\r\n            \'total_size\'   => $total_size,\r\n            \'total_drives\' => $total_drives,\r\n            \'total_users\'  => $total_users,\r\n            \'recent_docs\'  => $recent_docs,\r\n            \'activities\'   => $activities,\r\n            \'chart_data\'   => $chart_data,\r\n            \'is_admin\'     => $is_admin\r\n        ];\r\n\r\n        $this->load->view(\'dashboard/v_index\', $data);\r\n    }\r\n}', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:32:29'),
(46, 9, 'assistant', '### Code Explanation\n\n#### Overview\nThe provided code is a PHP class named `Dashboard` that extends the `Controller` class. It appears to be part of a web application built using the CodeIgniter framework. The class has a single method `index()` which is responsible for rendering the dashboard view.\n\n#### Constructor\n```php\npublic function __construct() {\n    parent::__construct();\n    require_once APPPATH . \'middleware/AuthMiddleware.php\';\n    AuthMiddleware::check();\n    $this->load->helper(\'url\');\n    $this->load->helper(\'file\');\n}\n```\nThe constructor:\n\n1. Calls the parent constructor.\n2. Includes the `AuthMiddleware` class and checks for authentication.\n3. Loads the `url` and `file` helpers.\n\n#### Index Method\n```php\npublic function index() {\n    // ...\n}\n```\nThe `index()` method is responsible for rendering the dashboard view. It performs the following tasks:\n\n1. **Retrieve User Data**\n   - Retrieves the user\'s ID and role ID from the session.\n   - Checks if the user is an administrator using the `AuthMiddleware::isManager()` method.\n\n2. **Storage Statistics**\n   - If the user is an administrator, it retrieves the total number of documents, total storage size, total number of drives, and total number of users.\n   - If the user is not an administrator, it retrieves the statistics based on the drives they have access to.\n\n3. **Recent Documents**\n   - Retrieves the 5 most recent documents, including their metadata (e.g., folder name, drive name, uploader name).\n   - If the user is not an administrator, it filters the documents based on the drives they have access to.\n\n4. **Activity Log**\n   - Retrieves the 6 most recent activity logs, including the user\'s name and avatar.\n\n5. **Chart Data (Storage per Drive)**\n   - Retrieves the storage size for each drive, grouped by drive name.\n   - If the user is not an administrator, it filters the data based on the drives they have access to.\n\n6. **Render Dashboard View**\n   - Passes the retrieved data to the `dashboard/v_index` view using the `$data` array.\n\n#### Data Array\n```php\n$data = [\n    \'title\'        => \'Dashboard\',\n    \'total_docs\'   => $total_docs,\n    \'total_size\'   => $total_size,\n    \'total_drives\' => $total_drives,\n    \'total_users\'  => $total_users,\n    \'recent_docs\'  => $recent_docs,\n    \'activities\'   => $activities,\n    \'chart_data\'   => $chart_data,\n    \'is_admin\'     => $is_admin\n];\n```\nThe `$data` array contains the following key-value pairs:\n\n* `title`: The title of the dashboard page.\n* `total_docs`, `total_size`, `total_drives`, `total_users`: The storage statistics.\n* `recent_docs`: The 5 most recent documents.\n* `activities`: The 6 most recent activity logs.\n* `chart_data`: The storage size for each drive.\n* `is_admin`: A boolean indicating whether the user is an administrator.\n\n#### Rendering the View\n```php\n$this->load->view(\'dashboard/v_index\', $data);\n```\nThe `dashboard/v_index` view is rendered, passing the `$data` array as a parameter. The view will display the dashboard page with the retrieved data.', 1113, 714, 'llama-3.3-70b-versatile', 2233, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:32:31'),
(47, 9, 'user', 'pakai bahasa indonesia dong penjelasannya', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:32:48'),
(48, 9, 'assistant', '### Penjelasan Kode\n\n#### Ikhtisar\nKode yang disediakan adalah sebuah kelas PHP bernama `Dashboard` yang memperluas kelas `Controller`. Kelas ini tampaknya merupakan bagian dari aplikasi web yang dibangun menggunakan kerangka CodeIgniter. Kelas ini memiliki satu metode `index()` yang bertanggung jawab untuk merender tampilan dasbor.\n\n#### Konstruktor\n```php\npublic function __construct() {\n    parent::__construct();\n    require_once APPPATH . \'middleware/AuthMiddleware.php\';\n    AuthMiddleware::check();\n    $this->load->helper(\'url\');\n    $this->load->helper(\'file\');\n}\n```\nKonstruktor:\n\n1. Memanggil konstruktor induk.\n2. Menginclude kelas `AuthMiddleware` dan memeriksa autentikasi.\n3. Memuat helper `url` dan `file`.\n\n#### Metode Index\n```php\npublic function index() {\n    // ...\n}\n```\nMetode `index()` bertanggung jawab untuk merender tampilan dasbor. Metode ini melakukan tugas-tugas berikut:\n\n1. **Mengambil Data Pengguna**\n   - Mengambil ID pengguna dan ID peran dari sesi.\n   - Memeriksa apakah pengguna adalah administrator menggunakan metode `AuthMiddleware::isManager()`.\n\n2. **Statistik Penyimpanan**\n   - Jika pengguna adalah administrator, maka mengambil total jumlah dokumen, total ukuran penyimpanan, total jumlah drive, dan total jumlah pengguna.\n   - Jika pengguna bukan administrator, maka mengambil statistik berdasarkan drive yang dapat diakses.\n\n3. **Dokumen Terbaru**\n   - Mengambil 5 dokumen terbaru, termasuk metadata (misalnya, nama folder, nama drive, nama pengunggah).\n   - Jika pengguna bukan administrator, maka memfilter dokumen berdasarkan drive yang dapat diakses.\n\n4. **Log Aktivitas**\n   - Mengambil 6 log aktivitas terbaru, termasuk nama pengguna dan avatar.\n\n5. **Data Grafik (Penyimpanan per Drive)**\n   - Mengambil ukuran penyimpanan untuk setiap drive, dikelompokkan berdasarkan nama drive.\n   - Jika pengguna bukan administrator, maka memfilter data berdasarkan drive yang dapat diakses.\n\n6. **Merender Tampilan Dasbor**\n   - Mengirimkan data yang diperoleh ke tampilan `dashboard/v_index` menggunakan array `$data`.\n\n#### Array Data\n```php\n$data = [\n    \'title\'        => \'Dasbor\',\n    \'total_docs\'   => $total_docs,\n    \'total_size\'   => $total_size,\n    \'total_drives\' => $total_drives,\n    \'total_users\'  => $total_users,\n    \'recent_docs\'  => $recent_docs,\n    \'activities\'   => $activities,\n    \'chart_data\'   => $chart_data,\n    \'is_admin\'     => $is_admin\n];\n```\nArray `$data` berisi pasangan kunci-nilai berikut:\n\n* `title`: Judul halaman dasbor.\n* `total_docs`, `total_size`, `total_drives`, `total_users`: Statistik penyimpanan.\n* `recent_docs`: 5 dokumen terbaru.\n* `activities`: 6 log aktivitas terbaru.\n* `chart_data`: Ukuran penyimpanan untuk setiap drive.\n* `is_admin`: Nilai boolean yang menunjukkan apakah pengguna adalah administrator.\n\n#### Merender Tampilan\n```php\n$this->load->view(\'dashboard/v_index\', $data);\n```\nTampilan `dashboard/v_index` dirender, dengan mengirimkan array `$data` sebagai parameter. Tampilan akan menampilkan halaman dasbor dengan data yang diperoleh.', 1846, 844, 'llama-3.3-70b-versatile', 3363, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:32:52'),
(49, 9, 'user', 'authmiddleware itu berikan penjelsan nya dan penerapanya beserta analoginya', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:34:22'),
(50, 9, 'assistant', '### AuthMiddleware\n\nAuthMiddleware adalah sebuah kelas yang digunakan untuk memeriksa autentikasi pengguna sebelum mereka dapat mengakses suatu halaman atau fungsi tertentu dalam aplikasi web. Dalam konteks aplikasi web, autentikasi adalah proses untuk memeriksa identitas pengguna dan memastikan bahwa mereka memiliki hak akses yang tepat untuk mengakses suatu sumber daya.\n\n#### Analogi\n\nBayangkan Anda ingin masuk ke sebuah gedung yang memiliki keamanan ketat. Sebelum Anda dapat masuk, Anda harus menunjukkan kartu identitas Anda kepada petugas keamanan. Jika kartu identitas Anda valid, maka petugas keamanan akan membiarkan Anda masuk. Namun, jika kartu identitas Anda tidak valid atau Anda tidak memiliki kartu identitas, maka Anda tidak akan diizinkan masuk.\n\nDalam hal ini, AuthMiddleware seperti petugas keamanan yang memeriksa kartu identitas Anda sebelum Anda dapat masuk ke gedung. Jika Anda memiliki kartu identitas yang valid, maka AuthMiddleware akan membiarkan Anda mengakses halaman atau fungsi yang Anda inginkan. Namun, jika Anda tidak memiliki kartu identitas yang valid, maka AuthMiddleware akan menghalangi Anda untuk mengakses halaman atau fungsi tersebut.\n\n#### Penerapan\n\nAuthMiddleware dapat diterapkan dalam berbagai cara, tergantung pada kebutuhan aplikasi web. Berikut adalah beberapa contoh penerapan AuthMiddleware:\n\n1. **Autentikasi Pengguna**: AuthMiddleware dapat digunakan untuk memeriksa identitas pengguna sebelum mereka dapat mengakses suatu halaman atau fungsi tertentu.\n2. **Otorisasi**: AuthMiddleware dapat digunakan untuk memeriksa hak akses pengguna sebelum mereka dapat mengakses suatu sumber daya tertentu.\n3. **Validasi Token**: AuthMiddleware dapat digunakan untuk memeriksa validitas token yang dikirimkan oleh pengguna sebelum mereka dapat mengakses suatu halaman atau fungsi tertentu.\n\n#### Contoh Kode\n\nBerikut adalah contoh kode AuthMiddleware dalam PHP:\n```php\nclass AuthMiddleware {\n    public function check($request) {\n        // Periksa apakah pengguna telah login\n        if (!isset($_SESSION[\'user_id\'])) {\n            // Jika tidak, maka redirect ke halaman login\n            header(\'Location: login.php\');\n            exit;\n        }\n\n        // Periksa apakah pengguna memiliki hak akses yang tepat\n        if (!has_access($_SESSION[\'user_id\'], $request->getPath())) {\n            // Jika tidak, maka tampilkan pesan error\n            echo \'Anda tidak memiliki hak akses untuk mengakses halaman ini.\';\n            exit;\n        }\n\n        // Jika semua periksaan telah lolos, maka biarkan pengguna mengakses halaman\n        return true;\n    }\n}\n```\nDalam contoh kode di atas, AuthMiddleware memeriksa apakah pengguna telah login dan memiliki hak akses yang tepat sebelum mereka dapat mengakses suatu halaman atau fungsi tertentu. Jika pengguna tidak memiliki hak akses yang tepat, maka AuthMiddleware akan menampilkan pesan error.', 2718, 715, 'llama-3.3-70b-versatile', 3423, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:34:25'),
(51, 9, 'user', 'berikan gambar nya agar saya mengerti', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:35:40'),
(52, 9, 'assistant', 'Saya tidak dapat menampilkan gambar secara langsung, tetapi saya dapat memberikan contoh diagram yang dapat membantu Anda memahami konsep AuthMiddleware.\n\n**Diagram AuthMiddleware**\n\n```\n                      +---------------+\n                      |  Pengguna  |\n                      +---------------+\n                             |\n                             |\n                             v\n                      +---------------+\n                      |  Request  |\n                      +---------------+\n                             |\n                             |\n                             v\n                      +---------------+\n                      |  AuthMiddleware  |\n                      |  (Periksa Autentikasi) |\n                      +---------------+\n                             |\n                             |\n                             v\n                      +---------------+\n                      |  Hasil Periksa  |\n                      |  (Valid/Invalid) |\n                      +---------------+\n                             |\n                             |\n                             v\n                      +---------------+\n                      |  Akses Diberikan  |\n                      |  (Jika Valid)      |\n                      +---------------+\n                             |\n                             |\n                             v\n                      +---------------+\n                      |  Akses Ditolak  |\n                      |  (Jika Invalid)  |\n                      +---------------+\n```\n\n**Penjelasan Diagram**\n\n1. **Pengguna**: Pengguna yang ingin mengakses suatu halaman atau fungsi tertentu.\n2. **Request**: Permintaan yang dikirimkan oleh pengguna untuk mengakses suatu halaman atau fungsi tertentu.\n3. **AuthMiddleware**: Middleware yang memeriksa autentikasi pengguna sebelum mereka dapat mengakses suatu halaman atau fungsi tertentu.\n4. **Hasil Periksa**: Hasil periksaan autentikasi yang dilakukan oleh AuthMiddleware.\n5. **Akses Diberikan**: Jika autentikasi valid, maka pengguna dapat mengakses suatu halaman atau fungsi tertentu.\n6. **Akses Ditolak**: Jika autentikasi tidak valid, maka pengguna tidak dapat mengakses suatu halaman atau fungsi tertentu.\n\n**Contoh Analogi**\n\nBayangkan Anda ingin masuk ke sebuah gedung yang memiliki keamanan ketat. Anda harus menunjukkan kartu identitas Anda kepada petugas keamanan sebelum Anda dapat masuk. Jika kartu identitas Anda valid, maka petugas keamanan akan membiarkan Anda masuk. Namun, jika kartu identitas Anda tidak valid, maka petugas keamanan akan menghalangi Anda untuk masuk.\n\nDalam hal ini, AuthMiddleware seperti petugas keamanan yang memeriksa kartu identitas Anda sebelum Anda dapat masuk ke gedung. Jika kartu identitas Anda valid, maka AuthMiddleware akan membiarkan Anda mengakses suatu halaman atau fungsi tertentu. Namun, jika kartu identitas Anda tidak valid, maka AuthMiddleware akan menghalangi Anda untuk mengakses suatu halaman atau fungsi tertentu.', 3451, 611, 'llama-3.3-70b-versatile', 2419, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:35:42'),
(53, 10, 'user', 'buatkan menu masakan gudeg jogja', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:42:05');
INSERT INTO `ai_messages` (`id`, `conversation_id`, `role`, `content`, `tokens_in`, `tokens_out`, `model_used`, `response_time_ms`, `metadata`, `status`, `created_at`) VALUES
(54, 10, 'assistant', '### Menu Masakan Gudeg Jogja\nGudeg Jogja adalah salah satu masakan khas Yogyakarta yang sangat terkenal dan disukai banyak orang. Berikut adalah beberapa menu masakan Gudeg Jogja yang bisa Anda coba:\n\n#### Bahan Utama\n* Gudeg (nangka muda yang dimasak dengan santan dan bumbu)\n* Nasi putih\n* Sambal goreng krecek (sambal yang terbuat dari kulit sapi yang digoreng dengan bumbu)\n* Telur rebus\n* Daging sapi atau ayam goreng\n* Sambal terasi\n\n#### Menu Lengkap\n1. **Gudeg Komplit**: Gudeg, nasi putih, sambal goreng krecek, telur rebus, dan daging sapi atau ayam goreng.\n2. **Gudeg Sederhana**: Gudeg, nasi putih, dan sambal terasi.\n3. **Gudeg Spesial**: Gudeg, nasi putih, sambal goreng krecek, telur rebus, daging sapi atau ayam goreng, dan sambal terasi.\n\n#### Minuman\n* Es teh manis\n* Es jeruk\n* Air mineral\n\n#### Harga\n* Gudeg Komplit: Rp 20.000 - Rp 30.000\n* Gudeg Sederhana: Rp 10.000 - Rp 20.000\n* Gudeg Spesial: Rp 30.000 - Rp 50.000\n\n### Cara Membuat Gudeg Jogja\nBerikut adalah cara membuat Gudeg Jogja yang lezat:\n\n#### Bahan\n* 1 kg nangka muda\n* 2 liter santan\n* 2 siung bawang putih\n* 3 siung bawang merah\n* 1 sdt garam\n* 1 sdt gula pasir\n* 1/2 sdt merica bubuk\n* 2 lembar daun salam\n* 2 lembar daun jeruk\n\n#### Cara Memasak\n1. Cuci nangka muda hingga bersih, lalu potong-potong menjadi ukuran yang diinginkan.\n2. Panaskan santan dalam wajan, lalu tambahkan bawang putih, bawang merah, garam, gula pasir, merica bubuk, daun salam, dan daun jeruk.\n3. Masukkan nangka muda ke dalam wajan, lalu masak hingga nangka muda empuk dan santan mengental.\n4. Angkat dan sajikan Gudeg Jogja dengan nasi putih, sambal goreng krecek, telur rebus, dan daging sapi atau ayam goreng.\n\n### Tips dan Variasi\n* Gunakan nangka muda yang masih muda dan segar untuk mendapatkan rasa yang lezat.\n* Tambahkan bumbu lain seperti kunyit, jahe, dan lengkuas untuk mendapatkan rasa yang lebih kompleks.\n* Sajikan Gudeg Jogja dengan sambal yang pedas untuk mendapatkan rasa yang lebih lezat.\n* Coba variasi Gudeg Jogja dengan menambahkan bahan lain seperti daging sapi atau ayam, telur, dan sayuran.', 96, 741, 'llama-3.3-70b-versatile', 2461, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:42:07'),
(55, 11, 'user', 'drive mana yang paling banyak menyimpan data', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:42:33'),
(56, 11, 'assistant', 'Berdasarkan data yang tersedia, **Drive Administrator** adalah drive yang paling banyak menyimpan data, dengan total kapasitas penyimpanan sebesar **7.63 MB** dan **4 file dokumen**. Ini menunjukkan bahwa drive Administrator saat ini merupakan drive yang paling aktif dan banyak digunakan untuk menyimpan dokumen di sistem E-Drive.', 668, 78, 'llama-3.3-70b-versatile', 1159, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:42:35'),
(57, 11, 'user', 'coba berikan rincian nya keseluruhabn drive', 0, 0, NULL, NULL, NULL, 1, '2026-08-03 14:42:54'),
(58, 11, 'assistant', 'Berikut adalah rincian kapasitas penyimpanan per drive di sistem E-Drive, diurutkan dari yang terbesar ke terkecil:\n\n* **Drive Administrator**: 7.63 MB (4 file dokumen)\n* **Drive Administrasi Laporan**: 0 B (0 file dokumen)\n* **Drive Administrasi Penagihan**: 0 B (0 file dokumen)\n* **Drive Administrasi SDM**: 0 B (0 file dokumen)\n* **Drive Energy & Power Supply**: 0 B (0 file dokumen)\n* **Drive K3**: 0 B (0 file dokumen)\n* **Drive Logistik**: 0 B (0 file dokumen)\n* **Drive Project Manager**: 0 B (0 file dokumen)\n* **Drive Terminal 1, 2 & Non Terminal**: 0 B (0 file dokumen)\n* **Drive Terminal 3**: 0 B (0 file dokumen)\n* **Drive Visual Aid & Utility**: 0 B (0 file dokumen)\n\nDari rincian di atas, dapat dilihat bahwa **Drive Administrator** adalah satu-satunya drive yang memiliki data, sedangkan drive lainnya masih kosong.', 769, 255, 'llama-3.3-70b-versatile', 1259, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-03 14:42:56'),
(59, 12, 'user', 'berapa banyak role drive di aplikasi ini n', 0, 0, NULL, NULL, NULL, 1, '2026-08-04 14:40:24'),
(60, 12, 'assistant', '⚠️ **[Cloud LLM API Error]**\n\nCloud LLM connection error: Could not resolve host: api.groq.com\n\n*Silakan periksa API Key atau Model Name Anda di menu **AI Settings**.*', 0, 0, 'cloud-error', 19, '{\"error\":\"Cloud LLM connection error: Could not resolve host: api.groq.com\"}', 1, '2026-08-04 14:40:24'),
(61, 13, 'user', 'apa itu kabel to poor', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:03:55'),
(62, 13, 'assistant', '### Kabel To Poor: Definisi dan Penjelasan\n\nKabel To Poor, atau lebih dikenal dengan istilah \"Kabel Tow\" atau \"Kabel Tarik\", adalah sebuah kabel yang digunakan untuk menarik atau menunda sebuah objek, seperti mobil, truk, atau peralatan berat, ketika mereka mengalami kerusakan atau mogok.\n\n#### Fungsi Kabel To Poor\n\nKabel To Poor memiliki beberapa fungsi utama, yaitu:\n\n* Menarik kendaraan yang mogok atau rusak ke tempat yang aman untuk diperbaiki\n* Membantu mengangkat atau memindahkan objek berat yang tidak dapat diangkat oleh manusia\n* Menghubungkan dua kendaraan atau objek untuk menarik atau menunda salah satunya\n\n#### Jenis-Jenis Kabel To Poor\n\nTerdapat beberapa jenis kabel To Poor yang tersedia, yaitu:\n\n* Kabel To Poor biasa: digunakan untuk menarik kendaraan ringan\n* Kabel To Poor berat: digunakan untuk menarik kendaraan berat atau objek besar\n* Kabel To Poor winch: digunakan untuk menarik objek dengan menggunakan winch (katrol)\n\n#### Keamanan dan Perawatan\n\nPenggunaan kabel To Poor harus dilakukan dengan hati-hati dan memperhatikan keamanan. Beberapa tips untuk menggunakan kabel To Poor dengan aman adalah:\n\n* Pastikan kabel To Poor dalam kondisi baik dan tidak rusak\n* Gunakan kabel To Poor yang sesuai dengan jenis dan berat objek yang akan ditarik\n* Jangan menggunakan kabel To Poor yang terlalu panjang atau terlalu pendek\n* Pastikan area sekitar bebas dari hambatan dan bahaya\n\nDengan demikian, kabel To Poor dapat menjadi alat yang sangat berguna dalam situasi darurat atau ketika diperlukan untuk menarik atau menunda objek berat.', 92, 445, 'llama-3.3-70b-versatile', 2292, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:03:57'),
(63, 13, 'user', 'apa itu busbar', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:04:23'),
(64, 13, 'assistant', '### Busbar: Definisi dan Penjelasan\n\nBusbar adalah sebuah komponen listrik yang berfungsi sebagai penghantar arus listrik yang kuat dan stabil. Busbar biasanya terbuat dari bahan konduktor seperti tembaga, aluminium, atau kuningan, dan memiliki bentuk yang panjang dan datar.\n\n#### Fungsi Busbar\n\nBusbar memiliki beberapa fungsi utama, yaitu:\n\n* Menghantarkan arus listrik yang kuat dan stabil antara komponen listrik yang berbeda\n* Menghubungkan komponen listrik seperti transformator, generator, dan motor listrik\n* Mengatur dan mengontrol arus listrik yang mengalir ke komponen listrik yang berbeda\n* Meningkatkan efisiensi dan keamanan sistem listrik\n\n#### Jenis-Jenis Busbar\n\nTerdapat beberapa jenis busbar yang tersedia, yaitu:\n\n* Busbar udara: digunakan untuk menghantarkan arus listrik di udara terbuka\n* Busbar tanah: digunakan untuk menghantarkan arus listrik di bawah tanah\n* Busbar indoor: digunakan untuk menghantarkan arus listrik di dalam bangunan\n* Busbar outdoor: digunakan untuk menghantarkan arus listrik di luar bangunan\n\n#### Karakteristik Busbar\n\nBusbar memiliki beberapa karakteristik yang penting, yaitu:\n\n* Kemampuan menghantarkan arus listrik yang kuat dan stabil\n* Tahan terhadap panas dan korosi\n* Dapat diinstal dengan mudah dan cepat\n* Dapat digunakan dalam berbagai aplikasi listrik\n\n#### Aplikasi Busbar\n\nBusbar digunakan dalam berbagai aplikasi listrik, yaitu:\n\n* Pembangkit listrik\n* Transmisi dan distribusi listrik\n* Industri manufaktur\n* Bangunan komersial dan residensial\n* Sistem listrik transportasi\n\nDengan demikian, busbar menjadi komponen yang sangat penting dalam sistem listrik modern, karena dapat menghantarkan arus listrik yang kuat dan stabil, serta meningkatkan efisiensi dan keamanan sistem listrik.', 550, 494, 'llama-3.3-70b-versatile', 1735, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:04:25'),
(65, 14, 'user', 'berapa banyak role drive di aplikasi ini', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:06:09'),
(66, 14, 'assistant', 'Berdasarkan data yang tersedia, terdapat **11 Role Resmi Pengguna E-Drive** di aplikasi ini. Role-role tersebut adalah:\n\n1. **Administrator**\n2. **Project Manager**\n3. **Energy & Power Supply**\n4. **Visual Aid & Utility**\n5. **Terminal 1, 2 & Non Terminal**\n6. **Terminal 3**\n7. **Administrasi Laporan**\n8. **Administrasi SDM**\n9. **Administrasi Penagihan**\n10. **Logistik**\n11. **K3** (Keselamatan & Kesehatan Kerja)', 667, 131, 'llama-3.3-70b-versatile', 1112, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:06:10'),
(67, 14, 'user', 'berapa penyimpinan pada drive sebutkan rolke terbanyak', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:06:34'),
(68, 14, 'assistant', 'Berdasarkan data yang tersedia, **Total Penggunaan Kapasitas Penyimpanan** di aplikasi E-Drive adalah **1.77 MB**.\n\nDrive dengan penyimpanan terbanyak adalah **Drive Administrator** dengan kapasitas penyimpanan sebesar **1.77 MB** dan memiliki **3 file dokumen**.\n\nJadi, role dengan penyimpanan terbanyak adalah **Administrator**.', 821, 90, 'llama-3.3-70b-versatile', 662, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:06:35'),
(69, 14, 'user', 'berikan rincian semuanya', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:06:49'),
(70, 14, 'assistant', 'Berdasarkan data yang tersedia, berikut adalah rincian kapasitas penyimpanan per drive di aplikasi E-Drive:\n\n* **Drive Administrator**: 1.77 MB (3 file dokumen)\n* **Drive Administrasi Laporan**: 0 B (0 file dokumen)\n* **Drive Administrasi Penagihan**: 0 B (0 file dokumen)\n* **Drive Administrasi SDM**: 0 B (0 file dokumen)\n* **Drive Energy & Power Supply**: 0 B (0 file dokumen)\n* **Drive K3**: 0 B (0 file dokumen)\n* **Drive Logistik**: 0 B (0 file dokumen)\n* **Drive Project Manager**: 0 B (0 file dokumen)\n* **Drive Terminal 1, 2 & Non Terminal**: 0 B (0 file dokumen)\n* **Drive Terminal 3**: 0 B (0 file dokumen)\n* **Drive Visual Aid & Utility**: 0 B (0 file dokumen)\n\nDari rincian di atas, dapat dilihat bahwa:\n* **Drive Administrator** memiliki kapasitas penyimpanan terbesar, yaitu 1.77 MB dengan 3 file dokumen.\n* Sisanya, 10 drive lainnya memiliki kapasitas penyimpanan 0 B dengan 0 file dokumen.', 928, 285, 'llama-3.3-70b-versatile', 966, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:06:50'),
(71, 15, 'user', 'buatkan format cv ats berikan file word atau .docx apakah bisa ?', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:12:01'),
(72, 15, 'assistant', 'Berikut adalah contoh format CV ATS (Application Tracking System) yang dapat Anda gunakan sebagai referensi:\n\n### Informasi Pribadi\n* Nama: [Nama Anda]\n* Alamat: [Alamat Anda]\n* No. Telepon: [No. Telepon Anda]\n* Email: [Email Anda]\n* LinkedIn: [LinkedIn Anda (jika ada)]\n\n### Ringkasan\n* Ringkasan singkat tentang pengalaman kerja, keterampilan, dan pencapaian Anda (maksimal 2-3 paragraf)\n\n### Pengalaman Kerja\n* **[Nama Perusahaan]**, **[Jabatan]**, **[Tahun Kerja]**\n + Deskripsi singkat tentang tanggung jawab dan pencapaian Anda\n + Keterampilan yang digunakan (misalnya: manajemen proyek, analisis data, dll.)\n* **[Nama Perusahaan]**, **[Jabatan]**, **[Tahun Kerja]**\n + Deskripsi singkat tentang tanggung jawab dan pencapaian Anda\n + Keterampilan yang digunakan (misalnya: manajemen tim, pengembangan software, dll.)\n\n### Pendidikan\n* **[Nama Sekolah]**, **[Jurusan]**, **[Tahun Lulus]**\n + Deskripsi singkat tentang pendidikan Anda\n + Keterampilan yang diperoleh (misalnya: analisis data, pemrograman, dll.)\n\n### Keterampilan\n* **Teknis**:\n + Bahasa pemrograman (misalnya: Python, Java, C++)\n + Sistem operasi (misalnya: Windows, Linux, macOS)\n + Database (misalnya: MySQL, MongoDB, PostgreSQL)\n* **Non-Teknis**:\n + Komunikasi\n + Manajemen proyek\n + Analisis data\n\n### Sertifikat dan Penghargaan\n* **[Nama Sertifikat]**, **[Tahun]** (jika ada)\n* **[Nama Penghargaan]**, **[Tahun]** (jika ada)\n\nSayangnya, saya tidak dapat memberikan file Word atau .docx secara langsung. Namun, Anda dapat menyalin dan menempelkan contoh di atas ke dalam dokumen Word atau .docx Anda sendiri.\n\nTips:\n* Pastikan untuk menyesuaikan CV Anda dengan lowongan pekerjaan yang Anda lamar.\n* Gunakan kata kunci yang relevan dengan lowongan pekerjaan untuk membantu ATS mendeteksi keterampilan Anda.\n* Jangan lupa untuk memeriksa ejaan dan tata bahasa sebelum mengirimkan CV Anda.', 104, 577, 'llama-3.3-70b-versatile', 2313, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:12:04'),
(73, 15, 'user', '1+1 = berapa', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:13:21'),
(74, 15, 'assistant', '1 + 1 = **2**', 696, 9, 'llama-3.3-70b-versatile', 920, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:13:22'),
(75, 15, 'user', 'buatkan prompt yang bagus dan detail tujuannya adalah untuk agen code saya mengerti , ini tulisan saya , saya mau di firut schedule ada tambahan gambar yang dimana bisa menampung gmbar bnyak dan tidak hanya satu gambar mangkanya perlu di bbuatkan tabel baru dengan nama gambar_schdule_calendars yang berelasi relasi dengan schedule_calendars dimana , karena 1 schedule_calendars mempunyai banyak gambar_schdule_calendars. Nanti nya di database hanya menyimpan nama file nya saja untuk gambarnya nantinya saya mau disimpan di path folder upload\\gambar_schdule\\[id] id dari user, gunakan fc path biar bisa langsung ke production , kemudian di view nya kasih button kamera sehingga user bisa langsung foto dari hp dan kasih button upload , berikan button untuk tambah gambar jadi tidak hanya 1 gambar saja melainkan bisa banyak', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:23:07'),
(76, 15, 'assistant', 'Berikut adalah prompt yang detail dan bagus untuk agen code Anda:\n\n**Tujuan:**\nMembuat fitur tambahan pada aplikasi untuk menampung banyak gambar pada schedule calendars, serta memungkinkan user untuk mengupload gambar dari hp dan menambahkan gambar secara dinamis.\n\n**Kebutuhan:**\n\n1. **Tabel baru:** Buat tabel baru dengan nama `gambar_schdule_calendars` yang berelasi dengan tabel `schedule_calendars` dengan relasi one-to-many (1 schedule_calendars memiliki banyak gambar_schdule_calendars).\n2. **Struktur tabel:** Tabel `gambar_schdule_calendars` harus memiliki kolom-kolom berikut:\n	* `id` (primary key)\n	* `schedule_calendars_id` (foreign key yang merujuk ke `id` pada tabel `schedule_calendars`)\n	* `nama_file` (string yang menyimpan nama file gambar)\n3. **Penyimpanan gambar:** Gambar harus disimpan di folder `upload\\gambar_schdule\\[id]`, di mana `[id]` adalah id dari user yang mengupload gambar. Gunakan `FC_PATH` untuk memastikan path yang benar dan dapat digunakan pada production.\n4. **View:** Buat view yang memungkinkan user untuk:\n	* Mengupload gambar dari hp menggunakan button kamera\n	* Mengupload gambar dari galeri menggunakan button upload\n	* Menambahkan gambar secara dinamis dengan button \"Tambah Gambar\"\n5. **Fungsi:** Buat fungsi yang dapat:\n	* Mengupload gambar ke folder yang tepat\n	* Menyimpan nama file gambar ke tabel `gambar_schdule_calendars`\n	* Menampilkan gambar yang telah diupload pada view\n\n**Spesifikasi teknis:**\n\n* Gunakan bahasa pemrograman yang sama dengan aplikasi yang ada\n* Gunakan framework yang sama dengan aplikasi yang ada\n* Pastikan kode yang dibuat dapat diintegrasikan dengan aplikasi yang ada\n* Gunakan best practice untuk keamanan dan performa\n\n**Deadline:** [Tentukan deadline yang realistis]\n\n**Kriteria keberhasilan:**\n\n* Fitur tambahan dapat menampung banyak gambar pada schedule calendars\n* User dapat mengupload gambar dari hp dan menambahkan gambar secara dinamis\n* Gambar disimpan di folder yang tepat dan nama file gambar disimpan di tabel `gambar_schdule_calendars`\n* View dapat menampilkan gambar yang telah diupload\n\nDengan prompt yang detail dan bagus ini, agen code Anda harus dapat memahami kebutuhan dan membuat fitur tambahan yang sesuai dengan kebutuhan Anda.', 922, 596, 'llama-3.3-70b-versatile', 2367, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:23:09'),
(77, 16, 'user', 'hello', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 10:56:42'),
(78, 16, 'assistant', 'Halo! Selamat datang! Saya siap membantu menjawab pertanyaan Anda. Apa yang ingin Anda tanyakan hari ini?', 87, 34, 'llama-3.3-70b-versatile', 937, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 10:56:43'),
(79, 17, 'user', 'buatkan', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 11:24:06'),
(80, 17, 'assistant', '### Pertanyaan dan Jawaban Umum\n\nUntuk memberikan jawaban yang jelas dan akurat, saya memerlukan pertanyaan spesifik dari Anda. Namun, saya dapat memberikan contoh jawaban umum dalam beberapa kategori:\n\n#### 1. Sejarah\n- **Pertanyaan:** Siapa yang dianggap sebagai penemu Amerika?\n- **Jawaban:** Christopher Columbus sering dianggap sebagai penemu Amerika, meskipun ada penduduk asli yang sudah tinggal di sana sebelum kedatangannya.\n\n#### 2. Sains\n- **Pertanyaan:** Apa yang dimaksud dengan fotosintesis?\n- **Jawaban:** Fotosintesis adalah proses yang dilakukan oleh tumbuhan, alga, dan beberapa jenis bakteri untuk mengubah energi cahaya menjadi energi kimia dalam bentuk gula.\n\n#### 3. Teknologi\n- **Pertanyaan:** Apa itu internet?\n- **Jawaban:** Internet adalah jaringan global dari komputer yang saling terhubung dan berbagi informasi melalui protokol standar.\n\n#### 4. Kesehatan\n- **Pertanyaan:** Bagaimana cara mencegah penyakit flu?\n- **Jawaban:** Mencegah penyakit flu dapat dilakukan dengan menjaga kebersihan, seperti mencuci tangan secara teratur, menghindari kontak dengan orang yang terinfeksi, dan melakukan vaksinasi flu.\n\n### Bagaimana Cara Mendapatkan Jawaban yang Lebih Spesifik?\nUntuk mendapatkan jawaban yang lebih spesifik dan akurat, silakan ajukan pertanyaan Anda. Saya akan berusaha menjawabnya dengan jelas dan menggunakan contoh yang relevan.', 88, 390, 'llama-3.3-70b-versatile', 2347, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 11:24:09'),
(81, 18, 'user', 'a', 0, 0, NULL, NULL, NULL, 1, '2026-08-07 11:26:57'),
(82, 18, 'assistant', 'Mohon maaf, tapi sepertinya Anda belum mengajukan pertanyaan. Silakan ajukan pertanyaan Anda, dan saya akan berusaha menjawabnya dengan jelas dan akurat.', 87, 44, 'llama-3.3-70b-versatile', 564, '{\"provider\":\"openai-compatible\",\"model\":\"llama-3.3-70b-versatile\"}', 1, '2026-08-07 11:26:58');

-- --------------------------------------------------------

--
-- Table structure for table `ai_prompts`
--

CREATE TABLE `ai_prompts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assistant_type` enum('general','edrive') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_prompts`
--

INSERT INTO `ai_prompts` (`id`, `name`, `assistant_type`, `content`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'general_system', 'general', 'Kamu adalah AI Assistant yang membantu menjawab pertanyaan umum. Berikan jawaban yang jelas, akurat, dan mudah dipahami dalam bahasa Indonesia. Gunakan format Markdown untuk jawaban yang terstruktur.', 1, NULL, NULL, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(2, 'edrive_system', 'edrive', 'Kamu adalah E-Drive Assistant, asisten AI khusus untuk aplikasi E-Drive — Enterprise Document Management System. Kamu membantu user dengan:\n- Tutorial penggunaan E-Drive (upload, download, folder, drive)\n- SOP dan FAQ terkait manajemen dokumen\n- Informasi storage, file, dan jadwal\n- Troubleshooting masalah umum\n\nGunakan data dari tools yang tersedia untuk memberikan jawaban akurat. Jawab dalam bahasa Indonesia dengan format Markdown.', 1, NULL, NULL, '2026-08-03 13:08:45', '2026-08-03 13:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `ai_settings`
--

CREATE TABLE `ai_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_settings`
--

INSERT INTO `ai_settings` (`id`, `key`, `value`, `description`, `group`, `updated_by`, `updated_at`) VALUES
(1, 'flowise_url', '', 'Flowise server URL', 'connection', 1, '2026-08-03 14:06:47'),
(2, 'flowise_api_key', '', 'Flowise API key', 'connection', 1, '2026-08-03 14:06:47'),
(3, 'flowise_flow_a_id', '', 'Flowise Flow ID for General AI Assistant', 'connection', 1, '2026-08-03 14:06:47'),
(4, 'flowise_flow_b_id', '', 'Flowise Flow ID for E-Drive Assistant', 'connection', 1, '2026-08-03 14:06:47'),
(5, 'ollama_url', '', 'Ollama server URL', 'connection', 1, '2026-08-03 14:06:47'),
(6, 'default_model', '', 'Default AI model name', 'model', 1, '2026-08-03 14:06:47'),
(7, 'edrive_model', '', 'Model for E-Drive Assistant', 'model', 1, '2026-08-03 14:06:47'),
(8, 'max_tokens', '', 'Maximum tokens per response', 'model', 1, '2026-08-03 14:06:47'),
(9, 'temperature', '', 'Model temperature (0-1)', 'model', 1, '2026-08-03 14:06:47'),
(10, 'vector_db_type', 'chromadb', 'Vector database type: chromadb or qdrant', 'connection', 1, '2026-08-03 14:06:47'),
(11, 'vector_db_url', '', 'Vector database URL', 'connection', 1, '2026-08-03 14:06:47'),
(12, 'internal_api_key', '', 'API key for internal REST API (auto-generated)', 'security', NULL, '2026-08-03 13:08:45'),
(13, 'rate_limit_per_minute', '', 'Max AI requests per minute per user', 'security', 1, '2026-08-03 14:06:47'),
(14, 'streaming_enabled', '0', 'Enable streaming responses (SSE)', 'feature', 1, '2026-08-03 14:06:47'),
(15, 'feedback_enabled', '0', 'Enable feedback buttons on AI responses', 'feature', 1, '2026-08-03 14:06:47'),
(16, 'history_retention_days', '', 'Days to retain conversation history', 'feature', 1, '2026-08-03 14:06:47'),
(17, 'cloud_api_url', 'https://api.groq.com/openai/v1', NULL, 'general', 1, '2026-08-03 14:06:47'),
(18, 'cloud_api_key', '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', NULL, 'general', 1, '2026-08-03 14:06:47'),
(19, 'cloud_model', 'llama-3.3-70b-versatile', NULL, 'general', 1, '2026-08-03 14:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `ai_tools`
--

CREATE TABLE `ai_tools` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `endpoint` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GET',
  `parameters` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_tools`
--

INSERT INTO `ai_tools` (`id`, `name`, `description`, `endpoint`, `method`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'get_storage_stats', 'Mendapatkan statistik penyimpanan: total storage, jumlah dokumen, jumlah drive', '/ai_api/storage_stats', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(2, 'get_dashboard_summary', 'Mendapatkan ringkasan dashboard: statistik dan aktivitas terbaru', '/ai_api/dashboard_summary', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(3, 'get_recent_documents', 'Mendapatkan daftar 10 dokumen terbaru yang diupload', '/ai_api/recent_documents', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(4, 'search_documents', 'Mencari dokumen berdasarkan nama atau tipe file', '/ai_api/search_documents', 'GET', '{\"q\": \"string (search query)\"}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(5, 'get_drive_list', 'Mendapatkan daftar semua drive yang tersedia', '/ai_api/drive_list', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(6, 'get_schedule_events', 'Mendapatkan agenda/jadwal mendatang', '/ai_api/schedule_events', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(7, 'get_schedule_today', 'Mendapatkan agenda hari ini', '/ai_api/schedule_today', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(8, 'get_user_activity', 'Mendapatkan log aktivitas user terbaru', '/ai_api/user_activity', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(9, 'get_statistics', 'Mendapatkan statistik sistem (storage per drive, chart data)', '/ai_api/statistics', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45'),
(10, 'get_help_topics', 'Mendapatkan daftar topik bantuan E-Drive', '/ai_api/help_topics', 'GET', '{}', 1, '2026-08-03 13:08:45', '2026-08-03 13:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `ai_usage`
--

CREATE TABLE `ai_usage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assistant_type` enum('general','edrive') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_tokens` int(11) NOT NULL DEFAULT '0',
  `total_requests` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_usage`
--

INSERT INTO `ai_usage` (`id`, `user_id`, `assistant_type`, `total_tokens`, `total_requests`, `date`, `created_at`) VALUES
(1, 1, 'general', 17808, 15, '2026-08-03', '2026-08-03 13:09:28'),
(2, 1, 'edrive', 13960, 11, '2026-08-03', '2026-08-03 13:13:33'),
(3, 4, 'general', 837, 1, '2026-08-03', '2026-08-03 14:42:07'),
(4, 4, 'edrive', 1770, 2, '2026-08-03', '2026-08-03 14:42:35'),
(5, 1, 'edrive', 54, 1, '2026-08-04', '2026-08-04 14:40:24'),
(6, 1, 'general', 5215, 8, '2026-08-07', '2026-08-07 10:03:58'),
(7, 1, 'edrive', 2922, 3, '2026-08-07', '2026-08-07 10:06:10');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `drive_id` int(11) DEFAULT NULL,
  `folder_id` int(11) DEFAULT NULL COMMENT 'NULL = root level in drive',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Display name',
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Original uploaded filename',
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Relative path in storage/',
  `file_size` bigint(20) DEFAULT '0' COMMENT 'Size in bytes',
  `file_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Extension e.g. pdf, docx',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` int(11) DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `tags` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma-separated tags',
  `download_count` int(11) DEFAULT '0',
  `last_accessed` datetime DEFAULT NULL,
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA-256 hash',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `drive_id`, `folder_id`, `name`, `original_name`, `file_path`, `file_size`, `file_type`, `mime_type`, `version`, `description`, `tags`, `download_count`, `last_accessed`, `checksum`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(2, 1, 1, '1784648560239-dc372108-ba1e-48a3-be5d-18f558211b81_1', '1784648560239-dc372108-ba1e-48a3-be5d-18f558211b81_1.jpg', 'drives/1/2026/test/test/1784648560239-dc372108-ba1e-48a3-be5d-18f558211b81_1.jpg', 187741, 'jpg', 'image/jpeg', 1, NULL, NULL, 0, NULL, '5ec109e06cad5e755230c83d88855b8f8fe96999495edb4c0f09009fef034726', 1, '2026-08-04 06:37:01', '2026-08-04 06:51:11', NULL, 1, 1),
(3, 1, 1, 'FOTO FAHRI', 'FOTO FAHRI.png', 'drives/1/2026/test/test/FOTO FAHRI.png', 691932, 'png', 'image/png', 1, NULL, NULL, 0, NULL, '2b70e89789cad8df5c411e1405d549dec9c8a1fa62e43f593db01087fc6132c2', 1, '2026-08-04 06:37:01', '2026-08-04 06:51:17', NULL, 1, 1),
(4, 1, 1, 'KAK_O&M_2026-2027[1]', 'KAK_O&M_2026-2027[1].pdf', 'drives/1/2026/test/test/KAK_O_M_2026-2027_1_.pdf', 980899, 'pdf', 'application/pdf', 1, NULL, NULL, 0, NULL, 'fd891949ededfd49175d5d2aab1b91e624ac946c6827950bb97daa7e5909150d', 1, '2026-08-04 06:37:01', '2026-08-04 06:51:25', NULL, 1, 1),
(5, 1, 1, 'Tes1', 'Tes1.docx', 'drives/1/2026/test/Tes1.docx', 13296, 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 1, NULL, NULL, 2, '2026-08-12 16:06:54', 'bb5de26d1e5ff0bbce1440d56f90273c763c76294cde6699d67afb6fc017f27a', 1, '2026-08-12 11:05:49', '2026-08-12 11:05:49', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `version_number` int(11) DEFAULT '1',
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint(20) DEFAULT '0',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_versions`
--

INSERT INTO `document_versions` (`id`, `document_id`, `version_number`, `file_path`, `file_size`, `mime_type`, `checksum`, `notes`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(2, 2, 1, 'drives/1/2026/test/test/1784648560239-dc372108-ba1e-48a3-be5d-18f558211b81_1.jpg', 187741, 'image/jpeg', '5ec109e06cad5e755230c83d88855b8f8fe96999495edb4c0f09009fef034726', 'Extracted from archive: test.zip', 1, '2026-08-04 06:37:01', NULL, NULL, 1, NULL),
(3, 3, 1, 'drives/1/2026/test/test/FOTO FAHRI.png', 691932, 'image/png', '2b70e89789cad8df5c411e1405d549dec9c8a1fa62e43f593db01087fc6132c2', 'Extracted from archive: test.zip', 1, '2026-08-04 06:37:01', NULL, NULL, 1, NULL),
(4, 4, 1, 'drives/1/2026/test/test/KAK_O_M_2026-2027_1_.pdf', 980899, 'application/pdf', 'fd891949ededfd49175d5d2aab1b91e624ac946c6827950bb97daa7e5909150d', 'Extracted from archive: test.zip', 1, '2026-08-04 06:37:01', NULL, NULL, 1, NULL),
(5, 5, 1, 'drives/1/2026/test/Tes1.docx', 13296, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'bb5de26d1e5ff0bbce1440d56f90273c763c76294cde6699d67afb6fc017f27a', 'Initial upload', 1, '2026-08-12 11:05:49', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drives`
--

CREATE TABLE `drives` (
  `id` int(11) NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_role_id` int(11) DEFAULT NULL COMMENT 'Role yang memiliki drive ini',
  `is_shared` tinyint(4) DEFAULT '0' COMMENT '1=Bisa diakses semua role',
  `order_num` int(11) DEFAULT '0',
  `total_size` bigint(20) DEFAULT '0' COMMENT 'Total ukuran file dalam bytes',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drives`
--

INSERT INTO `drives` (`id`, `name`, `description`, `icon`, `color`, `owner_role_id`, `is_shared`, `order_num`, `total_size`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'Administrator', 'Drive khusus Administrator', 'fa-shield-halved', '#2563EB', 1, 0, 1, 10534212, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(2, 'Project Manager', 'Drive khusus Project Manager', 'fa-diagram-project', '#7C3AED', 2, 0, 2, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(3, 'Energy & Power Supply', 'Drive Energy & Power Supply', 'fa-bolt', '#F59E0B', 3, 0, 3, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(4, 'Visual Aid & Utility', 'Drive Visual Aid & Utility', 'fa-display', '#10B981', 4, 0, 4, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(5, 'Terminal 1, 2 & Non Terminal', 'Drive Terminal 1, Terminal 2 & Non Terminal', 'fa-plane-departure', '#3B82F6', 5, 0, 5, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(6, 'Terminal 3', 'Drive Terminal 3', 'fa-plane-arrival', '#6366F1', 6, 0, 6, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(7, 'Administrasi Laporan', 'Drive Administrasi Laporan', 'fa-file-lines', '#EF4444', 7, 0, 7, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(8, 'Administrasi SDM', 'Drive Administrasi SDM', 'fa-users-gear', '#EC4899', 8, 0, 8, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(9, 'Administrasi Penagihan', 'Drive Administrasi Penagihan', 'fa-file-invoice', '#F97316', 9, 0, 9, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(10, 'Logistik', 'Drive Logistik', 'fa-truck-fast', '#14B8A6', 10, 0, 10, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(11, 'K3', 'Drive K3 (Keselamatan & Kesehatan Kerja)', 'fa-helmet-safety', '#84CC16', 11, 0, 11, 0, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `entity_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'document, folder, drive',
  `entity_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `drive_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'NULL = root level in drive',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` text COLLATE utf8mb4_unicode_ci COMMENT 'Full path for breadcrumb e.g. /Folder1/SubFolder',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'fa-folder',
  `total_files` int(11) DEFAULT '0',
  `total_size` bigint(20) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `drive_id`, `parent_id`, `name`, `slug`, `path`, `color`, `icon`, `total_files`, `total_size`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 1, NULL, 'test', NULL, '/test/', NULL, 'fa-folder', 7, 3734440, 1, '2026-08-04 06:37:01', '2026-08-04 06:37:01', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gambar_schdule_calendars`
--

CREATE TABLE `gambar_schdule_calendars` (
  `id` int(11) NOT NULL,
  `schedule_calendars_id` int(11) UNSIGNED NOT NULL COMMENT 'FK ke schedule_calendars.id',
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama file gambar di disk',
  `status` tinyint(4) DEFAULT '1' COMMENT '0=Draft, 1=Active, 8=SoftDelete',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gambar_schdule_calendars`
--

INSERT INTO `gambar_schdule_calendars` (`id`, `schedule_calendars_id`, `nama_file`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 2, '1786074177_6934ee46e22e5c89.jpg', 1, '2026-08-07 05:42:57', NULL, NULL, 1, NULL),
(2, 2, '1786074177_9e08d52cbe430217.jpeg', 1, '2026-08-07 05:42:57', NULL, NULL, 1, NULL),
(3, 3, '1786682145_a96d080b3e95c7ec.png', 1, '2026-08-14 06:35:45', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'info' COMMENT 'info, success, warning, danger',
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL to navigate when clicked',
  `is_read` tinyint(4) DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_level` tinyint(4) DEFAULT '3' COMMENT '1=SuperAdmin, 2=Manager, 3=Staff',
  `status` tinyint(4) DEFAULT '1' COMMENT '0=Draft, 1=Active, 8=SoftDelete',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_description`, `role_level`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'Administrator', 'Full akses seluruh sistem dan drive', 1, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(2, 'Project Manager', 'Melihat dan mengelola seluruh Drive', 2, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(3, 'Energy & Power Supply', 'Staf Energy & Power Supply', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(4, 'Visual Aid & Utility', 'Staf Visual Aid & Utility', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(5, 'Terminal 1, 2 & Non Terminal', 'Staf Terminal 1, Terminal 2 & Non Terminal', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(6, 'Terminal 3', 'Staf Terminal 3', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(7, 'Administrasi Laporan', 'Staf Administrasi Laporan', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(8, 'Administrasi SDM', 'Staf Administrasi SDM', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(9, 'Administrasi Penagihan', 'Staf Administrasi Penagihan', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(10, 'Logistik', 'Staf Logistik', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(11, 'K3', 'Staf K3 (Keselamatan & Kesehatan Kerja)', 3, 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_menu_access`
--

CREATE TABLE `role_menu_access` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_menu_access`
--

INSERT INTO `role_menu_access` (`id`, `role_id`, `menu_id`) VALUES
(172, 1, 1),
(173, 1, 2),
(174, 1, 6),
(175, 1, 7),
(176, 1, 9),
(177, 1, 10),
(178, 1, 11),
(179, 1, 12),
(180, 1, 13),
(181, 1, 14),
(182, 1, 15),
(183, 1, 16),
(184, 2, 1),
(185, 2, 2),
(186, 2, 12),
(187, 3, 1),
(188, 3, 2),
(189, 3, 12),
(190, 7, 1),
(191, 7, 2),
(192, 7, 12),
(193, 8, 1),
(194, 8, 2),
(195, 8, 12),
(196, 9, 1),
(197, 9, 2),
(198, 9, 12),
(199, 9, 16),
(200, 6, 1),
(201, 6, 2),
(202, 6, 12),
(203, 5, 1),
(204, 5, 2),
(205, 5, 12),
(206, 4, 1),
(207, 4, 2),
(208, 4, 12),
(209, 10, 1),
(210, 10, 2),
(211, 10, 12),
(212, 11, 1),
(213, 11, 2),
(214, 11, 12);

-- --------------------------------------------------------

--
-- Table structure for table `schedule_calendars`
--

CREATE TABLE `schedule_calendars` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Relasi manual ke users.id',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Meeting, PM, CM, Audit, Training, Deadline, dll',
  `priority` enum('Low','Medium','High','Critical') COLLATE utf8mb4_unicode_ci DEFAULT 'Medium',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `all_day` tinyint(1) DEFAULT '0',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#2563EB',
  `reminder_minutes` int(11) DEFAULT '30' COMMENT 'Menit sebelum agenda',
  `repeat_type` enum('None','Daily','Weekly','Monthly','Yearly') COLLATE utf8mb4_unicode_ci DEFAULT 'None',
  `repeat_until` date DEFAULT NULL,
  `visibility` enum('private','drive','public') COLLATE utf8mb4_unicode_ci DEFAULT 'private',
  `attachment_document_id` int(11) DEFAULT NULL COMMENT 'Relasi manual ke documents.id',
  `is_completed` tinyint(1) DEFAULT '0',
  `completed_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedule_calendars`
--

INSERT INTO `schedule_calendars` (`id`, `user_id`, `title`, `description`, `location`, `category`, `priority`, `start_date`, `end_date`, `start_time`, `end_time`, `all_day`, `color`, `reminder_minutes`, `repeat_type`, `repeat_until`, `visibility`, `attachment_document_id`, `is_completed`, `completed_at`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 1, 'tes', 'wd', 'm', NULL, 'Medium', '2026-08-03', '2026-08-03', '08:00:00', '17:00:00', 0, '#3788d8', 30, 'None', NULL, 'public', NULL, 0, NULL, 8, '2026-08-03 06:47:28', '2026-08-14 06:35:59', '2026-08-14 06:35:59', 1, 1),
(2, 1, 'Test Agenda Gambar', 'ddd', 'mps', NULL, 'Medium', '2026-08-07', '2026-08-07', '09:00:00', '10:00:00', 0, '#3788d8', 30, 'None', NULL, 'public', NULL, 0, NULL, 1, '2026-08-07 05:39:14', '2026-08-07 05:42:57', NULL, 1, 1),
(3, 1, 'dsddd', 'dddd', 'mps', NULL, 'Medium', '2026-08-04', '2026-08-04', '09:00:00', '10:00:00', 0, '#3788d8', 30, 'None', NULL, 'public', NULL, 0, NULL, 1, '2026-08-14 06:35:45', NULL, NULL, 1, NULL),
(4, 1, 'fgn', 'gfn', 'gn', NULL, 'Medium', '2026-08-05', '2026-08-06', '09:00:00', '10:00:00', 0, '#3788d8', 30, 'None', NULL, 'private', NULL, 0, NULL, 1, '2026-08-14 06:36:31', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shared_access`
--

CREATE TABLE `shared_access` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'document, folder, drive',
  `entity_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'User yang diberi akses',
  `shared_by` int(11) DEFAULT NULL COMMENT 'User yang membagikan',
  `permission` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'view' COMMENT 'view, edit, manage',
  `expires_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_menus`
--

CREATE TABLE `system_menus` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_num` int(11) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '1',
  `menu_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'sidebar' COMMENT 'sidebar, header',
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_menus`
--

INSERT INTO `system_menus` (`id`, `name`, `url`, `icon`, `parent_id`, `order_num`, `is_active`, `menu_type`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'Dashboard', 'dashboard', 'fa-solid fa-gauge-high', NULL, 1, 1, 'sidebar', 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(2, 'My Drive', 'drive', 'fa-solid fa-hard-drive', NULL, 2, 1, 'sidebar', 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(3, 'Favorites', 'favorite', 'fa-solid fa-star', NULL, 3, 1, 'sidebar', 8, '2026-07-22 14:48:47', NULL, '2026-07-23 06:29:49', NULL, 1),
(4, 'Shared with Me', 'shared', 'fa-solid fa-share-nodes', NULL, 4, 1, 'sidebar', 8, '2026-07-22 14:48:47', NULL, '2026-07-22 10:21:39', NULL, 1),
(5, 'Recent', 'recent', 'fa-solid fa-clock-rotate-left', NULL, 5, 1, 'sidebar', 8, '2026-07-22 14:48:47', NULL, '2026-07-22 10:21:57', NULL, 1),
(6, 'Recycle Bin', 'recycle_bin', 'fa-solid fa-trash-can', NULL, 6, 1, 'sidebar', 1, '2026-07-22 14:48:47', '2026-07-23 06:30:36', NULL, NULL, 1),
(7, 'Activity Log', 'activity_log', 'fa-solid fa-list-check', NULL, 7, 1, 'sidebar', 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(8, 'Notifications', 'notification', 'fa-solid fa-bell', NULL, 8, 1, 'sidebar', 8, '2026-07-22 14:48:47', NULL, '2026-07-23 06:29:57', NULL, 1),
(9, 'User Management', 'users', 'fa-solid fa-users', NULL, 9, 1, 'sidebar', 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(10, 'Menu Management', 'menus', 'fa-solid fa-bars', NULL, 10, 1, 'sidebar', 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(11, 'Search', 'search', 'fa-solid fa-magnifying-glass', NULL, 11, 0, 'sidebar', 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(12, 'Schedule', 'schedule', 'fa-regular fa-calendar-alt', NULL, 12, 1, 'sidebar', 1, NULL, '2026-07-24 05:13:33', NULL, NULL, 1),
(13, 'AI Assistant', 'ai_chat', 'fa-solid fa-robot', NULL, 90, 1, 'sidebar', 1, NULL, NULL, NULL, NULL, NULL),
(14, 'AI Analytics', 'ai_dashboard', 'fa-solid fa-chart-pie', NULL, 91, 1, 'sidebar', 1, NULL, NULL, NULL, NULL, NULL),
(15, 'AI Knowledge Base', 'ai_document', 'fa-solid fa-folder-tree', NULL, 92, 1, 'sidebar', 1, NULL, NULL, NULL, NULL, NULL),
(16, 'AI Settings', 'ai_setting', 'fa-solid fa-sliders', NULL, 93, 1, 'sidebar', 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `drive_id` int(11) DEFAULT NULL COMMENT 'Default drive assignment',
  `last_login` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `phone`, `avatar`, `role_id`, `drive_id`, `last_login`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'Administrator', 'admin', 'admin@edrive.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 1, NULL, '2026-08-14 10:49:12', 1, '2026-07-22 14:48:47', NULL, NULL, NULL, NULL),
(2, 'Muh. Jupri', 'Muh. Jupri', '', '$2y$10$TBmv6c1/Oaw/5Ei0mVSUr.eyRsrmaqSLFkFGJC1O9R6wdfJ43o1Yu', '', NULL, 2, NULL, NULL, 1, '2026-07-22 13:47:39', NULL, NULL, 1, 1),
(3, 'Reynaldi Erlangga', 'Reynaldi Erlangga', '', '$2y$10$kBoBqB5cZIb58NwZj7I5uuU2VpP/W/I/DbBhXPhgSy1t8hyEByvCS', '', NULL, 3, NULL, '2026-07-23 12:19:17', 1, '2026-07-22 13:48:03', NULL, NULL, 1, 1),
(4, 'Rico Pratama', 'Rico Pratama', '', '$2y$10$ReCyi15kxI7vl1UEwHOgz.23pSoy2qzHi.4G01Ls7.41c8ui.bPCm', '', NULL, 4, NULL, '2026-08-03 14:44:15', 1, '2026-07-22 13:48:23', NULL, NULL, 1, 1),
(5, 'Moch Alief Fikriansyah', 'alief', '', '$2y$10$gD7w9pUM2K1v05BZNLglA.oGFijkIKO8pq6sdN291DoVGL93Pj/Tu', '', NULL, 1, NULL, NULL, 1, '2026-08-07 04:56:07', NULL, NULL, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `ai_conversations`
--
ALTER TABLE `ai_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_assistant_type` (`assistant_type`);

--
-- Indexes for table `ai_documents`
--
ALTER TABLE `ai_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_embedding` (`embedding_status`);

--
-- Indexes for table `ai_embeddings`
--
ALTER TABLE `ai_embeddings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doc_id` (`document_id`);

--
-- Indexes for table `ai_feedback`
--
ALTER TABLE `ai_feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_msg_user` (`message_id`,`user_id`),
  ADD KEY `fk_ai_fb_user` (`user_id`);

--
-- Indexes for table `ai_messages`
--
ALTER TABLE `ai_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conv_id` (`conversation_id`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `ai_prompts`
--
ALTER TABLE `ai_prompts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_name` (`name`);

--
-- Indexes for table `ai_settings`
--
ALTER TABLE `ai_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_key` (`key`);

--
-- Indexes for table `ai_tools`
--
ALTER TABLE `ai_tools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_name` (`name`);

--
-- Indexes for table `ai_usage`
--
ALTER TABLE `ai_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_type_date` (`user_id`,`assistant_type`,`date`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drive_folder` (`drive_id`,`folder_id`),
  ADD KEY `idx_folder` (`folder_id`),
  ADD KEY `idx_file_type` (`file_type`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_document` (`document_id`);

--
-- Indexes for table `drives`
--
ALTER TABLE `drives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_entity` (`user_id`,`entity_type`,`entity_id`);

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drive_parent` (`drive_id`,`parent_id`),
  ADD KEY `idx_parent` (`parent_id`);

--
-- Indexes for table `gambar_schdule_calendars`
--
ALTER TABLE `gambar_schdule_calendars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_schedule_id` (`schedule_calendars_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_menu_access`
--
ALTER TABLE `role_menu_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule_calendars`
--
ALTER TABLE `schedule_calendars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shared_access`
--
ALTER TABLE `shared_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `system_menus`
--
ALTER TABLE `system_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `ai_conversations`
--
ALTER TABLE `ai_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `ai_documents`
--
ALTER TABLE `ai_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_embeddings`
--
ALTER TABLE `ai_embeddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_feedback`
--
ALTER TABLE `ai_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_messages`
--
ALTER TABLE `ai_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `ai_prompts`
--
ALTER TABLE `ai_prompts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ai_settings`
--
ALTER TABLE `ai_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ai_tools`
--
ALTER TABLE `ai_tools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ai_usage`
--
ALTER TABLE `ai_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `drives`
--
ALTER TABLE `drives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gambar_schdule_calendars`
--
ALTER TABLE `gambar_schdule_calendars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `role_menu_access`
--
ALTER TABLE `role_menu_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `schedule_calendars`
--
ALTER TABLE `schedule_calendars`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shared_access`
--
ALTER TABLE `shared_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_menus`
--
ALTER TABLE `system_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_conversations`
--
ALTER TABLE `ai_conversations`
  ADD CONSTRAINT `fk_ai_conv_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_embeddings`
--
ALTER TABLE `ai_embeddings`
  ADD CONSTRAINT `fk_ai_emb_doc` FOREIGN KEY (`document_id`) REFERENCES `ai_documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_feedback`
--
ALTER TABLE `ai_feedback`
  ADD CONSTRAINT `fk_ai_fb_msg` FOREIGN KEY (`message_id`) REFERENCES `ai_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ai_fb_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_messages`
--
ALTER TABLE `ai_messages`
  ADD CONSTRAINT `fk_ai_msg_conv` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_usage`
--
ALTER TABLE `ai_usage`
  ADD CONSTRAINT `fk_ai_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gambar_schdule_calendars`
--
ALTER TABLE `gambar_schdule_calendars`
  ADD CONSTRAINT `fk_gambar_schedule` FOREIGN KEY (`schedule_calendars_id`) REFERENCES `schedule_calendars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
