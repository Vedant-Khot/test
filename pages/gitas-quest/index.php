<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
 // Include the mail sending logic
 use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../assets/mail/PHPMailer.php';
require '../../assets/mail/SMTP.php';
require '../../assets/mail/Exception.php';
require '../../config/db.php';
require '../../includes/header.php';
$mail = new PHPMailer(true);
// Data Arrays
$howItWorksSteps = [
    [
         
        "title" => "Zoom LIVE Guided Sessions ", 
        "description" => "Each chapter is taught in an engaging Zoom session that breaks down the Bhagavad Gita's wisdom in a simple way. After each session, reinforce your learning with a short post-test to check your understanding and earn points. Your scores help you climb the leaderboard and track your progress among fellow students.",
        "date"=>"(20th JUL to 4th AUG)",
        "side" => "left",
        "icon" => "video"
    ],
    [
        "date" => "(6th AUG)", 
        "title" => "Online MCQ Test ", 
        "description" => "On 6th August, take the objective multiple-choice test based on your learning so far.",
        "side" => "right",
        "icon" => "list-check"
    ],
    [
        "date" => "(6th AUG)", 
        "title" => "Online Subjective Test ", 
        "description" => "Also on 6th August, express your understanding in a written format through a creative subjective test.",
        "side" => "left",
        "icon" => "file-text"
    ],
    [
        "date" => "(10th Aug)",  
        "title" => "Offline Rapid Fire Round ", 
        "description" => "Attend the live in-person rapid fire quiz round on 10th August for shortlisted participants.",
        "side" => "right",
        "icon" => "alarm-clock"
    ],
    [
        "date" => "(16th Aug)", 
        "title" => "Janmashtami Felicitation ", 
        "description" => "Winners and top participants will be honored during the Janmashtami celebration on 16th August.",
        "side" => "left",
        "icon" => "award"
    ]
];

$benefits = [
    ["icon" => "award", "title" => "Spiritual Wisdom", "text" => "Gain deep insights into life's biggest questions through the timeless teachings of the Gita."],
    ["icon" => "users", "title" => "Community & Friendship", "text" => "Connect with a network of like-minded peers who share your spiritual interests."],
    ["icon" => "zap", "title" => "Personal Growth", "text" => "Develop discipline, focus, and a positive mindset to navigate life's challenges with clarity."]
];

$prizes = [
    ["rank" => "1st", "title" => "Grand Prize", "details" => ["Cash prize of ₹15,000", "Spiritual Retreat Trip", "Winner's Trophy & Certificate"]],
    ["rank" => "2nd", "title" => "Runner-Up", "details" => ["Cash prize of ₹10,000", "Premium Book Set", "Medal & Certificate"]],
    ["rank" => "3rd", "title" => "Third Place", "details" => ["Cash prize of ₹5,000", "ISKCON Merchandise", "Medal & Certificate"]]
];

$testimonials = [
    ["quote" => "Gita ConQuest was a turning point for me. It presented ancient wisdom in a way I could actually understand and apply. I made friends and learned so much about myself.", "name" => "Priya Sharma", "detail" => "Engineering Student"],
    ["quote" => "The competitive aspect of the leaderboard was surprisingly motivating! It pushed me to study more deeply than I ever would have on my own. Highly recommended.", "name" => "Rahul Verma", "detail" => "Previous Participant"],
    ["quote" => "An incredible initiative. It connected me with our culture and gave me a community. The sessions were excellent and the coordinators were always helpful.", "name" => "Anjali Singh", "detail" => "Medical Student"]
];
// if SESSION is set, then change content to welcome message

// register.php

// Database credentials
// $host = 'localhost';
// $db   = 'iskcon_db';
// $user = 'root'; // use your DB username
// $pass = '';     // use your DB password
// $charset = 'utf8mb4';

// $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// $options = [
//     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//     PDO::ATTR_EMULATE_PREPARES   => false,
// ];

// try {
//     $pdo = new PDO($dsn, $user, $pass, $options);
// } catch (PDOException $e) {
//     echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
//     exit;
// }

// // if cookie is set, then change content to welcome message
// if (isset($_COOKIE['user_email']) && isset($_COOKIE['user_name'])) {
//     $welcomeMessage = "Welcome back, " . htmlspecialchars($_COOKIE['user_name']) . "!";
// } else {
//     $welcomeMessage = "Join the Gita Conquest and explore the wisdom of the Bhagavad Gita!";
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Collect form data
//     $name         = $_POST['name'] ?? '';
//     $email        = $_POST['email'] ?? '';
//     $city         = $_POST['city'] ?? '';
//     $state        = $_POST['state'] ?? '';
//     // $country      = $_POST['country'] ?? '';
//     $password     = $_POST['password'] ?? '';
//     $source     = $_POST['source'] ?? '';
//     $confirm_pass = $_POST['confirm_password'] ?? '';
//     $phone        = $_POST['phone'] ?? '';
//     $gender       = $_POST['gender'] ?? '';
//     $age          = $_POST['age'] ?? '';
//     $college      = $_POST['college_name'] ?? '';
//     $standard     = $_POST['standard'] ?? '';
//     $iskconYears  = $_POST['iskconYears'] ?? '';
//     $kcLevel      = $_POST['kcLevel'] ?? '';
//     $terms        = $_POST['terms'] ?? '';
//     $spiritual    = $_POST['spiritualPractices'] ?? [];
//     // created at timestamp
//     $createdAt    = date('Y-m-d H:i:s');
//     if ($password !== $confirm_pass) {
//         ?>
        <script>
//             alert("Passwords do not match. Please try again.");
        </script>
         <?php
//         //echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
//         exit;
//     }

//     // check if email already exists
//     $stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE email = ?");
//     $stmt->execute([$email]);
//     if ($stmt->fetchColumn() > 0) {
//         ?>
         <script>
//             alert("Email already exists. Please use a different email.");
//         </script>
       <?php
//         header('Location: gita_conquest_php.php?error=email_exists');
//         // echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
//         exit;
//     }

//     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
//     $spiritualJson = json_encode($spiritual);

//     // Insert into DB
//     $stmt = $pdo->prepare("INSERT INTO registrations
//         (name, email, city, state, password, phone, gender, age, college_name, standard, iskcon_years, spiritual_practices, kc_level,source)
//         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");

//     try {
//         $stmt->execute([$name, $email, $city, $state, $hashedPassword, $phone, $gender, $age, $college, $standard, $iskconYears, $spiritualJson, $kcLevel,$source]);
//     } catch (PDOException $e) {
//         ?>
         <script>
//             alert("Error saving data: ");
//         </script>
         <?php
//         //echo json_encode(['status' => 'error', 'message' => 'Error saving data: ' . $e->getMessage()]);
//         exit;
//     }

//     // set cookeie for 30 days after registration
//     setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), "/"); // 30 days expiration
//     setcookie('user_name', $name, time() + (30 * 24 * 60 * 60), "/"); // 30 days expiration
    


//     // // Send email to user
//     // $subject = "Welcome to Gita ConQuest!";
//     // $userMsg = "Dear $name,\n\nThank you for registering for Gita ConQuest! We're excited to have you on this spiritual journey.\n\nStay tuned for updates.\n\nHare Krishna!\nISKCON Team";
//     // $headers = "From: noreply@yourdomain.com";
//     // mail($email, $subject, $userMsg, $headers);

//     // // Send notification to admin
//     // $adminEmail = "admin@yourdomain.com"; // Replace with your email
//     // $adminSubject = "New Gita ConQuest Registration";
//     // $adminMsg = "New registration:\n\nName: $name\nEmail: $email\nCity: $city\nState: $state\nPhone: $phone\nGender: $gender\nKC Level: $kcLevel";
//     // mail($adminEmail, $adminSubject, $adminMsg, $headers);
//    $htmlBody = "
// <html>
// <head>
//   <style>
//     .container {
//       font-family: Arial, sans-serif;
//       padding: 20px;
//       background: #f4f4f4;
//       border-radius: 10px;
//       max-width: 600px;
//       margin: auto;
//     }
//     .header {
//       background: #e0c097;
//       padding: 10px;
//       text-align: center;
//       font-size: 20px;
//       font-weight: bold;
//     }
//     .content {
//       padding: 20px;
//       background: #fff;
//     }
//     .footer {
//       text-align: center;
//       font-size: 12px;
//       color: #888;
//     }
//   </style>
// </head>
// <body>
//   <div class='container'>
//     <div class='header'> Welcome to Gita Conquest!</div>
//     <div class='content'>
//       <p>Dear $name,</p>
//       <p>Thank you for registering for <strong>Gita Conquest</strong>! We're excited to have you join this journey of spiritual discovery.</p>
//       <p>Stay tuned for updates, session details, and community support through our WhatsApp group!</p>
//       <p><a href='https://chat.whatsapp.com/KvybmzEXA838yqZA1q5ask?mode=ac_t'>👉 Join WhatsApp Group</a></p>
//       <p>Hare Krishna,<br>ISKCON Team</p>
//     </div>
//     <div class='footer'>
//       This is an automated email. Please do not reply.
//     </div>
//   </div>
// </body>
// </html>";


// try {
//     // Server settings
//     $mail->isSMTP();
//     $mail->Host       = 'smtp.gmail.com'; // or your SMTP server
//     $mail->SMTPAuth   = true;
//     $mail->Username   = 'vedantkhot112@gmail.com'; // Your email
//     $mail->Password   = 'tnpl kjpi bouf ounp';    // App password
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//     $mail->Port       = 587;
    ?>
     <script>
//         alert("Registration successful! A confirmation email has been sent to <?php echo $email; ?>.");
    </script>
     <?php


//     // Recipients
//     $mail->setFrom('vedantkhot112@gmail.com', 'ISKCON Gita Conquest');
//     $mail->addAddress($email, $name);  // Send to user

//     // Content
//     $mail->isHTML(true);
//     $mail->Subject = 'Welcome to Gita Conquest!';
//     $mail->Body    = $htmlBody;

//     $mail->send();
    
//     header('Location: gita_conquest_php.php?success=1');
//     exit;
// } catch (Exception $e) {
//     echo "Email could not be sent. Error: {$mail->ErrorInfo}";
// }


//     //echo json_encode(['status' => 'success']);
    
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gita ConQuest - Janmashtami Special</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
   
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section section">
        <div class="container">
            <div class="hero-content fade-in-up">
                <img src="<?php echo BASE_URL;?>/assets/img/iskcon_logo.png" style="width:18vw" alt="">
                <br>
                <br>
                <div class="event-tagline floating">🎉 Janmashtami Special</div>
                <p>ISKCON Hubballi Presents</p>
                <h1 class="glow-on-hover c"><span><img src="<?php echo BASE_URL;?>/assets/img/2197d0fd-1f1f-4ca9-9858-c127367ca2be.jpg" style="width:8vw" alt=""> </span>Gita ConQuest</h1>
                <?php // if cookie is set , show welcome message should be something that he is now a part of the contest he is already registered and joined contest 
                if (isset($_SESSION['user_name'])): 
                    $userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
                ?>
                <p class="hero-subtitle"><strong>🎉 Welcome!</strong> , <?php echo $userName; ?>! You’ve successfully registered for <strong>Gita Conquest</strong>. <br> </p>
                
                 <a href="https://chat.whatsapp.com/KvybmzEXA838yqZA1q5ask?mode=ac_ts" class="whatsapp-btn floating-btn" target="_blank">
    💬 Join WhatsApp Group
</a>
                
                <p class="hero-subtitle">An immersive journey through the timeless wisdom of the Bhagavad Gita, designed for the modern seeker ready to transform their life.</p>
                <?php else: ?>
                <p class="hero-subtitle">An immersive journey through the timeless wisdom of the Bhagavad Gita, designed for the modern seeker ready to transform their life.</p>
                <!--<div style="text-align:center;width:100%;">-->
                <!--<p class="tag">Registrations Closing Soon!</p>-->
                <!--<p class="tag endless-gradient">Hurry Up!</p>-->
                <!--<p class="tag endless-breathe">🗓️Last Registration Date 19th JUL</p>-->
                </div>
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary" onclick="showModal()">🚀 Register Now</a>
                    <a href="#how" class="btn btn-secondary">📖 Learn More</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<section class="gita-section" style="display: flex; flex-wrap: wrap; align-items: center; padding: 50px;">
  
  <!-- Left Column: Image -->
  <div style="flex: 1; padding: 20px; text-align: center;">
    <img src="<?php echo BASE_URL;?>/assets/img/2a3ac95f-c48e-417a-ba45-81b9aa890465.jpg" alt="Gita Conquest" style="width: 100%; max-width: 450px; border-radius: 12px;">
  </div>

  <!-- Right Column: Content -->
  <div style="flex: 1; padding: 20px;">
    <h2 style="font-size: 30px; color: #4e2603; text-align: center; margin-bottom: 20px;">📖 What is Gita Conquest?</h2>
    <p style="font-size: 18px; line-height: 1.8; color: #333;">
      <strong>Gita Conquest</strong> is a dynamic spiritual learning initiative for youth, based on the timeless teachings of the <em>Bhagavad Gita</em>.
      Through video sessions, tests, and interactive events, you’ll explore deep wisdom, compete in quizzes, and grow with a like-minded community.
    </p>
    <p style="font-size: 17px; color: #5e4215;">
      The journey includes online chapters, post-tests, leaderboard challenges, and a final live event on <strong>Janmashtami</strong>.
    </p>
    
    <a href="#how" style="display: inline-block; margin-top: 20px; padding: 12px 22px; background-color: #e08e0b; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">
      🔍 See How It Works
    </a>
  </div>
<!-- <div class="notice-bar">
  <p>📢 Hurry Up! Registration closes on <strong>19th JUL</strong></p>
  <a  class="register-btn" onclick="showModal()">Register Now</a>
</div> -->

</section>
    <!-- Timeline Section - How It Works -->
    <section id="how" class="timeline-section section">
        <div class="container">
            <div class="section-intro fade-in-up">
                <h2>How It Works</h2>
                <p>Follow these simple steps to embark on your spiritual journey and compete for amazing prizes.</p>
            </div>
            <div class="timeline-container">
                <?php foreach ($howItWorksSteps as $index => $step): ?>
                <div class="timeline-item <?php echo $step['side']; ?> fade-in-up">
                    <div class="timeline-content">
                        <div class="timeline-icon-wrapper floating">
                            <i data-lucide="<?php echo $step['icon']; ?>"></i>
                        </div>
                        <span class="date"><?php echo $step['date']; ?></span>
                        <h3><?php echo $step['title']; ?></h3>
                        <p><?php echo $step['description']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
   <section class="section" >
    <div class="container " style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
        <div class="section-intro fade-in-up">
            <h2>Reference Book</h2>
            <p>"Embark on the Gita Conquest – discover timeless wisdom for modern life."</p>
        </div>
        <iframe src="https://drive.google.com/file/d/1B-TOEAzxyVx5cylgnFXnf-W-iyticQOA/preview" style="width: 80vw; height:90vh" allow="autoplay"></iframe>

    </div>


   </section>

 <?php
    if (!isset($_SESSION['user_name'])){?>
    <!-- CTA Section -->
    <section class="cta-section section">
        <div class="container">
            <div class="cta-content fade-in-up">
                <h2>Ready to Begin Your Gita Journey?</h2>
                <p>Join thousands of youth who are discovering clarity, courage, and connection through the Bhagavad Gita.</p>
                <a href="#" class="btn cta-btn" onclick="showModal()"> Register Now</a>
            </div>
        </div>
    </section>
    <?php } ?>
    <!-- Benefits Section -->
    <section class="section" style="background-color: var(--color-bg-alt);">
        <div class="container">
            <div class="section-intro fade-in-up">
                <h2>Why Join Gita ConQuest?</h2>
                <p>This is more than just a contest; it's a transformative journey that will enrich your spiritual understanding and personal growth.</p>
            </div>
            <div class="grid">
                <?php foreach ($benefits as $benefit): ?>
                <div class="content-card benefit-card fade-in-up glow-on-hover">
                    <div class="benefit-icon">
                        <i data-lucide="<?php echo $benefit['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $benefit['title']; ?></h3>
                    <p><?php echo $benefit['text']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Prizes Section -->
    <!-- <section class="section">
        <div class="container">
            <div class="section-intro fade-in-up">
                <h2>Prizes & Recognition</h2>
                <p>Excellence deserves extraordinary rewards. Top performers will be honored with exciting prizes and recognition.</p>
            </div>
            <div class="grid">
                <?php foreach ($prizes as $prize): ?>
                <div class="content-card prize-card fade-in-up glow-on-hover">
                    <div class="prize-header">
                        <span class="prize-rank"><?php echo $prize['rank']; ?> Place</span>
                        <h3><?php echo $prize['title']; ?></h3>
                    </div>
                    <ul class="prize-details">
                        <?php foreach ($prize['details'] as $detail): ?>
                        <li><i data-lucide="gift"></i><?php echo $detail; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section> -->

    <!-- Testimonials Section -->
    <!-- <section class="section" style="background-color: var(--color-bg-alt);">
        <div class="container">
            <div class="section-intro fade-in-up">
                <h2>What Participants Say</h2>
                <p>Hear from our previous participants about their transformative journey with Gita ConQuest.</p>
            </div>
            <div class="grid">
                <?php foreach ($testimonials as $testimonial): ?>
                                    <div class="content-card testimonial-card fade-in-up glow-on-hover">
                    <i data-lucide="quote" class="testimonial-quote-icon"></i>
                    <blockquote>
                        “<?php echo $testimonial['quote']; ?>”
                    </blockquote>
                    <cite>
                        <strong><?php echo $testimonial['name']; ?></strong>
                        <span><?php echo $testimonial['detail']; ?></span>
                    </cite>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section> -->

    <?php
    if (!isset($_SESSION['user_name'])){?>
    <!-- CTA Section -->
    <section class="cta-section section">
        <div class="container">
            <div class="cta-content fade-in-up">
                <h2>Ready to Begin Your Gita Journey?</h2>
                <p>Join thousands of youth who are discovering clarity, courage, and connection through the Bhagavad Gita.</p>
                <a href="#" class="btn cta-btn" onclick="showModal()"> Register Now</a>
            </div>
        </div>
    </section>
    <?php } ?>
    <!-- Modal Overlay -->
     <?php require '../../includes/footer.php'; ?>
</body>
</html>
