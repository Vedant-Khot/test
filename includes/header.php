<?php
// We must start the session on every page to access the $_SESSION variable.
// This allows us to check if a user is logged in.
session_start();
// $_SESSION['error_message'] = ""; // Initialize error message to an empty string
echo "\n<!-- Session started successfully. -->\n";
// We need the BASE_URL constant, so we include the config file.
// Using require_once ensures it's included only once, even if other files also include it.
require_once __DIR__ . '/../config/db.php';
// Base URL is not defined in the config file, so we define it here.
if (!defined('BASE_URL')) {
    // Define the base URL of the website.
    
    // This is critical for linking assets and internal pages correctly.
    define('BASE_URL', 'http://localhost/Frontend%201/iskcon-website/');
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__.'/../assets/mail/PHPMailer.php';
require_once __DIR__.'/../assets/mail/SMTP.php';
require_once __DIR__.'/../assets/mail/Exception.php';
$mail = new PHPMailer(true);
// register.php

// Database credentials
$host = 'localhost';
$db   = 'iskcon_db';
$user = 'root'; // use your DB username
$pass = '';     // use your DB password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// if cookie is set, then change content to welcome message
if (isset($_COOKIE['user_email']) && isset($_COOKIE['user_name'])) {
    $welcomeMessage = "Welcome back, " . htmlspecialchars($_COOKIE['user_name']) . "!";
} else {
    $welcomeMessage = "Join the Gita Conquest and explore the wisdom of the Bhagavad Gita!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    // Collect form data
    $name         = $_POST['name'] ?? '';
    $email        = $_POST['email'] ?? '';
    $city         = $_POST['city'] ?? '';
    $state        = $_POST['state'] ?? '';
    // $country      = $_POST['country'] ?? '';
    $password     = $_POST['password'] ?? '';
    $source     = $_POST['source'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';
    $phone        = $_POST['phone'] ?? '';
    $gender       = $_POST['gender'] ?? '';
    $age          = $_POST['age'] ?? '';
    $college      = $_POST['college_name'] ?? '';
    $standard     = $_POST['standard'] ?? '';
    $iskconYears  = $_POST['iskconYears'] ?? '';
    $kcLevel      = $_POST['kcLevel'] ?? '';
    $terms        = $_POST['terms'] ?? '';
    $spiritual    = $_POST['spiritualPractices'] ?? [];
    // created at timestamp
    $createdAt    = date('Y-m-d H:i:s');
    if ($password !== $confirm_pass) {
        ?>
        <script>
            alert("Passwords do not match. Please try again.");
        </script>
        <?php
        //echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    // check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        ?>
        <script>
            alert("Email already exists. Please use a different email.");
        </script>
        <?php
        header('Location: '.BASE_URL.'pages/gitas-quest/index.php?error=email_exists');
        // echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $spiritualJson = json_encode($spiritual);

    // Insert into DB
    $stmt = $pdo->prepare("INSERT INTO registrations
        (name, email, city, state, password, phone, gender, age, college_name, standard, iskcon_years, spiritual_practices, kc_level,source)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");

    try {
        $stmt->execute([$name, $email, $city, $state, $hashedPassword, $phone, $gender, $age, $college, $standard, $iskconYears, $spiritualJson, $kcLevel,$source]);
    } catch (PDOException $e) {
        ?>
        <script>
            alert("Error saving data: ");
        </script>
        <?php
        //echo json_encode(['status' => 'error', 'message' => 'Error saving data: ' . $e->getMessage()]);
        exit;
    }

    // set cookeie for 30 days after registration
    setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), "/"); // 30 days expiration
    setcookie('user_name', $name, time() + (30 * 24 * 60 * 60), "/"); // 30 days expiration
    


    // // Send email to user
    // $subject = "Welcome to Gita ConQuest!";
    // $userMsg = "Dear $name,\n\nThank you for registering for Gita ConQuest! We're excited to have you on this spiritual journey.\n\nStay tuned for updates.\n\nHare Krishna!\nISKCON Team";
    // $headers = "From: noreply@yourdomain.com";
    // mail($email, $subject, $userMsg, $headers);

    // // Send notification to admin
    // $adminEmail = "admin@yourdomain.com"; // Replace with your email
    // $adminSubject = "New Gita ConQuest Registration";
    // $adminMsg = "New registration:\n\nName: $name\nEmail: $email\nCity: $city\nState: $state\nPhone: $phone\nGender: $gender\nKC Level: $kcLevel";
    // mail($adminEmail, $adminSubject, $adminMsg, $headers);
   $htmlBody = "
<html>
<head>
  <style>
    .container {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f4f4f4;
      border-radius: 10px;
      max-width: 600px;
      margin: auto;
    }
    .header {
      background: #e0c097;
      padding: 10px;
      text-align: center;
      font-size: 20px;
      font-weight: bold;
    }
    .content {
      padding: 20px;
      background: #fff;
    }
    .footer {
      text-align: center;
      font-size: 12px;
      color: #888;
    }
  </style>
</head>
<body>
  <div class='container'>
    <div class='header'> Welcome to Gita Conquest!</div>
    <div class='content'>
      <p>Dear $name,</p>
      <p>Thank you for registering for <strong>Gita Conquest</strong>! We're excited to have you join this journey of spiritual discovery.</p>
      <p>Stay tuned for updates, session details, and community support through our WhatsApp group!</p>
      <p><a href='https://chat.whatsapp.com/KvybmzEXA838yqZA1q5ask?mode=ac_t'>👉 Join WhatsApp Group</a></p>
      <p>Hare Krishna,<br>ISKCON Team</p>
    </div>
    <div class='footer'>
      This is an automated email. Please do not reply.
    </div>
  </div>
</body>
</html>";


try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // or your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'vedantkhot112@gmail.com'; // Your email
    $mail->Password   = 'tnpl kjpi bouf ounp';    // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    ?>
    <script>
        alert("Registration successful! A confirmation email has been sent to <?php echo $email; ?>.");
    </script>
    <?php


    // Recipients
    $mail->setFrom('vedantkhot112@gmail.com', 'ISKCON Gita Conquest');
    $mail->addAddress($email, $name);  // Send to user

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to Gita Conquest!';
    $mail->Body    = $htmlBody;

    $mail->send();
    
    header('Location: '.BASE_URL.'gitas-quest/pages/index.php?success=1');
    exit;
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}


    //echo json_encode(['status' => 'success']);
    
}
$value=0;
if(isset($_SESSION['user_id'])){
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM gq_final_exam_attempts WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result); 
    $status= $row["status"];
    echo $status;
    switch ($status) {
        case "mcq_completed":
            $value =1;
             break;
        case "upload":
            $value = 2;
            break;
        case "submitted":
            $value = 3;
            break;
        default:
        $value = 0;
        break;
    }
}      
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- The Page Title - can be made dynamic later on a per-page basis -->
    <title>ISKCON Community & Learning Platform</title>

    <!-- Professional Fonts: Playfair Display for headings, Poppins for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core Theme and Custom Stylesheets (using BASE_URL for correct paths) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/header.css">
    <!-- Lucide Icons Library -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<style>

.disabled{
    pointer-events: none;
    opacity: 0.6;

}

</style>


<header class="main-header bg-light border-bottom">
  <nav class="navbar navbar-expand-lg navbar-light container " style="margin: 0 10px ;max-width: 100vw !important;">
    
    <!-- Logo -->
    <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>" style="font-family: 'Playfair Display', serif;">
     <img style="width: 2vw;" src="<?= BASE_URL.'assets/img/iskcon_logo.png';?>" alt=" ISKCON Logo"> 
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible menu -->
    <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
      
      <!-- Navigation links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-4">
        <li class="nav-item">
          <!-- <a class="nav-link" href="<?php echo BASE_URL; ?>">Home</a> -->
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>pages/gitas-quest/">Gita's Quest</a>
        </li>
        <?php if (isset($_SESSION['user_id']) ){ ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>pages/gitas-quest/sessions.php">My Sessions</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>pages/gitas-quest/sessions.php">My Exam</a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL.'pages/gitas-quest/leaderboard.php';?>">LeaderBoard</a>
        </li>
        <li class="nav-item disabled">
          <a class="nav-link" href="<?php echo BASE_URL.'pages/gitas-quest/pdf.php';?>">E-book</a>
        </li>
        
          <li class="nav-item <?php if ($value = 0){?>disabled<?php } ?>">
          <a class="nav-link " href="<?php echo BASE_URL.'pages/gitas-quest/final_paper.php?type=mcq';?>">Exam Phase 1</a>
        </li>
      
        
          <li class="nav-item <?php if ($value != 1){?>disabled<?php } ?>">
          <a class="nav-link" href="<?php echo BASE_URL.'pages/gitas-quest/final_paper.php?type=text';?>">Exam Phase 2</a>
        </li>
        <?php } ?>
      </ul>

      <!-- Auth Buttons -->
      <div class="d-flex gap-2">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="<?php echo BASE_URL; ?>pages/profile/" class="btn btn-outline-secondary btn-sm">Profile</a>
          <a href="<?php echo BASE_URL; ?>pages/account/logout.php" class="btn btn-primary btn-sm">Logout</a>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>pages/account/login.php" class="btn btn-outline-secondary btn-sm">Login</a>
          <a onclick="showModal()" class="btn btn-primary btn-sm">Register</a>
        <?php endif; ?>
      </div>

    </div>
  </nav>
</header>


<!-- The <main> tag is opened here and will be closed in footer.php -->
<!-- This ensures all page content is wrapped correctly. -->
<main>