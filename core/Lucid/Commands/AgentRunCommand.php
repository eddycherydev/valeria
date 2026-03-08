<?php

namespace Core\Lucid\Commands;

use Core\Agent\Agent;

class AgentRunCommand
{
    /**
     * @param array<int, string> $args Remaining CLI args (e.g. --skill=echo --input='{}')
     */
    public function handle(array $args = []): void
    {
        $agent = new Agent();
        $options = $this->parseArgs(implode(' ', $args));

        if (!empty($options['skill'])) {
            $input = $this->parseInput($options['input'] ?? '{}');
            $result = $agent->runSkill($options['skill'], $input);
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            exit($result['success'] ? 0 : 1);
        }

        if (!empty($options['message'])) {
            $agentName = $options['agent'] ?? null;
            $result = $agent->chatWithAgent($agentName !== '' ? $agentName : null, $options['message']);
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            exit($result['success'] ? 0 : 1);
        }

        echo "Usage:\n";
        echo "  php lucid agent:run --skill=echo --input='{\"message\":\"hello\"}'\n";
        echo "  php lucid agent:run --message=\"Your question\" [--agent=default]\n";
        echo "  php lucid agent:list   (list agents)\n";
        echo "  php lucid skill:list   (list skills)\n";
        exit(1);
    }

    private function parseArgs(string $str): array
    {
        $out = [];
        if (preg_match_all('/--(\w+)=([^\s]+|"[^"]*")/', $str, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $out[$match[1]] = trim($match[2], '"');
            }
        }
        return $out;
    }

    private function parseInput(string $json): array
    {
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
