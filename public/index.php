<?php


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/autoload.php'; // tu autoload propio
require_once __DIR__ . '/../core/helpers.php'; 

use Core\RouteLoader;
use Core\Router;

// Cargar todas las rutas API automáticamente
RouteLoader::loadDirectory(__DIR__ . '/../routes/api');

// Cargar todas las rutas WEB automáticamente
RouteLoader::loadDirectory(__DIR__ . '/../routes/web');

Router::dispatch();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);