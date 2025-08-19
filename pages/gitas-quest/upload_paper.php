<?php
// pages/gitas-quest/upload-paper.php
require_once '../../includes/header.php';

// Security: User must be logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/accounts/login.php");
    exit();
}
// Get the attempt ID from the URL
$attempt_id = filter_input(INPUT_GET, 'attempt', FILTER_VALIDATE_INT);
if (!$attempt_id) {
    die("Invalid attempt ID.");
}
?>

<section class="section">
    <div class="container" style="max-width: 650px;">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Upload Subjective Answers PDF</h2>
                <p>You have completed the MCQ section. Please upload your single PDF with all subjective answers to finalize your submission.</p>
            </div>

            <?php
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <form action="<?php echo BASE_URL; ?>controllers/exam_controller.php?action=submit_pdf" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="attempt_id" value="<?php echo $attempt_id; ?>">

                <div class="form-group">
                    <label for="subjective_pdf">Select PDF File</label>
                    <input type="file" id="subjective_pdf" name="subjective_pdf" class="form-control" accept=".pdf" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Finalize My Submission</button>
            </form>
        </div>
    </div>
</section>

<?php
require_once '../../includes/footer.php';
?>