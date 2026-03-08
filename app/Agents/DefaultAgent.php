<?php

namespace App\Agents;

use Core\Agent\AgentProfile;

/**
 * Default agent used when no agent name is specified. Customize systemPrompt() as needed.
 */
class DefaultAgent extends AgentProfile
{
    public static function name(): string
    {
        return 'default';
    }

    public static function systemPrompt(): string
    {
        return 'You are a helpful assistant. You can use tools (skills) when needed. Reply in a concise, useful way.';
    }
}
