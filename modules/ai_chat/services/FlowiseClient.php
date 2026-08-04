<?php

class FlowiseClient {
    private $url;
    private $api_key;

    public function __construct($url, $api_key = '') {
        $this->url = rtrim($url, '/');
        $this->api_key = $api_key;
    }

    /**
     * Send a message to a Flowise chatflow
     * 
     * @param string $chatflow_id The Flowise flow ID
     * @param string $question The user's message
     * @param string $session_id The session/conversation ID to maintain history
     * @param array $additional_data Extra data (like user ID) to pass to flowise
     * @return array Contains 'text', 'metadata', 'tokens'
     */
    public function sendMessage($chatflow_id, $question, $session_id, $additional_data = []) {
        $endpoint = $this->url . '/api/v1/prediction/' . $chatflow_id;
        
        $payload = [
            'question' => $question,
            'overrideConfig' => [
                'sessionId' => (string) $session_id
            ]
        ];

        // Pass internal API key and user context if we have internal endpoints
        if (!empty($additional_data)) {
            // Flowise doesn't natively forward arbitrary headers easily without custom tools,
            // but we can pass it in overrideConfig.vars if the flow is configured to accept it
            $payload['overrideConfig']['vars'] = $additional_data;
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        if (!empty($this->api_key)) {
            $headers[] = 'Authorization: Bearer ' . $this->api_key;
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Flowise connection error: " . $error);
        }

        if ($http_code >= 400) {
            $errData = json_decode($response, true);
            $errMsg = $errData['message'] ?? $response;
            throw new Exception("Flowise error ($http_code): " . $errMsg);
        }

        $data = json_decode($response, true);
        if (!$data) {
            throw new Exception("Invalid response from Flowise");
        }

        // Parse response
        // Flowise usually returns just the text string, or an object with 'text', 'sourceDocuments', etc.
        // It depends on the flow output type.
        
        $result = [
            'text' => '',
            'metadata' => [],
            'tokens' => 0
        ];

        if (is_string($data)) {
            $result['text'] = $data;
        } elseif (is_array($data)) {
            $result['text'] = $data['text'] ?? ($data['json'] ?? json_encode($data));
            
            // Extract sources if RAG is used
            if (isset($data['sourceDocuments'])) {
                $result['metadata']['sources'] = $data['sourceDocuments'];
            }
            
            // Flowise might pass token usage if configured in the agent
            if (isset($data['usedTokens'])) {
                $result['tokens'] = $data['usedTokens'];
            }
        }

        // Estimate tokens if not provided (rough estimate: 1 word ~ 1.3 tokens)
        if ($result['tokens'] === 0) {
            $word_count = str_word_count(strip_tags($result['text']) . ' ' . $question);
            $result['tokens'] = ceil($word_count * 1.3);
        }

        return $result;
    }
}
