<?php

namespace Core\AI;

/**
 * Abstraction for LLM providers (OpenAI, Anthropic, Ollama, etc.).
 */
interface LLMInterface
{
    /**
     * Send a chat request and return the assistant message content.
     *
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options Provider-specific (e.g. temperature, model)
     * @return string Assistant reply text
     * @throws \RuntimeException on API or network error
     */
    public function chat(array $messages, array $options = []): string;

    /**
     * Whether this provider is configured and available.
     */
    public function isAvailable(): bool;
}
