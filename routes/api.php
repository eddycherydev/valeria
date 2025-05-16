<?php

use Core\Router;
use App\Controllers\Auth;
use App\Controllers\HelloController;
use App\Controllers\DocumentationController;

Router::get('/', [HelloController::class, 'index']);
Router::post('/login', [Auth::class, 'login']);
Router::post('/ask', [HelloController::class, 'askAgent']);
Router::get('/home', [HelloController::class, 'home']);

Router::get('/docs/lucid', [DocumentationController::class, 'lucid']);

Router::dispatch();