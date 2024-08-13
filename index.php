<?php

declare(strict_types=1);


spl_autoload_register(function ($class) {
    $directories = [
        '/src/',
        '/src/Controller/',
        '/src/Database/'
    ];

    foreach ($directories as $directory) {
        $file = __DIR__. $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});


set_exception_handler('ErrorHandler::handleException');

require_once 'config.php';

header('Content-type: application/json; charset=UTF-8');



$full_url = explode("/", $_SERVER["REQUEST_URI"]);


if ($full_url[2] != "items") {
    http_response_code(404);
    exit;
}

// var_dump($full_url);
// exit;

$id = $full_url[3];

$database = new Database($dbhost, $dbname, $dbuser, $dbpass);

$database->getConnection();

$controller = new Controller;

$controller->getRequest($_SERVER['REQUEST_METHOD'], $id);
