<?php

class AiApi extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->_authenticate_request();
    }

    /**
     * Authenticate incoming request via API Key or active user session
     */
    private function _authenticate_request() {
        header('Content-Type: application/json');

        // Check X-AI-API-Key header
        $provided_key = $_SERVER['HTTP_X_AI_API_KEY'] ?? $_GET['api_key'] ?? '';
        
        $db = Database::get_instance();
        $setting = $db->query("SELECT `value` FROM ai_settings WHERE `key` = 'internal_api_key'")->fetch();
        $internal_key = $setting ? $setting->value : '';

        if (!empty($internal_key) && $provided_key === $internal_key) {
            return true; // Authenticated via API Key
        }

        // Fallback to active PHP Session if called internally from frontend
        require_once APPPATH . 'core/Session.php';
        if (Session::get('user_id')) {
            return true; // Authenticated via Session
        }

        // Unauthorized
        http_response_code(401);
        echo json_encode([
            'status' => false,
            'message' => 'Unauthorized: Invalid or missing X-AI-API-Key header.'
        ]);
        exit;
    }

    /**
     * Get target user ID from request context (Header, Query, or Session)
     */
    private function _get_user_id() {
        if (isset($_SERVER['HTTP_X_USER_ID'])) {
            return (int)$_SERVER['HTTP_X_USER_ID'];
        }
        if (isset($_GET['user_id'])) {
            return (int)$_GET['user_id'];
        }
        return Session::get('user_id') ?: 1;
    }

    /**
     * GET /ai_api/storage_stats
     */
    public function storage_stats() {
        $user_id = $this->_get_user_id();
        
        $total_docs = $this->db->query("SELECT COUNT(*) as cnt FROM documents WHERE status = 1")->row()->cnt ?? 0;
        $total_size = $this->db->query("SELECT SUM(file_size) as total FROM documents WHERE status = 1")->row()->total ?? 0;
        $total_drives = $this->db->query("SELECT COUNT(*) as cnt FROM drives WHERE status = 1")->row()->cnt ?? 0;

        echo json_encode([
            'status' => true,
            'data' => [
                'total_documents' => (int)$total_docs,
                'total_size_bytes' => (int)$total_size,
                'total_size_formatted' => format_file_size($total_size),
                'total_drives' => (int)$total_drives,
            ]
        ]);
    }

    /**
     * GET /ai_api/dashboard_summary
     */
    public function dashboard_summary() {
        $total_docs = $this->db->query("SELECT COUNT(*) as cnt FROM documents WHERE status = 1")->row()->cnt ?? 0;
        $total_size = $this->db->query("SELECT SUM(file_size) as total FROM documents WHERE status = 1")->row()->total ?? 0;
        $recent_docs = $this->db->query("SELECT id, name, file_type, file_size, created_at FROM documents WHERE status = 1 ORDER BY created_at DESC LIMIT 5")->fetchAll();

        echo json_encode([
            'status' => true,
            'data' => [
                'total_documents' => (int)$total_docs,
                'total_size' => format_file_size($total_size),
                'recent_uploads' => $recent_docs
            ]
        ]);
    }

    /**
     * GET /ai_api/recent_documents
     */
    public function recent_documents() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $docs = $this->db->query("
            SELECT d.id, d.name, d.file_type, d.file_size, d.created_at, u.name as uploader
            FROM documents d
            LEFT JOIN users u ON d.created_by = u.id
            WHERE d.status = 1
            ORDER BY d.created_at DESC
            LIMIT ?
        ", [$limit])->fetchAll();

        foreach ($docs as $d) {
            $d->file_size_formatted = format_file_size($d->file_size);
        }

        echo json_encode([
            'status' => true,
            'data' => $docs
        ]);
    }

    /**
     * GET /ai_api/search_documents?q=query
     */
    public function search_documents() {
        $query = trim($_GET['q'] ?? '');
        if (empty($query)) {
            echo json_encode(['status' => true, 'data' => []]);
            return;
        }

        $docs = $this->db->query("
            SELECT d.id, d.name, d.file_type, d.file_size, d.created_at
            FROM documents d
            WHERE d.status = 1 AND d.name LIKE ?
            ORDER BY d.created_at DESC
            LIMIT 15
        ", ['%' . $query . '%'])->fetchAll();

        foreach ($docs as $d) {
            $d->file_size_formatted = format_file_size($d->file_size);
        }

        echo json_encode([
            'status' => true,
            'data' => $docs
        ]);
    }

    /**
     * GET /ai_api/drive_list
     */
    public function drive_list() {
        $drives = $this->db->query("
            SELECT id, name, description, is_shared, created_at 
            FROM drives 
            WHERE status = 1 
            ORDER BY name ASC
        ")->fetchAll();

        echo json_encode([
            'status' => true,
            'data' => $drives
        ]);
    }

    /**
     * GET /ai_api/schedule_events
     */
    public function schedule_events() {
        $user_id = $this->_get_user_id();
        $events = $this->db->query("
            SELECT id, title, start_date, end_date, is_all_day, color
            FROM schedule_calendars
            WHERE status = 1 AND user_id = ? AND start_date >= CURDATE()
            ORDER BY start_date ASC
            LIMIT 10
        ", [$user_id])->fetchAll();

        echo json_encode([
            'status' => true,
            'data' => $events
        ]);
    }

    /**
     * GET /ai_api/schedule_today
     */
    public function schedule_today() {
        $user_id = $this->_get_user_id();
        $events = $this->db->query("
            SELECT id, title, start_date, end_date, is_all_day, color
            FROM schedule_calendars
            WHERE status = 1 AND user_id = ? AND DATE(start_date) = CURDATE()
            ORDER BY start_date ASC
        ", [$user_id])->fetchAll();

        echo json_encode([
            'status' => true,
            'data' => $events
        ]);
    }

    /**
     * GET /ai_api/role_list
     */
    public function role_list() {
        $roles = $this->db->query("
            SELECT id, role_name, role_description, role_level
            FROM roles
            WHERE status = 1
            ORDER BY id ASC
        ")->fetchAll();

        echo json_encode([
            'status' => true,
            'data' => $roles
        ]);
    }

    /**
     * GET /ai_api/user_activity
     */
    public function user_activity() {
        $user_id = $this->_get_user_id();
        $logs = $this->db->query("
            SELECT action, description, created_at
            FROM activity_logs
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 10
        ", [$user_id])->fetchAll();

        echo json_encode([
            'status' => true,
            'data' => $logs
        ]);
    }

    /**
     * GET /ai_api/help_topics
     */
    public function help_topics() {
        $topics = [
            [
                'topic' => 'Cara Upload Dokumen',
                'description' => 'Masuk ke menu Drive, pilih lokasi folder tujuan, lalu klik tombol Upload Baru di pojok kanan atas atau seret file ke area upload.'
            ],
            [
                'topic' => 'Cara Membagikan File / Drive',
                'description' => 'Klik kanan pada drive atau file yang ingin dibagikan, pilih menu Share/Bagikan, tentukan hak akses (Read-Only/Full), lalu pilih pengguna target.'
            ],
            [
                'topic' => 'Penggunaan Fitur AI Assistant',
                'description' => 'Gunakan AI Assistant untuk pertanyaan umum dan E-Drive Assistant untuk menanyakan data penyimpanan, dokumen, atau SOP E-Drive.'
            ],
            [
                'topic' => 'Fitur Agenda & Scheduler',
                'description' => 'Masuk ke modul Schedule untuk mencatat kegiatan, rapat, atau tenggat waktu dokumen.'
            ]
        ];

        echo json_encode([
            'status' => true,
            'data' => $topics
        ]);
    }
}
