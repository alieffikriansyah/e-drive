<?php

class Dashboard extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->helper('file');
    }

    public function index() {
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');
        $is_admin = AuthMiddleware::isManager();

        // 1. Storage Stats
        if ($is_admin) {
            $total_docs = $this->db->query("SELECT COUNT(*) as cnt FROM documents WHERE status = 1")->row()->cnt;
            $total_size = $this->db->query("SELECT SUM(file_size) as total FROM documents WHERE status = 1")->row()->total ?? 0;
            $total_drives = $this->db->query("SELECT COUNT(*) as cnt FROM drives WHERE status = 1")->row()->cnt;
            $total_users = $this->db->query("SELECT COUNT(*) as cnt FROM users WHERE status = 1")->row()->cnt;
        } else {
            // Stats based on accessible drives
            $drives = $this->db->query("
                SELECT id FROM drives 
                WHERE (owner_role_id = ? OR is_shared = 1) AND status = 1
                UNION
                SELECT entity_id as id FROM shared_access 
                WHERE entity_type = 'drive' AND user_id = ? AND status = 1
            ", [$role_id, $user_id])->fetchAll();
            
            $drive_ids = array_column($drives, 'id');
            if (empty($drive_ids)) {
                $total_docs = 0; $total_size = 0; $total_drives = 0;
            } else {
                $in_drives = implode(',', $drive_ids);
                $total_docs = $this->db->query("SELECT COUNT(*) as cnt FROM documents WHERE drive_id IN ($in_drives) AND status = 1")->row()->cnt;
                $total_size = $this->db->query("SELECT SUM(file_size) as total FROM documents WHERE drive_id IN ($in_drives) AND status = 1")->row()->total ?? 0;
                $total_drives = count($drive_ids);
            }
            $total_users = 0; // Not relevant for non-admins
        }

        // 2. Recent Documents
        $recent_docs_sql = "
            SELECT d.*, f.name as folder_name, dr.name as drive_name, u.name as uploader_name
            FROM documents d
            LEFT JOIN folders f ON d.folder_id = f.id
            LEFT JOIN drives dr ON d.drive_id = dr.id
            LEFT JOIN users u ON d.created_by = u.id
            WHERE d.status = 1
        ";
        
        if (!$is_admin && !empty($drive_ids)) {
            $recent_docs_sql .= " AND d.drive_id IN (" . implode(',', $drive_ids) . ")";
        } elseif (!$is_admin && empty($drive_ids)) {
            $recent_docs_sql .= " AND 1=0"; // Return empty
        }
        
        $recent_docs_sql .= " ORDER BY d.created_at DESC LIMIT 5";
        $recent_docs = $this->db->query($recent_docs_sql)->fetchAll();

        // 3. Activity Log
        $activities = $this->db->query("
            SELECT a.*, u.name as user_name, u.avatar
            FROM activity_logs a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC LIMIT 6
        ")->fetchAll();

        // 4. Chart Data (Storage per Drive) - Top 5
        $chart_sql = "
            SELECT dr.name, SUM(d.file_size) as total_size
            FROM drives dr
            LEFT JOIN documents d ON dr.id = d.drive_id AND d.status = 1
            WHERE dr.status = 1
        ";
        if (!$is_admin && !empty($drive_ids)) {
            $chart_sql .= " AND dr.id IN (" . implode(',', $drive_ids) . ")";
        } elseif (!$is_admin && empty($drive_ids)) {
            $chart_sql .= " AND 1=0";
        }
        $chart_sql .= " GROUP BY dr.id ORDER BY total_size DESC LIMIT 5";
        $chart_data = $this->db->query($chart_sql)->fetchAll();

        $data = [
            'title'        => 'Dashboard',
            'total_docs'   => $total_docs,
            'total_size'   => $total_size,
            'total_drives' => $total_drives,
            'total_users'  => $total_users,
            'recent_docs'  => $recent_docs,
            'activities'   => $activities,
            'chart_data'   => $chart_data,
            'is_admin'     => $is_admin
        ];

        $this->load->view('dashboard/v_index', $data);
    }
}
