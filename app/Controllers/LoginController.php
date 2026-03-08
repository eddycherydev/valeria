<?php
namespace App\Controllers;

use App\Models\User;
use Core\View\View;

class LoginController
{
    public function showLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = null;
        if (!empty($_SESSION['error'])) {
            $error = $_SESSION['error'];
            unset($_SESSION['error']); // Delete the error to not persist
        }
        
        View::render('Auth/login', ['error' => $error], 'layouts/layout');
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = User::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['error'] = 'Credentials invalid';
            header('Location: /login');
            exit;  // Better use exit after the header to stop execution
        }

        $_SESSION['user_id'] = $user->id;
        header('Location: /home');
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /login');
        exit;
    }

    /** Redirect the authenticated user to their /admin/home/{id} page */
    public function redirectToHome()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_SESSION['user_id'] ?? null;
        if ($id === null) {
            header('Location: /login');
            exit;
        }
        header('Location: /admin/home/' . $id);
        exit;
    }
}