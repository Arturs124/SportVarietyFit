<?php
require_once '../Profile/db.php';
// treniņa programma
$workout_id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT workouts.*, sports_categories.badge FROM workouts JOIN sports_categories ON workouts.sports_category_id = sports_categories.id WHERE workouts.id = ?");
$stmt->bind_param("i", $workout_id);
$stmt->execute();
$workout = $stmt->get_result()->fetch_assoc();
// workout programma
$res = $conn->prepare("
    SELECT wp.*, sc.badge, w.workout_title
    FROM workout_programs wp
    JOIN sports_categories sc ON wp.sports_category_id = sc.id
    JOIN workouts w ON wp.workout_type_id = w.id
    WHERE wp.workout_type_id = ?
    ORDER BY wp.id DESC
");
$res->bind_param("i", $workout_id);
$res->execute();
$res = $res->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/workout.css">
    <title>workouts</title>
</head>
<body>
    <?php include '../Include/header.php'; ?>
        <div class="workout-hero">
            <img src="../uploads/<?= htmlspecialchars($workout['image']) ?>" alt="<?= htmlspecialchars($workout['badge']) ?>" class="workout-hero-image">
            <div class="workout-hero-content">
                <span class="workout-badge"><?= htmlspecialchars($workout['badge']) ?></span>
                <div class="workout-title"><?= htmlspecialchars($workout['workout_title']) ?></div>
                <div class="workout-description"><?= htmlspecialchars($workout['description']) ?></div>
            </div>
        </div>
        <div style="max-width:1200px;margin:40px auto 0 auto;">
            <h2>Workout Programs</h2>
            <div class="workout-program-card">
                <?php while ($row = $res->fetch_assoc()): ?>
                    <a href="exercise.php?program_id=<?= $row['id'] ?>" class="workout-program-link">
                        <?php if (!empty($workout['image'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($workout['image']) ?>" class="workout-program-image">
                        <?php endif; ?>
                        <div><?= htmlspecialchars($row['title']) ?></div>
                        <div><?= htmlspecialchars($row['short_description']) ?></div>
                        <div class="age_group"><b>Age group: </b><?= htmlspecialchars($row['age_group']) ?></div>
                        <div class="level"><b>Level: </b><?= htmlspecialchars($row['level']) ?></div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    <?php include '../Include/footer.php'; ?>
</body>
</html>