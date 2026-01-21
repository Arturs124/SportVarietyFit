<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT full_name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$_SESSION['role'] = $user['role'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['password'];
    if (!empty($new_password)) {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_password_hash, $user_id);
        $update_stmt->execute();

        header("Location: profile.php?success=Password updated successfully");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="/SportVarietyFit/Assets/css/profile.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<div class="profile-flex-main">
    <!-- Kreisā kolonna - profila info -->
    <div class="profile-info-wide">
        <h2>Profile</h2>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
        <p><strong>Subscription:</strong></p>
    </div>
    <!-- Labā kolonna -->
    <div class="profile-actions-wide">
        <h2 style="font-size:1.15em;margin-bottom:18px;">Account Actions</h2>
        <a href="edit.php"><button class="profile-action-btn">Edit Profile</button></a>
        <a href="#"><button class="profile-action-btn" style="background:#e53935;">Cancel Subscription</button></a>
        <form action="delete_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');" style="display:inline;">
            <button type="submit" name="delete_account" class="profile-action-btn delete">Delete Account</button>
        </form>
        <?php if ($_SESSION['role'] === 'admin') { ?>
            <a href="admin.php"><button class="profile-action-btn">Admin Page</button></a>
        <?php } ?>
    </div>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>