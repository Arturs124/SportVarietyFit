<?php
require_once '../Profile/db.php';

$sport = strtolower($_GET['sport'] ?? '');
// sporta kategorija
$stmt = $conn->prepare("SELECT * FROM sports_categories WHERE LOWER(badge) = ?");
$stmt->bind_param("s", $sport);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    echo "<h1>Sport not found</h1>";
    exit;
}
// treniņa kategorija priekš specifiskas sporta kategorijas
$category_id = (int) $category['id'];
$result = $conn->query(" SELECT id, workout_title, image FROM workouts 
    WHERE sports_category_id = $category_id 
    ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports</title>
    <link rel="stylesheet" href="../Assets/css/sports.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<div class="workout-list-section">
    <h2>Workouts for <?= htmlspecialchars($category['badge']) ?></h2>
    <?php if ($result->num_rows > 0): ?>
        <div class="workout-cards-row">
            <?php while ($workout = $result->fetch_assoc()): ?>
                <div class="workout-card">
                    <?php if (!empty($workout['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($workout['image']) ?>" class="workout-card-image">
                    <?php endif; ?>
                    <div class="workout-card-title">
                        <?= htmlspecialchars($workout['workout_title']) ?>
                    </div>
                    <a href="workout.php?id=<?= $workout['id'] ?>" class="workout-card-link">View Workout</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No workouts yet for this sport.</p>
    <?php endif; ?>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>