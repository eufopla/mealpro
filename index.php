<?php

require_once 'controllers/MealController.php';
require_once 'config/Database.php';

$routes = require 'routes/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

if (isset($routes[$method][$uri])) {
    [$class, $action] = $routes[$method][$uri];
    (new $class())->$action();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}