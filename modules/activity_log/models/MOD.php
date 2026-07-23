<?php
class MOD extends Model {
    public function get_logs($is_admin, $user_id, $limit, $offset) {
        $sql = "
            SELECT a.*, u.name as user_name, u.avatar
            FROM activity_logs a
            JOIN users u ON a.user_id = u.id
        ";
        if (!$is_admin) {
            $sql .= " WHERE a.user_id = $user_id";
        }
        $sql .= " ORDER BY a.created_at DESC LIMIT $limit OFFSET $offset";
        return $this->db->query($sql)->fetchAll();
    }
    
    public function get_total_count($is_admin, $user_id) {
        $count_sql = "SELECT COUNT(*) as total FROM activity_logs";
        if (!$is_admin) {
            $count_sql .= " WHERE user_id = $user_id";
        }
        return $this->db->query($count_sql)->row()->total;
    }
}
