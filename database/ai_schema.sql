-- ================================================================
-- E-Drive AI Module — Database Schema
-- Prefix: ai_
-- Database: e_drive
-- ================================================================

-- ─── 1. ai_conversations ───
CREATE TABLE IF NOT EXISTS `ai_conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL DEFAULT 'New Chat',
    `assistant_type` ENUM('general','edrive') NOT NULL DEFAULT 'general',
    `model_used` VARCHAR(100) DEFAULT 'qwen3',
    `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=active, 8=deleted',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_status` (`user_id`, `status`),
    INDEX `idx_assistant_type` (`assistant_type`),
    CONSTRAINT `fk_ai_conv_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 2. ai_messages ───
CREATE TABLE IF NOT EXISTS `ai_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `role` ENUM('user','assistant','system') NOT NULL,
    `content` LONGTEXT NOT NULL,
    `tokens_in` INT NOT NULL DEFAULT 0,
    `tokens_out` INT NOT NULL DEFAULT 0,
    `model_used` VARCHAR(100) DEFAULT NULL,
    `response_time_ms` INT DEFAULT NULL,
    `metadata` LONGTEXT DEFAULT NULL COMMENT 'Tool calls, sources, citations',
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_conv_id` (`conversation_id`),
    INDEX `idx_role` (`role`),
    CONSTRAINT `fk_ai_msg_conv` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 3. ai_usage ───
CREATE TABLE IF NOT EXISTS `ai_usage` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `assistant_type` ENUM('general','edrive') NOT NULL,
    `total_tokens` INT NOT NULL DEFAULT 0,
    `total_requests` INT NOT NULL DEFAULT 0,
    `date` DATE NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_type_date` (`user_id`, `assistant_type`, `date`),
    CONSTRAINT `fk_ai_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 4. ai_prompts ───
CREATE TABLE IF NOT EXISTS `ai_prompts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `assistant_type` ENUM('general','edrive') NOT NULL,
    `content` TEXT NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` INT DEFAULT NULL,
    `updated_by` INT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 5. ai_documents ───
CREATE TABLE IF NOT EXISTS `ai_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` BIGINT NOT NULL DEFAULT 0,
    `chunk_count` INT NOT NULL DEFAULT 0,
    `embedding_status` ENUM('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
    `collection_name` VARCHAR(100) DEFAULT 'edrive_knowledge',
    `category` VARCHAR(100) DEFAULT NULL COMMENT 'SOP, Tutorial, FAQ, etc.',
    `uploaded_by` INT DEFAULT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_embedding` (`embedding_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 6. ai_embeddings ───
CREATE TABLE IF NOT EXISTS `ai_embeddings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT NOT NULL,
    `chunk_index` INT NOT NULL DEFAULT 0,
    `chunk_text` TEXT NOT NULL,
    `vector_id` VARCHAR(255) DEFAULT NULL COMMENT 'ID in vector DB',
    `token_count` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_doc_id` (`document_id`),
    CONSTRAINT `fk_ai_emb_doc` FOREIGN KEY (`document_id`) REFERENCES `ai_documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 7. ai_feedback ───
CREATE TABLE IF NOT EXISTS `ai_feedback` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `message_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` TINYINT NOT NULL COMMENT '1=thumbs_down, 2=thumbs_up',
    `comment` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_msg_user` (`message_id`, `user_id`),
    CONSTRAINT `fk_ai_fb_msg` FOREIGN KEY (`message_id`) REFERENCES `ai_messages`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ai_fb_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 8. ai_settings ───
CREATE TABLE IF NOT EXISTS `ai_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `updated_by` INT DEFAULT NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 9. ai_tools ───
CREATE TABLE IF NOT EXISTS `ai_tools` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `endpoint` VARCHAR(255) NOT NULL,
    `method` VARCHAR(10) NOT NULL DEFAULT 'GET',
    `parameters` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- DEFAULT DATA
-- ================================================================

-- ─── Default Settings ───
INSERT INTO `ai_settings` (`key`, `value`, `description`, `group`) VALUES
('flowise_url', 'http://localhost:3000', 'Flowise server URL', 'connection'),
('flowise_api_key', '', 'Flowise API key', 'connection'),
('flowise_flow_a_id', '', 'Flowise Flow ID for General AI Assistant', 'connection'),
('flowise_flow_b_id', '', 'Flowise Flow ID for E-Drive Assistant', 'connection'),
('ollama_url', 'http://localhost:11434', 'Ollama server URL', 'connection'),
('default_model', 'qwen3', 'Default AI model name', 'model'),
('edrive_model', 'qwen3', 'Model for E-Drive Assistant', 'model'),
('max_tokens', '2048', 'Maximum tokens per response', 'model'),
('temperature', '0.7', 'Model temperature (0-1)', 'model'),
('vector_db_type', 'chromadb', 'Vector database type: chromadb or qdrant', 'connection'),
('vector_db_url', 'http://localhost:8000', 'Vector database URL', 'connection'),
('internal_api_key', '', 'API key for internal REST API (auto-generated)', 'security'),
('rate_limit_per_minute', '60', 'Max AI requests per minute per user', 'security'),
('streaming_enabled', '1', 'Enable streaming responses (SSE)', 'feature'),
('feedback_enabled', '1', 'Enable feedback buttons on AI responses', 'feature'),
('history_retention_days', '90', 'Days to retain conversation history', 'feature')
ON DUPLICATE KEY UPDATE `key` = `key`;

-- ─── Default System Prompts ───
INSERT INTO `ai_prompts` (`name`, `assistant_type`, `content`, `is_active`) VALUES
('general_system', 'general', 'Kamu adalah AI Assistant yang membantu menjawab pertanyaan umum. Berikan jawaban yang jelas, akurat, dan mudah dipahami dalam bahasa Indonesia. Gunakan format Markdown untuk jawaban yang terstruktur.', 1),
('edrive_system', 'edrive', 'Kamu adalah E-Drive Assistant, asisten AI khusus untuk aplikasi E-Drive — Enterprise Document Management System. Kamu membantu user dengan:\n- Tutorial penggunaan E-Drive (upload, download, folder, drive)\n- SOP dan FAQ terkait manajemen dokumen\n- Informasi storage, file, dan jadwal\n- Troubleshooting masalah umum\n\nGunakan data dari tools yang tersedia untuk memberikan jawaban akurat. Jawab dalam bahasa Indonesia dengan format Markdown.', 1)
ON DUPLICATE KEY UPDATE `name` = `name`;

-- ─── Default Tools (REST API Endpoints) ───
INSERT INTO `ai_tools` (`name`, `description`, `endpoint`, `method`, `parameters`, `is_active`) VALUES
('get_storage_stats', 'Mendapatkan statistik penyimpanan: total storage, jumlah dokumen, jumlah drive', '/ai_api/storage_stats', 'GET', '{}', 1),
('get_dashboard_summary', 'Mendapatkan ringkasan dashboard: statistik dan aktivitas terbaru', '/ai_api/dashboard_summary', 'GET', '{}', 1),
('get_recent_documents', 'Mendapatkan daftar 10 dokumen terbaru yang diupload', '/ai_api/recent_documents', 'GET', '{}', 1),
('search_documents', 'Mencari dokumen berdasarkan nama atau tipe file', '/ai_api/search_documents', 'GET', '{"q": "string (search query)"}', 1),
('get_drive_list', 'Mendapatkan daftar semua drive yang tersedia', '/ai_api/drive_list', 'GET', '{}', 1),
('get_schedule_events', 'Mendapatkan agenda/jadwal mendatang', '/ai_api/schedule_events', 'GET', '{}', 1),
('get_schedule_today', 'Mendapatkan agenda hari ini', '/ai_api/schedule_today', 'GET', '{}', 1),
('get_user_activity', 'Mendapatkan log aktivitas user terbaru', '/ai_api/user_activity', 'GET', '{}', 1),
('get_statistics', 'Mendapatkan statistik sistem (storage per drive, chart data)', '/ai_api/statistics', 'GET', '{}', 1),
('get_help_topics', 'Mendapatkan daftar topik bantuan E-Drive', '/ai_api/help_topics', 'GET', '{}', 1)
ON DUPLICATE KEY UPDATE `name` = `name`;

-- ─── System Menus Registration ───
INSERT INTO `system_menus` (`name`, `url`, `icon`, `order_num`, `is_active`, `status`) VALUES
('AI Assistant', 'ai_chat', 'fa-solid fa-robot', 90, 1, 1),
('AI Analytics', 'ai_dashboard', 'fa-solid fa-chart-pie', 91, 1, 1),
('AI Knowledge Base', 'ai_document', 'fa-solid fa-folder-tree', 92, 1, 1),
('AI Settings', 'ai_setting', 'fa-solid fa-sliders', 93, 1, 1)
ON DUPLICATE KEY UPDATE `url` = `url`;

-- Grant menu access to Administrator (role_id = 1) and Project Manager (role_id = 2)
INSERT IGNORE INTO `role_menu_access` (`role_id`, `menu_id`)
SELECT r.id, m.id 
FROM `roles` r 
CROSS JOIN `system_menus` m 
WHERE m.url IN ('ai_chat', 'ai_dashboard', 'ai_document', 'ai_setting');
