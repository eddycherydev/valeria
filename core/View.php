<?php
namespace Core;

class View
{
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

        extract($data); // convierte ['name' => 'Valeria'] en $name = 'Valeria';

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
    }
}