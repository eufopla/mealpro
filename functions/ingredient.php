<?php
require_once 'log.php';

function getAllIngredients(PDO $db): array
{
    $stmt = $db->query("SELECT * FROM ingredient");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getIngredientById(PDO $db, int $id): array
{
    $stmt = $db->prepare("SELECT name, description FROM ingredient WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function createIngredient(PDO $db, string $name, string $description, string $measure): array
{
    if (empty($name) || empty($description) || empty($measure)) {
        return ['success' => false, 'message' => 'Tous les champs sont requis.'];
    }
    else if ($measure !== 'g' && $measure !== 'ml' && $measure !== 'l') {
        return ['success' => false, 'message' => 'Le champ de mesure est incorrect.'];
    }
    else {
        $stmt = $db->prepare("INSERT INTO ingredient (name, description, measure) VALUES (:name, :description, :measure)");
        $stmt->execute(['name' => $name, 'description' => $description, 'measure' => $measure]);
        createLog($db, $_SESSION['user_id'], date('Y-m-d H:i:s'), 'create', 'ingredient', $db->lastInsertId());
        return ['success' => true, 'message' => 'Ingrédient ajouté avec succès.'];
    }
}
function updateIngredient(PDO $db, int $id, string $name, string $description, string $measure, float $g_protein_for_100m, float $k_calories_for_100m, float $g_fat_for_100m, float $g_saturated_fat_for_100m, float $g_fiber_for_100m, float $g_carbohydrate_for_100m, float $g_sugars_for_100m, float $g_salt_for_100m): array
{
    if (empty($name) || empty($description) || empty($measure)) {
        return ['success' => false, 'message' => 'Tous les champs sont requis.'];
    }
    else if ($measure !== 'g' && $measure !== 'ml' && $measure !== 'l') {
        return ['success' => false, 'message' => 'Le champ de mesure est incorrect.'];
    }
    else if ($name === getIngredientById($db, $id)['name'] && $description === getIngredientById($db, $id)['description'] && $measure === getIngredientById($db, $id)['measure'] && $g_protein_for_100m === getIngredientById($db, $id)['g_protein_for_100m'] && $k_calories_for_100m === getIngredientById($db, $id)['k_calories_for_100m'] && $g_fat_for_100m === getIngredientById($db, $id)['g_fat_for_100m'] && $g_saturated_fat_for_100m === getIngredientById($db, $id)['g_saturated_fat_for_100m'] && $g_fiber_for_100m === getIngredientById($db, $id)['g_fiber_for_100m'] && $g_carbohydrate_for_100m === getIngredientById($db, $id)['g_carbohydrate_for_100m'] && $g_sugars_for_100m === getIngredientById($db, $id)['g_sugars_for_100m'] && $g_salt_for_100m === getIngredientById($db, $id)['g_salt_for_100m']) {
        return ['success' => false, 'message' => 'Aucune modification n\'a été effectuée.'];
    }
    else {
        $stmt = $db->prepare("UPDATE ingredient SET name = :name, description = :description, measure = :measure, g_protein_for_100m = :g_protein_for_100m, k_calories_for_100m = :k_calories_for_100m, g_fat_for_100m = :g_fat_for_100m, g_saturated_fat_for_100m = :g_saturated_fat_for_100m, g_fiber_for_100m = :g_fiber_for_100m, g_carbohydrate_for_100m = :g_carbohydrate_for_100m, g_sugars_for_100m = :g_sugars_for_100m, g_salt_for_100m = :g_salt_for_100m WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description, 'measure' => $measure, 'g_protein_for_100m' => $g_protein_for_100m, 'k_calories_for_100m' => $k_calories_for_100m, 'g_fat_for_100m' => $g_fat_for_100m, 'g_saturated_fat_for_100m' => $g_saturated_fat_for_100m, 'g_fiber_for_100m' => $g_fiber_for_100m, 'g_carbohydrate_for_100m' => $g_carbohydrate_for_100m, 'g_sugars_for_100m' => $g_sugars_for_100m, 'g_salt_for_100m' => $g_salt_for_100m]);
        createLog($db, $_SESSION['user_id'], date('Y-m-d H:i:s'), 'update', 'ingredient', $id);
        return ['success' => true, 'message' => 'Ingrédient mis à jour avec succès.'];
    }
}
function hardDeleteIngredient(PDO $db, int $id): array
{
    $stmt = $db->prepare("DELETE FROM ingredient WHERE id = :id");
    $stmt->execute(['id' => $id]);
    createLog($db, $_SESSION['user_id'], date('Y-m-d H:i:s'), 'delete', 'ingredient', $id);
    return ['success' => true, 'message' => 'Ingrédient supprimé avec succès.'];
}
?>