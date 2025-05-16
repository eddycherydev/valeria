<?php
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = __DIR__ . '/../';

    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
