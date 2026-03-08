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

        // Create the folder if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
           // echo "Directorio app/Models creado.\n";
        }

        // Avoid overwriting if it already exists
        if (file_exists($filePath)) {
            echo "The model $modelName already exists. Operation cancelled.\n";
            return;
        }

        file_put_contents($filePath, $modelTemplate);
        echo "Model $modelName created in app/Models.\n";
    }
}
