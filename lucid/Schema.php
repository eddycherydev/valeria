<?php
namespace Lucid;

use PDO;

class Schema {
    public static function create(string $table, callable $callback): void {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        $pdo = Connection::getInstance();
        $pdo->exec($sql);
    }
}

class Blueprint {
    private string $table;
    private array $columns = [];

    public function __construct(string $table) {
        $this->table = $table;
    }

    public function increments(string $column): self {
        $this->columns[] = "$column INTEGER PRIMARY KEY AUTOINCREMENT";
        return $this;
    }

    public function string(string $column, int $length = 255): self {
        $this->columns[] = "$column VARCHAR($length)";
        return $this;
    }

    public function integer(string $column): self {
        $this->columns[] = "$column INTEGER";
        return $this;
    }

    public function toSql(): string {
        $cols = implode(", ", $this->columns);
        return "CREATE TABLE IF NOT EXISTS {$this->table} ({$cols});";
    }
}
