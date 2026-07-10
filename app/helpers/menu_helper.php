<?php

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
        
        $html = $parent_id === null ? '<ul class="navbar">' : '<ul class="dropdown">';
        
        foreach ($menus as $menu) {
            $submenu_html = render_dynamic_menu($role_id, $menu->id);
            $has_submenu = !empty($submenu_html);
            
            $icon = $menu->icon ? '<i class="fa ' . htmlspecialchars($menu->icon) . '"></i> ' : '';
            $link = $menu->url == '#' ? '#' : site_url($menu->url);
            
            $html .= '<li class="' . ($has_submenu ? 'has-dropdown' : '') . '">';
            $html .= '<a href="' . $link . '">' . $icon . htmlspecialchars($menu->name) . '</a>';
            $html .= $submenu_html;
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        
        return $html;
    }
}
