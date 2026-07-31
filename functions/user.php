<?php

function checkIfUserExists(PDO $db, string $user_name): bool
{
    $stmt = $db->prepare("SELECT name FROM user WHERE name = :user_name");
    $stmt->execute(['user_name' => $user_name]);
    return $stmt->fetch();
}
?>