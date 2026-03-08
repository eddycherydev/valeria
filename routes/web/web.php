<?php

use Core\Http\Router;

use App\Controllers\LoginController;
use App\Controllers\HelloController;



/*-----------------------------------*
|       MACROS
|*-----------------------------------*/
Router::macro('admin', function ($uri, $action) {
    Router::get("/admin$uri", $action);
});



Router::get('/', [HelloController::class, 'index']);
Router::get('/login', [LoginController::class, 'showLogin']);
Router::post('/login', [LoginController::class, 'login']);
Router::get('/logout', [LoginController::class, 'logout']);

Router::middleware(['webAuth'], function () {
    Router::get('/home', [LoginController::class, 'redirectToHome']);
    Router::admin('/home/{id}', [HelloController::class, 'home']);
});


