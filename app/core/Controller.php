<?php

class Controller {
    private static $instance;
    public $load;
    public $db;
    public $input;

    public function __construct() {
        self::$instance =& $this;
        
        $this->load = new Loader();
        $this->db = Database::get_instance();
        $this->input = new Security();
        $this->load->helper('date');

        // Auto-parse JSON payload to $_POST
        $content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
        if (strpos($content_type, 'application/json') !== false) {
            $json = json_decode(file_get_contents('php://input'), true);
            if (is_array($json)) {
                $_POST = array_merge($_POST, $json);
            }
        }
    }

    public static function &get_instance() {
        return self::$instance;
    }
}
