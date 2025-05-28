<?php
namespace Core;

trait Macroable {
    protected static array $macros = [];

    public static function macro(string $name, callable $macro): void {
        static::$macros[$name] = $macro;
    }

    public function __call($method, $arguments) {
        if (isset(static::$macros[$method])) {
            return call_user_func_array(static::$macros[$method]->bindTo($this, static::class), $arguments);
        }
        throw new \BadMethodCallException("Method $method does not exist.");
    }

    public static function __callStatic($method, $arguments) {
        if (isset(static::$macros[$method])) {
            return call_user_func_array(static::$macros[$method], $arguments);
        }
        throw new \BadMethodCallException("Static method $method does not exist.");
    }
}