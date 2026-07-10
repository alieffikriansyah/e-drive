<?php

if (!function_exists('csrf_field')) {
    function csrf_field() {
        $CI =& Controller::get_instance();
        $token = $CI->input->generate_csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}

if (!function_exists('set_value')) {
    function set_value($field, $default = '') {
        if (isset($_POST[$field])) {
            $CI =& Controller::get_instance();
            return $CI->input->xss_clean($_POST[$field]);
        }
        return $default;
    }
}
