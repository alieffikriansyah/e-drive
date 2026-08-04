<?php

class AiDashboard extends Controller {

    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
    }

    public function index() {
        $user_id = Session::get('user_id');
        $is_admin = AuthMiddleware::isManager();

        // 1. Stats overview
        $total_conversations = $this->db->query("SELECT COUNT(*) as cnt FROM ai_conversations WHERE status = 1")->row()->cnt ?? 0;
        $total_messages = $this->db->query("SELECT COUNT(*) as cnt FROM ai_messages WHERE status = 1")->row()->cnt ?? 0;
        $total_tokens = $this->db->query("SELECT SUM(total_tokens) as total FROM ai_usage")->row()->total ?? 0;
        $total_kb_docs = $this->db->query("SELECT COUNT(*) as cnt FROM ai_documents WHERE status = 1")->row()->cnt ?? 0;

        // 2. Daily token usage chart data
        $chart_data = $this->db->query("
            SELECT date, SUM(total_tokens) as tokens, SUM(total_requests) as requests
            FROM ai_usage
            GROUP BY date
            ORDER BY date ASC
            LIMIT 14
        ")->fetchAll();

        // 3. Feedback distribution
        $feedback_stats = $this->db->query("
            SELECT 
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as thumbs_up,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as thumbs_down
            FROM ai_feedback
        ")->fetch();

        // 4. Recent conversations
        $recent_conversations = $this->db->query("
            SELECT c.*, u.name as user_name
            FROM ai_conversations c
            JOIN users u ON c.user_id = u.id
            WHERE c.status = 1
            ORDER BY c.updated_at DESC
            LIMIT 5
        ")->fetchAll();

        $data = [
            'title' => '🤖 AI Monitoring & Analytics Dashboard',
            'total_conversations' => $total_conversations,
            'total_messages' => $total_messages,
            'total_tokens' => $total_tokens,
            'total_kb_docs' => $total_kb_docs,
            'chart_data' => $chart_data,
            'feedback_stats' => $feedback_stats,
            'recent_conversations' => $recent_conversations,
            'is_admin' => $is_admin
        ];

        $this->load->view('ai_dashboard/v_index', $data);
    }
}
