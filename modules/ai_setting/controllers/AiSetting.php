<?php

class AiSetting extends Controller {
    
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->model('ai_setting/MOD', 'ai_mod');

        // Only admin can access AI settings
        if (!AuthMiddleware::isManager()) {
            if ($this->_isAjax()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['status' => false, 'message' => 'Access denied.']);
                exit;
            }
            redirect('dashboard');
        }
    }

    public function index() {
        $data = [
            'title' => 'AI Settings',
            'settings' => $this->ai_mod->get_all(),
            'groups' => $this->ai_mod->get_groups(),
            'tools' => $this->ai_mod->get_tools(),
            'prompts' => $this->ai_mod->get_prompts(),
        ];
        $this->load->view('ai_setting/v_index', $data);
    }

    /**
     * API: Update settings
     */
    public function api_save() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        try {
            $settings = $_POST['settings'] ?? [];
            $this->ai_mod->bulk_update($settings, Session::get('user_id'));
            echo json_encode(['status' => true, 'message' => 'Settings berhasil disimpan.']);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Toggle tool
     */
    public function api_toggle_tool() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$id) {
            echo json_encode(['status' => false, 'message' => 'ID tidak valid.']);
            return;
        }

        try {
            $this->ai_mod->toggle_tool($id);
            echo json_encode(['status' => true, 'message' => 'Tool status berhasil diubah.']);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Update prompt
     */
    public function api_save_prompt() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $content = $_POST['content'] ?? '';

        if (!$id || empty($content)) {
            echo json_encode(['status' => false, 'message' => 'ID dan content wajib diisi.']);
            return;
        }

        try {
            $this->ai_mod->update_prompt($id, [
                'content' => $content,
                'updated_by' => Session::get('user_id')
            ]);
            echo json_encode(['status' => true, 'message' => 'Prompt berhasil disimpan.']);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Test connection to Flowise/Ollama
     */
    public function api_test_connection() {
        header('Content-Type: application/json');
        $service = $_GET['service'] ?? '';
        
        $url = '';
        if ($service === 'flowise') {
            $url = $this->ai_mod->get_value('flowise_url', 'http://localhost:3000');
        } elseif ($service === 'ollama') {
            $url = $this->ai_mod->get_value('ollama_url', 'http://localhost:11434');
        } else {
            echo json_encode(['status' => false, 'message' => 'Service tidak valid.']);
            return;
        }

        // Test connection via cURL
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_NOBODY => false,
        ]);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo json_encode(['status' => false, 'message' => "Connection failed: $error"]);
        } elseif ($http_code >= 200 && $http_code < 400) {
            echo json_encode(['status' => true, 'message' => "✅ Connected successfully (HTTP $http_code)"]);
        } else {
            echo json_encode(['status' => false, 'message' => "Connection returned HTTP $http_code"]);
        }
    }

    /**
     * API: Generate internal API key
     */
    public function api_generate_key() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $key = 'edrive_' . bin2hex(random_bytes(24));
        $this->ai_mod->set_value('internal_api_key', $key, Session::get('user_id'));
        echo json_encode(['status' => true, 'message' => 'API Key berhasil di-generate.', 'key' => $key]);
    }

    private function _isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
