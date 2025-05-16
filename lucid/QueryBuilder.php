<?php
namespace Lucid;

class QueryBuilder {
    protected string $table = '';
    protected array $wheres = [];
    protected array $bindings = [];

    public static function table(string $table): self {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function where(string $column, $value): self {
        $this->wheres[] = "$column = ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function first(): ?object {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }
        $sql .= " LIMIT 1";

        $stmt = Connection::getInstance()->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    public function insert(array $data): bool {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = Connection::getInstance()->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function update(array $data): bool {
        $set = [];
        $values = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = ?";
            $values[] = $value;
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }
        $stmt = Connection::getInstance()->prepare($sql);
        return $stmt->execute(array_merge($values, $this->bindings));
    }
}
