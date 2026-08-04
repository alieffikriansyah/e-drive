<?php
class MOD extends Model {
    public function insert_folder($data) {
        return $this->db->table('folders')->insert($data);
    }
    
    public function get_folder_by_id($id) {
        return $this->db->table('folders')->where('id', $id)->where('status', 1)->row();
    }
    
    public function delete_folder($id) {
        $user_id = Session::get('user_id');
        $now = date('Y-m-d H:i:s');

        // Helper to collect all child folder IDs recursively
        $get_all_folder_ids = function($folder_id) use (&$get_all_folder_ids) {
            $ids = [(int)$folder_id];
            $subfolders = $this->db->query("SELECT id FROM folders WHERE parent_id = ?", [$folder_id])->fetchAll();
            foreach ($subfolders as $sf) {
                $ids = array_merge($ids, $get_all_folder_ids($sf->id));
            }
            return $ids;
        };

        $all_folder_ids = $get_all_folder_ids($id);

        if (!empty($all_folder_ids)) {
            $in_ids = implode(',', array_map('intval', $all_folder_ids));

            // 1. Soft delete all folders in hierarchy
            $this->db->query("UPDATE folders SET status = 8, deleted_at = ?, updated_by = ? WHERE id IN ($in_ids)", [$now, $user_id]);

            // 2. Soft delete all documents in hierarchy
            $this->db->query("UPDATE documents SET status = 8, updated_at = ?, updated_by = ? WHERE folder_id IN ($in_ids)", [$now, $user_id]);
        }

        return true;
    }
}
