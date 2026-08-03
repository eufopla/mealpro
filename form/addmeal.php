<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/meal.php';
require_once __DIR__ . '/../functions/ingredient.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$ingredients = getAllIngredients($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un repas</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <div>
        <h1>Meal API</h1>
        <p>By Hiro & Eufopla</p>
    </div>
</header>
<h2>Ajouter un repas</h2>
<form method="POST">
    <div class="form-row">
        <label for="name">Nom du repas</label>
        <input
            type="text"
            id="name"
            name="name"
            required
            maxlength="255"
        >
    </div>
    <div class="form-row">
        <label for="name">Choix des ingrédients</label>
        <?php foreach ($ingredients as $ingredient): ?>
            <div>
                <input
                    type="checkbox"
                    id="ingredient_<?= htmlspecialchars($ingredient['id']) ?>"
                    name="ingredients[]"
                    value="<?= htmlspecialchars($ingredient['id']) ?>"
                >
                <label for="ingredient_<?= htmlspecialchars($ingredient['id']) ?>">
                    <?= htmlspecialchars($ingredient['name']) ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="form-row">
        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            maxlength="255"
        ></textarea>
    </div>
    <div class="form-row">
        <label for="nbr_ppl">Nombre de personnes</label>
        <input
            type="number"
            id="nbr_ppl"
            name="nbr_ppl"
            min="1"
            value="2"
            required
        >
    </div>
    <div class="actions">
        <button class="btn-primary" type="submit">
            Créer le repas
        </button>
        <a href="index.php">
            <button type="button">
                Annuler
            </button>
        </a>
    </div>
</form>
</body>
</html>