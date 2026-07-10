<?php

class Loader {
    
    // Magic method to proxy property access to the main Controller instance
    // This allows $this->load->view() inside a view file (where $this is the Loader)
    public function __get($key) {
        $CI =& Controller::get_instance();
        return $CI->$key ?? null;
    }
    
    public function view($view_name, $data = []) {
        // Extract variables to be used in the view
        extract($data);
        
        // Find caller module
        $trace = debug_backtrace();
        $caller_file = $trace[0]['file'];
        
        $module = '';
        if (strpos($caller_file, MODULESPATH) !== false) {
            $parts = explode(DIRECTORY_SEPARATOR, str_replace(MODULESPATH, '', $caller_file));
            $module = $parts[0];
        }

        // Check global app/views/ first
        if (file_exists(APPPATH . 'views/' . $view_name . '.php')) {
            $view_file = APPPATH . 'views/' . $view_name . '.php';
        } else {
            // Parse view_name for module prefix e.g., 'auth/login'
            if (strpos($view_name, '/') !== false) {
                $parts = explode('/', $view_name);
                $module_name = $parts[0];
                $view_file = MODULESPATH . $module_name . '/views/' . substr($view_name, strlen($module_name) + 1) . '.php';
            } else {
                $view_file = MODULESPATH . $module . '/views/' . $view_name . '.php';
            }
        }

        if (file_exists($view_file)) {
            require $view_file;
        } else {
            die("View does not exist: " . $view_name);
        }
    }

    public function model($model_name) {
        $module = '';
        $model_class = $model_name;
        
        if (strpos($model_name, '/') !== false) {
            $parts = explode('/', $model_name);
            $module = $parts[0];
            $model_class = $parts[1];
        } else {
            $trace = debug_backtrace();
            foreach ($trace as $t) {
                if (isset($t['file']) && strpos($t['file'], MODULESPATH) !== false) {
                    $parts = explode(DIRECTORY_SEPARATOR, str_replace(MODULESPATH, '', $t['file']));
                    $module = $parts[0];
                    break;
                }
            }
        }
        
        $model_class_name = ucfirst($model_class);
        $model_file = MODULESPATH . $module . '/models/' . $model_class_name . '.php';
        
        if (file_exists($model_file)) {
            require_once $model_file;
            $CI =& Controller::get_instance();
            $CI->$model_class = new $model_class_name();
        } else {
            die("Model does not exist: " . $model_name);
        }
    }

    public function helper($helper_name) {
        $helper_file = APPPATH . 'helpers/' . $helper_name . '_helper.php';
        if (file_exists($helper_file)) {
            require_once $helper_file;
        } else {
            die("Helper does not exist: " . $helper_name);
        }
    }

    public function library($library_name) {
        $library_class = ucfirst($library_name);
        $library_file = APPPATH . 'libraries/' . $library_class . '.php';
        if (file_exists($library_file)) {
            require_once $library_file;
            $CI =& Controller::get_instance();
            $CI->$library_name = new $library_class();
        } else {
            die("Library does not exist: " . $library_name);
        }
    }
}
