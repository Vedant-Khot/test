// logout code snippet
<?php
// pages/account/logout.php
require_once '../../config/db.php';
session_start();
// Destroy the session to log out the user
session_destroy();
// Redirect to the home page or login page
header("Location: " . BASE_URL . "pages/account/login.php");
exit();
?>