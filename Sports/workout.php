<?php
require_once '../Profile/db.php';
// treniņa programma
$workout_id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT workouts.*, sports_categories.badge FROM workouts JOIN sports_categories ON workouts.sports_category_id = sports_categories.id WHERE workouts.id = ?");
$stmt->bind_param("i", $workout_id);
$stmt->execute();
$workout = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>workouts</title>
</head>
<body>
    <?php include '../Include/header.php'; ?>

    <?php include '../Include/footer.php'; ?>
</body>
</html>