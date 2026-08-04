<?php

class MOD extends Model {
    
    /**
     * Get all settings, optionally filtered by group
     */
    public function get_all($group = null) {
        $db = Database::get_instance();
        if ($group) {
            return $db->query("SELECT * FROM ai_settings WHERE `group` = ? ORDER BY `key` ASC", [$group])->fetchAll();
        }
        return $db->query("SELECT * FROM ai_settings ORDER BY `group` ASC, `key` ASC")->fetchAll();
    }

    /**
     * Get a single setting value by key
     */
    public function get_value($key, $default = null) {
        $db = Database::get_instance();
        $row = $db->query("SELECT `value` FROM ai_settings WHERE `key` = ?", [$key])->fetch();
        return $row ? $row->value : $default;
    }

    /**
     * Update a setting value
     */
    public function set_value($key, $value, $user_id = null) {
        $db = Database::get_instance();
        $existing = $db->query("SELECT id FROM ai_settings WHERE `key` = ?", [$key])->fetch();
        
        if ($existing) {
            $db->query(
                "UPDATE ai_settings SET `value` = ?, `updated_by` = ?, `updated_at` = NOW() WHERE `key` = ?",
                [$value, $user_id, $key]
            );
        } else {
            $db->query(
                "INSERT INTO ai_settings (`key`, `value`, `updated_by`, `updated_at`) VALUES (?, ?, ?, NOW())",
                [$key, $value, $user_id]
            );
        }
        return true;
    }

    /**
     * Bulk update settings
     */
    public function bulk_update($settings, $user_id = null) {
        foreach ($settings as $key => $value) {
            $this->set_value($key, $value, $user_id);
        }
        return true;
    }

    /**
     * Get distinct groups
     */
    public function get_groups() {
        $db = Database::get_instance();
        return $db->query("SELECT DISTINCT `group` FROM ai_settings ORDER BY `group` ASC")->fetchAll();
    }

    // ─── AI Tools ───

    /**
     * Get all registered tools
     */
    public function get_tools($active_only = false) {
        $db = Database::get_instance();
        $sql = "SELECT * FROM ai_tools";
        if ($active_only) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY name ASC";
        return $db->query($sql)->fetchAll();
    }

    /**
     * Toggle tool active status
     */
    public function toggle_tool($id) {
        $db = Database::get_instance();
        $db->query("UPDATE ai_tools SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW() WHERE id = ?", [$id]);
        return true;
    }

    // ─── AI Prompts ───

    /**
     * Get all prompts
     */
    public function get_prompts($assistant_type = null) {
        $db = Database::get_instance();
        if ($assistant_type) {
            return $db->query("SELECT * FROM ai_prompts WHERE assistant_type = ? ORDER BY name ASC", [$assistant_type])->fetchAll();
        }
        return $db->query("SELECT * FROM ai_prompts ORDER BY assistant_type ASC, name ASC")->fetchAll();
    }

    /**
     * Get active prompt for an assistant type
     */
    public function get_active_prompt($assistant_type) {
        $db = Database::get_instance();
        return $db->query(
            "SELECT * FROM ai_prompts WHERE assistant_type = ? AND is_active = 1 LIMIT 1",
            [$assistant_type]
        )->fetch();
    }

    /**
     * Update prompt
     */
    public function update_prompt($id, $data) {
        $db = Database::get_instance();
        $db->query(
            "UPDATE ai_prompts SET content = ?, updated_by = ?, updated_at = NOW() WHERE id = ?",
            [$data['content'], $data['updated_by'], $id]
        );
        return true;
    }
}
