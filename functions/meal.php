<?php
require_once 'log.php';
function getAllMeals(PDO $db): array
{
    $stmt = $db->query("SELECT name, description, nbr_ppl FROM meal");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    $id_meal = (int) $db->lastInsertId();

    createLog(
        $db,
        $_SESSION['user_id'],
        date('Y-m-d H:i:s'),
        'create',
        'meal',
        $id_meal
    );

    return [
        'success' => true,
        'message' => 'Repas ajouté avec succès.',
        'id_meal' => $id_meal
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
?>