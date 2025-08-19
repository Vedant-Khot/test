<?php
// pages/gitas-quest/result.php
require_once '../../includes/header.php';

// Security: User must be logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/accounts/login.php");
    exit();
}

// Get the attempt ID from the URL and the user ID from the session
$attempt_id = filter_input(INPUT_GET, 'attempt', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if (!$attempt_id) {
    die("Error: Invalid attempt ID provided.");
}

// Fetch the attempt data, BUT ONLY if it belongs to the currently logged-in user.
// This is the crucial security check.
$sql = "SELECT a.mcq_score, u.name 
        FROM gq_final_exam_attempts a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = ? AND a.user_id = ?";
$stmt = mysqli_prepare($conn, query: $sql);
mysqli_stmt_bind_param($stmt, "ii", $attempt_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$attempt_data = mysqli_fetch_assoc($result);

// If no data is found, it means the attempt doesn't exist or doesn't belong to this user.
if (!$attempt_data) {
    die("Access Denied: You do not have permission to view this result, or the attempt does not exist.");
}

// We also need the total number of MCQs for a percentage score
$total_mcq_sql = "SELECT COUNT(id) as total FROM gq_questions WHERE is_final_paper = TRUE AND question_type = 'mcq'";
$total_mcq_result = mysqli_query($conn, $total_mcq_sql);
$total_mcq_count = mysqli_fetch_assoc($total_mcq_result)['total'];
// echo''. $total_mcq_count .'';
?>
<style>
    /* --- Result Page Styles --- */
.result-display {
    margin: 2.5rem 0;
    padding: 2rem;
    background-color: var(--color-bg-light);
    border-radius: var(--border-radius);
}
.result-display p {
    margin: 0;
    font-size: 1.1rem;
    color: var(--color-text-muted);
}
.result-display h1 {
    font-size: 4rem;
    color: var(--color-primary);
    margin: 0.5rem 0 0 0;
}
.next-steps {
    margin-top: 2rem;
}
</style>
<section class="section">
    <div class="container" style="max-width: 650px; text-align: center;">
        <div class="auth-card">
            <div class="icon" style="background-color: #f0fdf4; color: #22c55e; border-radius: 50%; padding: 1.5rem; display: inline-flex; margin-bottom: 1.5rem;">
                <i data-lucide="award"></i>
            </div>
            <div class="auth-header">
                <h2>MCQ Result for <?php echo htmlspecialchars($attempt_data['name']); ?></h2>
                <p>Congratulations on completing the multiple-choice section of the Final Paper!</p>
            </div>

            <div class="result-display">
                <p>You Scored</p>
                <h1><?php echo htmlspecialchars($attempt_data['mcq_score']); ?> / <?php echo $total_mcq_count; ?></h1>
            </div>

            <div class="next-steps">
                <p>Your subjective answers (PDF) will be graded manually by our team. Your final rank will be determined after all papers have been reviewed.</p>
                <a href="<?php echo BASE_URL; ?>pages/profile/view.php" class="btn btn-primary" style="margin-top: 1rem;">Go to My Profile</a>
            </div>
        </div>
    </div>
</section>

<?php
require_once '../../includes/footer.php';
?>