<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$user_id = $_GET['id'];

$sql = "SELECT id, full_name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin.php");
    exit();
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_role = $_POST['role'];
    
    $update_sql = "UPDATE users SET role = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_role, $user_id);
    $update_stmt->execute();

    header("Location: admin.php?success=Role updated successfully");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/SportVarietyFit/Assets/css/edit_user.css">
    <title>Edit User Role</title>
</head>
<body>
<?php include '../Include/header.php'; ?>
<!-- Rediģēt lietotāja Role -->
<div class="edit-user-container">
    <h2>Edit User Role</h2>
    
    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
        
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        
        <label for="role">Role:</label>
        <select name="role" required>
            <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="admin.php">Back to Admin Page</a></p>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>