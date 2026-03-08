<?php

namespace Core\AI;

/**
 * Simple file-based cache for gateway responses. Key = hash of messages + model.
 */
class GatewayCache
{
    private string $path;
    private int $ttl;

    public function __construct(?string $basePath = null, int $ttl = 60)
    {
        $root = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../..');
        $this->path = rtrim($basePath ?? ($root . '/storage/ai-gateway/cache'), DIRECTORY_SEPARATOR);
        $this->ttl = $ttl;
    }

    public function get(string $key): ?string
    {
        $file = $this->path . '/' . $key . '.json';
        if (!is_file($file)) {
            return null;
        }
        $data = @json_decode((string) file_get_contents($file), true);
        if (!is_array($data) || !isset($data['content'], $data['at'])) {
            @unlink($file);
            return null;
        }
        if (time() - (int) $data['at'] > $this->ttl) {
            @unlink($file);
            return null;
        }
        return $data['content'];
    }

    public function set(string $key, string $content): void
    {
        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
        $file = $this->path . '/' . $key . '.json';
        file_put_contents($file, json_encode([
            'at' => time(),
            'content' => $content,
        ]));
    }
}
