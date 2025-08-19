<?php
// pages/gita-quest-register.php
require '../../includes/header.php';
if (!isset($_SESSION['user_id'])) { 
    $_SESSION['error_message'] = "Please log in to access Gita's Quest .";
    header("Location: " . BASE_URL . "pages/login.php?v=gq");
    exit();
}

?>

<section class="section">
    <div class="container" style="max-width: 70vw;">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Gita's Quest Registration</h2>
                <span><p>Create your account and enroll in the contest all in one step!</p>
                <p>If completed graduation please ignore</p></span>
            </div>

            <?php
            // Display any error messages from the controller
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <form action="<?php echo BASE_URL; ?>controllers/gq_enrollment.php" method="POST">
                
                <div class="form-grid">
                    <!-- Personal Details -->
                    <!-- <div class="form-group span-2">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" class="form-control" name="gender" required>
                            <option value="">-- Select --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div> -->

                    <!-- Location & College Details -->
                   
                    <div class="form-group span-2">
                        <label for="college_name">College/School Name</label>
                        <input type="text" id="college_name" placeholder="eg. (New College ..)" name="college_name" required>
                    </div>
                    <div class="form-group span-2">
                        <label for="standard">Stream and year of study</label>
                        <input type="text" placeholder="eg. (11th science,First Year BE, ...)" id="standard" name="standard" required>
                    </div>
                    
                   <div class="form-group" style="display: flex; align-items: center;">
                        <input type="checkbox" name="terms" id="terms" style="width: 10%" required>
                        <label for="terms" >
                            I agree to the <a href="<?php echo BASE_URL; ?>pages/terms.php" target="_blank">Terms and Conditions</a>
                        </label>
                    </div>


                <button type="submit" name="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;"> Enroll</button>
            </form>
<!-- 
            <p class="auth-footer-text">
                Already have an account? <a href="<?php echo BASE_URL; ?>pages/login.php">Login here</a>
            </p> -->
        </div>
    </div>
</section>

<?php
require '../../includes/footer.php';
?>