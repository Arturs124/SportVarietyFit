<?php
session_start();
require_once '../Profile/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /SportVarietyFit/index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$result = $conn->query("SELECT role FROM users WHERE id = $user_id LIMIT 1");
$user = $result?->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    header("Location: /SportVarietyFit/index.php");
    exit();
}

// Parāda visas sporta kategorijas select izvēlnē
$categories = [];
$res = $conn->query("SELECT id, badge FROM sports_categories ORDER BY badge ASC");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/add_program.css">
    <title>Add workout program & Exercises</title>
</head>
<body>
    <?php include '../Include/header.php'; ?>
    <div class="admin-wrapper">
        <h1 style="text-align: center;">Admin Panel</h1>
        <?php include '../Include/adminbar.php'; ?>
    </div>
    <!-- pievieno treniņa programmu -->
    <div class="form-container">
        <h2>Add Workout Program</h2>
        <form method="post">
            <label>Sports Category</label>
            <select name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['badge']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Image:</label>
            <input type="file" name="image">
            <label>Title:</label>
            <input type="text" name="title">
            <label>Short Description:</label>
            <textarea name="short_description" rows="3"></textarea>
            <label>Age Group:</label>
            <select>
                <option value="">Select Age Group</option>
                <option value="kid">Kid</option>
                <option value="teen">Teen</option>
                <option value="adult">Adult</option>
                <option value="senior">Senior</option>
            </select>
            <label>Level:</label>
            <select>
                <option value="">Select Level</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            <button type="submit">Add Program</button>
        </form>
    </div>
    <?php include '../Include/footer.php'; ?>
</body>
</html>