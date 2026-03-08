<?php

namespace Core\Lucid\Commands;

class MakeSkillCommand
{
    public function handle(string $name): void
    {
        $root = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../../..');
        $root = rtrim($root, DIRECTORY_SEPARATOR);
        $className = self::toClassName($name);
        $skillName = self::toSkillName($className);
        $directory = $root . '/app/Skills';
        $filePath = $directory . '/' . $className . '.php';

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filePath)) {
            fwrite(STDERR, "Skill $className already exists. Skipped.\n");
            return;
        }

        $content = <<<PHP
<?php

namespace App\Skills;

use Core\Contracts\SkillInterface;

class $className implements SkillInterface
{
    public static function name(): string
    {
        return '$skillName';
    }

    public static function description(): string
    {
        return 'Description of $skillName.';
    }

    public static function parameters(): array
    {
        return ['input'];
    }

    public function execute(array \$input): array
    {
        \$value = \$input['input'] ?? '';
        return [
            'success' => true,
            'result' => ['output' => \$value],
        ];
    }
}
PHP;

        file_put_contents($filePath, $content);
        echo "Skill created: app/Skills/$className.php\n";
    }

    private static function toClassName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name))) . 'Skill';
    }

    private static function toSkillName(string $className): string
    {
        return strtolower(preg_replace('/Skill$/', '', $className));
    }
}
