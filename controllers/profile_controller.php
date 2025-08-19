<?php
// controllers/profile_controller.php
session_start();
require_once '../config/db.php';

// Security: User must be logged in to update their profile.
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id = $_SESSION['user_id'];

    // 1. Sanitize and retrieve form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $age = filter_var(trim($_POST['age']), FILTER_VALIDATE_INT);
    $gender = trim($_POST['gender']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $country = trim($_POST['country']);

    // 2. Validation (can be expanded)
    if (empty($name) || empty($email)) {
        $_SESSION['error_message'] = "Name and Email are required.";
        header("Location: " . BASE_URL . "pages/profile/edit.php");
        exit();
    }

    // --- DATABASE TRANSACTION ---
    mysqli_begin_transaction($conn);
    try {
        // --- Part 1: Update `users` table ---
        // Note: Check if the new email is already taken by ANOTHER user
        $sql_check_email = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
        mysqli_stmt_bind_param($stmt_check_email, "si", $email, $user_id);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);
        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            throw new Exception("Email address is already in use by another account.");
        }
        mysqli_stmt_close($stmt_check_email);

        $sql_user = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt_user = mysqli_prepare($conn, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "ssi", $name, $email, $user_id);
        mysqli_stmt_execute($stmt_user);

        // --- Part 2: Update `user_profiles` table ---
        // Using an "UPSERT" logic: UPDATE if exists, INSERT if not.
        $sql_profile = "INSERT INTO user_profiles (user_id, phone, age, gender, city, state, country) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        phone = VALUES(phone), age = VALUES(age), gender = VALUES(gender), city = VALUES(city), state = VALUES(state), country = VALUES(country)";
        $stmt_profile = mysqli_prepare($conn, $sql_profile);
        mysqli_stmt_bind_param($stmt_profile, "issssss", $user_id, $phone, $age, $gender, $city, $state, $country);
        mysqli_stmt_execute($stmt_profile);

        // If successful, commit the transaction
        mysqli_commit($conn);

        // Update session name in case it changed
        $_SESSION['user_name'] = $name;

        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: " . BASE_URL . "pages/profile/view.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage() ?: "A database error occurred. Could not update profile.";
        header("Location: " . BASE_URL . "pages/profile/edit.php");
        exit();
    }
}
?>