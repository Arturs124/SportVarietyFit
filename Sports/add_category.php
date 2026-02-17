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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_category'])) {
    $badge = trim($_POST['badge']);
    $title = trim($_POST['card_title']);
    if ($badge && $title && !empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('sport_') . ".$ext";
        move_uploaded_file($_FILES['image']['tmp_name'],__DIR__ . "/../uploads/$filename");
        $stmt = $conn->prepare("INSERT INTO sports_categories (image, badge, card_title) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $filename, $badge, $title);
        $stmt->execute();
    }
    header("Location: add_category.php");
    exit();
}

// Dzēst kategoriju
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];

    // Dzēš kategoroju no datubāzes
    $delete_sql = "DELETE FROM sports_categories WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $category_id);

    if ($delete_stmt->execute()) {
        $_SESSION['success'] = "Category deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete category.";
    }
    header("Location: add_category.php");
    exit();
}

// Rediģēt kateogoriju
if (isset($_GET['edit_category'])) {
    $category_id = (int) $_GET['edit_category'];

    $stmt = $conn->prepare("SELECT * FROM sports_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows) {
        $category = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Category not found.";
        header("Location: add_category.php");
        exit;
    }
}

// Atjaunot kategoriju
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {

    $id = (int) $_POST['category_id'];
    $badge = trim($_POST['badge']);
    $card_title = trim($_POST['card_title']);
    // Jaunā bilde tiek augšupielādēta
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = __DIR__ . "/../uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = uniqid() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file_name);
        $stmt = $conn->prepare("UPDATE sports_categories SET badge=?, card_title=?, image=? WHERE id=?");
        $stmt->bind_param("sssi", $badge, $card_title, $file_name, $id);
    } else {
        // atjaunot tikai tekstu
        $stmt = $conn->prepare("UPDATE sports_categories SET badge=?, card_title=? WHERE id=?");
        $stmt->bind_param("ssi", $badge, $card_title, $id);
    }
    $stmt->execute();
    $_SESSION['success'] = "Category updated!";
    header("Location: add_category.php");
    exit;
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
                <a href="add_category.php?edit_category=<?= $category['id'] ?>" class="edit-btn">Edit</a>
                <a href="add_category.php?delete_category=<?= $category['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
            </li>
        <?php endforeach; ?>
        <!-- Rediģēt kategoriju -->
        <?php if (isset($_GET['edit_category'])) { ?>
            <h3>Edit Category</h3>
            <form action="add_category.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="category_id" value="<?= $category['id']; ?>">
                <label>Category Name:</label>
                <input type="text" name="badge" id="badge" value="<?= htmlspecialchars($category['badge']); ?>" required>
                <label>Card Title:</label>
                <input type="text" name="card_title" id="card_title" value="<?= htmlspecialchars($category['card_title']); ?>" required>
                <label>Image:</label>
                <input type="file" name="image" id="image" accept="image/*">
                <img src="../uploads/<?= $category['image']; ?>" width="80">
                <button type="submit" name="edit_category">Update Category</button>
            </form>
        <?php } ?>
    </div>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>