<?php

namespace Core\Lucid\Commands;

use Core\AgentRegistry;

class AgentListCommand
{
    public function handle(): void
    {
        AgentRegistry::discover();
        $list = AgentRegistry::list();
        if (empty($list)) {
            echo "No agents registered. Create one with: php lucid make:agent <Name>\n";
            return;
        }
        echo "Registered agents:\n\n";
        foreach ($list as $agent) {
            echo "  " . $agent['name'] . "\n";
            echo "    prompt: " . substr($agent['systemPrompt'], 0, 60) . (strlen($agent['systemPrompt']) > 60 ? '...' : '') . "\n";
            echo "    skills: " . ($agent['allowedSkills'] === null ? 'all' : implode(', ', $agent['allowedSkills'])) . "\n\n";
        }
    }
}
