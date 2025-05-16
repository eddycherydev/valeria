<?php
namespace Lucid\Commands;

class MakeModelCommand {
    public function handle(string $modelName): void {
        $modelTemplate = <<<PHP
<?php
namespace App\Models;

use Lucid\Model;

class $modelName extends Model {
    protected static string \$table = strtolower('$modelName') . 's';
}
PHP;

        $filePath = __DIR__ . '/../../app/Models/' . $modelName . '.php';
        file_put_contents($filePath, $modelTemplate);
        echo "Modelo $modelName creado en app/Models.\n";
    }
}
