<?php

session_start();

require_once 'config/database.php';
require_once 'functions/meal.php';
require_once 'functions/ingredient.php';
require_once 'functions/user.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    session_unset();
    session_destroy();

    header('Location: login.php');
    exit;
}

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
    <div class="scanlines"></div>
<div class="grid-bg"></div>
<header>
    <div>
        <h2>
            Bienvenue <?= htmlspecialchars($_SESSION['user_name']) ?>
        </h2>
        <h1>Meal API</h1>
        <p>By Hiro & Eufopla</p>
    </div>
</header>
<a href="index.php">
    <button type="button">
        Retour
    </button>
</a>
<br><br>
<button onclick="window.location.href='form/addmeal.php'">
    Ajouter un repas
</button>
<br><br>
<button onclick="window.location.href='form/addingredient.php'">
    Ajouter un ingrédient
</button>
<br><br>
<div class="section-title">
    Liste des ingrédients
</div>
<div class="meals-container">
<?php if (empty($ingredients)): ?>
    <p>Aucun ingrédient trouvé.</p>
<?php else: ?>
    <?php foreach ($ingredients as $ingredient): ?>
        <div class="meal-card">
            <h3>
                <?= htmlspecialchars($ingredient['name']) ?>
            </h3>
            <p>
                <?= htmlspecialchars($ingredient['description']) ?>
            </p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<br><br>
</body>
</html>