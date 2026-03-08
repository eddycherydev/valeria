<?php

namespace Core\AI;

/**
 * Renders prompt templates with placeholder substitution.
 * Use for system/user prompts in agents.
 */
class Prompt
{
    /**
     * Render a template string with {{ key }} placeholders.
     *
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = []): string
    {
        $out = $template;
        foreach ($data as $key => $value) {
            $out = str_replace('{{ ' . $key . ' }}', (string) $value, $out);
        }
        return $out;
    }

    /**
     * Load a template from config/prompts.php or app/Prompts/ by name.
     * Expects a key like "agent.system" or "agent.user".
     */
    public static function load(string $name, array $data = []): string
    {
        $baseDir = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../..');
        $configPath = $baseDir . '/config/prompts.php';
        if (file_exists($configPath)) {
            $prompts = require $configPath;
            $template = self::getNested($prompts, $name);
            if (is_string($template)) {
                return self::render($template, $data);
            }
        }
        return self::render($name, $data);
    }

    /**
     * @param array<string, mixed> $arr
     */
    private static function getNested(array $arr, string $key): mixed
    {
        $parts = explode('.', $key);
        $current = $arr;
        foreach ($parts as $part) {
            if (!is_array($current) || !array_key_exists($part, $current)) {
                return null;
            }
            $current = $current[$part];
        }
        return $current;
    }
}
