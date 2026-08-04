<?php

class MOD extends Model {

    public function get_all_menus() {
        $db = Database::get_instance();
        return $db->query("SELECT * FROM system_menus WHERE status != 8 ORDER BY order_num ASC")->fetchAll();
    }

    public function get_menu_by_id($id) {
        $db = Database::get_instance();
        return $db->query("SELECT * FROM system_menus WHERE id = ? AND status != 8", [$id])->fetch();
    }

    public function toggle_status($id) {
        $db = Database::get_instance();
        $db->query("UPDATE system_menus SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?", [$id]);
        return true;
    }

    public function update_menu($id, $data) {
        $db = Database::get_instance();
        $db->query("
            UPDATE system_menus 
            SET name = ?, icon = ?, order_num = ?
            WHERE id = ?
        ", [$data['name'], $data['icon'], $data['order_num'], $id]);
        return true;
    }

    public function get_roles() {
        $db = Database::get_instance();
        return $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
    }

    public function get_role_access() {
        $db = Database::get_instance();
        return $db->query("SELECT * FROM role_menu_access")->fetchAll();
    }

    public function update_role_access($role_id, $menu_ids) {
        $db = Database::get_instance();
        $db->query("DELETE FROM role_menu_access WHERE role_id = ?", [$role_id]);
        
        foreach ($menu_ids as $menu_id) {
            $db->query("INSERT IGNORE INTO role_menu_access (role_id, menu_id) VALUES (?, ?)", [$role_id, $menu_id]);
        }
        return true;
    }
}
