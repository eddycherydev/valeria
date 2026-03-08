<?php

/**
 * Prompt templates for agents. Use {{ key }} for placeholders.
 * Load with: Core\AI\Prompt::load('agent.system', ['context' => '...'])
 */
return [
    'agent' => [
        'system' => 'You are a helpful assistant. You can use tools (skills) when needed. Reply in a concise, useful way.',
        'user' => '{{ message }}',
        'with_skills' => 'You have access to these skills: {{ skills_list }}. Use them when appropriate. User request: {{ message }}',
    ],
];
