<?php
// pages/profile/index.php

// This file acts as a router for user profiles.

session_start();

// 1. --- Security Check: Is the user logged in? ---
// If 'user_id' is not set in the session, they are not logged in.
// Redirect them to the login page and stop the script.
if (!isset($_SESSION['user_id'])) {
    // We need the BASE_URL constant for a clean redirect.
    require_once __DIR__ . '/../../config/db.php';
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}


// 2. --- Role-Based Redirect ---
// Check the user's role stored in the session and redirect to the correct profile view.
$role = $_SESSION['user_role'] ?? 'youth';

switch ($role) {
    case 'youth':
        header("Location: view.php");
        exit();
    case 'congregation':
        // For now, we will also redirect congregation members to a specific page.
        // We will create this file next.
        header("Location: view.php");
        exit();
    case 'admin':
        // Admins can be redirected to a special dashboard or their own profile.
        // For now, let's send them to a generic profile page.
        // In the future, this could be header("Location: ../admin/dashboard.php");
        header("Location: view.php"); // Placeholder
        exit();
    default:
        // If the role is unknown for some reason, log them out for safety.
        require_once __DIR__ . '/../../config/db.php';
        header("Location: " . BASE_URL . "pages/logout.php");
        exit();
}