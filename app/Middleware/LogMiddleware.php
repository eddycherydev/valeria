<?php
namespace App\Middleware;

class LogMiddleware
{
    public function handle()
    {
        error_log("Middleware ejecutado: " . date("Y-m-d H:i:s"));
    }
}
