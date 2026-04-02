<?php
require_once '../Profile/db.php';

// treniņu dati
$exercises = $conn->query("SELECT * FROM exercises WHERE workout_program_id");
if ($exercises) {
    $exercises = $exercises->fetch_all(MYSQLI_ASSOC);
} else {
    $exercises = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise</title>
    <link rel="stylesheet" href="../Assets/css/exercise.css">
</head>
<body>
    <?php include '../Include/header.php'; ?>
    <div class="exercise-container">
        <div class="workout-section">
            <div class="workout-title">
                Exercises (<?= count($exercises) ?>) | Sets: <?= $exercises ? $exercises[0]['sets'] : 1 ?>
            </div>
            <?php if($exercises): ?>
                <div class="workout-cards-row">
                    <?php foreach ($exercises as $ex): ?>
                        <div class="workout-card">
                            <?php if($ex['image']): ?>
                                <img src="../uploads/<?= htmlspecialchars($ex['image']) ?>" class="workout-card-image">
                            <?php endif; ?>
                            <div class="workout-card-title">
                                <?= htmlspecialchars($ex['title']) ?>
                            </div>
                            <div class="workout-card-type">
                                <?= $ex['reps'] ? $ex['reps'] . ' reps' : ''?>
                                <?= $ex['time_seconds'] ? ' | ' . $ex['time_seconds'] . ' sec' : '' ?>
                            </div>
                            <div>
                                <?= htmlspecialchars($ex['description']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No exercises found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../Include/footer.php'; ?>
</body>
</html>