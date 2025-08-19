<?php
require_once 'config/db.php';
include 'includes/header.php';

// Data for our new timeline
$events = [
    ["date" => "Discover", "title" => "Structured Courses", "description" => "Begin your journey with foundational courses on Bhagavad Gita.", "side" => "left"],
    ["date" => "Engage", "title" => "Interactive Programs", "description" => "Participate in Gita's Quest, designed for joyful and deep learning.", "side" => "right"],
    ["date" => "Connect", "title" => "Community Fellowship", "description" => "Join our Sunday Feasts, festivals, and congregation activities.", "side" => "left"],
    ["date" => "Grow", "title" => "Advanced Studies", "description" => "Deepen your understanding with advanced scriptural studies and discussions.", "side" => "right"],
    ["date" => "Serve", "title" => "Seva Opportunities", "description" => "Experience the bliss of selfless service by volunteering in temple activities.", "side" => "left"],
];
?>

<section class="hero-section section fade-in-up">
    <div class="container">
        <h1>Serenity, Wisdom, and Community</h1>
        <p>Welcome to a sacred space for spiritual learning and growth. Explore the profound teachings of Krishna consciousness and find your path to inner harmony.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div>
            <a href="#" class="btn-hero">Start Your Journey</a>
        </div>
        <?php endif; ?>
        
    </div>
</section>

<section class="timeline-section section">
    <div class="container">
        <div class="section-intro fade-in-up center">
            <h2>Our Path of Devotion</h2>
            <br>
            <p>         From the first step of discovery to the bliss of selfless service, we provide a structured and supportive path for your spiritual evolution.</p>
        </div>

        <div class="timeline-container">
            <?php foreach ($events as $event): ?>
                <div class="timeline-item <?= htmlspecialchars($event['side']) ?> fade-in-up">
                    <div class="timeline-content">
                        <span class="date"><?= htmlspecialchars($event['date']) ?></span>
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p><?= htmlspecialchars($event['description']) ?></p>
                        <a href="#" class="link-underline">Learn More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>