<?php

namespace Core;

class View
{
    protected static $sections = [];
    protected static $viewContent = '';

    public static function render(string $view, array $data = [], string $layout = null): void
    {
        self::$sections = [];
        self::$viewContent = self::getViewFile($view, $data);

        if ($layout) {
            echo self::getViewFile($layout, $data);
        } else {
            echo self::$viewContent;
        }
    }

    protected static function getViewFile(string $file, array $data): string
    {
        $path = __DIR__ . '/../app/Views/' . $file . '.php';

        if (!file_exists($path)) {
            throw new \Exception("View not found: $file");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;
        return ob_get_clean();
    }

    public static function startSection(string $name): void
    {
        ob_start();
        self::$sections[$name] = '';
    }

    public static function endSection(): void
    {
        $buffer = ob_get_clean();
        end(self::$sections);
        $lastKey = key(self::$sections);
        self::$sections[$lastKey] = $buffer;
    }

    public static function yield(string $section): void
    {
        echo self::$sections[$section] ?? '';
    }

    public static function include(string $view, array $data = []): void
    {
        echo self::getViewFile($view, $data);
    }

    public static function e($value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}