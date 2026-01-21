<?php
session_start();
require_once 'db.php';
// Pārbauda vai lietotājs ir logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Pārbauda vai ir nospiesta dzēšanas poga
if (isset($_POST['delete_account'])) {
    $user_id = $_SESSION['user_id'];
    // Dzēš lietotāju
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $user_id);

    if ($delete_stmt->execute()) {
        session_unset();
        session_destroy();
        header("Location: login.php?success=Your account has been deleted successfully.");
        exit();
    } else {
        header("Location: profile.php?error=There was an issue deleting your account.");
        exit();
    }
}
?>