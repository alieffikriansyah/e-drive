<?php
class MOD extends Model {
    public function insert_folder($data) {
        return $this->db->table('folders')->insert($data);
    }
    
    public function get_folder_by_id($id) {
        return $this->db->table('folders')->where('id', $id)->where('status', 1)->row();
    }
    
    public function delete_folder($id) {
        return $this->db->table('folders')->where('id', $id)->update([
            'status' => 8,
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_by' => Session::get('user_id')
        ]);
    }
}
