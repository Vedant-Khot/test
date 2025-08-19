<?php 
require_once '../config/db.php';
function check_if_user_exists($conn){
    
        // check if session is set
        // session_start();
    if (isset($_SESSION['user_id'])) {
                // if he is registered for the contest or not , we have to check in tthe gq_registrations table
            $query = "SELECT * FROM gq_registrations WHERE user_id = ? AND quest_year = ?";
            $stmt = mysqli_prepare($conn, $query);
            //Warning: Undefined variable $conn in C:\xampp\htdocs\Frontend 1\iskcon-website\config\db.php on line 31
            if (!$stmt) {
                die("Database query failed: " . mysqli_error($conn));
            }
            // Check if the user is registered for the current year
            // We assume the current year is the quest year, which is usually the case for contests
            // Bind the parameters
            $quest_year = date("Y"); // Current year

            mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $quest_year);
            // Execute the statement    
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            // Check if the user is already registered
            if (mysqli_num_rows($result) > 0) {
                // User is already registered, redirect to sessions page
                $_SESSION['gq_registered'] = true;
                // header("Location: " . BASE_URL . "pages/gitas-quest/sessions.php");
                // exit();
            } 
    }else {
        // User is not registered, set a session variable
        $_SESSION['gq_registered'] = false;
        // Optionally, you can redirect them to the registration page
        header("Location: " . BASE_URL . "pages/gitas-quest/gita-quest-register.php");
        exit(); 
        return false; // User is not registered
    }
    return false; // User is not registered
}