<?php
namespace App\Controllers;

use App\Models\User;
use Core\View;

class LoginController
{
    public function showLogin()
    {
        session_start();

        $error = null;
        if (!empty($_SESSION['error'])) {
            $error = $_SESSION['error'];
            unset($_SESSION['error']); // Borra el error para que no persista
        }
        
        View::render('Auth/login', ['error' => $error], 'layouts/layout');
    }

    public function login()
    {
        session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = User::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['error'] = 'Credenciales inválidas';
            header('Location: /login');
            exit;  // Mejor usar exit después del header para detener ejecución
        }

        $_SESSION['user_id'] = $user->id;
        header('Location: /home');
        exit;
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}