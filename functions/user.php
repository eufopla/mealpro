<?php

function checkIfUserExists(PDO $db, string $user_name): bool
{
    $stmt = $db->prepare("SELECT name FROM user WHERE name = :user_name");
    $stmt->execute(['user_name' => $user_name]);
    return $stmt->fetch() !== false;
}

function getAllUserInfo(PDO $db): array
{
    $stmt = $db->prepare("SELECT * FROM user");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getUserById(PDO $db, int $user_id): ?array
{
    $stmt = $db->prepare("SELECT * FROM user WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
function createUser(PDO $db, string $name): bool
{
    $stmt = $db->prepare("INSERT INTO user (name) VALUES (:name)");
    return $stmt->execute(['name' => $name]);
}

function deleteUser(PDO $db, int $user_id): bool
{
    $stmt = $db->prepare("DELETE FROM user WHERE id = :user_id");
    return $stmt->execute(['user_id' => $user_id]);
}
function updateUser(PDO $db, int $user_id, string $name): bool
{
    $stmt = $db->prepare("UPDATE user SET name = :name WHERE id = :user_id");
    return $stmt->execute(['name' => $name, 'user_id' => $user_id]);
}
?>