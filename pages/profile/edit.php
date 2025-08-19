<?php
// pages/profile/edit.php
require_once '../../includes/header.php';
// echo '<!-- pages/profile/edit.php -->';
// Security check
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data to pre-fill the form
$sql = "SELECT * FROM registrations WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);
?>

<section class="section">
    <div class="container center" style="max-width: 70vw;margin-top: 2rem;">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Edit Your Profile</h2>
                <p>Keep your information up to date.</p>
            </div>

            <?php
            // Display any error messages
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <form action="<?php echo BASE_URL; ?>controllers/profile_controller.php" method="POST">
                
                <div class="form-grid">
                    <div class="form-group span-2">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                    </div>
                    <div class="form-group span-2">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user_data['age']); ?>" required>
                    </div>
                    <div class="form-group span-2">
                        <label for="gender">Gender</label>
                        <select id="gender"  class="form-control" name="gender" required>
                            <option value="Male" <?php echo ($user_data['gender'] == 'Male' ? 'selected' : ''); ?>>Male</option>
                            <option value="Female" <?php echo ($user_data['gender'] == 'Female' ? 'selected' : ''); ?>>Female</option>
                            <option value="Other" <?php echo ($user_data['gender'] == 'Other' ? 'selected' : ''); ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user_data['city']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user_data['state']); ?>" required>
                    </div>
                     <div class="form-group span-2">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user_data['country']); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Save Changes</button>
            </form>
        </div>
    </div>
</section>

<?php
require_once '../../includes/footer.php';
?>