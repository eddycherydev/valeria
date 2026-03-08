<?php

namespace Core\AI;

use Core\Env;

/**
 * Gateway: single entry point for LLM calls. Routes by model, fallback, cache, rate limit.
 */
class AIGateway implements LLMInterface
{
    private ?GatewayCache $cache = null;
    private ?GatewayRateLimit $rateLimit = null;

    public function isAvailable(): bool
    {
        $gateway = Config::get('gateway');
        if (!is_array($gateway) || empty($gateway['enabled'])) {
            $name = Config::defaultProvider();
            try {
                return $this->createDirectLLM($name)->isAvailable();
            } catch (\Throwable $e) {
                return false;
            }
        }
        $providers = $this->resolveProviderList();
        foreach ($providers as $name) {
            try {
                $llm = $this->createDirectLLM($name);
                if ($llm->isAvailable()) {
                    return true;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return false;
    }

    public function chat(array $messages, array $options = []): string
    {
        $gateway = Config::get('gateway');
        if (!is_array($gateway) || empty($gateway['enabled'])) {
            $name = Config::defaultProvider();
            return $this->createDirectLLM($name)->chat($messages, $options);
        }

        $model = $options['model'] ?? $this->resolveModelFromDefaultProvider();
        $providerName = $this->resolveProviderFromModel($model);
        $cacheKey = null;
        $cache = $this->getCache($gateway);
        if ($cache !== null) {
            $cacheKey = $this->cacheKey($messages, $model, $options);
            $cached = $cache->get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $this->checkRateLimit($gateway);

        $fallback = $gateway['fallback'] ?? [$providerName];
        if (!in_array($providerName, $fallback, true)) {
            $fallback = array_merge([$providerName], $fallback);
        }
        $lastException = null;
        foreach ($fallback as $name) {
            try {
                $llm = $this->createDirectLLM($name);
                if (!$llm->isAvailable()) {
                    continue;
                }
                $reply = $llm->chat($messages, array_merge($options, ['model' => $model]));
                if ($cache !== null && $cacheKey !== null) {
                    $cache->set($cacheKey, $reply);
                }
                return $reply;
            } catch (\Throwable $e) {
                $lastException = $e;
                continue;
            }
        }
        throw $lastException ?? new \RuntimeException('No AI provider available.');
    }

    /**
     * Create LLM without going through gateway (avoid recursion).
     */
    private function createDirectLLM(string $providerName): LLMInterface
    {
        $provider = Config::getProvider($providerName);
        if ($provider === null) {
            throw new \RuntimeException("Unknown provider: $providerName");
        }
        $class = $provider['class'] ?? null;
        if (!$class || !class_exists($class)) {
            throw new \RuntimeException("Invalid provider class: $providerName");
        }
        $envKey = $provider['env_key'] ?? null;
        $apiKey = $envKey !== null ? (Env::get($envKey, '') ?? '') : '';
        $baseUrl = $provider['base_url'] ?? null;
        $model = $provider['model'] ?? null;
        return new $class($apiKey, $baseUrl, $model);
    }

    private function resolveProviderList(): array
    {
        $gateway = Config::get('gateway');
        $default = is_array($gateway) ? ($gateway['default_provider'] ?? null) : null;
        $default = $default ?? Config::defaultProvider();
        $fallback = is_array($gateway) && isset($gateway['fallback']) ? $gateway['fallback'] : [];
        return array_unique(array_merge([$default], $fallback));
    }

    private function resolveModelFromDefaultProvider(): string
    {
        $gateway = Config::get('gateway');
        $defaultName = (is_array($gateway) && !empty($gateway['default_provider']))
            ? $gateway['default_provider']
            : Config::defaultProvider();
        $provider = Config::getProvider($defaultName);
        return $provider['model'] ?? 'gpt-4o-mini';
    }

    private function resolveProviderFromModel(string $model): string
    {
        $gateway = Config::get('gateway');
        $routing = (is_array($gateway) && isset($gateway['routing']) && is_array($gateway['routing']))
            ? $gateway['routing']
            : [];
        foreach ($routing as $pattern => $providerName) {
            $regex = '#^' . str_replace(['*', '.'], ['.*', '\.'], $pattern) . '$#i';
            if (preg_match($regex, $model)) {
                return $providerName;
            }
        }
        $default = is_array($gateway) ? ($gateway['default_provider'] ?? null) : null;
        return $default ?? Config::defaultProvider();
    }

    private function getCache(array $gateway): ?GatewayCache
    {
        if ($this->cache !== null) {
            return $this->cache;
        }
        $cacheConfig = $gateway['cache'] ?? null;
        if (!is_array($cacheConfig) || empty($cacheConfig['enabled'])) {
            return null;
        }
        $ttl = (int) ($cacheConfig['ttl'] ?? 60);
        $this->cache = new GatewayCache(null, $ttl);
        return $this->cache;
    }

    private function cacheKey(array $messages, string $model, array $options): string
    {
        $normalized = json_encode([
            'messages' => $messages,
            'model' => $model,
            'temperature' => $options['temperature'] ?? null,
        ]);
        return md5($normalized);
    }

    private function checkRateLimit(array $gateway): void
    {
        $rlConfig = $gateway['rate_limit'] ?? null;
        if (!is_array($rlConfig) || !isset($rlConfig['requests_per_minute'])) {
            return;
        }
        if ($this->rateLimit === null) {
            $this->rateLimit = new GatewayRateLimit(null, (int) $rlConfig['requests_per_minute']);
        }
        $this->rateLimit->check('default');
    }
}
