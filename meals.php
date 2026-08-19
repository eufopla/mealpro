<?php

session_start();

require_once 'config/database.php';
require_once 'functions/meal.php';
require_once 'functions/ingredient.php';
require_once 'functions/user.php';
require_once 'controllers/meal.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    session_unset();
    session_destroy();

    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_meal_id'])) {
    $result = hardDeleteMealController(
        $pdo,
        (int) $_POST['delete_meal_id'],
        (int) $_SESSION['user_id']
    );

    if ($result['success']) {
        header('Location: meals.php');
        exit;
    }

    $error = $result['message'];
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
<?php require_once 'sidebar.php'; ?>

<div class="page-with-sidebar">
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
        <?php $nutriments = getNutrimentForMeal($pdo, $meal['id']); ?>
        <div class="meal-card">
            <h3>
                <?= htmlspecialchars($meal['name']) ?>
            </h3>
            <p>
                <?= htmlspecialchars($meal['description']) ?>
            </p>
            <p>
                <?= htmlspecialchars($meal['nbr_ppl']) ?> pers.
            </p>
            <?php if ($nutriments['g_protein'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['g_protein'], 2, '.', ''), '0'), '.') ?> g de Protéine</p>
            <?php endif; ?>
            <?php if ($nutriments['k_calories'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['k_calories'], 2, '.', ''), '0'), '.') ?> kcal</p>
            <?php endif; ?>
            <?php if ($nutriments['g_fat'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['g_fat'], 2, '.', ''), '0'), '.') ?> g de Lipides</p>
            <?php endif; ?>
            <?php if ($nutriments['g_saturated_fat'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['g_saturated_fat'], 2, '.', ''), '0'), '.') ?> g d'Acides gras saturés</p>
            <?php endif; ?>
            <?php if ($nutriments['g_fiber'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['g_fiber'], 2, '.', ''), '0'), '.') ?> g de Fibres</p>
            <?php endif; ?>
            <?php if ($nutriments['g_carbohydrate'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['g_carbohydrate'], 2, '.', ''), '0'), '.') ?> g de Glucides</p>
            <?php endif; ?>
            <?php if ($nutriments['g_sugar'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['g_sugar'], 2, '.', ''), '0'), '.') ?> g de Sucres</p>
            <?php endif; ?>
            <?php if ($nutriments['g_salt'] != 0): ?>
                <p><?= rtrim(rtrim(number_format($nutriments['g_salt'], 2, '.', ''), '0'), '.') ?> g de Sel</p>
            <?php endif; ?>
            <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce repas ?');">
            <input type="hidden" name="delete_meal_id" value="<?= (int) $meal['id'] ?>">
            <button type="submit">Supprimer</button>
            </form>
        </div>
        <br><br>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</div>
<br><br>

</body>
</html>