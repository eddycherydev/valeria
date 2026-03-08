<?php

namespace Core\Contracts;

/**
 * An agent profile defines a named agent with its own system prompt and optional skill filter.
 * Create classes in app/Agents/ that implement this interface (or extend AgentProfile).
 */
interface AgentProfileInterface
{
    /**
     * Unique name to invoke this agent (e.g. "support", "sales").
     */
    public static function name(): string;

    /**
     * System prompt for the LLM when this agent is used.
     */
    public static function systemPrompt(): string;

    /**
     * List of skill names this agent is allowed to use. Return null or empty to allow all.
     *
     * @return array<string>|null
     */
    public static function allowedSkills(): ?array;
}
