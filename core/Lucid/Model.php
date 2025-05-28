<?php
namespace Core\Lucid;

use Core\Lucid\Contracts\ModelInterface;
use Core\Lucid\QueryBuilder;


abstract class Model implements ModelInterface {
    protected static string $table;
    protected array $attributes = [];
    protected bool $exists = false;

    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }

    public function fill(array $attributes): void {
        $this->attributes = $attributes;
    }

    public function save(): bool {
        $connection = Connection::getInstance();
        if ($this->exists) {
            $query = QueryBuilder::table(static::$table)->where('id', $this->attributes['id'])->update($this->attributes);
        } else {
            $query = QueryBuilder::table(static::$table)->insert($this->attributes);
            $this->exists = true;
        }
        return $query;
    }

    public static function find(int $id): ?self {
        $result = QueryBuilder::table(static::$table)->where('id', $id)->first();
        if ($result) {
            return (new static((array)$result))->markAsExists();
        }
        return null;
    }

    public static function where(string $column, $value): QueryBuilder {
        return QueryBuilder::table(static::$table)->where($column, $value);
    }

    public static function orderBy(string $column, string $direction = 'ASC'): QueryBuilder {
        return QueryBuilder::table(static::$table)->orderBy($column, $direction);
    }

    public static function groupBy(string ...$columns): QueryBuilder {
        return QueryBuilder::table(static::$table)->groupBy(...$columns);
    }

    public static function limit(int $limit): QueryBuilder {
        return QueryBuilder::table(static::$table)->limit($limit);
    }

    public static function all(): array {
        return static::getFromQueryBuilder(QueryBuilder::table(static::$table));
    }

    public static function getFromQueryBuilder(QueryBuilder $query): array {
        $results = $query->get();
        return array_map(fn($row) => (new static((array)$row))->markAsExists(), $results);
    }

    protected function markAsExists(): self {
        $this->exists = true;
        return $this;
    }

    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
}
