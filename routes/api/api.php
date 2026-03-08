<?php

use Core\Router;
use App\Controllers\HelloController;
use App\Controllers\Auth;
use App\Controllers\AgentController;

Router::macro('api', function ($uri, $action) {
    Router::post("/api$uri", $action);
});

Router::get('/api/skills', [AgentController::class, 'listSkills']);
Router::get('/api/agents', [AgentController::class, 'listAgents']);
Router::post('/api/agent', [AgentController::class, 'run']);

Router::api('/ask', [HelloController::class, 'askAgent']);
Router::post('/api-login', [Auth::class, 'login']);














