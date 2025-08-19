<?php
// pages/profile/congregation.php

require_once __DIR__ . '/../../includes/header.php';

// Security check
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}
?>

<section class="section">
    <div class="container">
        <div class="profile-header fade-in-up">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p class="profile-tagline">This is your Congregation Member dashboard.</p>
        </div>

        <div class="profile-content fade-in-up" style="margin-top: 4rem;">
            <h2>Your Engagement</h2>
            <p class="text-muted">In the future, your participation history, upcoming event registrations, and spiritual goals will be tracked and displayed here.</p>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>