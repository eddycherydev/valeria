<?php

namespace Core\AI;

use Core\Support\Env;

/**
 * Loads config/ai.php and provides access to default provider, models, and LLM creation.
 */
class Config
{
    private static ?array $config = null;

    public static function load(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }
        $baseDir = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../..');
        $path = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/config/ai.php';
        if (file_exists($path)) {
            self::$config = require $path;
        } else {
            self::$config = self::defaults();
        }
        return self::$config;
    }

    /**
     * Get a top-level key (e.g. 'default', 'temperature').
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $config = self::load();
        return $config[$key] ?? $default;
    }

    /**
     * Get a provider config by name (e.g. 'openai').
     *
     * @return array<string, mixed>|null
     */
    public static function getProvider(string $name): ?array
    {
        $config = self::load();
        $providers = $config['providers'] ?? [];
        return $providers[$name] ?? null;
    }

    /**
     * Name of the default provider from config.
     */
    public static function defaultProvider(): string
    {
        return (string) self::get('default', 'openai');
    }

    /**
     * Build an LLM instance. If gateway is enabled in config, returns AIGateway; otherwise the direct provider.
     */
    public static function createLLM(?string $providerName = null): LLMInterface
    {
        $config = self::load();
        $gateway = $config['gateway'] ?? null;
        if (is_array($gateway) && !empty($gateway['enabled'])) {
            return new AIGateway();
        }

        $name = $providerName ?? self::defaultProvider();
        $provider = self::getProvider($name);
        if ($provider === null) {
            throw new \RuntimeException("Unknown AI provider: $name. Check config/ai.php.");
        }
        $class = $provider['class'] ?? null;
        if (!$class || !class_exists($class)) {
            throw new \RuntimeException("Invalid or missing provider class in config/ai.php for: $name.");
        }
        $envKey = $provider['env_key'] ?? null;
        $apiKey = $envKey !== null ? (Env::get($envKey, '') ?? '') : '';
        $baseUrl = $provider['base_url'] ?? null;
        $model = $provider['model'] ?? null;
        return new $class($apiKey, $baseUrl, $model);
    }

    /**
     * List provider names and their model (for display / debugging).
     *
     * @return array<int, array{name: string, model: string|null}>
     */
    public static function listProviders(): array
    {
        $config = self::load();
        $providers = $config['providers'] ?? [];
        $out = [];
        foreach ($providers as $name => $p) {
            $out[] = [
                'name' => $name,
                'model' => $p['model'] ?? null,
            ];
        }
        return $out;
    }

    private static function defaults(): array
    {
        return [
            'default' => 'openai',
            'temperature' => 0.7,
            'providers' => [
                'openai' => [
                    'class' => OpenAILLM::class,
                    'model' => 'gpt-4o-mini',
                    'base_url' => 'https://api.openai.com',
                    'env_key' => 'OPENAI_API_KEY',
                ],
            ],
        ];
    }
}
