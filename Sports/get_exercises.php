<?php
require_once '../Profile/db.php';
header('Content-Type: application/json');

$program_id = isset($_GET['program_id']) ? (int)$_GET['program_id'] : 0;

$exercises = [];

if ($program_id > 0) {
    $stmt = $conn->prepare("
        SELECT id, title, description, sets, reps, time_seconds, image
        FROM exercises
        WHERE workout_program_id = ?
        ORDER BY id DESC
    ");
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $exercises[] = $row;
    }
}

echo json_encode($exercises);