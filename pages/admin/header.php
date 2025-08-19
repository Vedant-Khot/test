
<?php
// pages/profile/edit.php
// Ensure the BASE_URL constant is defined
require_once __DIR__ . '/../../config/db.php';
session_start();
// echo $_SESSION['user_id_admin'] ?? '4';
// Security check
if (!isset($_SESSION['user_id_admin'])) {
    header("Location: " . BASE_URL . "pages/admin/login.php");
    exit();
}
// check if they have completed phase 1 final_exam_attempts


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
    <!-- <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css"> -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
    
</head>

<body>
    <div class="admin-container">
         <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h2>ISKCON Hubbali</h2>
                <p>Management Dashboard</p>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="<?= BASE_URL;?>pages/admin/dashboard.php" class="nav-link active">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                        </svg>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL;?>pages/admin/courses_manager.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                        Sessions
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= BASE_URL;?>pages/admin/questions_manager.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z" />
                        </svg>
                        Questions
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?=BASE_URL;?>pages/admin/reports.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
                        </svg>
                        Scores
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?=BASE_URL;?>pages/admin/view_submission.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        Submissions
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                        Settings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">☰</button>
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Search..." onkeyup="searchTable()" id="searchInput">
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">JD</div>
                        <span>John Doe</span>
                    </div>
                </div>
            </header>
  <!-- Dashboard Content -->
            <main class="dashboard">
                <!-- Stats Cards -->
                <!-- Stats -->