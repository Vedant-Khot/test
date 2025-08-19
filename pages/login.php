<?php
// pages/login.php
require_once __DIR__ . '/../includes/header.php';
// This function checks if the user is registered for Gita's Quest and redirects them if they are not.
?>

<section class="section">
    <div class="container center" style="max-width: 550px;margin:10px auto;">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Log in to continue your journey.</p>
            </div>

            <?php
            // This PHP block will display messages like "Registration successful!" or "Invalid password."
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <form action="<?php echo BASE_URL; ?>controllers/auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>

            <p class="auth-footer-text">
                Don't have an account? 
                <a href="<?php echo BASE_URL; ?>pages/register.php">Sign up here</a>
            </p>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>