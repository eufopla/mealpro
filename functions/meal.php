<?php
function getAllMeals(PDO $db): array
{
    $stmt = $this->db->query("SELECT 'name' FROM meals");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>