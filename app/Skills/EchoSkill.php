<?php

namespace App\Skills;

use Core\Contracts\SkillInterface;

/**
 * Example skill: echoes the input message. Useful for testing the agent/skills pipeline.
 */
class EchoSkill implements SkillInterface
{
    public static function name(): string
    {
        return 'echo';
    }

    public static function description(): string
    {
        return 'Echoes the given message back. Use for testing.';
    }

    public static function parameters(): array
    {
        return ['message'];
    }

    public function execute(array $input): array
    {
        $message = $input['message'] ?? '';
        return [
            'success' => true,
            'result' => ['echo' => $message],
        ];
    }
}
