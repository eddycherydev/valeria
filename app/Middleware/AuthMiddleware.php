<?php
namespace App\Middleware;

use Core\Contracts\MiddlewareInterface;
use Core\Env;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $secret = Env::get('JWT_SECRET');
        if (empty($secret)) {
            http_response_code(500);
            echo json_encode(['error' => 'JWT_SECRET not configured in .env']);
            exit;
        }

        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token no given']);
            exit;
        }

        $jwt = $matches[1];

        try {
            $decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($secret, 'HS256'));
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token invalid or expired']);
            exit;
        }
    }
}
