<?php
namespace App\Middleware;

use Core\Contracts\MiddlewareInterface;

class LogMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        error_log("Middleware executed: " . date("Y-m-d H:i:s"));
    }
}
