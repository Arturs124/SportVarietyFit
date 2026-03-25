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
// Parāda visas treniņu programmas select izvēlnē
$workout_types = [];
$res2 = $conn->query("SELECT id, workout_title FROM workouts ORDER BY workout_title ASC");
while ($row2 = $res2->fetch_assoc()) {
    $workout_types[] = $row2;
}
// Pievieno workout programmu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_program'])) {
    $sports_category_id = (int) $_POST['sports_category_id'];
    $workout_type_id = (int) $_POST['workout_type_id'];
    $title = trim($_POST['title']);
    $short_description = trim($_POST['short_description']);
    $age_group = $_POST['age_group'];
    $level = $_POST['level'];
    $image = '';
    // Pievieno bildi
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('program_') . ".$ext";
        move_uploaded_file($_FILES['image']['tmp_name'],__DIR__ . "/../uploads/" . $image);
    }
    // Pievieno datu bāzē
    if ($sports_category_id && $workout_type_id && $title && $short_description && $age_group && $level) {
        $stmt = $conn->prepare("
            INSERT INTO workout_programs 
            (sports_category_id, workout_type_id, image, title, short_description, age_group, level) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", 
            $sports_category_id, 
            $workout_type_id, 
            $image, 
            $title, 
            $short_description, 
            $age_group, 
            $level
        );
        $stmt->execute();
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    $error = "All fields are required.";
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
            <label>Workout Type:</label>
            <select name="workout_type_id" required>
                <option value="">Select Type</option>
                <?php foreach ($workout_types as $type): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['workout_title']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Image:</label>
            <input type="file" name="image" accept="image/*" required>
            <label>Title:</label>
            <input type="text" name="title" required>
            <label>Short Description:</label>
            <textarea name="short_description" rows="3" required></textarea>
            <label>Age Group:</label>
            <select name="age_group" required>
                <option value="">Select Age Group</option>
                <option value="kid">Kid</option>
                <option value="teen">Teen</option>
                <option value="adult">Adult</option>
                <option value="senior">Senior</option>
            </select>
            <label>Level:</label>
            <select name="level" required>
                <option value="">Select Level</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
            <button type="submit" name="add_program">Add Program</button>
        </form>
    </div>
    <?php include '../Include/footer.php'; ?>
</body>
</html>