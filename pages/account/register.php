<?php
// Using __DIR__ makes the pathing more robust. It's best practice.
require_once '../../includes/header.php';
?>

<section class="section" >
    <div class="container">
        <div class="form-wrapper auth-card" style="max-width: 70vw; margin: 10px auto;">
            <div class="section-intro fade-in-up">
                <h2>Create Your Account</h2>
                <p>Join our community to begin your spiritual journey with us.</p>
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

            <form action="<?php echo BASE_URL; ?>controllers/auth.php" method="POST" class="auth-form fade-in-up">
                <!-- Hidden input to specify the action -->
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State / Province</label>
                        <input type="text" id="state" name="state" required>
                    </div>
                     <div class="form-group span-2">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" required>
                    </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                </div>

                <!-- ADDED: Confirm Password field for security and usability -->
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number (Optional)</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                </div>
                <!-- // add a feild with select dropdown for gender  -->
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select name="gender" class="form-control" id="gender">
                        <option>Select Gender</option>
                        <option>Male</option>
                        <option>female</option>
                    </select>
                <div class="form-group">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" id="age" name="age" class="form-control" required>
                </div>
                  
                    <div class="form-group span-2">
                        <label for="college_name">College/School Name</label>
                        <input type="text" id="college_name" placeholder="eg. (New College ..)" name="college_name" required>
                    </div>
                    <div class="form-group span-2">
                        <label for="standard">Stream and year of study</label>
                        <input type="text" placeholder="eg. (11th science,First Year BE, ...)" id="standard" name="standard" required>
                    </div>
                    <div class="mb-3 form-group">
                    <label for="iskconYears" class="form-label">🕉️ How long have you been in touch with ISKCON?</label>
                    <input type="text" class="form-control" id="iskconYears" name="iskconYears" placeholder="e.g., 6 months, 2 years">
                    </div>

                    <div class="mb-3 ">
                    <label class="form-label">🧘 Are you doing any spiritual practices?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="chanting" name="spiritualPractices[]" value="Chanting">
                        <label class="form-check-label" for="chanting">Chanting</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="reading" name="spiritualPractices[]" value="Reading Gita">
                        <label class="form-check-label" for="reading">Reading Gita</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="templeVisit" name="spiritualPractices[]" value="Temple Visit">
                        <label class="form-check-label" for="templeVisit">Temple Visit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="otherPractice" name="spiritualPractices[]" value="Other">
                        <label class="form-check-label" for="otherPractice">Other</label>
                    </div>
                    </div>

                    <div class="mb-3 form-group">
                    <label for="kcLevel" class="form-label">🌿 Your Connection to Krishna Consciousness</label>
                    <select class="form-select" id="kcLevel" name="kcLevel">
                        <option selected disabled>Select your connection</option>
                        <option value="New">New</option>
                        <option value="Occasional">Occasional</option>
                        <option value="Regular">Regular</option>
                    </select>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center;">
                                            <input type="checkbox" name="terms" id="terms" style="width: 10%" required>
                                            <label for="terms" >
                                                I agree to the <a href="<?php echo BASE_URL; ?>pages/terms.php" target="_blank">Terms and Conditions</a>
                                            </label>
                                        </div>
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
            </form>

            <div class="form-footer-text">
                <p>Already have an account? <a href="<?php echo BASE_URL; ?>pages/account/login.php" class="link-underline" style="color: var(--color-primary);">Log In</a></p>
            </div>
        </div>
    </div>
</section>

<?php
require '../../includes/footer.php';
?>