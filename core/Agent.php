<?php

namespace Core;

use Core\AI\Config as AIConfig;
use Core\AI\LLMInterface;
use Core\AI\Prompt;

/**
 * Orchestrator: runs skills by name or delegates to an LLM with skills in context.
 * LLM and model come from config/ai.php unless you pass a custom LLM.
 */
class Agent
{
    private ?LLMInterface $llm = null;

    public function __construct(?LLMInterface $llm = null)
    {
        $this->llm = $llm ?? AIConfig::createLLM();
    }

    /**
     * Run a single skill by name. No LLM involved.
     *
     * @param array<string, mixed> $input
     * @return array{success: bool, result?: mixed, error?: string}
     */
    public function runSkill(string $skillName, array $input): array
    {
        return SkillRegistry::run($skillName, $input);
    }

    /**
     * Chat with the LLM (default prompt and all skills).
     * If no API key, returns a message suggesting to run a skill instead.
     */
    public function chat(string $message, bool $includeSkillsInContext = true): array
    {
        return $this->chatWithAgent(null, $message, $includeSkillsInContext);
    }

    /**
     * Chat using a registered agent profile by name (app/Agents/).
     * Uses the profile's systemPrompt() and allowedSkills(); if agent name is null, uses default.
     */
    public function chatWithAgent(?string $agentName, string $message, bool $includeSkillsInContext = true): array
    {
        if (!$this->llm->isAvailable()) {
            return [
                'success' => false,
                'error' => 'LLM not configured. Set OPENAI_API_KEY in .env or use agent:run --skill=echo --input=\'{"message":"hi"}\'',
                'skills' => $this->skillsListForResponse(null),
            ];
        }
        $system = $this->resolveSystemPrompt($agentName);
        $allowedSkills = $this->resolveAllowedSkills($agentName);
        if ($includeSkillsInContext) {
            $list = $this->skillsListForResponse($allowedSkills);
            if ($list !== '') {
                $system .= "\n\nAvailable tools (skills): " . $list . ". Suggest using one when appropriate.";
            }
        }
        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $message],
        ];
        $options = [];
        $temperature = AIConfig::get('temperature');
        if ($temperature !== null) {
            $options['temperature'] = (float) $temperature;
        }
        try {
            $reply = $this->llm->chat($messages, $options);
            return ['success' => true, 'reply' => $reply];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function resolveSystemPrompt(?string $agentName): string
    {
        if ($agentName !== null && $agentName !== '') {
            $class = AgentRegistry::get($agentName);
            if ($class !== null) {
                return $class::systemPrompt();
            }
        }
        return Prompt::load('agent.system');
    }

    /**
     * @param array<string>|null $allowedSkills
     */
    private function resolveAllowedSkills(?string $agentName): ?array
    {
        if ($agentName !== null && $agentName !== '') {
            $class = AgentRegistry::get($agentName);
            if ($class !== null) {
                return $class::allowedSkills();
            }
        }
        return null;
    }

    /**
     * List all registered skills (for CLI or API).
     *
     * @return array<int, array{name: string, description: string, parameters: array<string>}>
     */
    public function listSkills(): array
    {
        return SkillRegistry::list();
    }

    /**
     * List all registered agent profiles (app/Agents/).
     *
     * @return array<int, array{name: string, systemPrompt: string, allowedSkills: array|null}>
     */
    public function listAgents(): array
    {
        return AgentRegistry::list();
    }

    /**
     * @param array<string>|null $allowedSkills If set, only these skill names are included; otherwise all.
     */
    private function skillsListForResponse(?array $allowedSkills = null): string
    {
        $list = SkillRegistry::list();
        $parts = [];
        foreach ($list as $s) {
            if ($allowedSkills !== null && $allowedSkills !== [] && !in_array($s['name'], $allowedSkills, true)) {
                continue;
            }
            $params = implode(', ', $s['parameters']);
            $parts[] = $s['name'] . '(' . $params . '): ' . $s['description'];
        }
        return implode('; ', $parts);
    }
}
