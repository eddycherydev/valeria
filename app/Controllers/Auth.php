<?php
namespace App\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    private $secret_key = 'mi_secreto_ultra_seguro_123!'; // Cambia por algo seguro

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Usuario simulado - reemplaza con base de datos
        $validUser = 'admin@example.com';
        $validPass = '123456';

        if ($email === $validUser && $password === $validPass) {
            $payload = [
                'iss' => 'tu-api',         // issuer
                'iat' => time(),           // issued at
                'exp' => time() + 3600,    // expiración 1 hora
                'sub' => $email,           // subject (usuario)
            ];

            $jwt = JWT::encode($payload, $this->secret_key, 'HS256');

            echo json_encode([
                'token' => $jwt,
                'expires_in' => 3600,
                'message' => 'Autenticación exitosa'
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales inválidas']);
        }
    }
}