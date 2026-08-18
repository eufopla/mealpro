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
<button onclick="window.location.href='form/addmeal.php'">
    Ajouter un repas
</button>
<button onclick="window.location.href='form/addingredient.php'">
    Ajouter un ingrédient
</button>
<br><br>
<div class="section-title">
    Liste des repas
</div>
<div class="meals-container">
<?php if (empty($meals)): ?>
    <p>Aucun repas trouvé.</p>
<?php else: ?>
    <?php foreach ($meals as $meal): ?>
    <div class="meal-card">
        <h3>
            <?= htmlspecialchars($meal['name']) ?>
        </h3>
        <p>
            <strong>Description :</strong><br>
            <?= htmlspecialchars($meal['description']) ?>
        </p>
        <p>
            <strong>Nombre de personnes :</strong>
            <?= htmlspecialchars($meal['nbr_ppl']) ?>
        </p>
        <?php if ($meal['g_protein_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['g_protein_for_100m']) ?> g de Protéine</p>
        <?php endif; ?>
        <?php if ($meal['k_calories_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['k_calories_for_100m']) ?> kcal</p>
        <?php endif; ?>
        <?php if ($meal['g_fat_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['g_fat_for_100m']) ?> g de Lipides</p>
        <?php endif; ?>
        <?php if ($meal['g_saturated_fat_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['g_saturated_fat_for_100m']) ?> g d'Acides gras saturés</p>
        <?php endif; ?>
        <?php if ($meal['g_fiber_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['g_fiber_for_100m']) ?> g de Fibres</p>
        <?php endif; ?>
        <?php if ($meal['g_carbohydrate_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['g_carbohydrate_for_100m']) ?> g de Glucides</p>
        <?php endif; ?>
        <?php if ($meal['g_sugar_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['g_sugar_for_100m']) ?> g de Sucres</p>
        <?php endif; ?>
        <?php if ($meal['g_salt_for_100m'] != 0): ?>
            <p><?= htmlspecialchars($meal['g_salt_for_100m']) ?> g de Sel</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php endif; ?>
</div>
<br><br>

</body>
</html>