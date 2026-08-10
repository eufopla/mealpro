<?php
function getLastLogs(PDO $db, int $limit = 10): array
{
    $stmt = $db->prepare("SELECT * FROM log ORDER BY timestamp DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLogsByUserId(PDO $db, int $user_id): array
{
    $stmt = $db->prepare("SELECT * FROM log WHERE user_id = :user_id ORDER BY timestamp DESC");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function hardDeleteLog(PDO $db, int $log_id): bool
{
    $stmt = $db->prepare("DELETE FROM log WHERE id = :log_id");
    return $stmt->execute(['log_id' => $log_id]);
}

function createLog(PDO $db, int $id_user, string $date_time, string $ac, string $table_name, int $table_id): bool
{
    $stmt = $db->prepare("INSERT INTO log (id_user, date_time, ac, table_name, table_id) VALUES (:id_user, :date_time, :ac, :table_name, :table_id)");
    return $stmt->execute([
        'id_user' => $id_user,
        'date_time' => $date_time,
        'ac' => $ac,
        'table_name' => $table_name,
        'table_id' => $table_id
    ]);
}
?>