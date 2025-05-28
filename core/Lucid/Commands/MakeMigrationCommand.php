<?php
namespace Core\Lucid\Commands;

class MakeMigrationCommand
{
    public function handle(string $name): void
    {
        $timestamp = date('Ymd_His');
        $fileName = "{$timestamp}_{$name}.php";
        $className = $this->toClassName($name);

        $path = __DIR__ . '/../../database/migrations/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $content = <<<PHP
        <?php

        use Core\Lucid\Schema;
        use Core\Lucid\Blueprint;

        return new class {
            public function up(): void {
                // Crea la tabla aquí
                Schema::create('nombre_de_tabla', function (\Lucid\Blueprint \$table) {
                    \$table->increments('id');
                    \$table->string('name');
                    \$table->timestamps();
                });
            }

            public function down(): void {
                // Elimina la tabla aquí
                \Lucid\Blueprint::dropIfExists('nombre_de_tabla');
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