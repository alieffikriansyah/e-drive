<?php

class AuthMiddleware {
    public static function check() {
        // Enforce all required session variables
        if (!Session::get('user_id') || !Session::get('role_id') || !Session::get('role_name')) {
            Session::destroy();
            
            // AJAX request → return JSON 401
            if (self::isAjax()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Session expired. Please login again.']);
                exit;
            }
            
            redirect('auth');
        }

        // RBAC Check
        $CI =& Controller::get_instance();
        
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $script_name = $_SERVER['SCRIPT_NAME'];
        $base_path = str_replace('index.php', '', $script_name);
        $uri = str_replace($base_path, '', $uri);
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        $segments = explode('/', $uri);
        $module = !empty($segments[0]) ? $segments[0] : 'dashboard';

        $role_id = Session::get('role_id');
        
        // AI Chat (AI Assistant & E-Drive Assistant) is accessible to ALL logged-in users
        if ($module === 'ai_chat') {
            return;
        }

        // Check if module exists in system_menus
        $menu = $CI->db->table('system_menus')->where('url', $module)->row();
        
        if ($menu) {
            $has_access = $CI->db->query(
                "SELECT * FROM role_menu_access WHERE role_id = ? AND menu_id = ?",
                [$role_id, $menu->id]
            )->fetch();
            
            if (!$has_access) {
                if (self::isAjax()) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Access denied.']);
                    exit;
                }
                
                http_response_code(403);
                echo '<!DOCTYPE html><html><head><title>Access Denied — E-Drive</title>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
                    <style>
                        body{font-family:Inter,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#F1F5F9;color:#1E293B;}
                        .box{text-align:center;background:#fff;padding:48px 56px;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid #E2E8F0;}
                        h1{color:#EF4444;font-size:64px;margin:0;font-weight:800;}
                        h2{color:#1E293B;font-size:20px;margin:12px 0 8px;}
                        p{color:#64748B;font-size:14px;margin:0 0 24px;}
                        a{color:#2563EB;text-decoration:none;font-weight:600;font-size:14px;}
                        a:hover{text-decoration:underline;}
                    </style>
                </head><body>
                    <div class="box">
                        <h1>403</h1>
                        <h2>Access Denied</h2>
                        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                        <a href="javascript:history.back()">&#8592; Kembali</a>
                    </div>
                </body></html>';
                exit;
            }
        }
    }

    /**
     * Check if current user has specific role level
     */
    public static function isAdmin() {
        return in_array(strtolower(Session::get('role_name') ?? ''), ['administrator']);
    }

    public static function isManager() {
        return in_array(strtolower(Session::get('role_name') ?? ''), ['administrator', 'project manager']);
    }

    /**
     * Check if user can access a specific drive
     */
    public static function canAccessDrive($drive_id) {
        if (self::isManager()) return true; // Admin & PM can access all drives
        
        $CI =& Controller::get_instance();
        $user_role_id = Session::get('role_id');
        
        // Check if drive belongs to user's role
        $drive = $CI->db->table('drives')->where('id', $drive_id)->row();
        if ($drive && ($drive->owner_role_id == $user_role_id || $drive->is_shared == 1)) {
            return true;
        }
        
        // Check shared_access
        $shared = $CI->db->query(
            "SELECT id FROM shared_access WHERE entity_type = 'drive' AND entity_id = ? AND user_id = ? AND status = 1",
            [$drive_id, Session::get('user_id')]
        )->fetch();
        
        return $shared ? true : false;
    }

    private static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
