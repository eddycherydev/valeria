<?php
namespace Core\Lucid\Commands;

class MakeMigrationCommand
{
    private static function migrationsPath(): string
    {
        $root = defined('PROJECT_ROOT') ? PROJECT_ROOT : (__DIR__ . '/../../..');
        return rtrim($root, DIRECTORY_SEPARATOR) . '/database/migrations';
    }

    public function handle(string $name): void
    {
        $timestamp = date('Ymd_His');
        $fileName = "{$timestamp}_{$name}.php";

        $path = self::migrationsPath() . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $content = <<<PHP
<?php
use Core\Lucid\Schema;
use Core\Lucid\Blueprint;

return new class {
    public function up(): void {
        Schema::create('nombre_de_tabla', function (Blueprint \$table) {
            \$table->increments('id');
            \$table->string('name');
            \$table->timestamps();
        });
    }

    public function down(): void {
        Blueprint::drop('nombre_de_tabla');
    }
};
PHP;

        file_put_contents($path . $fileName, $content);
        echo "Migración creada: $fileName\n";
    }

    private function toClassName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    }
}