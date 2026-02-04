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
//  pievieno kategoriju
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $badge = trim($_POST['badge']);
    $title = trim($_POST['card_title']);

    if ($badge && $title && $_FILES['image']['name']) {

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('sport_') . ".$ext";

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            __DIR__ . "/../uploads/$filename"
        );

        $stmt = $conn->prepare(
            "INSERT INTO sports_categories (image, badge, card_title) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $filename, $badge, $title);
        $stmt->execute();
    }

    header("Location: add_category.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add sports category</title>
    <link rel="stylesheet" href="../Assets/css/add_category.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<div class="admin-wrapper">
    <h1 style="text-align: center;">Admin Panel</h1>
    <?php include '../Include/adminbar.php'; ?>

    <div class="form-container" style="max-width:500px;margin:30px auto;padding:24px;background:#fff;border-radius:12px;box-shadow:0 2px 16px #0001;">
        <h2>Add New Sports Category</h2>
        <!-- Pievienot kategoriju -->
        <form method="post" enctype="multipart/form-data">
            <label>Category Image:</label>
            <input type="file" accept="image/*" name="image" style="margin-bottom:10px;"><br>
            <label>Badge:</label>
            <input type="text" name="badge" placeholder="Category name" style="width:100%;margin-bottom:10px;"><br>
            <label>Card Title (short description):</label>
            <input type="text" name="card_title" placeholder="short description" style="width:100%;margin-bottom:10px;"><br>
            <button type="submit" style="padding:10px 22px;background:#a71d2a;color:#fff;border:none;border-radius:6px;font-weight:600;">Add Category</button>
        </form>
        <!-- Esošās kategorijas -->
        <h3 style="margin-top:30px;">Existing Categories</h3>
        <?php $categories = $conn ->query("SELECT id, badge, image, card_title FROM sports_categories") ->fetch_all(MYSQLI_ASSOC);?>
        <?php foreach ($categories as $category): ?>
            <li>
                <img src="../uploads/<?= htmlspecialchars($category['image']) ?>">
                <span><?= htmlspecialchars($category['badge']) ?></span>
                <span><?= htmlspecialchars($category['card_title']) ?></span>
                <a href="#" class="edit-btn">Edit</a>
                <a href="#" class="delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
            </li>
        <?php endforeach; ?>
        <!-- Rediģēt kategoriju -->
        <h3>Edit Category</h3>
        <form>
            <label>Category Name:</label>
            <input type="text" value="Current badge name" style="width:100%;margin-bottom:10px;">
            <label>Card Title:</label>
            <input type="text" value="Current card title" style="width:100%;margin-bottom:10px;">
            <label>Image:</label>
            <input type="file" accept="image/*"><br><br>
            <img src="" alt="Current image" style="width:80px;height:50px;object-fit:cover;border-radius:4px;"><br><br>
            <button type="submit">Update Category</button>
        </form>
    </div>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>