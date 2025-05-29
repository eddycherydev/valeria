<?php
namespace App\Controllers;

use App\Models\User;
use Core\View;

class LoginController
{
    public function showLogin()
    {
        $error = null;
        if (!empty($_SESSION['error'])) {
            $error = $_SESSION['error'];
        }
        View::render('Auth/login', ['error' => $error ], 'layouts/layout');
    }

    public function login()
    {
        session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::where('email',$email)->first();

        if (!$user || !$user->checkPassword($password)) {
            $_SESSION['error'] = 'Credenciales inválidas';
            header('Location: /login');
            return;
        }

        $_SESSION['user_id'] = $user->id;
        header('Location: /home');
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /login');
    }
}