<?php
namespace Core\Lucid;

use PDO;
use Core\Lucid\Connection;
use Core\Lucid\Blueprint;

class Schema {
    public static function create(string $table, callable $callback): void {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        $pdo = Connection::getInstance()->getPDO();
        $pdo->exec($sql);
    }
}

