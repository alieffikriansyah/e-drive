<?php
class Onlyoffice extends Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('jwt');
        $this->load->model('document/MOD', 'doc_mod');
    }

    public function edit($doc_id) {
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        
        $doc = $this->doc_mod->get_document_by_id($doc_id);
        if (!$doc) {
            die('Dokumen tidak ditemukan.');
        }
        if (!AuthMiddleware::canAccessDrive($doc->drive_id)) {
            die('Akses ditolak.');
        }

        $allowed = ['docx', 'xlsx', 'pptx'];
        if (!in_array(strtolower($doc->file_type), $allowed)) {
            die('Format dokumen ini tidak didukung untuk diedit.');
        }

        $secret = Env::get('ONLYOFFICE_SECRET');
        if (!$secret) {
            die('Konfigurasi ONLYOFFICE_SECRET belum diatur di .env');
        }

        $download_token = JwtHelper::encode(['doc_id' => $doc_id, 'action' => 'download'], $secret);
        $callback_token = JwtHelper::encode(['doc_id' => $doc_id, 'action' => 'callback'], $secret);
        
        $download_url = base_url('onlyoffice/download?token=' . $download_token);
        $callback_url = base_url('onlyoffice/callback?token=' . $callback_token);

        // base_url() sometimes doesn't contain http:// in some setups, but we should make sure it has http:// or https://
        if (strpos($download_url, 'http') !== 0) {
            $download_url = 'http://' . $download_url;
            $callback_url = 'http://' . $callback_url;
        }

        // ONLYOFFICE Server needs to download the file from the web server.
        // Jika ONLYOFFICE berjalan di dalam Docker (misal di Windows), gunakan host.docker.internal.
        // Jika ONLYOFFICE berjalan native di host (misal via Snap di Linux), gunakan localhost secara langsung.
        $use_docker_host = Env::get('ONLYOFFICE_USE_DOCKER_HOST');
        if ($use_docker_host === 'true' || ($use_docker_host === null && PHP_OS_FAMILY === 'Windows')) {
            $download_url = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $download_url);
            $callback_url = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $callback_url);
        }

        $doc_key = $doc_id . '_' . strtotime($doc->updated_at ?? $doc->created_at);

        $documentType = 'word';
        if ($doc->file_type === 'xlsx') $documentType = 'cell';
        if ($doc->file_type === 'pptx') $documentType = 'slide';

        $config = [
            "document" => [
                "fileType" => strtolower($doc->file_type),
                "key" => $doc_key,
                "title" => $doc->name . '.' . $doc->file_type,
                "url" => $download_url
            ],
            "documentType" => $documentType,
            "editorConfig" => [
                "callbackUrl" => $callback_url,
                "customization" => [
                    "forcesave" => true
                ],
                "user" => [
                    "id" => (string)Session::get('user_id'),
                    "name" => Session::get('user_name') ?? 'User'
                ]
            ]
        ];

        $jwt_secret = Env::get('ONLYOFFICE_JWT_SECRET') ?? $secret;
        $config['token'] = JwtHelper::encode($config, $jwt_secret);

        $data = [
            'title' => 'Edit Dokumen',
            'config' => $config,
            'doc' => $doc,
            'oo_url' => Env::get('ONLYOFFICE_URL')
        ];

        $this->load->view('onlyoffice/v_edit', $data);
    }

    public function download() {
        $token = $_GET['token'] ?? '';
        $secret = Env::get('ONLYOFFICE_SECRET');
        $payload = JwtHelper::decode($token, $secret);

        if (!$payload || $payload['action'] !== 'download') {
            http_response_code(403);
            die('Forbidden');
        }

        $doc_id = $payload['doc_id'];
        $doc = $this->doc_mod->get_document_by_id($doc_id);
        if (!$doc) {
            http_response_code(404);
            die('Not Found');
        }

        $file_path = STORAGEPATH . $doc->file_path;
        if (!file_exists($file_path) && file_exists(STORAGEPATH . 'documents/' . $doc->file_path)) {
            $file_path = STORAGEPATH . 'documents/' . $doc->file_path;
        }

        if (!file_exists($file_path)) {
            http_response_code(404);
            die('File Not Found');
        }

        header('Content-Type: ' . $doc->mime_type);
        header('Content-Disposition: attachment; filename="' . $doc->name . '.' . $doc->file_type . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }

    public function callback() {
        $token = $_GET['token'] ?? '';
        $secret = Env::get('ONLYOFFICE_SECRET');
        $payload = JwtHelper::decode($token, $secret);

        if (!$payload || $payload['action'] !== 'callback') {
            echo json_encode(["error" => 1, "message" => "Invalid token"]);
            exit;
        }

        $body_stream = file_get_contents("php://input");
        $body = json_decode($body_stream, true);

        // Log the callback for debugging purposes
        file_put_contents(STORAGEPATH . 'onlyoffice_debug.txt', date('Y-m-d H:i:s') . "\n" . $body_stream . "\n\n", FILE_APPEND);

        // ONLYOFFICE wraps the callback body in a JWT token if JWT is enabled
        if (isset($body['token'])) {
            $decoded = JwtHelper::decode($body['token'], $secret);
            if ($decoded) {
                $body = $decoded;
            }
        }

        $doc_id = $payload['doc_id'];

        if (isset($body['status']) && $body['status'] == 2) {
            $download_uri = $body['url'];
            
            $doc = $this->doc_mod->get_document_by_id($doc_id);
            if ($doc) {
                $file_path = STORAGEPATH . $doc->file_path;
                if (!file_exists($file_path) && file_exists(STORAGEPATH . 'documents/' . $doc->file_path)) {
                    $file_path = STORAGEPATH . 'documents/' . $doc->file_path;
                }
                
                $old_size = filesize($file_path);
                
                // Coba gunakan context untuk file_get_contents jika ada masalah jaringan lokal
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 15,
                        'ignore_errors' => true
                    ]
                ]);
                $new_data = file_get_contents($download_uri, false, $context);
                
                if ($new_data !== false && !empty($new_data)) {
                    file_put_contents($file_path, $new_data);
                    $new_size = filesize($file_path);
                    
                    file_put_contents(STORAGEPATH . 'onlyoffice_debug.txt', "SUCCESS SAVING: " . $file_path . " (Size: " . $new_size . ")\n\n", FILE_APPEND);
                    
                    $this->db->query("UPDATE documents SET file_size = ?, updated_at = NOW() WHERE id = ?", [$new_size, $doc_id]);
                    
                    $diff = $new_size - $old_size;
                    if ($diff != 0) {
                        if ($doc->folder_id) {
                            $this->db->query("UPDATE folders SET total_size = total_size + ? WHERE id = ?", [$diff, $doc->folder_id]);
                        }
                        $this->db->query("UPDATE drives SET total_size = total_size + ? WHERE id = ?", [$diff, $doc->drive_id]);
                    }
                } else {
                    file_put_contents(STORAGEPATH . 'onlyoffice_debug.txt', "FAILED TO FETCH: " . $download_uri . " - " . print_r(error_get_last(), true) . "\n\n", FILE_APPEND);
                }
            }
        }

        echo json_encode(["error" => 0]);
        exit;
    }
}
