<?php

use Core\Router;
use App\Controllers\HelloController;
use App\Controllers\Auth;

Router::get('/', [HelloController::class, 'index']);
Router::post('/login', [Auth::class, 'login']);
Router::post('/ask', [HelloController::class, 'askAgent']);
Router::get('/home', [HelloController::class, 'home']);

Router::dispatch();