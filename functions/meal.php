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
        return $stmt->execute(['name' => $name, 'description' => $description, 'measure' => $measure]);
    }
}