<?php
// config/db.php

// The most important setting for an "unbreakable" website.
// This tells your website its home address.
// ** MAKE SURE THIS MATCHES YOUR BROWSER'S URL BAR EXACTLY **
// ** DONT FORGET THE TRAILING SLASH / **
// define('BASE_URL', 'http://localhost/iskcon-website/');
    define('BASE_URL', 'http://localhost/Frontend%201/iskcon-website/');

// Timezone difinition kolkata timezone
date_default_timezone_set('Asia/Kolkata');
// --- Database Connection ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'iskcon_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
echo mysqli_connect_error();
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
// create pdo instance for better error handling
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}


?>