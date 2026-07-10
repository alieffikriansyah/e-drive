<?php

class Router {
    private $controller;
    private $method;
    private $params = [];
    private $module = '';

    public function __construct() {
        global $config;
        $this->controller = $config['default_controller'];
        $this->method = $config['default_method'];
        $this->module = $config['default_module'];
    }

    public function parseUrl() {
        if (isset($_GET['/'])) {
            $url = rtrim($_GET['/'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            if (empty($url)) return [];
            return explode('/', $url);
        }
        
        // Also support PATH_INFO or REQUEST_URI if $_GET['/'] is not set by .htaccess
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $script_name = $_SERVER['SCRIPT_NAME'];
        
        // Remove base path from URI (only at the beginning)
        $base_path = str_replace('index.php', '', $script_name);
        if (strpos($uri, $base_path) === 0) {
            $uri = substr($uri, strlen($base_path));
        }
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        $uri = trim($uri, '/');
        
        if (!empty($uri)) {
             return explode('/', $uri);
        }

        return [];
    }

    public function dispatch() {
        $url = $this->parseUrl();

        if (empty($url)) {
            // Default route
            if ($this->loadController()) {
                try {
                    if (method_exists($this->controller, '_middleware')) {
                         $this->controller->_middleware($this->method);
                    }
                    call_user_func_array([$this->controller, $this->method], []);
                } catch (\Throwable $e) {
                    $this->handleError($e);
                }
            }
            return;
        }

        // Check if first segment is a module
        if (file_exists(MODULESPATH . $url[0])) {
            $this->module = $url[0];
            unset($url[0]);
            $url = array_values($url);
            
            // Controller is the same as module name if not specified
            if (isset($url[0]) && file_exists(MODULESPATH . $this->module . '/controllers/' . str_replace(' ', '', ucwords(str_replace('_', ' ', $url[0]))) . '.php')) {
                $this->controller = $url[0];
                unset($url[0]);
            } else {
                $this->controller = $this->module; // fallback to module name
            }
        } 
        // If not a module, maybe a core controller (we don't strictly support this in HMVC unless needed, but let's keep it simple: everything is a module)
        else {
            $this->module = $url[0];
            $this->controller = $url[0];
            unset($url[0]);
        }
        
        $url = array_values($url);

        if (!$this->loadController()) {
            return;
        }

        if (isset($url[0])) {
            if (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                unset($url[0]);
            } else {
                $this->show404();
                return;
            }
        }

        $this->params = $url ? array_values($url) : [];

        // Execute middleware before method if implemented in controller
        try {
            if (method_exists($this->controller, '_middleware')) {
                 $this->controller->_middleware($this->method);
            }
            call_user_func_array([$this->controller, $this->method], $this->params);
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    private function loadController() {
        $controller_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->controller)));
        $controller_file = MODULESPATH . $this->module . '/controllers/' . $controller_name . '.php';

        if (file_exists($controller_file)) {
            require_once $controller_file;
            $this->controller = new $controller_name();
            return true;
        } else {
            $this->show404();
            return false;
        }
    }

    private function show404() {
        header("HTTP/1.0 404 Not Found");
        echo "404 - Page Not Found";
    }

    private function handleError(\Throwable $e) {
        $isAjax = (
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        );

        if ($isAjax) {
            // Pastikan tidak ada output HTML yang sudah tercetak
            if (ob_get_level() > 0) ob_end_clean();
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ]);
        } else {
            http_response_code(500);
            echo '<b>Error:</b> ' . htmlspecialchars($e->getMessage());
        }
        exit;
    }
}
