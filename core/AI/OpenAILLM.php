<?php

namespace Core\AI;

use Core\Env;

/**
 * OpenAI-compatible chat API (OpenAI, Azure OpenAI, or local proxies).
 */
class OpenAILLM implements LLMInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct(?string $apiKey = null, ?string $baseUrl = null, ?string $model = null)
    {
        $this->apiKey = $apiKey ?? Env::get('OPENAI_API_KEY', '');
        $this->baseUrl = rtrim($baseUrl ?? Env::get('OPENAI_BASE_URL', 'https://api.openai.com'), '/');
        $this->model = $model ?? Env::get('OPENAI_MODEL', 'gpt-4o-mini');
    }

    public function isAvailable(): bool
    {
        return $this->apiKey !== '';
    }

    public function chat(array $messages, array $options = []): string
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('OpenAI API key not set (OPENAI_API_KEY).');
        }
        $model = $options['model'] ?? $this->model;
        $body = [
            'model' => $model,
            'messages' => $messages,
        ];
        if (isset($options['temperature'])) {
            $body['temperature'] = (float) $options['temperature'];
        }
        $json = json_encode($body);
        if ($json === false) {
            throw new \RuntimeException('JSON encode failed.');
        }
        $url = $this->baseUrl . '/v1/chat/completions';
        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiKey,
                ],
                'content' => $json,
                'ignore_errors' => true,
            ],
        ]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            $err = error_get_last();
            throw new \RuntimeException('OpenAI request failed: ' . ($err['message'] ?? 'unknown'));
        }
        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('OpenAI invalid JSON: ' . substr($response, 0, 200));
        }
        if (isset($decoded['error']['message'])) {
            throw new \RuntimeException('OpenAI API error: ' . $decoded['error']['message']);
        }
        $content = $decoded['choices'][0]['message']['content'] ?? '';
        return (string) $content;
    }
}
