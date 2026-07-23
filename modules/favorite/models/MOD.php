<?php
class MOD extends Model {
    public function get_user_favorites($user_id) {
        return $this->db->query("
            SELECT d.*, dr.name as drive_name, u.name as uploader_name
            FROM favorites fav
            JOIN documents d ON fav.entity_id = d.id AND fav.entity_type = 'document'
            LEFT JOIN drives dr ON d.drive_id = dr.id
            LEFT JOIN users u ON d.created_by = u.id
            WHERE fav.user_id = ? AND fav.status = 1 AND d.status = 1
            ORDER BY fav.created_at DESC
        ", [$user_id])->fetchAll();
    }
    
    public function check_favorite($user_id, $entity_type, $entity_id) {
        return $this->db->table('favorites')
                 ->where('user_id', $user_id)
                 ->where('entity_type', $entity_type)
                 ->where('entity_id', $entity_id)
                 ->row();
    }
    
    public function update_favorite($id, $data) {
        return $this->db->table('favorites')->where('id', $id)->update($data);
    }
    
    public function insert_favorite($data) {
        return $this->db->insert('favorites', $data);
    }
}
