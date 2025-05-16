<?php
namespace Lucid;

use Lucid\Contracts\ModelInterface;

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
        $connection = Connection::getInstance();
        $result = QueryBuilder::table(static::$table)->where('id', $id)->first();
        if ($result) {
            $model = new static((array)$result);
            $model->exists = true;
            return $model;
        }
        return null;
    }

    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
}
