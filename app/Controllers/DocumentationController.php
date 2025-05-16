<?php
namespace App\Controllers;
use Core\Vlex;

use Core\Attributes\Middleware;


class DocumentationController
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
            'message' => 'Hola desde Valeria Framework!',
            'csrf_token' => bin2hex(random_bytes(32))
        ]);
    }


    public function home()
    {  
       Vlex::render('home/home', ['name' => 'Valeria'], 'layout.vlex');
    }


    public function lucid()
    {  
       Vlex::render('documentation/lucid', [], 'layout.vlex');
    }


    
}
