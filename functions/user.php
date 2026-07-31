<?php

function checkIfUserExists(PDO $db, string $user_name): bool
{
    $stmt = $db->prepare("SELECT name FROM user WHERE name = :user_name");
    $stmt->execute(['user_name' => $user_name]);
    return $stmt->fetch();
}

function getAllUserInfo(PDO $db): array
{
    $stmt = $db->prepare("SELECT name FROM user");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>