<?php

class MOD extends Model {

    /**
     * Create a new conversation
     */
    public function create_conversation($user_id, $assistant_type = 'general', $title = 'New Chat') {
        $db = Database::get_instance();
        $db->query(
            "INSERT INTO ai_conversations (user_id, title, assistant_type, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())",
            [$user_id, $title, $assistant_type]
        );
        return $db->query("SELECT LAST_INSERT_ID() as id")->fetch()->id;
    }

    /**
     * Get conversations for a user
     */
    public function get_conversations($user_id, $assistant_type = null, $limit = 50) {
        $db = Database::get_instance();
        $sql = "SELECT c.*, 
                    (SELECT COUNT(*) FROM ai_messages WHERE conversation_id = c.id AND status = 1) as message_count,
                    (SELECT content FROM ai_messages WHERE conversation_id = c.id AND role = 'user' AND status = 1 ORDER BY id ASC LIMIT 1) as first_message
                FROM ai_conversations c 
                WHERE c.user_id = ? AND c.status = 1";
        $params = [$user_id];

        if ($assistant_type) {
            $sql .= " AND c.assistant_type = ?";
            $params[] = $assistant_type;
        }

        $sql .= " ORDER BY c.is_pinned DESC, c.updated_at DESC LIMIT ?";
        $params[] = $limit;

        return $db->query($sql, $params)->fetchAll();
    }

    /**
     * Get a single conversation
     */
    public function get_conversation($id, $user_id = null) {
        $db = Database::get_instance();
        $sql = "SELECT * FROM ai_conversations WHERE id = ? AND status = 1";
        $params = [$id];
        if ($user_id) {
            $sql .= " AND user_id = ?";
            $params[] = $user_id;
        }
        return $db->query($sql, $params)->fetch();
    }

    /**
     * Rename conversation
     */
    public function rename_conversation($id, $title, $user_id) {
        $db = Database::get_instance();
        $db->query(
            "UPDATE ai_conversations SET title = ?, updated_at = NOW() WHERE id = ? AND user_id = ?",
            [$title, $id, $user_id]
        );
        return true;
    }

    /**
     * Soft delete conversation
     */
    public function delete_conversation($id, $user_id) {
        $db = Database::get_instance();
        $db->query(
            "UPDATE ai_conversations SET status = 8, updated_at = NOW() WHERE id = ? AND user_id = ?",
            [$id, $user_id]
        );
        return true;
    }

    /**
     * Pin/unpin conversation
     */
    public function toggle_pin($id, $user_id) {
        $db = Database::get_instance();
        $db->query(
            "UPDATE ai_conversations SET is_pinned = IF(is_pinned = 1, 0, 1), updated_at = NOW() WHERE id = ? AND user_id = ?",
            [$id, $user_id]
        );
        return true;
    }

    /**
     * Get messages for a conversation
     */
    public function get_messages($conversation_id, $user_id) {
        $db = Database::get_instance();
        // Verify ownership
        $conv = $this->get_conversation($conversation_id, $user_id);
        if (!$conv) return [];

        return $db->query(
            "SELECT m.*, 
                    (SELECT rating FROM ai_feedback WHERE message_id = m.id AND user_id = ? LIMIT 1) as user_rating
             FROM ai_messages m 
             WHERE m.conversation_id = ? AND m.status = 1 
             ORDER BY m.id ASC",
            [$user_id, $conversation_id]
        )->fetchAll();
    }

    /**
     * Add a message to a conversation
     */
    public function add_message($conversation_id, $role, $content, $meta = []) {
        $db = Database::get_instance();
        $db->query(
            "INSERT INTO ai_messages (conversation_id, role, content, tokens_in, tokens_out, model_used, response_time_ms, metadata, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                $conversation_id,
                $role,
                $content,
                $meta['tokens_in'] ?? 0,
                $meta['tokens_out'] ?? 0,
                $meta['model_used'] ?? null,
                $meta['response_time_ms'] ?? null,
                isset($meta['metadata']) ? json_encode($meta['metadata']) : null
            ]
        );
        
        // Update conversation timestamp and auto-title from first user message
        $db->query("UPDATE ai_conversations SET updated_at = NOW() WHERE id = ?", [$conversation_id]);
        
        // Auto-title from first user message
        if ($role === 'user') {
            $msg_count = $db->query("SELECT COUNT(*) as cnt FROM ai_messages WHERE conversation_id = ? AND role = 'user' AND status = 1", [$conversation_id])->fetch()->cnt;
            if ($msg_count <= 1) {
                $auto_title = mb_substr($content, 0, 60);
                if (mb_strlen($content) > 60) $auto_title .= '...';
                $db->query("UPDATE ai_conversations SET title = ? WHERE id = ? AND title = 'New Chat'", [$auto_title, $conversation_id]);
            }
        }

        return $db->query("SELECT LAST_INSERT_ID() as id")->fetch()->id;
    }

    /**
     * Track usage
     */
    public function track_usage($user_id, $assistant_type, $tokens) {
        $db = Database::get_instance();
        $date = date('Y-m-d');
        $existing = $db->query(
            "SELECT id FROM ai_usage WHERE user_id = ? AND assistant_type = ? AND date = ?",
            [$user_id, $assistant_type, $date]
        )->fetch();

        if ($existing) {
            $db->query(
                "UPDATE ai_usage SET total_tokens = total_tokens + ?, total_requests = total_requests + 1 WHERE id = ?",
                [$tokens, $existing->id]
            );
        } else {
            $db->query(
                "INSERT INTO ai_usage (user_id, assistant_type, total_tokens, total_requests, date, created_at) VALUES (?, ?, ?, 1, ?, NOW())",
                [$user_id, $assistant_type, $tokens, $date]
            );
        }
    }

    /**
     * Save feedback
     */
    public function save_feedback($message_id, $user_id, $rating, $comment = null) {
        $db = Database::get_instance();
        $db->query(
            "INSERT INTO ai_feedback (message_id, user_id, rating, comment, created_at) 
             VALUES (?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)",
            [$message_id, $user_id, $rating, $comment]
        );
        return true;
    }

    /**
     * Get AI setting value
     */
    public function get_setting($key, $default = null) {
        $db = Database::get_instance();
        $row = $db->query("SELECT `value` FROM ai_settings WHERE `key` = ?", [$key])->fetch();
        return $row ? $row->value : $default;
    }

    /**
     * Get active system prompt
     */
    public function get_system_prompt($assistant_type) {
        $db = Database::get_instance();
        $row = $db->query(
            "SELECT content FROM ai_prompts WHERE assistant_type = ? AND is_active = 1 LIMIT 1",
            [$assistant_type]
        )->fetch();
        return $row ? $row->content : '';
    }
}
