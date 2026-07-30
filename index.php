<?php

require_once 'functions/meal.php';
require_once 'config/database.php';

$routes = require 'routes/mealroutes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

if ($uri === '' || $uri === '/') {
    header('Content-Type: text/html');

    // Récupération des repas
    $meals = getAllMeals($pdo);
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
        <h1>Meal API</h1>
        <p>By Hiro & Eufopla</p>
    </div>
</header>

<div class="section-title">Routes disponibles</div>

<div class="routes">
    <div class="route">
        <span class="method GET">GET</span>
        <span class="route-path">/meals</span>
        <span class="route-desc">Liste tous les repas</span>
    </div>

    <div class="route">
        <span class="method GET">GET</span>
        <span class="route-path">/meals/{id}</span>
        <span class="route-desc">Détail d'un repas</span>
    </div>

    <div class="route">
        <span class="method POST">POST</span>
        <span class="route-path">/meals</span>
        <span class="route-desc">Créer un repas</span>
    </div>

    <div class="route">
        <span class="method PUT">PUT</span>
        <span class="route-path">/meals/{id}</span>
        <span class="route-desc">Modifier un repas</span>
    </div>

    <div class="route">
        <span class="method DELETE">DELETE</span>
        <span class="route-path">/meals/{id}</span>
        <span class="route-desc">Supprimer un repas</span>
    </div>
</div>

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