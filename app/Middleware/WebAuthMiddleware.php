<?php
namespace App\Middleware;

use Core\Contracts\MiddlewareInterface;

class WebAuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}