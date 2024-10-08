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
$request = (array) json_decode(file_get_contents('php://input'), true);

if ($full_url[2] != 'api') {
    http_response_code(404);
    echo json_encode([
        "message" => "api end point accessed without '/api' !",
        "hint_url" => "localhost/project_dir/api/...",
        'status' => 'not found'
    ]);
    exit;
}

$id = $full_url[4];

$database = new Database($dbhost, $dbname, $dbuser, $dbpass);

$schema = new Schema($database);

$controller = new Controller($schema);

if ($full_url[3] == 'create_table') {
    $controller->createTable($full_url[4], $request);
}
elseif ($full_url[3] == 'remove_table') {
    $controller->dropTable($full_url[3]);
}
else {
    $controller->getRequest($request, $_SERVER['REQUEST_METHOD'], $full_url[3], $id);
}
