<?php

class MOD extends Model {
    
    public function get_accessible_drives($is_admin, $user_id, $role_id) {
        $sql = "
            SELECT d.*, 
                   (SELECT COUNT(*) FROM folders f WHERE f.drive_id = d.id AND f.status = 1) as folder_count,
                   (SELECT COUNT(*) FROM documents doc WHERE doc.drive_id = d.id AND doc.status = 1) as doc_count,
                   (SELECT SUM(file_size) FROM documents doc WHERE doc.drive_id = d.id AND doc.status = 1) as used_size,
                   r.role_name as owner_role
            FROM drives d
            LEFT JOIN roles r ON d.owner_role_id = r.id
            WHERE d.status = 1
        ";

        if (!$is_admin) {
            $sql .= " AND (d.owner_role_id = $role_id OR d.is_shared = 1 
                           OR d.id IN (SELECT entity_id FROM shared_access WHERE entity_type = 'drive' AND user_id = $user_id AND status = 1))";
        }
        
        $sql .= " ORDER BY d.order_num ASC";
        
        return $this->db->query($sql)->fetchAll();
    }

    public function get_drive_by_id($drive_id) {
        return $this->db->table('drives')->where('id', $drive_id)->row();
    }

    public function get_folders_in_drive($drive_id, $parent_folder_id = null) {
        $this->db->table('folders f')
                 ->select('f.*, 
                           ((SELECT COUNT(*) FROM folders sub WHERE sub.parent_id = f.id AND sub.status = 1) + 
                           (SELECT COUNT(*) FROM documents doc WHERE doc.folder_id = f.id AND doc.status = 1)) as dynamic_total_files')
                 ->where('f.drive_id', $drive_id)
                 ->where('f.status', 1);
        if ($parent_folder_id) {
            $this->db->where('f.parent_id', $parent_folder_id);
        } else {
            $this->db->where('f.parent_id IS NULL');
        }
        return $this->db->order_by('f.name', 'ASC')->result();
    }

    public function get_documents_in_drive($drive_id, $folder_id = null) {
        $this->db->table('documents')
                 ->where('drive_id', $drive_id)
                 ->where('status', 1);
        if ($folder_id) {
            $this->db->where('folder_id', $folder_id);
        } else {
            $this->db->where('folder_id IS NULL');
        }
        return $this->db->order_by('created_at', 'DESC')->result();
    }

    public function get_all_folders_by_drive($drive_id) {
        return $this->db->table('folders')
                 ->where('drive_id', $drive_id)
                 ->where('status', 1)
                 ->order_by('name', 'ASC')
                 ->result();
    }
}
