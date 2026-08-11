```php
<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/ingredient.php';
require_once __DIR__ . '/../controllers/meal.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$ingredients = getAllIngredients($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $nbr_ppl = (int) ($_POST['nbr_ppl'] ?? 2);
    $selectedIngredients = $_POST['ingredients'] ?? [];

    $result = createMealController(
        $pdo,
        $name,
        $description,
        $nbr_ppl,
        $selectedIngredients,
        (int) $_SESSION['user_id']
    );

    if (!$result['success']) {
        die($result['message']);
    }

    header('Location: ../index.php');
    exit;
}
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
        <h3>Nom du repas</h3>
        <input
            type="text"
            id="name"
            name="name"
            required
            maxlength="255"
        >
    </div>

    <div class="form-row">
        <h3>Choix des ingrédients</h3>

        <input
            type="search"
            id="ingredient-search"
            placeholder="Rechercher un ingrédient..."
            autocomplete="off"
        >

        <div class="ingredient-area">
            <div class="ingredient-panel">
                <h4>Résultats</h4>
                <div id="ingredient-results"></div>
            </div>

            <div class="ingredient-panel">
                <h4>Ingrédients sélectionnés</h4>
                <div id="selected-ingredients">
                    <p>Aucun ingrédient sélectionné.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <h3>Description</h3>
        <textarea
            id="description"
            name="description"
            maxlength="255"
        ></textarea>
    </div>

    <div class="form-row">
        <h3>Nombre de personnes</h3>
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

        <a href="../index.php">
            <button type="button">
                Annuler
            </button>
        </a>
    </div>
</form>

<script>
const ingredients = <?= json_encode(
    $ingredients,
    JSON_UNESCAPED_UNICODE |
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
) ?>;

const selectedIngredients = {};

const searchInput = document.getElementById('ingredient-search');
const results = document.getElementById('ingredient-results');
const selected = document.getElementById('selected-ingredients');

function displayResults() {
    const search = searchInput.value.toLowerCase().trim();

    results.innerHTML = '';

    if (!search) {
        return;
    }

    ingredients
        .filter(ingredient =>
            ingredient.name.toLowerCase().includes(search)
        )
        .forEach(ingredient => {
            const button = document.createElement('button');

            button.type = 'button';
            button.className = 'ingredient-result';
            button.textContent = ingredient.name;

            if (selectedIngredients[ingredient.id]) {
                button.disabled = true;
                button.textContent += ' ✓';
            } else {
                button.addEventListener('click', () => {
                    selectedIngredients[ingredient.id] = {
                        id: ingredient.id,
                        name: ingredient.name,
                        measure: ingredient.measure,
                        quantity: 100
                    };

                    displaySelected();
                    displayResults();
                });
            }

            results.appendChild(button);
        });
}

function displaySelected() {
    selected.innerHTML = '';

    const list = Object.values(selectedIngredients);

    if (list.length === 0) {
        selected.innerHTML = '<p>Aucun ingrédient sélectionné.</p>';
        return;
    }

    list.forEach(ingredient => {
        const row = document.createElement('div');

        row.className = 'selected-ingredient';

        row.innerHTML = `
            <span class="selected-name">${ingredient.name}</span>

            <input
                type="number"
                name="ingredients[${ingredient.id}]"
                value="${ingredient.quantity}"
                min="1"
                class="quantity-input"
            >

            <span>${ingredient.measure}</span>

            <button
                type="button"
                class="remove-ingredient"
            >
                ×
            </button>
        `;

        row.querySelector('.quantity-input').addEventListener('input', event => {
            ingredient.quantity = event.target.value;
        });

        row.querySelector('.remove-ingredient').addEventListener('click', () => {
            delete selectedIngredients[ingredient.id];

            displaySelected();
            displayResults();
        });

        selected.appendChild(row);
    });
}

searchInput.addEventListener('input', displayResults);

displaySelected();
</script>

</body>
</html>
```
