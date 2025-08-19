<?php
// We will need the config file for the BASE_URL and for the future database connection
require '../../config/db.php';
// The header contains the top part of the HTML, including the <head>
include '../../includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Log in to access your spiritual journey and community content.</p>
            </div>
            <?php
            // This block will display feedback messages from auth.php  
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']); // Clear message after displaying
            }
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>
            <!-- The form will submit to our auth controller -->
                


            <!-- The action will point to our future controller file -->
            <form action="<?php echo BASE_URL; ?>controllers/auth.php" method="POST">
                
                <!-- Email Input -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <!-- Hidden field to specify the action -->
                <input type="hidden" name="action" value="login">

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary form-submit-btn">Login</button>
            </form>

            <div class="form-footer-text">
                <a href="#" class="link-underline" style="margin-right: 1.5rem;">Forgot Password?</a>
                <p style="margin-top: 1rem; display: inline;">
                    Don't have an account? <a href="<?php echo BASE_URL; ?>pages/account/register.php" class="link-underline">Register Here</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php
// The footer contains the bottom part of the HTML
include '../../includes/footer.php';
?>