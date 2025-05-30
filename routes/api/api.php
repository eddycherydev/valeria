<?php

use Core\Router;



use App\Controllers\HelloController;
use App\Controllers\Auth;



/*-----------------------------------*
|       MACROS
|*-----------------------------------*/
Router::macro('api', function ($uri, $action) {
    Router::post("/api$uri", $action);
});



Router::api('/ask', [HelloController::class, 'askAgent']);



Router::post('/api-login', [Auth::class, 'login']);














