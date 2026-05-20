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
// Lietotāja datu iegūšana no db
$user_id = (int) $_GET['id'];// iegūst lietotāja ID no URL
$stmt = $conn->prepare("SELECT id, full_name, email, role FROM users WHERE id = ?");// sagatavo SQL, lai atrastu lietotāju pēc id
$stmt->bind_param("i", $user_id);// ievieto id vaicājumā
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();// iegūst lietotāja datus
if (!$user) {
    header("Location: admin.php");
    exit();
}
//Lietotāja lomas maiņa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?"); // sagatavo SQL, lai mainītu lietotāja lomu
    $stmt->bind_param("si", $_POST['role'], $user_id); // ieliek jauno lomu un lietotāja id
    $stmt->execute();
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