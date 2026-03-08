<?php

namespace Core;

use Core\Contracts\AgentProfileInterface;

class AgentRegistry
{
    private static array $agents = [];
    private static bool $discovered = false;

    public static function register(string $className): void
    {
        if (!is_subclass_of($className, AgentProfileInterface::class)) {
            return;
        }
        $name = $className::name();
        self::$agents[$name] = $className;
    }

    public static function discover(): void
    {
        if (self::$discovered) {
            return;
        }
        $baseDir = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/..');
        $path = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/app/Agents';
        if (!is_dir($path)) {
            self::$discovered = true;
            return;
        }
        foreach (glob($path . '/*.php') as $file) {
            $className = self::pathToClass($file, $path);
            if ($className && class_exists($className)) {
                self::register($className);
            }
        }
        self::$discovered = true;
    }

    public static function get(string $name): ?string
    {
        self::discover();
        return self::$agents[$name] ?? null;
    }

    /**
     * @return array<int, array{name: string, systemPrompt: string, allowedSkills: array|null}>
     */
    public static function list(): array
    {
        self::discover();
        $out = [];
        foreach (self::$agents as $name => $class) {
            $out[] = [
                'name' => $name,
                'systemPrompt' => $class::systemPrompt(),
                'allowedSkills' => $class::allowedSkills(),
            ];
        }
        return $out;
    }

    private static function pathToClass(string $file, string $basePath): ?string
    {
        $relative = str_replace([$basePath . '/', '.php'], ['', ''], $file);
        return 'App\\Agents\\' . str_replace('/', '\\', $relative);
    }
}
