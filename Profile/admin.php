<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /SportVarietyFit/index.php");
    exit();
}
// Dzēš lietotāju no db
if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?"); // sagatavo SQL vaicājumu, lai dzēstu lietotāju pēc ID
    $stmt->bind_param("i", $id); // ievieto ID vaicājumā
    $success = $stmt->execute(); // izpilda vaicājumu un saglabā rezultātu
    $_SESSION['success'] = $success ? "User deleted successfully!" : "Failed to delete user."; // Izvada paziņojumu
    header("Location: admin.php");
    exit();
}
// Iegūst visus lietotājus, ieskaitot adminus, bet ne pašreizējo adminu
$sql = "SELECT id, full_name, email, role FROM users WHERE id != ?";
$stmt = $conn->prepare($sql);
$currentAdminId = $_SESSION['user_id'];
$stmt->bind_param("i", $currentAdminId);
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
        <form method="GET" style="text-align:center; margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search by name or email" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
        <div class="table-responsive">
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
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>