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
        $method = $_SERVER['REQUEST_METHOD'];

        $routeData = self::$routes[$method][$uri] ?? null;

        if ($routeData) {
            $callback = $routeData['callback'];
            $routeMiddlewares = $routeData['middlewares'] ?? [];

            $controllerClass = $callback[0];
            $methodName = $callback[1];

            $refClass = new \ReflectionClass($controllerClass);
            $classAttributes = $refClass->getAttributes(\Core\Attributes\Middleware::class);
            $refMethod = $refClass->getMethod($methodName);
            $methodAttributes = $refMethod->getAttributes(\Core\Attributes\Middleware::class);

            $middlewares = [];

            // Middlewares por atributos
            foreach ($classAttributes as $attr) {
                $middlewares = array_merge($middlewares, $attr->newInstance()->middlewares);
            }

            foreach ($methodAttributes as $attr) {
                $middlewares = array_merge($middlewares, $attr->newInstance()->middlewares);
            }

            // Middlewares por contexto de grupo
            $middlewares = array_merge($routeMiddlewares, $middlewares);

            // Ejecutar los middlewares
            foreach ($middlewares as $mw) {
                $mwClass = "App\\Middleware\\" . ucfirst($mw) . "Middleware";
                if (class_exists($mwClass)) {
                    (new $mwClass())->handle();
                }
            }

            // Ejecutar el controlador
            $controller = new $controllerClass();
            call_user_func([$controller, $methodName]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Ruta no encontrada']);
        }
    }
}