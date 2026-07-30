<?php
function getAllMeals(PDO $db): array
{
    $stmt = $db->query("SELECT 'name' FROM meals");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>