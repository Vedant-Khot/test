<?php
// controllers/gq_enrollment.php
session_start();
require_once '../config/db.php';
require_once 'functions.php';
// --- Step 1: Security Check - User MUST be logged in ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please log in before enrolling in the quest.";
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

// --- Step 2: Process the form ONLY if it was submitted via POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $quest_year = date("Y");

    // --- Step 3: Fetch the user's age from the database FIRST ---
    // This is the critical missing piece.
    $sql_age = "SELECT age FROM user_profiles WHERE user_id = ? LIMIT 1";
    $stmt_age = mysqli_prepare($conn, $sql_age);
    mysqli_stmt_bind_param($stmt_age, "i", $user_id);
    mysqli_stmt_execute($stmt_age);
    $result_age = mysqli_stmt_get_result($stmt_age);
    $profile_data = mysqli_fetch_assoc($result_age);
    $user_age = $profile_data['age'] ?? 0; // Get the age, default to 0 if not found
    mysqli_stmt_close($stmt_age);

    // --- Step 4: Perform all validations at the beginning ---
    
    // a) Validate Age
    if ($user_age < 15 || $user_age > 30) {
        $_SESSION['error_message'] = "We're sorry, this contest is only open to participants aged 15-30.";
        header("Location: " . BASE_URL . "pages/gitas-quest/"); // Send them back to the main GQ page
        exit();
    }

    // b) Validate Form Fields
    $college_name = trim($_POST['college_name']);
    $standard = trim($_POST['standard']);
    if (empty($college_name) || empty($standard)) {
        $_SESSION['error_message'] = "Please provide both your college name and your year of study.";
        header("Location: " . BASE_URL . "pages/gita-quest-register.php");
        exit();
    }
    if (!isset($_POST['terms'])) {
        $_SESSION['error_message'] = "You must agree to the Terms and Conditions.";
        header("Location: " . BASE_URL . "pages/gita-quest-register.php");
        exit();
    }

    // --- Step 5: Check if the user is already enrolled for this year ---
    $sql_check = "SELECT id FROM gq_registrations WHERE user_id = ? AND quest_year = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $quest_year);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        // User is already enrolled, just send them to the sessions page.
        header("Location: " . BASE_URL . "pages/gitas-quest/sessions.php");
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // --- Step 6: If all checks pass, insert the enrollment record ---
    $sql_insert = "INSERT INTO gq_registrations (user_id, college_name, standard, quest_year) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "issi", $user_id, $college_name, $standard, $quest_year);

    if (mysqli_stmt_execute($stmt_insert)) {
        // Success! Redirect them to the sessions to start the quest.
        $_SESSION['gq_registered'] = true; // Set a session variable to indicate they are registered
        header("Location: " . BASE_URL . "pages/gitas-quest/sessions.php?enrollment=success");
        exit();
    } else {
        $_SESSION['error_message'] = "A database error occurred. Please try again.";
        header("Location: " . BASE_URL . "pages/gita-quest-register.php");
        exit();
    }
} else {
    // Redirect if accessed directly via GET request
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: " . BASE_URL . "pages/gitas-quest-register.php");
    exit();
}
?>