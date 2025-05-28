<?php
namespace Core\Lucid\Commands;

use Core\Lucid\Connection;
use PDO;

class RollbackCommand {
    public function handle(): void {
        $pdo = Connection::getInstance()->getPDO();

        // Obtener el batch más reciente
        $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
        $maxBatch = $stmt->fetch(PDO::FETCH_ASSOC)['max_batch'];

        if (!$maxBatch) {
            echo "No hay migraciones para revertir.\n";
            return;
        }

        // Obtener migraciones del último batch
        $stmt = $pdo->prepare("SELECT migration FROM migrations WHERE batch = ?");
        $stmt->execute([$maxBatch]);
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Revertir migraciones en orden inverso
        rsort($migrations);

        foreach ($migrations as $migrationName) {
            $file = __DIR__ . '/../../database/migrations/' . $migrationName;
            if (!file_exists($file)) {
                echo "Archivo de migración $migrationName no encontrado, se omite.\n";
                continue;
            }

            $migration = require $file;

            if (method_exists($migration, 'down')) {
                $migration->down();
                echo "Rollback de $migrationName ejecutado.\n";

                // Eliminar registro de migración
                $stmt = $pdo->prepare("DELETE FROM migrations WHERE migration = ?");
                $stmt->execute([$migrationName]);
            } else {
                echo "No se puede revertir $migrationName: falta método down().\n";
            }
        }

        echo "Rollback del batch $maxBatch completado.\n";
    }
}