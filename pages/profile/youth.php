<?php
// pages/profile/youth.php

// The header contains the session start and pulls in the config.
require_once __DIR__ . '/../../includes/header.php';

// Security: If a guest tries to access this page directly, kick them out.
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}
?>

<section class="section">
    <div class="container">
        <div class="profile-header fade-in-up">
            <!-- Greet the user by name, using the session variable -->
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p class="profile-tagline">This is your Youth Profile dashboard. Your spiritual journey starts here.</p>
        </div>

        <div class="profile-content fade-in-up" style="margin-top: 4rem;">
            <h2>Your Progress</h2>
            <p class="text-muted">In the future, your enrolled courses, Gita's Quest progress, and leaderboard rank will be displayed here.</p>
            <!-- We will add dynamic content cards here later -->
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>