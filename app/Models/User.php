<?php
namespace App\Models;

use Core\Lucid\Model;

class User extends Model {
    protected static string $table = 'users';

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}