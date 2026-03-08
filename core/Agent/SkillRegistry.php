<?php

namespace Core\Agent;

use Core\Contracts\SkillInterface;

class SkillRegistry
{
    private static array $skills = [];
    private static bool $discovered = false;

    public static function register(string $className): void
    {
        if (!is_subclass_of($className, SkillInterface::class)) {
            return;
        }
        $name = $className::name();
        self::$skills[$name] = $className;
    }

    public static function discover(): void
    {
        if (self::$discovered) {
            return;
        }
        $baseDir = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../..');
        $path = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/app/Skills';
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

    public static function get(string $name): ?SkillInterface
    {
        self::discover();
        $class = self::$skills[$name] ?? null;
        if ($class === null) {
            return null;
        }
        return new $class();
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, result?: mixed, error?: string}
     */
    public static function run(string $name, array $input): array
    {
        $skill = self::get($name);
        if ($skill === null) {
            return ['success' => false, 'error' => "Skill not found: $name"];
        }
        return $skill->execute($input);
    }

    /**
     * @return array<int, array{name: string, description: string, parameters: array<string>}>
     */
    public static function list(): array
    {
        self::discover();
        $out = [];
        foreach (self::$skills as $name => $class) {
            $out[] = [
                'name' => $name,
                'description' => $class::description(),
                'parameters' => $class::parameters(),
            ];
        }
        return $out;
    }

    private static function pathToClass(string $file, string $basePath): ?string
    {
        $relative = str_replace([$basePath . '/', '.php'], ['', ''], $file);
        return 'App\\Skills\\' . str_replace('/', '\\', $relative);
    }
}
