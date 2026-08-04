<?php

class OpenAiClient {
    private $api_key;
    private $api_url;

    public function __construct($api_key, $api_url = 'https://api.openai.com/v1') {
        $this->api_key = $api_key;
        $this->api_url = rtrim($api_url, '/');
    }

    /**
     * Send chat completion request to OpenAI / Groq / OpenRouter
     */
    public function chat($model, $messages, $options = []) {
        $endpoint = $this->api_url . '/chat/completions';

        $max_tokens = (int)($options['max_tokens'] ?? 2048);
        if ($max_tokens <= 0) $max_tokens = 2048;

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => (float)($options['temperature'] ?? 0.7),
            'max_tokens' => $max_tokens,
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Cloud LLM connection error: " . $error);
        }

        if ($http_code >= 400) {
            $errData = json_decode($response, true);
            $errMsg = $errData['error']['message'] ?? $response;
            throw new Exception("Cloud LLM error ($http_code): " . $errMsg);
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['choices'][0]['message']['content'])) {
            throw new Exception("Invalid response from Cloud LLM API");
        }

        $reply_text = $data['choices'][0]['message']['content'];
        $tokens_in = $data['usage']['prompt_tokens'] ?? 0;
        $tokens_out = $data['usage']['completion_tokens'] ?? 0;

        return [
            'text' => $reply_text,
            'model' => $data['model'] ?? $model,
            'tokens_in' => $tokens_in,
            'tokens_out' => $tokens_out,
            'metadata' => [
                'provider' => 'openai-compatible',
                'model' => $data['model'] ?? $model
            ]
        ];
    }
}
