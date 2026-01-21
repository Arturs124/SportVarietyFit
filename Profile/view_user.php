<?php
session_start();
require_once 'db.php';
// Ja lietotājs nav logged in vai nav admins, viņš tiek aizvirzīts uz login lapu
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['id'];
// Iegūst lietotāja info no db
$sql = "SELECT full_name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Ja lietotājs nav atrasts, novirza atpakaļ uz admin lapu
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/SportVarietyFit/Assets/css/view_user.css">
    <title>View User</title>
</head>
<body>
<?php include '../Include/header.php'; ?>

<div class="user-details-container">
    <h2>User Details</h2>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

    <p><a href="admin.php">Back to Admin Page</a></p>
</div>

<?php include '../Include/footer.php'; ?>
</body>
</html>