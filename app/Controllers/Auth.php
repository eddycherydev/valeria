<?php
namespace App\Controllers;

use App\Models\User;
use Core\Env;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    public function login(): void
    {
        $secret = Env::get('JWT_SECRET');
        if (empty($secret)) {
            http_response_code(500);
            echo json_encode(['error' => 'JWT_SECRET no configured in .env']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = User::where('email', $email)->first();
        if (!$user || !password_verify($password, $user->password)) {
            http_response_code(401);
            echo json_encode(['error' => 'Credentials invalid']);
            return;
        }

        $payload = [
            'iss' => Env::get('APP_URL', 'valeria-api'),
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => (string) $user->id,
        ];

        $jwt = JWT::encode($payload, $secret, 'HS256');

        echo json_encode([
            'token' => $jwt,
            'expires_in' => 3600,
            'message' => 'Authentication successful',
        ]);
    }
}