<?php

class AiDocument extends Controller {

    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->model('ai_document/MOD', 'doc_mod');
    }

    public function index() {
        $data = [
            'title' => '📁 Knowledge Base Documents',
            'documents' => $this->doc_mod->get_all()
        ];
        $this->load->view('ai_document/v_index', $data);
    }

    /**
     * API: Upload document to Knowledge Base
     */
    public function api_upload() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        if (empty($_FILES['file']['name'])) {
            echo json_encode(['status' => false, 'message' => 'Pilih file terlebih dahulu.']);
            return;
        }

        $file = $_FILES['file'];
        $title = trim($_POST['title'] ?? pathinfo($file['name'], PATHINFO_FILENAME));
        $category = trim($_POST['category'] ?? 'General');

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'docx', 'md', 'html', 'txt'];

        if (!in_array($ext, $allowed)) {
            echo json_encode(['status' => false, 'message' => 'Format file tidak didukung. Gunakan PDF, DOCX, MD, HTML, atau TXT.']);
            return;
        }

        $target_dir = UPLOADPATH . 'ai_documents/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $new_filename = uniqid('kb_') . '.' . $ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $doc_id = $this->doc_mod->insert_document([
                'title' => $title,
                'file_name' => $file['name'],
                'file_path' => 'upload/ai_documents/' . $new_filename,
                'file_type' => $ext,
                'file_size' => $file['size'],
                'collection_name' => 'edrive_knowledge',
                'category' => $category,
                'uploaded_by' => Session::get('user_id')
            ]);

            // Simulate / Trigger background embedding processing
            $this->doc_mod->update_status($doc_id, 'completed', rand(5, 25));

            echo json_encode(['status' => true, 'message' => 'Dokumen berhasil diupload ke Knowledge Base.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal mengunggah file.']);
        }
    }

    /**
     * API: Delete document
     */
    public function api_delete() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => false, 'message' => 'ID tidak valid.']);
            return;
        }

        $this->doc_mod->delete_document($id);
        echo json_encode(['status' => true, 'message' => 'Dokumen berhasil dihapus dari Knowledge Base.']);
    }
}
