<?php

class Session {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        session_destroy();
    }

    public static function set_flashdata($key, $value) {
        $_SESSION['flash_data'][$key] = $value;
    }

    public static function flashdata($key) {
        if (isset($_SESSION['flash_data'][$key])) {
            $value = $_SESSION['flash_data'][$key];
            unset($_SESSION['flash_data'][$key]);
            return $value;
        }
        return null;
    }
}
