<?php
// controllers/auth.php
session_start();
require '../config/db.php';
require 'functions.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Block 1: Handle Sign-Up --

    // Inside controllers/auth.php

    // Find your existing registration block and replace it with this one.



    // --- Block 2: Handle Gita Quest Registration ---


    // --- Block 2: Handle Login ---
    if (isset($_POST['action']) && $_POST['action'] == 'login') {

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $_SESSION['error_message'] = "Please enter email and password.";
            header("Location: " . BASE_URL . "pages/login.php");
            exit();
        }
        // check if user exists in the database
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);
        $sql = "SELECT id, name, email, password FROM registrations WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // if user exists, verify password
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                // $_SESSION['user_role'] = $user['role'];
                // create cookie for credentials
                setcookie('user_id', $user['id'], time() + (30*24*60*60),'/');
                // Redirect to the appropriate dashboard based on user role

                header("Location: " . BASE_URL . "pages/profile/");

                exit();
            }
        }

        // use join to fetch user profile data
        // $sql = "SELECT u.id, u.name, u.email, u.password_hash, u.role, p.age FROM users u JOIN user_profiles p ON u.id = p.user_id WHERE u.email = ? LIMIT 1";
        // $stmt = mysqli_prepare($conn, $sql);
        // mysqli_stmt_bind_param($stmt, "s", $email);
        // mysqli_stmt_execute($stmt);
        // $result = mysqli_stmt_get_result($stmt);    
        // if ($user = mysqli_fetch_assoc($result)) {
        //     if (password_verify($password, $user['password_hash'])) {
        //         session_regenerate_id(true);
        //         $_SESSION['user_id'] = $user['id'];
        //         $_SESSION['user_name'] = $user['name'];
        //         $_SESSION['user_age'] = $user['age'];
        // $_SESSION['user_role'] = $user['role'];
        //     if(check_if_user_exists($conn)) {
        //         // User is already registered for the Gita Quest, redirect to sessions page
        //         // debugging: set a session variable to indicate they are registered
        //         // session variable to indicate they are registered
        //         $_SESSION['gq_registered'] = true;
        //     }
        //     header("Location: " . BASE_URL . "pages/profile/");
        //     exit();
        // }
    }
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: " . BASE_URL . "pages/account/login.php");
    exit();
}
