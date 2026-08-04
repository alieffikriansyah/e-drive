<?php

class OllamaClient {
    private $url;

    public function __construct($url) {
        $this->url = rtrim($url, '/');
    }

    /**
     * Send a chat request to Ollama
     * 
     * @param string $model The model name (e.g., qwen3, llama3)
     * @param array $messages Array of message objects [['role' => 'user', 'content' => 'hello'], ...]
     * @param array $options Additional model options (temperature, etc.)
     * @return array Contains 'text', 'metadata', 'tokens'
     */
    public function chat($model, $messages, $options = []) {
        $endpoint = $this->url . '/api/chat';
        
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'stream' => false,
        ];

        if (!empty($options)) {
            $payload['options'] = $options;
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 20
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Ollama connection error: " . $error);
        }

        if ($http_code >= 400) {
            $errData = json_decode($response, true);
            $errMsg = $errData['error'] ?? $response;
            throw new Exception("Ollama error ($http_code): " . $errMsg);
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['message'])) {
            throw new Exception("Invalid response from Ollama");
        }

        $result = [
            'text' => $data['message']['content'] ?? '',
            'metadata' => [
                'model' => $data['model'],
                'created_at' => $data['created_at'],
                'total_duration' => $data['total_duration'] ?? 0,
            ],
            'tokens_in' => $data['prompt_eval_count'] ?? 0,
            'tokens_out' => $data['eval_count'] ?? 0,
        ];
        
        $result['tokens'] = $result['tokens_in'] + $result['tokens_out'];

        return $result;
    }
}
