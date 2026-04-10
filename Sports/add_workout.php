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
// Parāda visus treniņus tabulā
$sql = "
    SELECT workouts.id, workouts.workout_title, workouts.description, workouts.image, sports_categories.badge
    FROM workouts
    JOIN sports_categories ON workouts.sports_category_id = sports_categories.id
    ORDER BY workouts.id DESC
";
$result = $conn->query($sql);
// Dzēst treniņu
if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    $conn->query("DELETE FROM workouts WHERE id=$id");
    header("Location: add_workout.php");
    exit;
}
// Iegūst treniņa datus rediģēšanai
$workout_to_update = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM workouts WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $workout_to_update = $stmt->get_result()->fetch_assoc();
}
// Atjauno treniņa datus datubāzē
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_workout'])) {
    $id = (int) $_POST['workout_id'];
    $cat = (int) $_POST['sports_category_id'];
    $title = $_POST['workout_title'];
    $type = $_POST['workout_type'];
    $desc = $_POST['description'];
    $image = $_POST['existing_image'];
    // atjauno jaunu bildi
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('workout_') . ".$ext";
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/../uploads/" . $image);
    }
    $stmt = $conn->prepare("
        UPDATE workouts 
        SET sports_category_id=?, workout_title=?, workout_type=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("issssi", $cat, $title, $type, $desc, $image, $id);
    $stmt->execute();

    header("Location: add_workout.php");
    exit;
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
        <h2><?= $workout_to_update ? 'Edit Workout' : 'Add New Workout' ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <?php if ($workout_to_update): ?>
                <input type="hidden" name="workout_id" value="<?= $workout_to_update['id'] ?>">
                <input type="hidden" name="existing_image" value="<?= $workout_to_update['image'] ?>">
            <?php endif; ?>
            <label>Sport Category:</label>
            <select name="sports_category_id" required>
                <option value="">Select Sport</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"
                        <?= $workout_to_update && $workout_to_update['sports_category_id'] == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['badge']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Workout Title:</label>
            <input type="text" name="workout_title" value="<?= $workout_to_update ? htmlspecialchars($workout_to_update['workout_title']) : '' ?>"required>
            <label>Description:</label>
            <textarea name="description"><?= $workout_to_update ? htmlspecialchars($workout_to_update['description']) : '' ?></textarea>
            <label>Image:</label>
            <input type="file" name="image">
            <?php if ($workout_to_update && $workout_to_update['image']): ?>
                    <img src="../uploads/<?= $workout_to_update['image'] ?>" width="100">
            <?php endif; ?>
            <button type="submit" name="<?= $workout_to_update ? 'update_workout' : 'add_workout' ?>">
                <?= $workout_to_update ? 'Update Workout' : 'Add Workout' ?>
            </button>
        </form>
    </div>
</div>
<div class="table-wrapper">
    <h2 style="text-align: center;">All Workouts</h2>
    <div class="table-responsive">
        <table class="workout-table">
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
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['badge']) ?></td>
                    <td><?= htmlspecialchars($row['workout_title']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" width="80">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="add_workout.php?edit=<?= $row['id'] ?>">Edit</a>
                        <a href="add_workout.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this workout?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>