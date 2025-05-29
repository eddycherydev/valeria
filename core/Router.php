<?php

namespace Core;
use Core\Macroable;

class Router {
    use Macroable;

    private static array $routes = [];
    private static array $middlewareStack = [];

    public static function get($path, $callback) {
        self::addRoute('GET', $path, $callback);
    }

    public static function post($path, $callback) {
        self::addRoute('POST', $path, $callback);
    }

    public static function middleware(array $middlewares, \Closure $callback) {
        // Guardar la pila actual para soportar anidamiento
        $previousStack = self::$middlewareStack;

        // Mezclar la nueva con la pila actual
        self::$middlewareStack = array_merge(self::$middlewareStack, $middlewares);

        // Ejecutar el callback con la pila actual
        $callback();

        // Restaurar la pila anterior
        self::$middlewareStack = $previousStack;
    }

    private static function addRoute($method, $path, $callback) {
        self::$routes[$method][$path] = [
            'callback' => $callback,
            'middlewares' => self::$middlewareStack, // Guardar los middlewares del contexto
        ];
    }

    public static function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // 🔧 Normalizar quitando el base path
        $basePath = dirname($_SERVER['SCRIPT_NAME']); // da /valeria/public
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . ltrim($uri, '/'); // asegurar formato /ruta

        $method = $_SERVER['REQUEST_METHOD'];
        $routes = self::$routes[$method] ?? [];

        foreach ($routes as $route => $routeData) {
            $params = self::matchRoute($route, $uri);
            if ($params !== false) {
                $callback = $routeData['callback'];
                $routeMiddlewares = $routeData['middlewares'] ?? [];

                $controllerClass = $callback[0];
                $methodName = $callback[1];

                $refClass = new \ReflectionClass($controllerClass);
                $classAttributes = $refClass->getAttributes(\Core\Attributes\Middleware::class);
                $refMethod = $refClass->getMethod($methodName);
                $methodAttributes = $refMethod->getAttributes(\Core\Attributes\Middleware::class);

                $middlewares = [];

                foreach ($classAttributes as $attr) {
                    $middlewares = array_merge($middlewares, $attr->newInstance()->middlewares);
                }

                foreach ($methodAttributes as $attr) {
                    $middlewares = array_merge($middlewares, $attr->newInstance()->middlewares);
                }

                $middlewares = array_merge($routeMiddlewares, $middlewares);

                foreach ($middlewares as $mw) {
                    $mwClass = "App\\Middleware\\" . ucfirst($mw) . "Middleware";
                    if (class_exists($mwClass)) {
                        (new $mwClass())->handle();
                    }
                }

                $controller = new $controllerClass();
                call_user_func_array([$controller, $methodName], $params);

                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }

    protected static function matchRoute(string $routePattern, string $uri)
    {
        // Extraer los nombres de parámetros {id}, {slug}, etc.
        preg_match_all('#\{(\w+)\}#', $routePattern, $paramNames);

        // Convertir patrón con {} a expresión regular
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '([^/]+)', $routePattern) . '$#';

        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches); // quitar coincidencia completa

            $params = [];
            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index];
            }

            return $params;
        }

        return false;
    }
}