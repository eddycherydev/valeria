<?php

use Core\Router;

use App\Controllers\LoginController;
use App\Controllers\HelloController;



Router::get('/login', [LoginController::class, 'showLogin']);
Router::post('/login', [LoginController::class, 'login']);

Router::middleware(['webAuth'], function () {
    Router::get('/home', [HelloController::class, 'home']);
});

