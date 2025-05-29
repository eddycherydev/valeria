<?php
namespace Core\Lucid;

class QueryBuilder
{
    /*---------------------------------
    | Atributos
    |---------------------------------*/
    protected string  $table       = '';
    protected array   $wheres      = [];
    protected array   $bindings    = [];
    protected array   $orderBys    = [];
    protected ?int    $limit       = null;
    protected array   $groupBys    = [];
    protected array   $joins       = [];

    /** Clase del modelo que pidió el builder */
    protected ?string $modelClass  = null;

    /*---------------------------------
    | Instanciación
    |---------------------------------*/
    public static function table(string $table): self
    {
        $instance        = new self();
        $instance->table = $table;
        return $instance;
    }

    /** Para que el modelo (User, Post…) se asocie al builder */
    public function setModel(string $class): self
    {
        $this->modelClass = $class;
        return $this;
    }

    /*---------------------------------
    | Cláusulas
    |---------------------------------*/
    public function where(string $column, $value): self
    {
        $this->wheres[]   = "$column = ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBys[] = "$column $direction";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groupBys = array_merge($this->groupBys, $columns);
        return $this;
    }

    /* Joins */
    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }

    /*---------------------------------
    | Ejecución
    |---------------------------------*/
    public function first(): ?object
    {
        $sql = $this->buildSelect() . " LIMIT 1";

        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
        $stmt->execute($this->bindings);

        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        if (!$result) {
            return null;
        }

        return $this->castToModel($result);
    }

    public function get(): array
    {
        $sql  = $this->buildSelect();
        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
        $stmt->execute($this->bindings);

        $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (!$this->modelClass) {
            return $rows;
        }

        return array_map([$this, 'castToModel'], $rows);
    }

    /*---------------------------------
    | Insert / Update
    |---------------------------------*/
    public function insert(array $data): bool
    {
        $columns      = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql          = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function update(array $data): bool
    {
        $set    = [];
        $values = [];
        foreach ($data as $key => $value) {
            $set[]    = "$key = ?";
            $values[] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }

        $stmt = Connection::getInstance()->getPDO()->prepare($sql);
        return $stmt->execute(array_merge($values, $this->bindings));
    }

    /*---------------------------------
    | Helpers internos
    |---------------------------------*/
    private function buildSelect(): string
    {
        $sql = "SELECT * FROM {$this->table}";

        if ($this->joins) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
        if ($this->wheres) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }
        if ($this->groupBys) {
            $sql .= " GROUP BY " . implode(", ", $this->groupBys);
        }
        if ($this->orderBys) {
            $sql .= " ORDER BY " . implode(", ", $this->orderBys);
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        return $sql;
    }

    /** Convierte stdClass → instancia del modelo (si corresponde) */
    private function castToModel(object $row): object
    {
        if (!$this->modelClass || !class_exists($this->modelClass)) {
            return $row;
        }

        $model = new $this->modelClass();
        foreach (get_object_vars($row) as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }
}