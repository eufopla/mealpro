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
            <?php if ($ingredient['g_protein_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['g_protein_for_100m'], 2, '.', ''), '0'), '.') ?> g de Protéine</p>
            <?php endif; ?>
            <?php if ($ingredient['k_calories_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['k_calories_for_100m'], 2, '.', ''), '0'), '.') ?> kcal</p>
            <?php endif; ?>
            <?php if ($ingredient['g_fat_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['g_fat_for_100m'], 2, '.', ''), '0'), '.') ?> g de Lipides</p>
            <?php endif; ?>
            <?php if ($ingredient['g_saturated_fat_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['g_saturated_fat_for_100m'], 2, '.', ''), '0'), '.') ?> g d'Acides gras saturés</p>
            <?php endif; ?>
            <?php if ($ingredient['g_fiber_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['g_fiber_for_100m'], 2, '.', ''), '0'), '.') ?> g de Fibres</p>
            <?php endif; ?>
            <?php if ($ingredient['g_carbohydrate_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['g_carbohydrate_for_100m'], 2, '.', ''), '0'), '.') ?> g de Glucides</p>
            <?php endif; ?>
            <?php if ($ingredient['g_sugar_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['g_sugar_for_100m'], 2, '.', ''), '0'), '.') ?> g de Sucres</p>
            <?php endif; ?>
            <?php if ($ingredient['g_salt_for_100m'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($ingredient['g_salt_for_100m'], 2, '.', ''), '0'), '.') ?> g de Sel</p>
            <?php endif; ?>
        </div>
        <br><br>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<br><br>
</body>
</html>