<?php

/**
 * AI / LLM configuration. Edit here to change models, providers, and defaults.
 * API keys stay in .env (see 'env_key' per provider); do not put secrets in this file.
 *
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Default provider
    |--------------------------------------------------------------------------
    | Key of the provider to use when none is specified (e.g. in Agent chat).
    */
    'default' => 'openai',

    /*
    |--------------------------------------------------------------------------
    | Default temperature
    |--------------------------------------------------------------------------
    | 0 = deterministic, 1 = more creative. Used when not overridden per request.
    */
    'temperature' => 0.7,

    /*
    |--------------------------------------------------------------------------
    | AI Gateway
    |--------------------------------------------------------------------------
    | When enabled, all LLM calls go through the gateway: routing by model,
    | fallback to other providers on failure, optional cache and rate limit.
    */
    'gateway' => [
        'enabled' => false,
        'default_provider' => 'openai',
        'routing' => [
            'gpt-4*' => 'openai',
            'gpt-3.5*' => 'openai',
            'claude*' => 'anthropic',
            'llama*' => 'ollama',
        ],
        'fallback' => ['openai', 'openai_gpt4'],
        'cache' => [
            'enabled' => true,
            'ttl' => 60,
        ],
        'rate_limit' => [
            'requests_per_minute' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    | Each provider can have: class, model, base_url, env_key (for API key), etc.
    | API key is read from .env using env_key (e.g. OPENAI_API_KEY).
    */
    'providers' => [
        'openai' => [
            'class' => \Core\AI\OpenAILLM::class,
            'model' => 'gpt-4o-mini',
            'base_url' => 'https://api.openai.com',
            'env_key' => 'OPENAI_API_KEY',
        ],
        'openai_gpt4' => [
            'class' => \Core\AI\OpenAILLM::class,
            'model' => 'gpt-4o',
            'base_url' => 'https://api.openai.com',
            'env_key' => 'OPENAI_API_KEY',
        ],
        // Example: local Ollama (no API key)
        // 'ollama' => [
        //     'class' => \Core\AI\OllamaLLM::class,
        //     'model' => 'llama3.2',
        //     'base_url' => 'http://localhost:11434',
        //     'env_key' => null,
        // ],
    ],
];
