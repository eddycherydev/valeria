<?php

namespace App\Controllers;

use Core\Agent;

class AgentController
{
    /**
     * GET /api/skills — list registered skills (name, description, parameters).
     */
    public function listSkills(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $agent = new Agent();
        echo json_encode(['skills' => $agent->listSkills()], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/agents — list registered agent profiles (app/Agents/).
     */
    public function listAgents(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $agent = new Agent();
        echo json_encode(['agents' => $agent->listAgents()], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/agent — run a skill or chat with the LLM (optionally with an agent profile).
     * Body: {"skill": "echo", "input": {...}} or {"message": "...", "agent": "default"}.
     */
    public function run(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?? [];
        $agent = new Agent();

        if (!empty($body['skill'])) {
            $result = $agent->runSkill($body['skill'], $body['input'] ?? []);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return;
        }

        if (!empty($body['message'])) {
            $agentName = isset($body['agent']) && $body['agent'] !== '' ? (string) $body['agent'] : null;
            $result = $agent->chatWithAgent($agentName, (string) $body['message']);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Provide "skill" + "input" or "message" (optional "agent") in the request body.',
        ], JSON_UNESCAPED_UNICODE);
    }
}
