<?php
namespace Lucid;

class Blueprint {
    private string $table;
    private array $columns = [];
    private array $foreignKeys = [];

    public function __construct(string $table) {
        $this->table = $table;
    }

    public function increments(string $column): self {
        $this->columns[] = "$column INT PRIMARY KEY AUTO_INCREMENT";
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

    public function timestamps(): self {
        $this->columns[] = "created_at DATETIME DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    public static function dropIfExists(string $table): void {
        $sql = "DROP TABLE IF EXISTS $table";
        $pdo = Connection::getInstance()->getPDO();
        $pdo->exec($sql);
    }
    
    public static function create(string $table, callable $callback): void {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $sql = $blueprint->toSql();
        $pdo = Connection::getInstance()->getPDO();
        $pdo->exec($sql);
    }

    public static function drop(string $table): void {
        $sql = "DROP TABLE IF EXISTS $table;";
        $pdo = Connection::getInstance()->getPDO();
        $pdo->exec($sql);
    }

    public function boolean(string $column): self {
        $this->columns[] = "$column TINYINT(1)";
        return $this;
    }

    public function text(string $column): self {
        $this->columns[] = "$column TEXT";
        return $this;
    }

    public function decimal(string $column, int $precision = 8, int $scale = 2): self {
        $this->columns[] = "$column DECIMAL($precision, $scale)";
        return $this;
    }

    public function date(string $column): self {
        $this->columns[] = "$column DATE";
        return $this;
}

    public function datetime(string $column): self {
        $this->columns[] = "$column DATETIME";
        return $this;
    }

    

    public function foreign(string $column, string $references, string $on): self {
        $this->foreignKeys[] = "FOREIGN KEY ($column) REFERENCES $on($references)";
        return $this;
    }

    
 
}