<?php
// pages/register.php
require '../../includes/header.php';
?>

<section class="section">
    <div class="container center" style="max-width: 550px;margin:10px auto;">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Create Your Account</h2>
                <p>Join our community to start your journey.</p>
            </div>

            <?php
            // This PHP block will display any error messages from the controller
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']); // Clear the message
            }
            ?>

            <form action="<?php echo BASE_URL; ?>controllers/auth.php" method="POST">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" minlength="8" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Sign Up</button>
            </form>

            <p class="auth-footer-text">
                Already have an account? 
                <a href="<?php echo BASE_URL; ?>pages/login.php">Login here</a>
            </p>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>