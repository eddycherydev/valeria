<?php
namespace Lucid;

use PDO;
use PDOException;

use Core\Env;

class Connection
{
    protected static ?Connection $instance = null;
    protected PDO $pdo;

    public function __construct()
    {
        Env::load(__DIR__ . '/../.env'); // o ajusta la ruta

        $dsn = Env::get('DB_CONNECTION', 'mysql') . 
              ":host=" . Env::get('DB_HOST', '127.0.0.1') .
              ";dbname=" . Env::get('DB_DATABASE', 'valeria') .
              ";port=" . Env::get('DB_PORT', '3306') .
              ";charset=" . Env::get('DB_CHARSET', 'utf8mb4');
              

        try {
            $this->pdo = new PDO(
                $dsn,
                Env::get('DB_USERNAME', 'root'),
                Env::get('DB_PASSWORD', ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]
            );
        } catch (PDOException $e) {
            die("DB Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Connection
    {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance;
    }

    

    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}