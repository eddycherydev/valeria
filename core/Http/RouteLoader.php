<?php

namespace Core\Http;

class RouteLoader
{
    public static function loadDirectory(string $path): void
    {
        foreach (glob($path . '/*.php') as $routeFile) {
            require $routeFile;
        }
    }
}
