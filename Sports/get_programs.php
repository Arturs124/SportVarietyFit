<?php
require_once '../Profile/db.php';
header('Content-Type: application/json');

$sports_category_id = isset($_GET['sports_category_id']) ? (int)$_GET['sports_category_id'] : 0;
$workout_type_id = isset($_GET['workout_type_id']) ? (int)$_GET['workout_type_id'] : 0;

$programs = [];

if ($sports_category_id > 0 && $workout_type_id > 0) {
    $stmt = $conn->prepare("
        SELECT id, title 
        FROM workout_programs 
        WHERE sports_category_id = ? AND workout_type_id = ?
        ORDER BY title ASC
    ");
    $stmt->bind_param("ii", $sports_category_id, $workout_type_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $programs[] = $row;
    }
}

echo json_encode($programs);