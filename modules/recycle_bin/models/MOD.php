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
        // 1. Delete physical main file
        $doc = $this->db->query("SELECT file_path FROM documents WHERE id = ?", [$doc_id])->fetch();
        if ($doc && !empty($doc->file_path)) {
            $full_path = STORAGEPATH . $doc->file_path;
            if (file_exists($full_path)) {
                @unlink($full_path);
            }
        }

        // 2. Delete physical version files
        $versions = $this->db->query("SELECT file_path FROM document_versions WHERE document_id = ?", [$doc_id])->fetchAll();
        foreach ($versions as $v) {
            if (!empty($v->file_path)) {
                $full_path = STORAGEPATH . $v->file_path;
                if (file_exists($full_path)) {
                    @unlink($full_path);
                }
            }
        }

        // 3. Remove DB rows
        $this->db->query("DELETE FROM document_versions WHERE document_id = ?", [$doc_id]);
        $this->db->query("DELETE FROM favorites WHERE entity_type = 'document' AND entity_id = ?", [$doc_id]);
        return $this->db->query("DELETE FROM documents WHERE id = ?", [$doc_id]);
    }

    public function get_deleted_folders($drive_ids, $is_admin = false) {
        $where = "f.status = 8";
        if (!$is_admin) {
            if (empty($drive_ids)) return [];
            $in_drives = implode(',', $drive_ids);
            $where .= " AND f.drive_id IN ($in_drives)";
        }
        
        return $this->db->query("
            SELECT f.*, dr.name as drive_name, u.name as deleted_by_name
            FROM folders f
            LEFT JOIN drives dr ON f.drive_id = dr.id
            LEFT JOIN users u ON f.updated_by = u.id
            WHERE $where
            ORDER BY f.deleted_at DESC
        ")->fetchAll();
    }
    
    public function restore_folder($folder_id) {
        $get_all_folder_ids = function($f_id) use (&$get_all_folder_ids) {
            $ids = [(int)$f_id];
            $subfolders = $this->db->query("SELECT id FROM folders WHERE parent_id = ?", [$f_id])->fetchAll();
            foreach ($subfolders as $sf) {
                $ids = array_merge($ids, $get_all_folder_ids($sf->id));
            }
            return $ids;
        };

        $all_folder_ids = $get_all_folder_ids($folder_id);

        if (!empty($all_folder_ids)) {
            $in_ids = implode(',', array_map('intval', $all_folder_ids));
            $this->db->query("UPDATE folders SET status = 1 WHERE id IN ($in_ids)");
            $this->db->query("UPDATE documents SET status = 1 WHERE folder_id IN ($in_ids)");
        }

        return true;
    }
    
    public function permanent_delete_folder($folder_id) {
        // Collect all folder IDs recursively
        $get_all_folder_ids = function($f_id) use (&$get_all_folder_ids) {
            $ids = [(int)$f_id];
            $subfolders = $this->db->query("SELECT id FROM folders WHERE parent_id = ?", [$f_id])->fetchAll();
            foreach ($subfolders as $sf) {
                $ids = array_merge($ids, $get_all_folder_ids($sf->id));
            }
            return $ids;
        };

        $all_folder_ids = $get_all_folder_ids($folder_id);

        if (!empty($all_folder_ids)) {
            $in_ids = implode(',', array_map('intval', $all_folder_ids));

            // Permanently delete all documents inside these folders (safely removes physical files)
            $docs = $this->db->query("SELECT id FROM documents WHERE folder_id IN ($in_ids)")->fetchAll();
            foreach ($docs as $doc) {
                $this->permanent_delete($doc->id);
            }

            // Remove DB records
            $this->db->query("DELETE FROM favorites WHERE entity_type = 'folder' AND entity_id IN ($in_ids)");
            $this->db->query("DELETE FROM folders WHERE id IN ($in_ids)");
        }

        return true;
    }
}
