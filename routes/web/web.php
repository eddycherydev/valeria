<?php

use Core\Router;

use App\Controllers\HelloController;


Router::get('/home', [HelloController::class, 'home']);


