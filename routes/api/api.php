<?php

use Core\Router;



use App\Controllers\HelloController;



/*-----------------------------------*
|       MACROS
|*-----------------------------------*/
Router::macro('api', function ($uri, $action) {
    Router::post("/api$uri", $action);
    
});



Router::api('/ask', [HelloController::class, 'askAgent']);














