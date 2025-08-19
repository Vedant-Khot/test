<?php
require_once __DIR__ . '/../../includes/header.php';
// require_once __DIR__ . '/../../assets/mail/PHPMailer.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../assets/mail/PHPMailer.php';
require_once __DIR__ . '/../../assets/mail/SMTP.php';
require_once __DIR__ . '/../../assets/mail/Exception.php';
date_default_timezone_set('Asia/Kolkata');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $stmt  = $conn->prepare("SELECT id FROM registrations WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        // generate 64-char token
        $token     = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $upd = $conn->prepare("UPDATE registrations SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $upd->bind_param('sss', $token, $expires, $email);
        $upd->execute();

        // **demo** – echo link instead of sending mail
        $link = BASE_URL . "pages/account/reset_password.php?token=$token";
        $mail = new PHPMailer(true);

        try {
            // ---------- SMTP setup ----------
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'vedantkhot112@gmail.com';
            $mail->Password   = 'tnpl kjpi bouf ounp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ---------- Recipients ----------
            $mail->setFrom('you@domain.com', 'ISKCON Site');
            $mail->addAddress($email);

            // ---------- Message ----------
            $resetUrl = BASE_URL . 'pages/account/reset_password.php?token=' . $token;

            $mail->isHTML(true);                   // Enable HTML
            $mail->Subject = 'Password Reset';
            $mail->CharSet = 'UTF-8';

            // --- HTML body ---
           $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Your Password - ISKCON</title>
  <style>
    /* Base Styles */
    * { box-sizing: border-box; }
    body {
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: linear-gradient(135deg, #fef3e2 0%, #fde68a 50%, #fed7aa 100%);
      min-height: 100vh;
      line-height: 1.6;
    }
    
    /* Email Container */
    .email-wrapper {
      width: 100%;
      background: linear-gradient(135deg, #fef3e2 0%, #fde68a 50%, #fed7aa 100%);
      padding: 20px 0;
    }
    
    .email-container {
      max-width: 640px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    /* Header Section */
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .logo-section {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 20px;
      padding: 25px;
      margin-bottom: 20px;
      box-shadow: 0 8px 25px rgba(180, 83, 9, 0.1);
      backdrop-filter: blur(10px);
    }
    
    .om-symbol {
      font-size: 48px;
      color: #b45309;
      margin-bottom: 10px;
      text-shadow: 2px 2px 4px rgba(180, 83, 9, 0.2);
    }
    
    .site-name {
      font-size: 24px;
      font-weight: 700;
      color: #92400e;
      margin: 0;
      letter-spacing: 1px;
    }
    
    /* Main Card */
    .main-card {
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(254, 247, 224, 0.95) 100%);
      border: 2px solid rgba(245, 215, 163, 0.6);
      border-radius: 20px;
      box-shadow: 
        0 20px 40px rgba(224, 146, 54, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
      padding: 50px;
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }
    
    .main-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #f59e0b, #d97706, #b45309);
    }
    
    /* Typography */
    .greeting {
      color: #b45309;
      font-size: 32px;
      font-weight: 700;
      margin: 0 0 25px;
      text-align: center;
      text-shadow: 1px 1px 2px rgba(180, 83, 9, 0.1);
    }
    
    .intro-text {
      color: #78350f;
      font-size: 18px;
      margin: 0 0 25px;
      text-align: center;
    }
    
    .main-text {
      color: #78350f;
      font-size: 16px;
      margin: 0 0 25px;
    }
    
    .highlight {
      background: linear-gradient(120deg, rgba(251, 191, 36, 0.3) 0%, rgba(245, 158, 11, 0.3) 100%);
      padding: 2px 6px;
      border-radius: 4px;
      font-weight: 600;
    }
    
    /* Button Styling */
    .button-container {
      text-align: center;
      margin: 35px 0;
    }
    
    .reset-button {
      display: inline-block;
      background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
      color: #ffffff;
      text-decoration: none;
      padding: 18px 40px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 16px;
      letter-spacing: 0.5px;
      box-shadow: 
        0 8px 20px rgba(234, 88, 12, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
   
    .reset-button:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      transform: translateY(-2px);
      box-shadow: 
        0 12px 25px rgba(234, 88, 12, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    /* Security Info Box */
    .security-info {
      background: rgba(254, 243, 199, 0.8);
      border: 1px solid #fbbf24;
      border-radius: 12px;
      padding: 20px;
      margin: 30px 0;
      border-left: 4px solid #f59e0b;
    }
    
    .security-title {
      color: #92400e;
      font-weight: 600;
      font-size: 16px;
      margin: 0 0 10px;
      display: flex;
      align-items: center;
    }
    
    .security-icon {
      margin-right: 8px;
      font-size: 18px;
    }
    
    .security-text {
      color: #78350f;
      font-size: 14px;
      margin: 0;
    }
    
    /* Timer Display */
    .timer-display {
      background: linear-gradient(135deg, #fef3e2 0%, #fed7aa 100%);
      border: 2px solid #f59e0b;
      border-radius: 50px;
      padding: 12px 24px;
      display: inline-block;
      font-weight: 700;
      color: #92400e;
      font-size: 14px;
      margin: 0 0 20px;
    }
    
    /* Footer */
    .footer {
      text-align: center;
      margin-top: 40px;
      padding: 25px;
      background: rgba(255, 255, 255, 0.7);
      border-radius: 15px;
      backdrop-filter: blur(5px);
    }
    
    .footer-text {
      color: #a16207;
      font-size: 14px;
      margin: 0 0 10px;
    }
    
    .blessing {
      color: #92400e;
      font-style: italic;
      font-size: 13px;
      margin: 0;
    }
    
    /* Decorative Elements */
    .lotus-divider {
      text-align: center;
      margin: 30px 0;
      font-size: 24px;
      color: #f59e0b;
      opacity: 0.7;
    }
    
    /* Mobile Responsiveness */
    @media only screen and (max-width: 600px) {
      .email-container { padding: 0 15px; }
      .main-card { 
        padding: 30px 25px;
        border-radius: 15px;
      }
      .greeting { 
        font-size: 26px;
        margin-bottom: 20px;
      }
      .intro-text { font-size: 16px; }
      .main-text { font-size: 15px; }
      .reset-button { 
        padding: 16px 30px;
        font-size: 15px;
      }
      .om-symbol { font-size: 36px; }
      .site-name { font-size: 20px; }
      .logo-section { padding: 20px; }
    }
    
    @media only screen and (max-width: 480px) {
      .main-card { padding: 25px 20px; }
      .greeting { font-size: 24px; }
      .reset-button { 
        padding: 14px 25px;
        font-size: 14px;
      }
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
      .main-card {
        background: linear-gradient(145deg, rgba(41, 37, 36, 0.95) 0%, rgba(68, 64, 60, 0.95) 100%);
        border-color: rgba(180, 83, 9, 0.3);
      }
      .main-text, .intro-text { color: #fbbf24; }
      .greeting { color: #fbbf24; }
    }
  </style>
</head>
<body>
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="width: 100%; background: linear-gradient(135deg, #fef3e2 0%, #fde68a 50%, #fed7aa 100%);">
    <tr>
      <td>
        <div class="email-wrapper">
          <div class="email-container">
            
            <!-- Header Section -->
            <div class="header">
              <div class="logo-section">
                <!-- <div class="om-symbol"></div> -->
                <h2 class="site-name">ISKCON</h2>
              </div>
            </div>
            
            <!-- Main Content Card -->
            <div class="main-card">
              <h1 class="greeting">Hare Krishna! 🙏</h1>
              
              <p class="intro-text">
                Peace and blessings upon you, devotee
              </p>
              
              <p class="main-text">
                You have requested to reset your password for your sacred account on <span class="highlight">ISKCON Portal</span>. We're here to help you regain access to your spiritual community.
              </p>
              
              <div class="security-info">
                <div class="security-title">
                  <span class="security-icon">🔒</span>
                  Security Notice
                </div>
                <p class="security-text">
                  This password reset link is valid for <span class="timer-display">⏰ 60 minutes only</span> to ensure your account's security.
                </p>
              </div>
              
              <p class="main-text">
                Click the sacred button below to create your new password and continue your spiritual journey with us:
              </p>
              
              <div class="button-container">
                <a href="{$resetUrl}" class="reset-button">
                  🔑 Reset My Password
                </a>
              </div>
              
              <div class="lotus-divider">🪷 ✨ 🪷</div>
              
              <p class="main-text">
                If you did not request this password reset, please disregard this email. Your account remains secure and unchanged. Consider this a gentle reminder to keep your spiritual practice and digital security both strong.
              </p>
              
              <div class="security-info">
                <div class="security-title">
                  <span class="security-icon">💡</span>
                  Helpful Tip
                </div>
                <p class="security-text">
                  For enhanced security, consider using a strong password that combines letters, numbers, and symbols - just as Krishna's teachings combine wisdom, devotion, and action.
                </p>
              </div>
              
            </div>
            
            <!-- Footer -->
            <div class="footer">
              <p class="footer-text">
                With divine love and service<br>
                <strong>ISKCON Portal Team</strong> © 2025
              </p>
              <p class="blessing">
                "Sarve bhavantu sukhinah, sarve santu niramayah" <br>
                <em>May all beings be happy, may all beings be healthy</em>
              </p>
            </div>
            
          </div>
        </div>
      </td>
    </tr>
  </table>
</body>
</html>
HTML; // --- Plain-text fallback ---
            $mail->AltBody = "Hello,\n\n"
                . "You requested a password reset for your account on ISKCON Site.\n\n"
                . "Click the link below to choose a new password (expires in 30 minutes):\n\n"
                . $resetUrl . "\n\n"
                . "If you didn’t make this request, simply ignore this email.\n\n"
                . "ISKCON Site";

            $mail->send();

            $_SESSION['success_message'] = 'Password reset link sent to your email.';
            header('Location: ' . BASE_URL . 'pages/account/forgot_password.php?ok');
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        }
    } else {
        echo "<div class='glass-card'><p>Email not found.</p></div>";
    }
}
?>
<section class="section container center">
    <div class="glass-card card card-body" style="margin: 10px;">
        <h2>Forgot Password</h2>
        <br>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars(string: $_SESSION['success_message']) ?>
            </div>
            <?php //unset($_SESSION['success_message']); 
            ?>
        <?php endif; ?>
        <form method="post">
            <input class="form-control" type="email" name="email" placeholder="Email address" required>
            <br>
            <button class="btn btn-primary">Send Reset Link</button>
        </form>
    </div>
</section>