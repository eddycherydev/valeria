<?php
namespace Core\Lucid;

use Core\Lucid\Connection;

class Lucid
{
    protected static $connection;

    public static function boot()
    {
        self::$connection = new Connection();
    }

    public static function connection()
    {
        return self::$connection->getPDO();
    }
}