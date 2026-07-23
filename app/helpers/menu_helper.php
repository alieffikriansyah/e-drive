<?php

/**
 * Render sidebar menu berdasarkan role
 * (Fungsi ini sudah tidak digunakan langsung di header karena query dilakukan di header.php,
 *  tetapi dipertahankan untuk backward compatibility dan penggunaan di tempat lain)
 */
if (!function_exists('render_dynamic_menu')) {
    function render_dynamic_menu($role_id, $parent_id = null) {
        $CI =& Controller::get_instance();
        
        $sql = "
            SELECT m.* 
            FROM system_menus m
            JOIN role_menu_access rma ON m.id = rma.menu_id
            WHERE rma.role_id = ? AND m.is_active = 1 AND m.status != 8
        ";
        
        $params = [$role_id];
        
        if ($parent_id === null) {
            $sql .= " AND m.parent_id IS NULL";
        } else {
            $sql .= " AND m.parent_id = ?";
            $params[] = $parent_id;
        }
        
        $sql .= " ORDER BY m.order_num ASC";
        
        $menus = $CI->db->query($sql, $params)->fetchAll();
        
        if (empty($menus)) {
            return '';
        }
        
        $html = '';
        foreach ($menus as $menu) {
            $submenu_html = render_dynamic_menu($role_id, $menu->id);
            $icon = $menu->icon ? '<i class="' . htmlspecialchars($menu->icon) . '"></i> ' : '';
            $link = $menu->url == '#' ? '#' : site_url($menu->url);
            
            $html .= '<a href="' . $link . '" class="sidebar-link">';
            $html .= $icon;
            $html .= '<span class="sidebar-text">' . htmlspecialchars($menu->name) . '</span>';
            $html .= '</a>';
            $html .= $submenu_html;
        }
        
        return $html;
    }
}

/**
 * Get menu list for a role as array
 */
if (!function_exists('get_menu_list')) {
    function get_menu_list($role_id, $parent_id = null) {
        $CI =& Controller::get_instance();
        
        $sql = "
            SELECT m.* 
            FROM system_menus m
            JOIN role_menu_access rma ON m.id = rma.menu_id
            WHERE rma.role_id = ? AND m.is_active = 1 AND m.status != 8
        ";
        $params = [$role_id];
        
        if ($parent_id === null) {
            $sql .= " AND m.parent_id IS NULL";
        } else {
            $sql .= " AND m.parent_id = ?";
            $params[] = $parent_id;
        }
        
        $sql .= " ORDER BY m.order_num ASC";
        
        return $CI->db->query($sql, $params)->fetchAll();
    }
}
