<?php

declare(strict_types=1);

spl_autoload_register(function($class){
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-Type: application/json; charset=UTF-8");

$segment = explode('/', $_SERVER['REQUEST_URI']);

if ($segment[2] != "subscriber")
{
    http_response_code(404);
    exit;
}

$segment_id = $segment[3] ?? null;

$entry = new SubscriberEntry();

$controller = new SubscriberController($entry);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $segment_id);

