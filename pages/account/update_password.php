<?php
// TOP of update-password.php – immediately after opening tag
ob_start();                       // 4) capture any accidental output
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token     = trim($_POST['token'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (!$token || strlen($password) < 6) {
        $_SESSION['error_message'] = 'Invalid input.';
        header('Location: forgot-password.php');
        exit;
    }

    // 1) check token still valid
    $stmt = $conn->prepare(
        "SELECT id FROM registrations
         WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        $_SESSION['error_message'] = 'Token expired or invalid.';
        header('Location: forgot-password.php');
        exit;
    }

    // 2) update
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $upd = $conn->prepare(
        "UPDATE registrations
         SET password = ?, reset_token = NULL, reset_expires = NULL
         WHERE reset_token = ?");
    $upd->bind_param('ss', $hash, $token);
    $upd->execute();

    if ($upd->affected_rows) {
        $_SESSION['success_message'] = 'Password changed. Log in.';
        header('Location: login.php');
    } else {
        $_SESSION['error_message'] = 'Could not update password.';
        header('Location: forgot-password.php');
    }
    exit;
}
ob_end_clean();
?>