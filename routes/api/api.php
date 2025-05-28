<?php

use Core\Router;


use App\Controllers\Auth;
use App\Controllers\HelloController;
use App\Controllers\DocumentationController;



Router::get('/', [HelloController::class, 'index']);
Router::post('/login', [Auth::class, 'login']);
Router::post('/ask', [HelloController::class, 'askAgent']);



Router::get('/docs/lucid', [DocumentationController::class, 'lucid']);
Router::get('/docs/templateEngine', [DocumentationController::class, 'templateEngine']);


Router::macro('admin', function ($uri, $action) {
    Router::get("/admin/$uri", $action);
});

Router::middleware(['auth'], function () {
    Router::admin('home', [HelloController::class, 'home']);
});










