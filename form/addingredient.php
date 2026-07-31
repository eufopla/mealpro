<?php

require_once 'functions/meal.php';
require_once 'functions/user.php';
require_once 'config/database.php';

$routes = require 'routes/mealroutes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

if ($uri === '' || $uri === '/') {
    header('Content-Type: text/html');
    $meals = getAllMeals($pdo);
    $ingredients = getAllIngredients($pdo);
    $users_info = getAllUserInfo($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="section-title">Ajouter un ingrédient</div>

<form method="POST">
    <strong>Nom de l'ingrédient :</strong>
    <input type="text" name="name">

    <strong>Description :</strong>
    <textarea name="description"></textarea>

    <strong>Mesure (g, ml, l) :</strong>
    <input type="text" name="measure">

    <button type="submit">Ajouter l'ingrédient</button>
</form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $measure = $_POST['measure'];

    createIngredient($pdo, $name, $description, $measure);
}
?>
</body>
</html>

<?php
    exit;
}

if (isset($routes[$method][$uri])) {
    [$class, $action] = $routes[$method][$uri];
    (new $class())->$action();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}