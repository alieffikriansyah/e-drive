<?php

class Security {
    public function xss_clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->xss_clean($value);
            }
            return $data;
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public function generate_csrf_token() {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public function verify_csrf_token($token) {
        if (hash_equals(Session::get('csrf_token', ''), $token)) {
            return true;
        }
        return false;
    }
}
