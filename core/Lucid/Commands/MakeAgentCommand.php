<?php

namespace Core\Lucid\Commands;

class MakeAgentCommand
{
    public function handle(string $name): void
    {
        $root = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../../..');
        $root = rtrim($root, DIRECTORY_SEPARATOR);
        $className = self::toClassName($name);
        $agentName = self::toAgentName($className);
        $directory = $root . '/app/Agents';
        $filePath = $directory . '/' . $className . '.php';

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filePath)) {
            fwrite(STDERR, "Agent $className already exists. Skipped.\n");
            return;
        }

        $content = <<<PHP
<?php

namespace App\Agents;

use Core\Agent\AgentProfile;

class $className extends AgentProfile
{
    public static function name(): string
    {
        return '$agentName';
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
PHP;

        file_put_contents($filePath, $content);
        echo "Agent created: app/Agents/$className.php\n";
    }

    private static function toClassName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name))) . 'Agent';
    }

    private static function toAgentName(string $className): string
    {
        $base = preg_replace('/Agent$/', '', $className);
        return strtolower($base);
    }
}
