<?php
class MOD extends Model {
    public function get_deleted_documents($drive_ids, $is_admin = false) {
        $where = "d.status = 8";
        if (!$is_admin) {
            if (empty($drive_ids)) return [];
            $in_drives = implode(',', $drive_ids);
            $where .= " AND d.drive_id IN ($in_drives)";
        }
        
        return $this->db->query("
            SELECT d.*, dr.name as drive_name, u.name as deleted_by_name
            FROM documents d
            LEFT JOIN drives dr ON d.drive_id = dr.id
            LEFT JOIN users u ON d.updated_by = u.id
            WHERE $where
            ORDER BY d.updated_at DESC
        ")->fetchAll();
    }
    
    public function restore_document($doc_id) {
        return $this->db->table('documents')->where('id', $doc_id)->update(['status' => 1]);
    }
    
    public function permanent_delete($doc_id) {
        $this->db->query("DELETE FROM document_versions WHERE document_id = ?", [$doc_id]);
        $this->db->query("DELETE FROM favorites WHERE entity_type = 'document' AND entity_id = ?", [$doc_id]);
        return $this->db->query("DELETE FROM documents WHERE id = ?", [$doc_id]);
    }
}
