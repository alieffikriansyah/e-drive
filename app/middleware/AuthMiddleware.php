<?php

class AuthMiddleware {
    public static function check() {
        // Enforce all required session variables to exist (forces relogin for stale sessions)
        if (!Session::get('user_id') || !Session::get('role_id') || !Session::get('role_name') || !array_key_exists('id_cabang', $_SESSION)) {
            // Not logged in or missing new session variables from old session, redirect to auth module
            Session::destroy();
            redirect('auth');
        }

        // RBAC Check
        $CI =& Controller::get_instance();
        
        // Dapatkan URL saat ini dari request uri
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $script_name = $_SERVER['SCRIPT_NAME'];
        $base_path = str_replace('index.php', '', $script_name);
        $uri = str_replace($base_path, '', $uri);
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = trim($uri, '/');
        
        // Ambil segmen pertama sebagai modul/menu utama
        $segments = explode('/', $uri);
        $module = !empty($segments[0]) ? $segments[0] : 'dashboard';

        $role_id = Session::get('role_id');
        
        // Cek apakah modul ini ada di system_menus
        $menu = $CI->db->table('system_menus')->where('url', $module)->row();
        
        if ($menu) {
            // Jika menu ada di database, cek akses berdasarkan role
            $has_access = $CI->db->query(
                "SELECT * FROM role_menu_access WHERE role_id = ? AND menu_id = ?",
                [$role_id, $menu->id]
            )->fetch();
            
            if (!$has_access) {
                http_response_code(403);
                echo "<!DOCTYPE html><html><head><title>Access Denied</title>
                    <style>
                        body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#f4f4f4;}
                        .box{text-align:center;background:#fff;padding:40px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1);}
                        h1{color:#dc3545;font-size:60px;margin:0;}
                        h2{color:#333;}
                        a{color:#007bff;text-decoration:none;}
                    </style>
                </head><body>
                    <div class='box'>
                        <h1>403</h1>
                        <h2>Access Denied</h2>
                        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                        <a href='javascript:history.back()'>&#8592; Kembali</a>
                    </div>
                </body></html>";
                exit;
            }
        }
    }
}
