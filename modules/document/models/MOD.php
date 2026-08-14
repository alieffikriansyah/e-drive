<?php
class MOD extends Model {
    public function insert_document($data) {
        return $this->db->insert('documents', $data);
    }
    
    public function insert_folder($data) {
        return $this->db->table('folders')->insert($data);
    }
    
    public function insert_version($data) {
        return $this->db->insert('document_versions', $data);
    }
    
    public function get_document_by_id($id, $status = 1) {
        return $this->db->table('documents')->where('id', $id)->where('status', $status)->row();
    }
    
    public function update_document($id, $data) {
        return $this->db->table('documents')->where('id', $id)->update($data);
    }
    
    public function delete_document($id) {
        return $this->db->table('documents')->where('id', $id)->update([
            'status' => 8,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Session::get('user_id')
        ]);
    }
}
