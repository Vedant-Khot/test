<?php
require_once __DIR__ . '/../../includes/header.php';

$token = $_GET['token'] ?? '';
$stmt  = $conn->prepare("SELECT id FROM registrations WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->bind_param('s', $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
// print_r($user); // Debugging line to check user data
if (!$user) { die('Invalid or expired token.'); }
?>
<section class="section container center">
  <div class="glass-card card card-body" style="margin: 10px;">
    <h2>New Password</h2>
    <form method="post" action="update_password.php">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input class="glow-input form-control" type="password" name="password" placeholder="New password" required minlength="6">
      <br>
      <button class="btn btn-primary">Set Password</button>
    </form>
  </div>
</section>