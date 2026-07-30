<?php
function getAllMeals(PDO $db): array
{
    $stmt = $db->query("SELECT name, description, nbr_ppl FROM meal");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllIngredients(PDO $db): array
{
    $stmt = $db->query("SELECT name, description FROM ingredient");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>