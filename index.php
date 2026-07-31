<?php

require_once 'functions/meal.php';
require_once 'functions/user.php';
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
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

<header>
    <div>
        <h2>Bienvenue <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
        <h1>Meal API</h1>
        <p>By Hiro & Eufopla</p>
    </div>
</header>

<div class="section-title">Liste des repas</div>
<div class="meals-container">
    <?php if (empty($meals)): ?>
        <p>Aucun repas trouvé.</p>
    <?php else: ?>
        <?php foreach ($meals as $meal): ?>
            <div class="meal-card">
                <h3><?= htmlspecialchars($meal['name']) ?></h3>
                <p>
                    <strong>Description :</strong><br>
                    <?= htmlspecialchars($meal['description']) ?>
                </p>
                <p>
                    <strong>Nombre de personnes :</strong>
                    <?= htmlspecialchars($meal['nbr_ppl']) ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<br><br>
<div class="section-title">Liste des ingrédients</div>
<div class="meals-container">
    <?php if (empty($ingredients)): ?>
        <p>Aucun ingrédient trouvé.</p>
    <?php else: ?>
        <?php foreach ($ingredients as $ingredient): ?>
            <div class="meal-card">
                <h3><?= htmlspecialchars($ingredient['name']) ?></h3>
                <p>
                    <?= htmlspecialchars($ingredient['description']) ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<br><br>
</div>
<div class="section-title">Liste des utilisateurs</div>
<div class="meals-container">
    <?php if (empty($users_info)): ?>
        <p>Aucun utilisateur trouvé.</p>
    <?php else: ?>
        <?php foreach ($users_info as $user): ?>
            <div class="meal-card">
                <h3><?= htmlspecialchars($user['name']) ?></h3>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
</div>
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