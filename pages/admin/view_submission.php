<?php
// pages/admin/view_submission.php
require_once 'header.php';
// Include any necessary functions like the score recalculator if it's in a separate file.

// --- Step 1: Security & Get Attempt ID ---
if (!isset($_SESSION['user_id_admin'])) { /* ... your full admin security check ... */
    exit();
}
$attempt_id = filter_input(INPUT_GET, 'attempt_id', FILTER_VALIDATE_INT);
if (!$attempt_id) {
    die("Invalid attempt ID.");
}

// --- Step 2: Handle Grading Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_grades') {
    $subjective_marks = filter_input(INPUT_POST, 'subjective_marks', FILTER_VALIDATE_INT);
    $admin_user_id = $_SESSION['user_id'];

    // Update the DB with subjective marks and set status to 'graded'
    $sql_grade = "UPDATE gq_final_exam_attempts SET subjective_marks = ?, graded_by = ?, status = 'graded' WHERE id = ?";
    $stmt_grade = mysqli_prepare($conn, $sql_grade);
    mysqli_stmt_bind_param($stmt_grade, "iii", $subjective_marks, $admin_user_id, $attempt_id);
    if (mysqli_stmt_execute($stmt_grade)) {
        $_SESSION['success_message'] = "Grades saved successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to save grades.";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?attempt_id=" . $attempt_id);
    exit();
}

// --- Step 3: Fetch ALL Necessary Data ---

// a) Get the main attempt details (scores, PDF path, user info)
$sql_attempt = "SELECT a.mcq_score, a.subjective_marks, a.subjective_pdf_path, a.status, u.name, u.email
                FROM gq_final_exam_attempts a JOIN users u ON a.user_id = u.id
                WHERE a.id = ?";
$stmt_attempt = mysqli_prepare($conn, $sql_attempt);
mysqli_stmt_bind_param($stmt_attempt, "i", $attempt_id);
mysqli_stmt_execute($stmt_attempt);
$attempt_details = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_attempt));

if (!$attempt_details) {
    die("No submission found with this ID.");
}



// The SQL query is now simpler: we only need ONE query.
// This fetches all subjective questions and pairs each one with the user's answer for this specific attempt.
$sql_subjective = "SELECT 
                        q.question_text, 
                        s.subjective_answer AS user_subjective_answer
                   FROM gq_questions q
                   LEFT JOIN gq_final_submissions s ON q.id = s.question_id AND s.attempt_id = ?
                   WHERE q.is_final_paper = TRUE AND q.question_type = 'text'
                   ORDER BY q.id ASC";

$stmt_subjective = mysqli_prepare($conn, $sql_subjective);
mysqli_stmt_bind_param($stmt_subjective, "i", $attempt_id);
mysqli_stmt_execute($stmt_subjective);
$result_subjective = mysqli_stmt_get_result($stmt_subjective);

// This variable now holds an array where each item contains both a question and its corresponding answer.
$subjective_qna = mysqli_fetch_all($result_subjective, MYSQLI_ASSOC);

// --- Logic for the "Next Submission" button ---
$sql_next = "SELECT id FROM gq_final_exam_attempts WHERE status = 'submitted' AND id > ? ORDER BY id ASC LIMIT 1";
$stmt_next = mysqli_prepare($conn, $sql_next);
mysqli_stmt_bind_param($stmt_next, "i", $attempt_id);
mysqli_stmt_execute($stmt_next);
$next_attempt = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_next));
$next_attempt_id = $next_attempt['id'] ?? null;
?>
<style>
    /*
=================================================================
  Admin: View Submission Page - Base Styles
=================================================================
*/

    .submission-summary {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-border);
        margin-bottom: 2rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 1rem;
        align-items: center;
    }

    .summary-grid strong {
        color: var(--color-text);
    }

    .answers-container h3 {
        margin-bottom: 1.5rem;
    }

    .answer-card {
        background: white;
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .answer-card-header {
        padding: 1.5rem;
        background-color: var(--color-bg-light);
    }

    .answer-card-header p {
        margin: 0.5rem 0 0 0;
        font-size: 1.1rem;
        color: var(--color-text);
    }

    .answer-card-body {
        padding: 1.5rem;
    }

    /* MCQ Options List */
    .options-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .options-list li {
        padding: 0.75rem 1rem;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        position: relative;
    }

    .subjective-placeholder,
    .skipped-answer {
        font-style: italic;
        color: var(--color-text-muted);
    }

    .check-icon {
        width: 16px;
        height: 16px;
        display: inline-block;
        vertical-align: middle;
    }

    /* --- Answer Status Styling --- */

    .options-list li.correct-answer {
        color: #15803d;
    }

    .options-list li.user-correct {
        background-color: #dcfce7;
        border-color: #4ade80;
        color: #166534;
        font-weight: 700;
    }

    .options-list li.user-incorrect {
        background-color: #fee2e2;
        border-color: #f87171;
        color: #991b1b;
        font-weight: 700;
    }

    /*
=================================================================
  Grading Panel Layout
=================================================================
*/

    .grading-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }

    .grading-panel {
        background: white;
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        align-self: start;
        position: sticky;
        top: 100px;
    }

    .grading-panel h3 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--color-border);
    }

    .grading-box {
        margin-bottom: 1.5rem;
    }

    .grading-box strong {
        display: block;
        margin-bottom: 0.75rem;
    }

    /*
=================================================================
  RESPONSIVE STYLES
=================================================================
*/

    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
            text-align: left;
        }

        .answer-card-header,
        .answer-card-body {
            padding: 1rem;
        }

        .options-list li {
            padding: 0.6rem 0.75rem;
        }

        .grading-panel {
            position: static;
            top: unset;
        }

        .grading-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .submission-summary {
            padding: 1rem;
        }

        .answer-card-header p {
            font-size: 1rem;
        }

        .grading-panel {
            padding: 1rem;
        }

        .grading-box strong {
            font-size: 0.95rem;
        }
    }

    .form-control {
        border-color: #ccc;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1rem;
    }
</style>

<section class="section">
    <div class="container" style="max-width: 900px;">
        <!-- messgaes sessions  -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars(string: $_SESSION['success_message']) ?>
            </div>
        <?php unset($_SESSION['success_message']);
        endif; ?>
        <div class="submission-summary">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Exam Submission Details</h2>
                <!-- The "Re-calculate Score" button/form -->
                <form method="POST" action="">
                    <input type="hidden" name="action" value="recalculate_mcq">
                    <button type="submit" class="btn btn-secondary">Re-calculate Score</button>
                </form>
            </div>
            <!-- Submission Summary Card -->
            <div class="submission-summary">

            </div>

            <div class="submission-summary">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <h2>Grading Submission #<?php echo $attempt_id; ?></h2>
                    <?php if ($next_attempt_id): ?>
                        <a href="?attempt_id=<?php echo $next_attempt_id; ?>" class="btn btn-primary">Grade Next Submission &rarr;</a>
                    <?php endif; ?>
                </div>
                <h2>Exam Submission Details</h2>
                <div class="summary-grid">
                    <strong>Student:</strong> <span><?php echo htmlspecialchars($attempt_details['name']); ?></span>
                    <strong>Email:</strong> <span><?php echo htmlspecialchars($attempt_details['email']); ?></span>
                    <strong>Status:</strong> <span><?php echo htmlspecialchars(ucfirst($attempt_details['status'])); ?></span>
                    <strong>Subjective PDF:</strong>
                    <?php if ($attempt_details['subjective_pdf_path']): ?>
                        <a href="<?php echo BASE_URL . htmlspecialchars($attempt_details['subjective_pdf_path']); ?>" target="_blank" class="btn btn-secondary">
                            View Submitted PDF
                        </a>
                    <?php else: ?>
                        <span>Not submitted yet.</span>
                    <?php endif; ?>
                </div>
                <strong>Student:</strong> <span><?php echo htmlspecialchars($attempt_details['name']); ?></span>
                <strong>MCQ Score:</strong> <span><?php echo is_null($attempt_details['mcq_score']) ? 'N/A' : htmlspecialchars($attempt_details['mcq_score']); ?></span>
                <strong>Status:</strong> <span><strong><?php echo htmlspecialchars(strtoupper($attempt_details['status'])); ?></strong></span>
            </div>
        </div>
    </div>
    <!-- Answers Display -->

    <div class="grading-layout">
            <!-- Left Column: Answers -->
           <div class="answers-container">
    <h3>Subjective Questions & Answers</h3>

    <?php if (empty($subjective_qna)): ?>
        <p>No subjective questions were found for this exam.</p>
    <?php else: ?>
        <?php foreach ($subjective_qna as $index => $item): ?>
            <div class="answer-card">
                <div class="answer-card-header">
                    <!-- The Question -->
                    <strong>Question <?php echo $index + 1; ?>:</strong>
                    <p><?php echo htmlspecialchars($item['question_text']); ?></p>
                </div>
                <div class="answer-card-body">
                    <!-- The User's Answer for that Question -->
                    <strong>Student's Answer:</strong>
                    <div class="subjective-answer-display">
                        <?php if (!empty($item['user_subjective_answer'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($item['user_subjective_answer'])); ?></p>
                        <?php else: ?>
                            <p class="skipped-answer"><i>This question was not answered.</i></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

            <!-- Right Column: Grading Panel -->
            <aside class="grading-panel">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="save_grades">
                    <h3>Grading Panel</h3>
                    <div class="grading-box">
                        <strong>Subjective PDF</strong>
                        <?php if ($attempt_details['subjective_pdf_path']): ?>
                            <a href="<?php echo BASE_URL . htmlspecialchars($attempt_details['subjective_pdf_path']); ?>" target="_blank" class="btn btn-secondary" style="width: 100%;">View Uploaded PDF</a>
                        <?php else: ?>
                            <p>No separate PDF was submitted.</p>
                        <?php endif; ?>
                    </div>

                    <div class="grading-box">
                        <label for="subjective_marks"><strong>Enter Subjective Marks</strong></label>
                        <input type="number" id="subjective_marks" name="subjective_marks" class="form-control" value="<?php echo htmlspecialchars($attempt_details['subjective_marks'] ?? ''); ?>" placeholder="Enter score here..." required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Save Grades & Finalize</button>
                </form>
            </aside>
        </div>
    </div>
</section>

<?php
require_once 'footer.php';
?>