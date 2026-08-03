<?php
require_once 'functions/meal.php';
require_once 'functions/ingredient.php';

function checkIfLinkExists(PDO $db, int $id_meal, int $id_ingredient): bool
{
    $stmt = $db->prepare("SELECT * FROM link WHERE id_meal = :id_meal AND id_ingredient = :id_ingredient");
    $stmt->execute(['id_meal' => $id_meal, 'id_ingredient' => $id_ingredient]);
    return $stmt->fetch() !== false;
}

function createLink(PDO $db, int $id_meal, int $id_ingredient, float $quantity): bool
{
    if (checkIfLinkExists($db, $id_meal, $id_ingredient)) {
        return false;
    } else if (checkIfMealExists($db, $id_meal) && checkIfIngredientExists($db, $id_ingredient)) {
        $stmt = $db->prepare("INSERT INTO link (id_meal, id_ingredient, quantity) VALUES (:id_meal, :id_ingredient, :quantity)");
        return $stmt->execute(['id_meal' => $id_meal, 'id_ingredient' => $id_ingredient, 'quantity' => $quantity]);
    } else {
        return false;
    }
}

function getLinkByIds(PDO $db, int $id_meal, int $id_ingredient): ?array
{
    $stmt = $db->prepare("SELECT * FROM link WHERE id_meal = :id_meal AND id_ingredient = :id_ingredient");
    $stmt->execute(['id_meal' => $id_meal, 'id_ingredient' => $id_ingredient]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function deleteLink(PDO $db, int $id_meal, int $id_ingredient): bool
{
    $stmt = $db->prepare("DELETE FROM link WHERE id_meal = :id_meal AND id_ingredient = :id_ingredient");
    return $stmt->execute(['id_meal' => $id_meal, 'id_ingredient' => $id_ingredient]);
}

function updateLink(PDO $db, int $id_meal, int $id_ingredient, float $quantity): bool
{
    $stmt = $db->prepare("UPDATE link SET quantity = :quantity WHERE id_meal = :id_meal AND id_ingredient = :id_ingredient");
    return $stmt->execute(['quantity' => $quantity, 'id_meal' => $id_meal, 'id_ingredient' => $id_ingredient]);
}

?>