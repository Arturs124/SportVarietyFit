<?php
session_start();
require_once 'db.php';
// Ja lietotājs nav pieteicies, viņš tiek novirzīts uz login lapu
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Iegūt lietotāja datus no datubāzes
$user_id = $_SESSION['user_id'];
$sql = "SELECT full_name, email, password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_full_name = $_POST['full_name'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    // Pārbauda vai jaunā parole un apstiprinājuma parole sakrīt
    if ($new_password === $confirm_password) {
        // Hasho jauno paroli
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        // Atjaunina pilno vārdu un paroli datubāzē
        $update_sql = "UPDATE users SET full_name = ?, password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $new_full_name, $new_password_hash, $user_id);
        $update_stmt->execute();
        // Novirza atpakaļ uz profila lapu
        header("Location: profile.php?success=Profile updated successfully");
        exit();
    } else {
        $error = "New password and confirmation do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/SportVarietyFit/Assets/css/profile.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<!-- Rediģēt profilu -->
<div class="profile-container">
    <h2>Edit Your Profile</h2>

    <?php if (isset($error)) { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <form action="edit.php" method="POST">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

        <h3>Change Your Password</h3>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password">

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password">

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="profile.php">Back to Profile</a></p>
</div>
<?php include '../Include/footer.php'; ?>
</body>
</html>