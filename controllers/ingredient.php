<?php
require_once __DIR__ . '/../functions/ingredient.php';
require_once __DIR__ . '/../functions/link.php';
require_once __DIR__ . '/../functions/log.php';

function createIngredientController(
    PDO $db,
    int $userId,
    string $name,
    string $description,
    string $measure,
    int $g_protein_for_100m,
    int $k_calories_for_100m,
    int $g_fat_for_100m,
    int $g_saturated_fat_for_100m,
    int $g_fiber_for_100m,
    int $g_carbohydrate_for_100m,
    int $g_sugar_for_100m,
    int $g_salt_for_100m
): array {
    try {
        $db->beginTransaction();

        $ingredient = createIngredient(
            $db,
            $name,
            $description,
            $measure,
            $g_protein_for_100m,
            $k_calories_for_100m,
            $g_fat_for_100m,
            $g_saturated_fat_for_100m,
            $g_fiber_for_100m,
            $g_carbohydrate_for_100m,
            $g_sugar_for_100m,
            $g_salt_for_100m
        );

        if (!$ingredient['success']) {
            throw new Exception($ingredient['message']);
        }

        $db->commit();

        return [
            'success' => true,
            'message' => 'Ingrédient ajouté avec succès.',
            'id_ingredient' => $ingredient['id_ingredient']
        ];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}