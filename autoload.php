<?php
spl_autoload_register(function (string $class) {
    $path = str_replace('Epic/', '', str_replace('\\', '/', $class)) . '.php';
    require __DIR__ . '/src/' . $path;
});
