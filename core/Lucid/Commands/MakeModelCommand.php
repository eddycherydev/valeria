<?php
namespace Core\Lucid\Commands;

class MakeModelCommand {
    public function handle(string $modelName): void {
        $tableName = strtolower($modelName) . 's';

        $modelTemplate = <<<PHP
<?php
namespace App\Models;

use Core\Lucid\Model;

class $modelName extends Model {
    protected static string \$table = '$tableName';
}
PHP;

        $directory = __DIR__ . '/../../app/Models/';
        $filePath = $directory . $modelName . '.php';

        // Crear la carpeta si no existe
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
           // echo "Directorio app/Models creado.\n";
        }

        // Evitar sobrescribir si ya existe
        if (file_exists($filePath)) {
            echo "El modelo $modelName ya existe. Operación cancelada.\n";
            return;
        }

        file_put_contents($filePath, $modelTemplate);
        echo "Modelo $modelName creado en app/Models.\n";
    }
}
