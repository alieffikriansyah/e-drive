<?php
class MOD extends Model {
    public function search_documents($query_string, $drive_ids) {
        if (empty($drive_ids)) return [];
        
        $in_drives = implode(',', $drive_ids);
        $like_q = "%$query_string%";
        
        return $this->db->query("
            SELECT d.*, dr.name as drive_name, f.name as folder_name, u.name as uploader_name
            FROM documents d
            LEFT JOIN drives dr ON d.drive_id = dr.id
            LEFT JOIN folders f ON d.folder_id = f.id
            LEFT JOIN users u ON d.created_by = u.id
            WHERE d.status = 1 
              AND d.drive_id IN ($in_drives)
              AND (d.name LIKE ? OR d.original_name LIKE ? OR d.tags LIKE ?)
            ORDER BY d.created_at DESC
            LIMIT 50
        ", [$like_q, $like_q, $like_q])->fetchAll();
    }
}
