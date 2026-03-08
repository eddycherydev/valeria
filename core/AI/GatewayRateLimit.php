<?php

namespace Core\AI;

/**
 * Simple file-based rate limit: max N requests per minute per key.
 */
class GatewayRateLimit
{
    private string $path;
    private int $requestsPerMinute;

    public function __construct(?string $basePath = null, int $requestsPerMinute = 60)
    {
        $root = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../..');
        $this->path = rtrim($basePath ?? ($root . '/storage/ai-gateway/rate'), DIRECTORY_SEPARATOR);
        $this->requestsPerMinute = $requestsPerMinute;
    }

    /**
     * Check and consume one slot. Throws if over limit.
     */
    public function check(string $key = 'default'): void
    {
        $file = $this->path . '/' . preg_replace('/[^a-z0-9_-]/i', '_', $key) . '.json';
        $now = time();
        $windowStart = $now - 60;

        $timestamps = [];
        if (is_file($file)) {
            $data = @json_decode((string) file_get_contents($file), true);
            if (is_array($data)) {
                $timestamps = array_filter($data, fn($t) => (int) $t >= $windowStart);
            }
        }

        if (count($timestamps) >= $this->requestsPerMinute) {
            throw new \RuntimeException('AI gateway rate limit exceeded. Try again later.');
        }

        $timestamps[] = $now;
        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
        file_put_contents($file, json_encode(array_values($timestamps)));
    }
}
