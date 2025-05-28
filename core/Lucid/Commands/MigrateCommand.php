<?php
namespace Core\Lucid\Commands;

use Core\Lucid\Connection;
use PDO;

class MigrateCommand {
    public function handle(): void {
        $pdo = Connection::getInstance()->getPDO();

        // Crear tabla migrations si no existe
       $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT PRIMARY KEY AUTO_INCREMENT,
                migration VARCHAR(255),
                batch INT,
                migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Migraciones ya ejecutadas
        $stmt = $pdo->query("SELECT migration FROM migrations");
        $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Archivos de migraciones ordenados
        $files = glob(__DIR__ . '/../../database/migrations/*.php');
        sort($files);

        // Último batch
        $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
        $maxBatch = $stmt->fetch(PDO::FETCH_ASSOC)['max_batch'] ?? 0;
        $batch = $maxBatch + 1;

        foreach ($files as $file) {
            $migrationName = basename($file);

            if (in_array($migrationName, $executed)) {
                continue; // Saltar migraciones ya ejecutadas
            }

            $migration = require $file;

            if (method_exists($migration, 'up')) {
                $migration->up();
                echo "Migración $migrationName ejecutada.\n";

                // Registrar migración
                $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$migrationName, $batch]);
            }
        }

        echo "Todas las migraciones pendientes se ejecutaron.\n";
    }
}