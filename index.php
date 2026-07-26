<?php

require_once 'controllers/MealController.php';
require_once 'config/Database.php';
require 'functions/meal.php';

$routes = require 'routes/mealroutes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

if ($uri === '' || $uri === '/') {
    header('Content-Type: text/html');
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

  <div class="section-title">Test</div>
  <div class="routes">
    <div class="route"><span class="method GET">GET</span><span class="route-path">/meals</span><span class="route-desc">Liste tous les repas</span></div>
    <div class="route"><span class="method GET">GET</span><span class="route-path">/meals/{id}</span><span class="route-desc">Détail d'un repas</span></div>
    <div class="route"><span class="method POST">POST</span><span class="route-path">/meals</span><span class="route-desc">Créer un repas</span></div>
    <div class="route"><span class="method PUT">PUT</span><span class="route-path">/meals/{id}</span><span class="route-desc">Modifier un repas</span></div>
    <div class="route"><span class="method DELETE">DELETE</span><span class="route-path">/meals/{id}</span><span class="route-desc">Supprimer un repas</span></div>
  </div>

  <div>Liste des plats</div>
  <?php foreach ($meals as $meal): ?>
  <div class="section-title">Tester — créer un repas</div>
  <div class="card">
    <div class="form-row">
      <label>Nom du repas</label>
      <input type="text" id="name" placeholder="ex: Pasta carbonara">
    </div>
    <div class="form-row">
      <label>Description</label>
      <textarea id="desc" placeholder="ex: Pâtes crémeuses avec lardons..."></textarea>
    </div>
    <div class="form-row">
      <label>Calories</label>
      <input type="number" id="cal" placeholder="ex: 650">
    </div>
    <div class="actions">
      <button class="btn-primary" onclick="sendReq()">POST /meals</button>
      <button onclick="clearForm()">Effacer</button>
    </div>
    <div class="result" id="result" style="display:none">
      <pre id="result-text"></pre>
    </div>
  </div>

  <script>
    async function sendReq() {
      const body = {
        name:        document.getElementById('name').value,
        description: document.getElementById('desc').value,
        calories:    parseInt(document.getElementById('cal').value) || null,
      };
      const res  = await fetch('/meals', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
      const data = await res.json();
      document.getElementById('result').style.display = 'block';
      document.getElementById('result-text').textContent = JSON.stringify(data, null, 2);
    }

    function clearForm() {
      ['name', 'desc', 'cal'].forEach(id => document.getElementById(id).value = '');
      document.getElementById('result').style.display = 'none';
    }
  </script>

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