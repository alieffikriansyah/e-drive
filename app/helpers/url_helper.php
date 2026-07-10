<?php

if (!function_exists('base_url')) {
    function base_url($uri = '') {
        global $config;
        $base = rtrim($config['base_url'], '/');
        $uri = ltrim($uri, '/');
        return $uri ? "$base/$uri" : "$base/";
    }
}

if (!function_exists('site_url')) {
    function site_url($uri = '') {
        return base_url($uri);
    }
}

if (!function_exists('redirect')) {
    function redirect($uri = '') {
        header("Location: " . site_url($uri));
        exit;
    }
}
