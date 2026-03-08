<?php

namespace Core\Agent;

use Core\Contracts\AgentProfileInterface;

/**
 * Base class for agent profiles. Override systemPrompt() and allowedSkills() as needed.
 */
abstract class AgentProfile implements AgentProfileInterface
{
    public static function allowedSkills(): ?array
    {
        return null;
    }
}
