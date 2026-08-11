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