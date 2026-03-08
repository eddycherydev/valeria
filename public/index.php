<?php

// Configuración de errores al inicio (antes de cualquier ejecución)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/autoload.php';
require_once __DIR__ . '/../core/helpers.php';

// Cargar .env al inicio para que Env::get() esté disponible en toda la app
\Core\Env::load(__DIR__ . '/..');

use Core\RouteLoader;
use Core\Router;

// Cargar todas las rutas API automáticamente
RouteLoader::loadDirectory(__DIR__ . '/../routes/api');

// Cargar todas las rutas WEB automáticamente
RouteLoader::loadDirectory(__DIR__ . '/../routes/web');

Router::dispatch();