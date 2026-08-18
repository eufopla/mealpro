<?php
require_once 'log.php';
function getAllMeals(PDO $db): array
{
    $stmt = $db->query("SELECT * FROM meal");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getNutrimentForMeal(PDO $db, int $meal_id): array
{
    $stmt = $db->prepare("
        SELECT
            COALESCE(SUM(ingredient.g_protein_for_100m * link.quantity / 100), 0) AS g_protein,
            COALESCE(SUM(ingredient.k_calories_for_100m * link.quantity / 100), 0) AS k_calories,
            COALESCE(SUM(ingredient.g_fat_for_100m * link.quantity / 100), 0) AS g_fat,
            COALESCE(SUM(ingredient.g_saturated_fat_for_100m * link.quantity / 100), 0) AS g_saturated_fat,
            COALESCE(SUM(ingredient.g_fiber_for_100m * link.quantity / 100), 0) AS g_fiber,
            COALESCE(SUM(ingredient.g_carbohydrate_for_100m * link.quantity / 100), 0) AS g_carbohydrate,
            COALESCE(SUM(ingredient.g_sugar_for_100m * link.quantity / 100), 0) AS g_sugar,
            COALESCE(SUM(ingredient.g_salt_for_100m * link.quantity / 100), 0) AS g_salt
        FROM link
        INNER JOIN ingredient ON ingredient.id = link.id_ingredient
        WHERE link.id_meal = :meal_id
    ");

    $stmt->execute([
        'meal_id' => $meal_id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getMealById(PDO $db, int $id): array
{
    $stmt = $db->prepare("SELECT name, description, nbr_ppl FROM meal WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function checkIfMealExists(PDO $db, int $id_meal): bool
{
    $stmt = $db->prepare("
        SELECT id
        FROM meal
        WHERE id = :id_meal
    ");

    $stmt->execute([
        'id_meal' => $id_meal
    ]);

    return $stmt->fetch() !== false;
}
function createMeal(PDO $db, string $name, string $description, int $nbr_ppl): array
{
    if (empty($name) || empty($description) || empty($nbr_ppl)) {
        return [
            'success' => false,
            'message' => 'Tous les champs sont requis.'
        ];
    }
    if ($nbr_ppl <= 0) {
        return [
            'success' => false,
            'message' => 'Le nombre de personnes doit être supérieur à zéro.'
        ];
    }
    $stmt = $db->prepare("
        INSERT INTO meal (name, description, nbr_ppl)
        VALUES (:name, :description, :nbr_ppl)
    ");
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'nbr_ppl' => $nbr_ppl
    ]);
    return [
        'success' => true,
        'message' => 'Repas ajouté avec succès.',
        'id_meal' => (int) $db->lastInsertId()
    ];
}
function updateMeal(PDO $db, int $id, string $name, string $description, int $nbr_ppl): array
{
    if (empty($name) || empty($description) || empty($nbr_ppl)) {
        return ['success' => false, 'message' => 'Tous les champs sont requis.'];
    }
    else if ($nbr_ppl <= 0) {
        return ['success' => false, 'message' => 'Le nombre de personnes doit être supérieur à zéro.'];
    }
    else if ($name === getMealById($db, $id)['name'] && $description === getMealById($db, $id)['description'] && $nbr_ppl === getMealById($db, $id)['nbr_ppl']) {
        return ['success' => false, 'message' => 'Aucune modification n\'a été effectuée.'];
    }
    else {
        $stmt = $db->prepare("UPDATE meal SET name = :name, description = :description, nbr_ppl = :nbr_ppl WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description, 'nbr_ppl' => $nbr_ppl]);
        createLog($db, $_SESSION['user_id'], date('Y-m-d H:i:s'), 'update', 'meal', $id);
        return ['success' => true, 'message' => 'Repas mis à jour avec succès.'];
    }
}
function hardDeleteMeal(PDO $db, int $id): array
{
    $stmt = $db->prepare("DELETE FROM meal WHERE id = :id");
    $stmt->execute(['id' => $id]);
    createLog($db, $_SESSION['user_id'], date('Y-m-d H:i:s'), 'delete', 'meal', $id);
    return ['success' => true, 'message' => 'Repas supprimé avec succès.'];
}
function mealAlreadyExists(
    PDO $db,
    string $name,
    string $description,
    int $nbr_ppl,
    array $ingredients
): bool {
    $newIngredients = [];
    foreach ($ingredients as $idIngredient => $quantity) {
        $newIngredients[(int) $idIngredient] = (float) $quantity;
    }
    ksort($newIngredients);
    $stmt = $db->prepare("
        SELECT id, name, description, nbr_ppl
        FROM meal
        WHERE name = :name
          AND nbr_ppl = :nbr_ppl
          AND (
              description = :description
              OR (description IS NULL AND :description = '')
          )
    ");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':nbr_ppl' => $nbr_ppl
    ]);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($meals)) {
        return false;
    }
    foreach ($meals as $meal) {
        $stmtIngredients = $db->prepare("
            SELECT id_ingredient, quantity
            FROM link
            WHERE id_meal = :id_meal
        ");
        $stmtIngredients->execute([
            ':id_meal' => $meal['id']
        ]);
        $existingIngredients = [];
        while ($ingredient = $stmtIngredients->fetch(PDO::FETCH_ASSOC)) {
            $existingIngredients[(int) $ingredient['id_ingredient']] =
                (float) $ingredient['quantity'];
        }
        if (count($existingIngredients) !== count($newIngredients)) {
            continue;
        }
        ksort($existingIngredients);
        if ($existingIngredients === $newIngredients) {
            return true;
        }
    }
    return false;
}
?>