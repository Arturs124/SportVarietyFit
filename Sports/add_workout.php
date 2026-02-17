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
        <form method="post">
            <label>Sport Category:</label>
            <select>
                <option>Select Sport</option>
            </select>
            <label>Workout Title:</label>
            <input type="text">
            <label>Description:</label>
            <textarea></textarea>
            <label>Image:</label>
            <input type="file">
            <button type="submit">Submit</button>
        </form>
    </div>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>