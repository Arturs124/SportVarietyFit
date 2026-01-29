<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /SportVarietyFit/index.php");
    exit();
}
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if ($delete_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account!";
        header("Location: admin.php");
        exit();
    }
    // Dzēš lietotāju no db
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete user.";
    }
    header("Location: admin.php");
    exit();
}
$sql = "SELECT id, full_name, email, role FROM users WHERE role != 'admin'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="/SportVarietyFit/Assets/css/admin.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<div class="admin-wrapper">
    <h1 style="text-align: center;">Admin Panel</h1>
    <?php include '../Include/adminbar.php'; ?>
    <div class="admin-container">
        <h2>Admin Dashboard</h2>
        
        <h3>Registered Users</h3>
        
        <?php if (isset($_SESSION['error'])) { ?>
            <p class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php } ?>
        
        <?php if (isset($_SESSION['success'])) { ?>
            <p class="success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php } ?>

        <table>
            <thead>
                <tr>
                    <th style="text-align: center;">Full Name</th>
                    <th style="text-align: center;">Email</th>
                    <th style="text-align: center;">Role</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                    <tr>
                        <td style="text-align: center;"><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td style="text-align: center;"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="text-align: center;"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-link edit">Edit Role</a>
                            <a href="admin.php?delete_id=<?php echo $user['id']; ?>" class="action-link delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>