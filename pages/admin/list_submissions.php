<?php
// pages/admin/list_submissions.php

// Use a dedicated admin header if you have one, otherwise the main header is fine.
require_once 'header.php'; 

// --- Step 1: Security Check ---
// Ensure that only logged-in admins can access this page.
if (!isset($_SESSION['user_id_admin'])) {
    // Redirect to an admin login page or the main site login.
    header("Location: " . BASE_URL . "pages/login.php"); 
    exit();
}

// --- Step 2: Fetch All Exam Attempts ---
// This query joins the attempts table with the users table to get the student's name.
// We order by status first to show ungraded papers at the top, then by the newest attempt.
$sql = "SELECT 
            a.id AS attempt_id, 
            u.name AS student_name,
            a.status,
            a.mcq_score,
            a.start_time
        FROM gq_final_exam_attempts a
        INNER JOIN users u ON a.user_id = u.id
        ORDER BY 
            CASE a.status
                WHEN 'submitted' THEN 1
                WHEN 'mcq_completed' THEN 2
                WHEN 'graded' THEN 3
                ELSE 4
            END,
            a.start_time DESC";

$result = mysqli_query($conn, $sql);
$attempts = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<section class="section">
    <div class="container">
        <div class="profile-header" style="margin-bottom: 2rem;">
            <h2>Gita Quest Submissions</h2>
            <p class="text-muted">A list of all final paper attempts. Submissions awaiting grading are shown first.</p>
        </div>

        <!-- This container makes the table scrollable on small screens -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Attempt ID</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>MCQ Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attempts)): ?>
                        <tr>
                            <!-- This message shows if no one has submitted the exam yet -->
                            <td colspan="5" style="text-align: center;">No exam submissions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attempts as $attempt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($attempt['attempt_id']); ?></td>
                                <td><?php echo htmlspecialchars($attempt['student_name']); ?></td>
                                <td>
                                    <!-- Add a nice visual badge for the status -->
                                    <span class="status-badge status-<?php echo htmlspecialchars($attempt['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $attempt['status']))); ?>
                                    </span>
                                </td>
                                <td><?php echo is_null($attempt['mcq_score']) ? 'N/A' : htmlspecialchars($attempt['mcq_score']); ?></td>
                                <td>
                                    <!-- This is the "View/Edit" link you wanted -->
                                    <a href="view_submission.php?attempt_id=<?php echo htmlspecialchars($attempt['attempt_id']); ?>" class="btn btn-secondary">
                                        View / Grade
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
require_once 'footer.php'; 
?>