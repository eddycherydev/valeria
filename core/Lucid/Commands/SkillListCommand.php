<?php

namespace Core\Lucid\Commands;

use Core\SkillRegistry;

class SkillListCommand
{
    public function handle(): void
    {
        SkillRegistry::discover();
        $list = SkillRegistry::list();
        if (empty($list)) {
            echo "No skills registered. Add classes in app/Skills/ that implement Core\\Contracts\\SkillInterface.\n";
            return;
        }
        echo "Available skills:\n\n";
        foreach ($list as $skill) {
            echo "  " . $skill['name'] . "\n";
            echo "    " . $skill['description'] . "\n";
            echo "    parameters: " . implode(', ', $skill['parameters']) . "\n\n";
        }
    }
}
