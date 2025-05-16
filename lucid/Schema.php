<?php
namespace Lucid;

use PDO;
use Lucid\Connection;
use Lucid\Blueprint;

class Schema {
    public static function create(string $table, callable $callback): void {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        $pdo = Connection::getInstance()->getPDO();
        $pdo->exec($sql);
    }
}

