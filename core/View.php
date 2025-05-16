<?php

namespace Core;
namespace Core;

use Jenssegers\Blade\Blade;

class View
{
    protected static $blade;

    public static function init()
    {
        if (!self::$blade) {
            $views = __DIR__ . '/../app/Views';
            $cache = __DIR__ . '/../storage/cache';

            self::$blade = new Blade($views, $cache);
        }

        return self::$blade;
    }

    public static function render(string $view, array $data = [])
    {
        echo self::init()->make($view, $data)->render();
    }
}