<?php
require_once '../Profile/db.php';
header('Content-Type: application/json');

$sports_category_id = isset($_GET['sports_category_id']) ? (int)$_GET['sports_category_id'] : 0;

$types = [];

if ($sports_category_id > 0) {
    $stmt = $conn->prepare("
        SELECT id, workout_title 
        FROM workouts 
        WHERE sports_category_id = ?
        ORDER BY workout_title ASC
    ");
    $stmt->bind_param("i", $sports_category_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $types[] = $row;
    }
}

echo json_encode($types);