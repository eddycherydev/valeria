<?php
namespace App\Controllers;
use Core\View;

use Core\Attributes\Middleware;


class HelloController
{
    // #[Middleware('log')]
    public function askAgent()
    {
        echo json_encode(['message' => 'Pregunta procesada']);
    }

    // #[Middleware('auth')]
    public function index()
    {
        echo json_encode([
            'message' => 'Hola desde NovaFlux!',
            'csrf_token' => bin2hex(random_bytes(32))
        ]);
    }


    public function home()
    {
       
       View::render('home/home.html', ['name' => 'Valeria']);
    }
}
