<?php

namespace Core\Contracts;

/**
 * A skill is a single, reusable capability that an agent can invoke.
 * Implementations live in app/Skills/ and are discovered by SkillRegistry.
 */
interface SkillInterface
{
    /**
     * Unique name used to invoke this skill (e.g. "translate", "summarize").
     */
    public static function name(): string;

    /**
     * Short description for the agent or API (e.g. "Translates text to the given language").
     */
    public static function description(): string;

    /**
     * List of parameter names this skill expects (e.g. ["text", "target_lang"]).
     *
     * @return array<string>
     */
    public static function parameters(): array;

    /**
     * Execute the skill with the given input.
     *
     * @param array<string, mixed> $input Keys should match parameters(); extra keys are ignored.
     * @return array{success: bool, result?: mixed, error?: string}
     */
    public function execute(array $input): array;
}
