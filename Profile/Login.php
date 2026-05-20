<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // login
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        // Meklē lietotāju
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?"); // sagatavo SQL vaicājumu, lai atrastu lietotāju pēc e-pasta
        $stmt->bind_param("s", $email); // ievieto e-pastu vaicājumā
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Pārbauda paroli
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                header("Location: profile.php"); // lietotājs tiek novirzīts uz profila lapu
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
            }
        } else {
            $_SESSION['error'] = "User not found!";
        }
    }
    // atjaunina paroli
    if (isset($_POST['reset_password'])) {
        $email = $_POST['email'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // jaunā parole tiek hashota
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?"); // parole tiek atjaunināta datubāzē
        $stmt->bind_param("ss", $new_password, $email); // ievieto jauno hashoto paroli un e-pastu
        if ($stmt->execute()) {
            $_SESSION['success'] = "Password updated!";
        }
    }
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/SportVarietyFit/Assets/css/registerLogin.css">
</head>
<body>
<?php include '../Include/header.php'; ?>
<div class="form-container">
    <h2>Login</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <!-- Login forma -->
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p><a href="#" id="showReset">Forgot Password?</a></p>
    <!-- Reset password forma -->
    <form method="POST" id="resetForm" style="display:none;">
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="password" name="new_password" placeholder="Enter new password" required>
        <button type="submit" name="reset_password">Reset Password</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
<?php include '../Include/footer.php'; ?>
<script>
document.getElementById("showReset").onclick = function(e) {
    e.preventDefault();
    document.getElementById("resetForm").style.display = "block";
};
</script>
</body>
</html>