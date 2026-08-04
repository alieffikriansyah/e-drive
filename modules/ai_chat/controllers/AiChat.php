<?php

require_once MODULESPATH . 'ai_chat/services/FlowiseClient.php';
require_once MODULESPATH . 'ai_chat/services/OllamaClient.php';
require_once MODULESPATH . 'ai_chat/services/OpenAiClient.php';

class AiChat extends Controller {

    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->model('ai_chat/MOD', 'ai_mod');
    }

    /**
     * General AI Assistant Page
     */
    public function general() {
        $this->_render_chat_page('general', '🤖 AI Assistant');
    }

    /**
     * E-Drive Assistant Page
     */
    public function edrive() {
        $this->_render_chat_page('edrive', '📁 E-Drive Assistant');
    }

    /**
     * Default index redirects to general
     */
    public function index() {
        redirect('ai_chat/general');
    }

    private function _render_chat_page($type, $title) {
        $user_id = Session::get('user_id');
        $conversations = $this->ai_mod->get_conversations($user_id, $type);
        
        // Active conversation ID from query parameter ?c=
        $active_conv_id = isset($_GET['c']) ? (int)$_GET['c'] : 0;
        $active_conv = null;
        $messages = [];

        if ($active_conv_id > 0) {
            $active_conv = $this->ai_mod->get_conversation($active_conv_id, $user_id);
            if ($active_conv) {
                $messages = $this->ai_mod->get_messages($active_conv_id, $user_id);
            } else {
                $active_conv_id = 0;
            }
        }

        $data = [
            'title' => $title,
            'assistant_type' => $type,
            'conversations' => $conversations,
            'active_conv_id' => $active_conv_id,
            'active_conv' => $active_conv,
            'messages' => $messages,
            'feedback_enabled' => $this->ai_mod->get_setting('feedback_enabled', '1') === '1'
        ];

        $this->load->view('ai_chat/v_chat', $data);
    }

    /**
     * API: Send Message & Get Response
     */
    public function api_send() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $user_id = Session::get('user_id');
        $message = trim($_POST['message'] ?? '');
        $assistant_type = $_POST['assistant_type'] ?? 'general';
        $conversation_id = (int)($_POST['conversation_id'] ?? 0);

        if (empty($message)) {
            echo json_encode(['status' => false, 'message' => 'Pesan tidak boleh kosong.']);
            return;
        }

        try {
            // 1. Get or create conversation
            if ($conversation_id <= 0) {
                $conversation_id = $this->ai_mod->create_conversation($user_id, $assistant_type);
            } else {
                // Verify ownership
                $conv = $this->ai_mod->get_conversation($conversation_id, $user_id);
                if (!$conv) {
                    echo json_encode(['status' => false, 'message' => 'Conversation tidak ditemukan.']);
                    return;
                }
            }

            // 2. Save user message to database
            $user_msg_id = $this->ai_mod->add_message($conversation_id, 'user', $message);

            // 3. Process AI Response
            $start_time = microtime(true);
            $ai_response = $this->_get_ai_response($assistant_type, $message, $conversation_id, $user_id);
            $duration_ms = round((microtime(true) - $start_time) * 1000);

            // 4. Save assistant response to database
            $meta = [
                'tokens_in' => $ai_response['tokens_in'] ?? 0,
                'tokens_out' => $ai_response['tokens_out'] ?? 0,
                'model_used' => $ai_response['model'] ?? 'unknown',
                'response_time_ms' => $duration_ms,
                'metadata' => $ai_response['metadata'] ?? []
            ];

            $assistant_msg_id = $this->ai_mod->add_message($conversation_id, 'assistant', $ai_response['text'], $meta);

            // 5. Track Usage
            $total_tokens = ($meta['tokens_in'] + $meta['tokens_out']) ?: ceil(strlen($message . $ai_response['text']) / 4);
            $this->ai_mod->track_usage($user_id, $assistant_type, $total_tokens);

            // 6. Return JSON Response
            echo json_encode([
                'status' => true,
                'conversation_id' => $conversation_id,
                'user_message_id' => $user_msg_id,
                'assistant_message_id' => $assistant_msg_id,
                'reply' => $ai_response['text'],
                'metadata' => $ai_response['metadata'] ?? [],
                'response_time_ms' => $duration_ms
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Core AI dispatcher: Cloud API (Groq/OpenRouter/OpenAI) -> Flowise -> Ollama Fallback -> Smart Local Generator
     */
    private function _get_ai_response($type, $question, $conv_id, $user_id) {
        // Attempt 1: Direct Cloud LLM API (Groq / OpenRouter / OpenAI) if API key is provided
        $cloud_api_key = trim($this->ai_mod->get_setting('cloud_api_key', ''));
        $cloud_api_url = trim($this->ai_mod->get_setting('cloud_api_url', 'https://api.groq.com/openai/v1'));
        $cloud_model = trim($this->ai_mod->get_setting('cloud_model', 'llama-3.3-70b-versatile'));

        if (!empty($cloud_api_key)) {
            try {
                $client = new OpenAiClient($cloud_api_key, $cloud_api_url);
                $system_prompt = $this->ai_mod->get_system_prompt($type);
                
                if ($type === 'edrive') {
                    // Helper to format bytes
                    $formatBytes = function($bytes) {
                        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
                        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
                        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
                        return ($bytes > 0 ? $bytes . ' bytes' : '0 B');
                    };

                    // 1. Roles
                    $roles_db = $this->db->query("SELECT role_name, role_description FROM roles WHERE status = 1 ORDER BY id ASC")->fetchAll();
                    $roles_list = [];
                    foreach ($roles_db as $r) {
                        $roles_list[] = "- **" . $r->role_name . "**" . ($r->role_description ? ": " . $r->role_description : "");
                    }

                    // 2. Total Stats
                    $total_docs = $this->db->query("SELECT COUNT(*) as cnt FROM documents WHERE status = 1")->fetch()->cnt ?? 0;
                    $total_bytes = $this->db->query("SELECT SUM(file_size) as total FROM documents WHERE status = 1")->fetch()->total ?? 0;
                    $total_drives = $this->db->query("SELECT COUNT(*) as cnt FROM drives WHERE status = 1")->fetch()->cnt ?? 0;

                    // 3. Storage Breakdown Per Drive (Sorted from Largest to Smallest)
                    $drive_storage = $this->db->query("
                        SELECT dr.name, COUNT(d.id) as doc_count, COALESCE(SUM(d.file_size), 0) as total_bytes
                        FROM drives dr
                        LEFT JOIN documents d ON dr.id = d.drive_id AND d.status = 1
                        WHERE dr.status = 1
                        GROUP BY dr.id
                        ORDER BY total_bytes DESC, dr.name ASC
                    ")->fetchAll();

                    $drive_storage_list = [];
                    foreach ($drive_storage as $ds) {
                        $drive_storage_list[] = "- Drive **" . $ds->name . "**: " . $formatBytes($ds->total_bytes) . " (" . $ds->doc_count . " file dokumen)";
                    }

                    $system_prompt .= "\n\n--- DATA REAL SISTEM & PENYIMPANAN E-DRIVE (LIVE DARI DATABASE) ---\n";
                    $system_prompt .= "Ringkasan Total Sistem E-Drive saat ini:\n";
                    $system_prompt .= "- Total Dokumen: " . $total_docs . " file\n";
                    $system_prompt .= "- Total Penggunaan Kapasitas Penyimpanan: " . $formatBytes($total_bytes) . "\n";
                    $system_prompt .= "- Total Drive Aktif: " . $total_drives . " drive\n\n";
                    
                    $system_prompt .= "Rincian Kapasitas Penyimpanan per Drive (Urut dari yang Terbesar ke Terkecil):\n";
                    $system_prompt .= implode("\n", $drive_storage_list) . "\n\n";

                    $system_prompt .= "Daftar Role Resmi Pengguna E-Drive (Total " . count($roles_list) . " Role):\n" . implode("\n", $roles_list) . "\n\n";
                    $system_prompt .= "Gunakan DATA REAL LIVE di atas untuk menjawab pertanyaan mengenai statistik, kapasitas drive terbesar/terkecil, dan role pengguna E-Drive secara 100% akurat!";
                }

                $history = $this->ai_mod->get_messages($conv_id, $user_id);
                $messages = [];
                
                if (!empty($system_prompt)) {
                    $messages[] = ['role' => 'system', 'content' => $system_prompt];
                }

                $recent_history = array_slice($history, -10);
                foreach ($recent_history as $h) {
                    $messages[] = ['role' => $h->role, 'content' => $h->content];
                }

                $max_tokens = (int)$this->ai_mod->get_setting('max_tokens', 2048);
                if ($max_tokens <= 0) $max_tokens = 2048;

                $res = $client->chat($cloud_model, $messages, [
                    'temperature' => (float)$this->ai_mod->get_setting('temperature', 0.7),
                    'max_tokens' => $max_tokens
                ]);

                return [
                    'text' => $res['text'],
                    'model' => $res['model'] ?? $cloud_model,
                    'tokens_in' => $res['tokens_in'],
                    'tokens_out' => $res['tokens_out'],
                    'metadata' => $res['metadata']
                ];
            } catch (Exception $e) {
                return [
                    'text' => "⚠️ **[Cloud LLM API Error]**\n\n" . $e->getMessage() . "\n\n*Silakan periksa API Key atau Model Name Anda di menu **AI Settings**.*",
                    'model' => 'cloud-error',
                    'tokens_in' => 0,
                    'tokens_out' => 0,
                    'metadata' => ['error' => $e->getMessage()]
                ];
            }
        }

        // Attempt 2: Flowise
        $flowise_url = trim($this->ai_mod->get_setting('flowise_url', 'http://localhost:3000'));
        $flowise_api_key = trim($this->ai_mod->get_setting('flowise_api_key', ''));
        
        $flow_id = ($type === 'general') 
            ? trim($this->ai_mod->get_setting('flowise_flow_a_id', ''))
            : trim($this->ai_mod->get_setting('flowise_flow_b_id', ''));

        if (!empty($flowise_url) && !empty($flow_id)) {
            try {
                $client = new FlowiseClient($flowise_url, $flowise_api_key);
                $res = $client->sendMessage($flow_id, $question, $conv_id, ['user_id' => $user_id]);
                return [
                    'text' => $res['text'],
                    'model' => 'flowise-' . $type,
                    'tokens_in' => 0,
                    'tokens_out' => $res['tokens'] ?? 0,
                    'metadata' => $res['metadata'] ?? []
                ];
            } catch (Exception $e) {
                // Fallback to Ollama
            }
        }

        // Attempt 3: Direct Ollama
        $ollama_url = trim($this->ai_mod->get_setting('ollama_url', ''));
        $model = ($type === 'general') 
            ? trim($this->ai_mod->get_setting('default_model', 'qwen3'))
            : trim($this->ai_mod->get_setting('edrive_model', 'qwen3'));

        if (!empty($ollama_url) && $ollama_url !== 'http://localhost:11434') {
            try {
                $client = new OllamaClient($ollama_url);
                $system_prompt = $this->ai_mod->get_system_prompt($type);
                
                $history = $this->ai_mod->get_messages($conv_id, $user_id);
                $messages = [];
                
                if (!empty($system_prompt)) {
                    $messages[] = ['role' => 'system', 'content' => $system_prompt];
                }

                $recent_history = array_slice($history, -10);
                foreach ($recent_history as $h) {
                    $messages[] = ['role' => $h->role, 'content' => $h->content];
                }

                $res = $client->chat($model, $messages);
                return [
                    'text' => $res['text'],
                    'model' => $res['metadata']['model'] ?? $model,
                    'tokens_in' => $res['tokens_in'],
                    'tokens_out' => $res['tokens_out'],
                    'metadata' => $res['metadata']
                ];
            } catch (Exception $e) {
                // Fallback to smart local generator
            }
        }

        // Attempt 4: Smart Local Fallback Response (Works offline for demo/testing)
        return $this->_generate_smart_fallback($type, $question);
    }

    /**
     * Smart local fallback response generator when external AI services are offline
     */
    private function _generate_smart_fallback($type, $question) {
        $q = strtolower($question);
        $reply = "";

        if (strpos($q, '17 agustus') !== false || strpos($q, 'undangan') !== false || strpos($q, 'surat') !== false) {
            $reply = "### 📜 **FORMAT SURAT UNDANGAN PERINGATAN HUT RI KE-81 (17 AGUSTUS)**\n\n" .
                     "**PANITIA PERINGATAN HUT KEMERDEKAAN RI KE-81**  \n" .
                     "**RT 05 / RW 03 KELURAHAN SUKAMAJU**  \n" .
                     "____________________________________________________________________\n\n" .
                     "**Nomor:** 017/PAN-HUT-RI/VIII/2026  \n" .
                     "**Hal:** Undangan Rapat & Peringatan HUT RI ke-81  \n" .
                     "**Lampiran:** -  \n\n" .
                     "**Kepada Yth.**  \n" .
                     "Bapak/Ibu/Saudara/i warga RT 05 / RW 03  \n" .
                     "Di Tempat\n\n" .
                     "*Assalamu'alaikum Wr. Wb. / Salam Sejahtera,* \n\n" .
                     "Dalam rangka memeriahkan Hari Ulang Tahun (HUT) Kemerdekaan Republik Indonesia yang ke-81, kami mengundang Bapak/Ibu/Saudara/i untuk hadir pada acara Malam Puncak Peringatan 17 Agustus yang akan dilaksanakan pada:\n\n" .
                     "- 🗓️ **Hari / Tanggal:** Sabtu, 17 Agustus 2026  \n" .
                     "- ⏰ **Waktu:** 19.30 WIB s.d. Selesai  \n" .
                     "- 📍 **Tempat:** Lapangan Serbaguna RT 05  \n" .
                     "- 👕 **Pakaian:** Nuansa Merah Putih  \n\n" .
                     "**Susunan Acara:**\n" .
                     "1. Pembukaan & Menyanyikan Lagu Indonesia Raya\n" .
                     "2. Sambutan Ketua Panitia & Ketua RT\n" .
                     "3. Penyerahan Hadiah Lomba 17 Agustus\n" .
                     "4. Pentas Seni Warga & Ramah Tamah\n\n" .
                     "Demikian surat undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu/Saudara/i, kami ucapkan terima kasih.\n\n" .
                     "*Wassalamu'alaikum Wr. Wb.*\n\n" .
                     "**Hormat Kami,**  \n" .
                     "**Ketua Panitia HUT RI**\n\n" .
                     "*( Nama Ketua Panitia )*";
        } elseif (strpos($q, 'folder') !== false) {
            $reply = "### 📁 **Panduan Membuat Folder Baru di E-Drive**\n\n" .
                     "Berikut langkah-langkah membuat folder di aplikasi E-Drive:\n\n" .
                     "1. Masuk ke halaman **My Drive** melalui menu navigasi utama di bagian atas.\n" .
                     "2. Pilih **Drive** tempat Anda ingin menyimpan folder baru.\n" .
                     "3. Klik tombol **`+ Folder Baru`** atau klik kanan pada area ruang penyimpanan yang kosong.\n" .
                     "4. Masukkan **Nama Folder** yang diinginkan (contoh: *Dokumen Keuangan 2026*).\n" .
                     "5. Klik tombol **Simpan / Buat**.\n\n" .
                     "💡 *Tips: Anda juga dapat membuat sub-folder di dalam folder yang sudah ada dengan cara mengklik folder tersebut terlebih dahulu sebelum menekan tombol '+ Folder Baru'.*";
        } elseif (strpos($q, 'share') !== false || strpos($q, 'bagikan') !== false || strpos($q, 'akses') !== false) {
            $reply = "### 🔗 **Panduan Membagikan Folder / Drive**\n\n" .
                     "1. Masuk ke menu **My Drive**.\n" .
                     "2. Cari drive atau folder yang ingin Anda bagikan.\n" .
                     "3. Klik ikon titik tiga `⋮` atau klik kanan pada item tersebut, lalu pilih **Bagikan / Share**.\n" .
                     "4. Masukkan **Nama Pengguna / Role** yang ingin diberikan akses.\n" .
                     "5. Tentukan tingkat hak akses (*Lihat Saja / Full Edit*), lalu klik **Simpan Akses**.";
        } elseif (strpos($q, 'jadwal') !== false || strpos($q, 'agenda') !== false || strpos($q, 'rapat') !== false) {
            $events = $this->db->query("SELECT title, start_date FROM schedule_calendars WHERE status = 1 AND start_date >= CURDATE() ORDER BY start_date ASC LIMIT 5")->fetchAll();
            
            if (!empty($events)) {
                $reply = "### 📅 **Agenda & Jadwal Mendatang Anda**\n\n";
                foreach ($events as $ev) {
                    $reply .= "- 🗓️ **" . htmlspecialchars($ev->title) . "** (" . date('d M Y H:i', strtotime($ev->start_date)) . ")\n";
                }
                $reply .= "\nAnda dapat menambahkan agenda baru melalui modul **Schedule**.";
            } else {
                $reply = "### 📅 **Agenda & Jadwal Mendatang**\n\nBelum ada jadwal rapat atau agenda kegiatan mendatang yang tercatat di modul Schedule.";
            }
        } elseif (strpos($q, 'storage') !== false || strpos($q, 'penyimpanan') !== false || strpos($q, 'kuota') !== false) {
            $total_docs = $this->db->query("SELECT COUNT(*) as cnt FROM documents WHERE status = 1")->row()->cnt ?? 0;
            $total_size = $this->db->query("SELECT SUM(file_size) as total FROM documents WHERE status = 1")->row()->total ?? 0;
            $total_drives = $this->db->query("SELECT COUNT(*) as cnt FROM drives WHERE status = 1")->row()->cnt ?? 0;

            $reply = "### 📊 **Ringkasan Penyimpanan E-Drive**\n\n" .
                     "Berikut adalah data kapasitas penyimpanan Anda saat ini:\n\n" .
                     "- 📄 **Total Dokumen:** " . number_format($total_docs) . " file\n" .
                     "- 💾 **Total Ukuran Terpakai:** " . format_file_size($total_size) . "\n" .
                     "- 🗂️ **Jumlah Drive Diakses:** " . number_format($total_drives) . " drive\n\n" .
                     "Anda dapat mengelola atau menghapus file yang tidak diperlukan melalui menu **My Drive** atau **Recycle Bin**.";
        } elseif (strpos($q, 'upload') !== false || strpos($q, 'unggah') !== false) {
            $reply = "### 📖 **Panduan Mengunggah File ke E-Drive**\n\n" .
                     "1. Masuk ke halaman **My Drive** dari menu navigasi atas.\n" .
                     "2. Pilih folder tujuan penyimpanan file Anda.\n" .
                     "3. Klik tombol **Upload Baru** di sudut kanan atas.\n" .
                     "4. Pilih file dari komputer Anda atau seret *(drag & drop)* file ke area upload.\n" .
                     "5. Tunggu hingga proses upload selesai (100%).";
        } else {
            $assistant_name = ($type === 'general') ? 'AI Assistant' : 'E-Drive Assistant';
            $reply = "Terima kasih atas pertanyaan Anda mengenai **\"" . htmlspecialchars($question) . "\"**.\n\n" .
                     "Saya dapat membantu Anda membuat dokumen, draft surat, ringkasan teks, memberikan panduan E-Drive, maupun menganalisis data penyimpanan.\n\n" .
                     "> 💡 *Catatan: Server Ollama/Flowise saat ini belum berjalan di localhost. Setelah layanan Flowise/Ollama Anda diaktifkan di menu **AI Settings**, jawaban akan dihasilkan secara real-time dari model LLM (Qwen3/Llama3).*";
        }

        return [
            'text' => $reply,
            'model' => 'edrive-smart-fallback',
            'tokens_in' => ceil(strlen($question) / 4),
            'tokens_out' => ceil(strlen($reply) / 4),
            'metadata' => ['mode' => 'smart_fallback']
        ];
    }

    /**
     * API: Get messages for active conversation
     */
    public function api_messages($conv_id = 0) {
        header('Content-Type: application/json');
        $user_id = Session::get('user_id');
        $conv_id = (int)$conv_id;

        if ($conv_id <= 0) {
            echo json_encode(['status' => false, 'messages' => []]);
            return;
        }

        $messages = $this->ai_mod->get_messages($conv_id, $user_id);
        echo json_encode(['status' => true, 'messages' => $messages]);
    }

    /**
     * API: New conversation
     */
    public function api_new_conversation() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $user_id = Session::get('user_id');
        $type = $_POST['assistant_type'] ?? 'general';
        $title = trim($_POST['title'] ?? 'New Chat');

        $conv_id = $this->ai_mod->create_conversation($user_id, $type, $title);
        echo json_encode(['status' => true, 'conversation_id' => $conv_id, 'title' => $title]);
    }

    /**
     * API: Rename conversation
     */
    public function api_rename() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $user_id = Session::get('user_id');
        $conv_id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');

        if ($conv_id <= 0 || empty($title)) {
            echo json_encode(['status' => false, 'message' => 'Data tidak valid.']);
            return;
        }

        $this->ai_mod->rename_conversation($conv_id, $title, $user_id);
        echo json_encode(['status' => true, 'message' => 'Judul chat berhasil diubah.']);
    }

    /**
     * API: Delete conversation
     */
    public function api_delete() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $user_id = Session::get('user_id');
        $conv_id = (int)($_POST['id'] ?? 0);

        if ($conv_id <= 0) {
            echo json_encode(['status' => false, 'message' => 'ID tidak valid.']);
            return;
        }

        $this->ai_mod->delete_conversation($conv_id, $user_id);
        echo json_encode(['status' => true, 'message' => 'Chat berhasil dihapus.']);
    }

    /**
     * API: Toggle pin
     */
    public function api_toggle_pin() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $user_id = Session::get('user_id');
        $conv_id = (int)($_POST['id'] ?? 0);

        if ($conv_id <= 0) {
            echo json_encode(['status' => false, 'message' => 'ID tidak valid.']);
            return;
        }

        $this->ai_mod->toggle_pin($conv_id, $user_id);
        echo json_encode(['status' => true, 'message' => 'Status pin diubah.']);
    }

    /**
     * API: Feedback
     */
    public function api_feedback() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $user_id = Session::get('user_id');
        $message_id = (int)($_POST['message_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($message_id <= 0 || !in_array($rating, [1, 2])) {
            echo json_encode(['status' => false, 'message' => 'Rating tidak valid.']);
            return;
        }

        $this->ai_mod->save_feedback($message_id, $user_id, $rating, $comment);
        echo json_encode(['status' => true, 'message' => 'Terima kasih atas feedback Anda!']);
    }
}
