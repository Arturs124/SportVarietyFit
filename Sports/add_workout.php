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

// Pievieno jaunu treniņu datubāzē
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_workout'])) {
    $sports_category_id = (int) $_POST['sports_category_id'];
    $workout_title = trim($_POST['workout_title']);
    $description = trim($_POST['description']);
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('workout_') . ".$ext";
        $upload_dir = __DIR__ . "/../uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
    }

    if ($sports_category_id && $workout_title) {
        $stmt = $conn->prepare("
            INSERT INTO workouts (sports_category_id, workout_title, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $sports_category_id, $workout_title, $description, $image);
        $stmt->execute();
        $_SESSION['success'] = "Workout added successfully!";
        header("Location: add_workout.php");
        exit;
    }
    $error = "Sport category and workout title are required.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add workout</title>
    <link rel="stylesheet" href="../Assets/css/add_workout.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<div class="admin-wrapper">
    <h1 style="text-align: center;">Admin Panel</h1>
    <?php include '../Include/adminbar.php'; ?>
    <div class="form-container">
        <h2>Add New Workout</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Sport Category:</label>
            <select name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>">
                        <?= htmlspecialchars($category['badge']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Workout Title:</label>
            <input type="text" name="workout_title" required>
            <label>Description:</label>
            <textarea name="description"></textarea>
            <label>Image:</label>
            <input type="file" name="image">
            <button type="submit" name="add_workout">Submit</button>
        </form>
    </div>
</div>
<div class="form-container">
    <h2>All Workouts</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <a href="#">Edit</a>
                    <a href="#">Delete</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>