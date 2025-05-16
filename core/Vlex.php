<?php

namespace Core;

class Vlex
{
    protected static array $sections = [];

    public static function render(string $view, array $data = [], string $layout = null)
    {
        $viewPath = __DIR__ . '/../app/Views/' . $view . '.vlex';
        if (!file_exists($viewPath)) {
            throw new \Exception("Vlex view not found: $viewPath");
        }

        // Compilar la vista principal y recolectar secciones
        $viewContent = file_get_contents($viewPath);
        $compiledView = self::compile($viewContent, $data);

        if ($layout) {
            $layoutPath = __DIR__ . '/../app/Views/' . $layout;
            if (!file_exists($layoutPath)) {
                throw new \Exception("Vlex layout not found: $layoutPath");
            }

            $layoutContent = file_get_contents($layoutPath);
            $layoutCompiled = self::compile($layoutContent, $data);

            // Insertar secciones en el layout
            $output = preg_replace_callback('/@yield\s*\(\s*[\'"](.+?)[\'"]\s*\)/', function ($matches) {
                return self::$sections[$matches[1]] ?? '';
            }, $layoutCompiled);

            echo $output;
        } else {
            // Si no hay layout, muestra el contenido directamente
            echo $compiledView;
        }
    }

    protected static function compile(string $template, array $data): string
    {
        // Comentarios
        $template = preg_replace('/\{\{\-\-.*?\-\-\}\}/s', '', $template);

        // Echo con escape: {{ $var }}
        $template = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars($1) ?>', $template);

        // Echo sin escape: {!! $var !!}
        $template = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '<?= $1 ?>', $template);

        // Includes
        $template = preg_replace_callback('/{{\s*include\(\'([^\']+)\'(?:,\s*(\[[^\]]*\]))?\)\s*}}/', function ($matches) use ($data) {
            $path = __DIR__ . '/../app/Views/' . $matches[1] . '.vlex';
            $vars = isset($matches[2]) ? eval("return {$matches[2]};") : [];
            return file_exists($path) ? self::compile(file_get_contents($path), array_merge($data, $vars)) : '';
        }, $template);

        // Condicionales
        $template = preg_replace('/@if\s*\((.*?)\)/', '<?php if($1): ?>', $template);
        $template = preg_replace('/@elseif\s*\((.*?)\)/', '<?php elseif($1): ?>', $template);
        $template = preg_replace('/@else/', '<?php else: ?>', $template);
        $template = preg_replace('/@endif/', '<?php endif; ?>', $template);

        // Bucles
        $template = preg_replace('/@foreach\s*\((.*?)\)/', '<?php foreach($1): ?>', $template);
        $template = preg_replace('/@endforeach/', '<?php endforeach; ?>', $template);

        // isset / empty
        $template = preg_replace('/@isset\s*\((.*?)\)/', '<?php if(isset($1)): ?>', $template);
        $template = preg_replace('/@endisset/', '<?php endif; ?>', $template);
        $template = preg_replace('/@empty\s*\((.*?)\)/', '<?php if(empty($1)): ?>', $template);
        $template = preg_replace('/@endempty/', '<?php endif; ?>', $template);

        // Componentes
        $template = preg_replace_callback('/@component\s*\(\s*[\'"](.+?)[\'"]\s*,?\s*(\[.*?\])?\s*\)(.*?)@endcomponent/s', function ($matches) use ($data) {
            $view = $matches[1];
            $params = isset($matches[2]) ? eval("return {$matches[2]};") : [];
            $params['slot'] = $matches[3];
            $path = __DIR__ . '/../app/Views/' . $view . '.vlex';
            return file_exists($path) ? self::compile(file_get_contents($path), array_merge($data, $params)) : '';
        }, $template);

        // Secciones
        $template = preg_replace_callback('/@section\s*\(\s*[\'"](.+?)[\'"]\s*\)(.*?)@endsection/s', function ($matches) {
            self::$sections[$matches[1]] = $matches[2];
            return '';
        }, $template);

        // Ejecutar plantilla
        ob_start();
        extract($data);
        eval('?>' . $template);
        return ob_get_clean();
    }
}