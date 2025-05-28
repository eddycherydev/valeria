<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private $secret_key = 'mi_secreto_ultra_seguro_123!'; // Debe ser igual al del controlador

    public function handle()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Token no proporcionado']);
            exit;
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            // Aquí podrías pasar info del usuario decodificado si quieres:
            // e.g. $_REQUEST['user'] = $decoded->sub;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido o expirado']);
            exit;
        }
    }
}
