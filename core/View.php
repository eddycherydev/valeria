<?php
namespace Core;

class View
{
    protected static array $sections = [];
    protected static ?string $currentSection = null;

    public static function render(string $view, array $data = [], string $layout = 'layout')
    {
        $viewPath = __DIR__ . "/../app/Views/{$view}.php";
        $layoutPath = __DIR__ . "/../app/Views/{$layout}.php";

        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: {$viewPath}");
        }

        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout file not found: {$layoutPath}");
        }

        extract($data); 

        // Hacer que View sea accesible como $View dentro de las vistas
        $View = new \Core\View();

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
    }

    public static function section(string $name)
    {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection()
    {
        if (self::$currentSection) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    public static function yield(string $name)
    {
        echo self::$sections[$name] ?? '';
    }
}