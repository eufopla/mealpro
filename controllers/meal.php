<?php

require_once __DIR__ . '/../functions/meal.php';
require_once __DIR__ . '/../functions/link.php';
require_once __DIR__ . '/../functions/log.php';

function createMealController(
    PDO $db,
    string $name,
    string $description,
    int $nbr_ppl,
    array $ingredients,
    int $userId
): array {
    try {
    $db->beginTransaction();

    if (mealAlreadyExists(
        $db,
        $name,
        $description,
        $nbr_ppl,
        $ingredients
    )) {
        throw new Exception(
            "Ce repas existe déjà avec exactement les mêmes informations et ingrédients."
        );
    }
    $meal = createMeal(
        $db,
        $name,
        $description,
        $nbr_ppl
    );
        if (!$meal['success']) {
            throw new Exception($meal['message']);
        }
        $id_meal = $meal['id_meal'];
        foreach ($ingredients as $id_ingredient => $quantity) {
            if (!createLink(
                $db,
                $id_meal,
                (int) $id_ingredient,
                (float) $quantity
            )) {
                throw new Exception(
                    "Impossible d'ajouter l'ingrédient au repas."
                );
            }
        }
        if (!createLog(
            $db,
            $userId,
            date('Y-m-d H:i:s'),
            'create',
            'meal',
            $id_meal
        )) {
            throw new Exception(
                "Impossible de créer le log."
            );
        }
        $db->commit();
        return [
            'success' => true,
            'message' => 'Repas ajouté avec succès.',
            'id_meal' => $id_meal
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
function hardDeleteMealController(
    PDO $db,
    int $id,
    int $userId
): array {
    try {
        $db->beginTransaction();
        if (!checkIfMealExists($db, $id)) {
            throw new Exception("Ce repas n'existe pas.");
        }
        $stmt = $db->prepare("DELETE FROM link WHERE id_meal = :id_meal");
        if (!$stmt->execute(['id_meal' => $id])) {
            throw new Exception("Impossible de supprimer les liens.");
        }
        $deletedMeal = hardDeleteMeal($db, $id);
        if (!$deletedMeal['success']) {
            throw new Exception($deletedMeal['message']);
        }
        if (!createLog(
            $db,
            $userId,
            date('Y-m-d H:i:s'),
            'delete',
            'meal',
            $id
        )) {
            throw new Exception("Impossible de créer le log.");
        }
        $db->commit();
        return [
            'success' => true,
            'message' => 'Repas supprimé avec succès.',
            'id_meal' => $id
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