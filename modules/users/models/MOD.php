<?php

class MOD extends Model {
    public function get_all_users() {
        return $this->db->query("
            SELECT u.id, u.username, u.name, u.email, u.phone, u.status, u.last_login, r.role_name, d.name as drive_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN drives d ON u.drive_id = d.id
            WHERE u.status != 8
            ORDER BY u.created_at DESC
        ")->fetchAll();
    }

    public function get_user_by_id($id) {
        return $this->db->query("
            SELECT id, username, name, email, phone, role_id, drive_id, status 
            FROM users 
            WHERE id = ?
        ", [$id])->fetch(PDO::FETCH_ASSOC);
    }

    public function check_username_exists($username, $exclude_id = null) {
        if ($exclude_id) {
            return $this->db->query("SELECT id FROM users WHERE username = ? AND id != ? AND status != 8", [$username, $exclude_id])->fetch();
        }
        return $this->db->query("SELECT id FROM users WHERE username = ? AND status != 8", [$username])->fetch();
    }

    public function insert_user($data) {
        return $this->db->table('users')->insert($data);
    }

    public function update_user($id, $data) {
        return $this->db->table('users')->where('id', $id)->update($data);
    }

    public function get_active_roles() {
        return $this->db->query("SELECT id, role_name FROM roles WHERE status = 1 ORDER BY id ASC")->fetchAll();
    }

    public function get_active_drives() {
        return $this->db->query("SELECT id, name FROM drives WHERE status = 1")->fetchAll();
    }
}
