<?php

session_start();

require_once 'config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$profile = $data['profile'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM user WHERE LOWER(name) = LOWER(?)");
$stmt->execute([$profile]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

if ($user) {

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    echo json_encode([
        'success' => true
    ]);

} else {

    http_response_code(401);

    echo json_encode([
        'success' => false
    ]);

}