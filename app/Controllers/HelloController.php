<?php
namespace App\Controllers;
use App\Models\User;

use Core\Attributes\Middleware;
use Core\Lucid\QueryBuilder;
use Core\View;


class HelloController
{
    #[Middleware('auth')]
    public function askAgent()
    {
        // $user = new User([
        //     'email' => 'admin2@example.com',
        //     'name' => 'usuario'.rand(),
        //     'password' => password_hash('123456', PASSWORD_DEFAULT)
        // ]);

        // $user->save();

        // $user = User::find(2);

        // if ($user) {
        //     var_dump($user);
        // } else {
        //     echo "Usuario no encontrado.";
        // }
        
        
        $user = User::where('email', 'admin2@example.com')->first();
        var_dump($user);

    }

    // #[Middleware('auth')]
    public function index()
    {
        echo json_encode([
            'message' => 'Hola desde Valeria Framework!',
            'csrf_token' => bin2hex(random_bytes(32))
        ]);
    }


    public function home($id)
    {  

       View::render('home/home', ['name' => 'Valeria '.$id], 'layouts/layout');
    }
   


    


    
}
