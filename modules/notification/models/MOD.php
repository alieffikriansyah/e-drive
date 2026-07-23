<?php
class MOD extends Model {
    public function get_user_notifications($user_id, $limit = 50) {
        return $this->db->query("
            SELECT * FROM notifications 
            WHERE user_id = ? AND status = 1
            ORDER BY created_at DESC 
            LIMIT $limit
        ", [$user_id])->fetchAll();
    }
    
    public function mark_all_read($user_id) {
        return $this->db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$user_id]);
    }
}
