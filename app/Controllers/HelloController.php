<?php
namespace App\Controllers;
use Core\Vlex;
use App\Models\User;

use Core\Attributes\Middleware;
use Lucid\QueryBuilder;


class HelloController
{
    #[Middleware('auth')]
    public function askAgent()
    {
        // $user = new User([
        //     'email' => 'admin1@example.com',
        //     'password' => password_hash('123456', PASSWORD_DEFAULT)
        // ]);

        // $user->save();

        // $user = User::find(1);

        // if ($user) {
        //     echo $user->email;
        // } else {
        //     echo "Usuario no encontrado.";
        // }
        
        $user = QueryBuilder::table('users')
            ->where('email', 'admin1@example.com')
            ->first();

        if ($user) {
            echo $user->name;
}
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


    


    
}
