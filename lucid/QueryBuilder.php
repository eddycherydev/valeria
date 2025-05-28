<?php
namespace Lucid;

class QueryBuilder {
    protected string $table = '';
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $orderBys = [];
    protected ?int $limit = null;
    protected array $groupBys = [];

    protected array $joins = [];



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

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }
        $sql .= " LIMIT 1";

        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    public function insert(array $data): bool {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
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
        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
        return $stmt->execute(array_merge($values, $this->bindings));
    }

    

    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBys[] = "$column $direction";
        return $this;
    }

    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }

    public function groupBy(string ...$columns): self {
        $this->groupBys = array_merge($this->groupBys, $columns);
        return $this;
    }

    public function get(): array {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }

        if (!empty($this->groupBys)) {
            $sql .= " GROUP BY " . implode(", ", $this->groupBys);
        }

        if (!empty($this->orderBys)) {
            $sql .= " ORDER BY " . implode(", ", $this->orderBys);
        }

        if (!is_null($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
        }

        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function join(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }

    
}
