<?php
namespace Core;

class Router {
    private static array $routes = [];

    public static function get($path, $callback) {
        self::$routes['GET'][$path] = $callback;
    }

    public static function post($path, $callback) {
        self::$routes['POST'][$path] = $callback;
    }

    public static function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'];

        $route = self::$routes[$method][$uri] ?? null;

        if ($route) {
            $controllerClass = $route[0];
            $methodName = $route[1];

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

            foreach ($middlewares as $mw) {
                $mwClass = "App\\Middleware\\" . ucfirst($mw) . "Middleware";
                if (class_exists($mwClass)) {
                    (new $mwClass())->handle();
                }
            }

            $controller = new $controllerClass();
            call_user_func([$controller, $methodName]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Ruta no encontrada']);
        }
    }
}
