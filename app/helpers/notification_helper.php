<?php

/**
 * Notification Helper — E-Drive
 */

if (!function_exists('send_notification')) {
    /**
     * Send notification to a user
     */
    function send_notification($user_id, $title, $message, $type = 'info', $link = null, $icon = null) {
        $CI =& Controller::get_instance();
        
        $data = [
            'user_id'    => $user_id,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'icon'       => $icon,
            'link'       => $link,
            'is_read'    => 0,
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['user_id'] ?? null,
        ];
        
        return $CI->db->query(
            "INSERT INTO notifications (user_id, title, message, type, icon, link, is_read, status, created_at, created_by) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($data)
        );
    }
}

if (!function_exists('get_unread_notification_count')) {
    function get_unread_notification_count($user_id) {
        $CI =& Controller::get_instance();
        $result = $CI->db->query(
            "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0 AND status = 1",
            [$user_id]
        )->fetch();
        return $result ? (int)$result->cnt : 0;
    }
}

if (!function_exists('notify_admins')) {
    /**
     * Send notification to all administrators
     */
    function notify_admins($title, $message, $type = 'info', $link = null) {
        $CI =& Controller::get_instance();
        $admins = $CI->db->query("SELECT id FROM users WHERE role_id = 1 AND status = 1")->fetchAll();
        foreach ($admins as $admin) {
            send_notification($admin->id, $title, $message, $type, $link);
        }
    }
}
