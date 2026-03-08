<?php

namespace Core\Support;

class Env
{
    public static function load(string $path): void
    {
        if (is_dir($path)) {
            $path = rtrim($path, DIRECTORY_SEPARATOR) . '/.env';
        }

        if (!file_exists($path)) {
            throw new \Exception("Env file not found at: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));

            if (!array_key_exists($key, $_ENV)) {
                $value = trim($value, "\"'");
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
