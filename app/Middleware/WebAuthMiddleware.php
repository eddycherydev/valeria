<?php
namespace App\Middleware;

class WebAuthMiddleware
{
    public function handle()
    {
        session_start();

        if (empty($_SESSION['user_id'])) {
            // Redirigir a login si no está autenticado
            header('Location: /login');
            exit;
        }

        // Si quieres, puedes añadir lógica para verificar roles, expiración, etc.
        // Ejemplo:
        // if ($_SESSION['role'] !== 'admin') { ... }
    }
}