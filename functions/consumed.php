<?php
require_once 'log.php';
function getAllConsumedMeals(PDO $db): array
{
    $stmt = $db->query("SELECT name, description, nbr_ppl FROM meal WHERE id IN (SELECT id_meal FROM consumed)");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getConsumedMealsByUserId(PDO $db, int $id): array
{
    $stmt = $db->prepare("SELECT name, description, nbr_ppl FROM meal WHERE id IN (SELECT id_meal FROM consumed WHERE id_user = :id)");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getConsumedMealsByUserInMonth(PDO $db, int $userId, int $month, int $year): array
{
    $stmt = $db->prepare("
        SELECT
            c.date_time,
            m.name
        FROM consumed c
        INNER JOIN meal m ON m.id = c.id_meal
        WHERE c.id_user = :id_user
        AND MONTH(c.date_time) = :month
        AND YEAR(c.date_time) = :year
        ORDER BY c.date_time
    ");

    $stmt->execute([
        'id_user' => $userId,
        'month' => $month,
        'year' => $year
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function createConsumption(PDO $db, $iduser, $idmeal, $datetime): array
{
    $stmt = $db->prepare("INSERT INTO consumed (id_user, id_meal, date_time) VALUES (:id_user, :id_meal, :date_time)");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>