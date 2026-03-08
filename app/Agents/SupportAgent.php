<?php

namespace App\Agents;

use Core\Agent\AgentProfile;

class SupportAgent extends AgentProfile
{
    public static function name(): string
    {
        return 'support';
    }

    public static function systemPrompt(): string
    {
        return 'You are a helpful assistant specialized in your role. Use the available skills when appropriate.';
    }

    /** Return null to allow all skills, or e.g. ['echo', 'summarize'] to restrict. */
    public static function allowedSkills(): ?array
    {
        return null;
    }
}