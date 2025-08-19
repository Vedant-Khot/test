<?php
// controllers/exam_controller.php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

// --- Security Check: User must be logged in ---
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit();
}

$user_id = $_SESSION['user_id'] || $_COOKIE['user_id'];
$action = $_GET['action'] ?? '';
$type = $_GET['type'] ??'';

// --- Main Logic Router ---
switch ($action) {
    case 'start':
        start_exam($conn, $user_id, $type);
        break;
    case 'submit_mcq': // New action for MCQ submission
        submit_mcq_part($conn, $user_id);
        break;
    case 'submit_pdf': // New action for PDF submission
        submit_pdf_part($conn, $user_id);
        break;
    case 'submit_text': 
        submit_text($conn, $user_id);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        exit();
}

// --- Function to start the exam ---
function start_exam($conn, $user_id , $type) {
    $quest_year = date("Y");

    // Check if user already has an attempt in progress or submitted
    $sql_check = "SELECT id, status FROM gq_final_exam_attempts WHERE user_id = ? AND quest_year = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $quest_year);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    if ($attempt = mysqli_fetch_assoc($result_check)) {
        if ($attempt['status'] == 'submitted') {
            echo json_encode(['success' => false, 'error' => 'You have already submitted your final paper.']);
            exit();
        }
    }
    
    // before creating a new attempt, check if user has completed the previous attempt
    $sql_check = 'SELECT id, status FROM gq_final_exam_attempts WHERE user_id = ? AND quest_year = ? ';
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check,'ii', $user_id, $quest_year);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $attempt = mysqli_fetch_assoc($result_check);
    $count = mysqli_num_rows($result_check);
    if ($count == 0) {
        // Create a new exam attempt record
        $start_time = date("Y-m-d H:i:s");
        $sql_attempt = "INSERT INTO gq_final_exam_attempts (user_id, quest_year, start_time, status) VALUES (?, ?, ?, 'in_progress')";
        $stmt_attempt = mysqli_prepare($conn, $sql_attempt);
        mysqli_stmt_bind_param($stmt_attempt, "iis", $user_id, $quest_year, $start_time);
        mysqli_stmt_execute($stmt_attempt);
        $attempt_id = mysqli_insert_id($conn);
    }else{
        $attempt_id = $attempt['id'];
    }
    

    // Fetch all final paper questions and their options
    $sql_q = "SELECT q.id, q.question_text, q.question_type FROM gq_questions q WHERE q.is_final_paper = TRUE AND q.question_type = '$type' ORDER BY q.id";
    $result_q = mysqli_query($conn, $sql_q);
    $questions = mysqli_fetch_all($result_q, MYSQLI_ASSOC);

    $sql_o = "SELECT o.id, o.question_id, o.option_text FROM gq_options o WHERE o.question_id IN (SELECT id FROM gq_questions WHERE is_final_paper = TRUE)";
    $result_o = mysqli_query($conn, $sql_o);
    $options_raw = mysqli_fetch_all($result_o, MYSQLI_ASSOC);
    $options = [];
    foreach ($options_raw as $opt) {
        $options[$opt['question_id']][] = $opt;
    }

    // Combine questions with their options
    foreach ($questions as $key => $q) {
        if ($q['question_type'] == 'mcq') {
            $questions[$key]['options'] = $options[$q['id']] ?? [];
        }
    }

    echo json_encode(['success' => true, 'questions' => $questions, 'attempt_id' => $attempt_id]);
    exit();
}

// --- Function to submit the exam ---
// - NEW FUNCTION to handle MCQ submission ---
// Inside controllers/exam_controller.php -> REPLACE this function

/*************  ✨ Windsurf Command ⭐  *************/
/**
 * Submits the Multiple Choice Questions (MCQ) part of the exam.
 *
 * This function processes the user's MCQ answers, calculates the score by
 * comparing user answers with the correct answers, and updates the exam
 * attempt record with the score and completion status. It expects to receive
 * data via a JSON payload containing the attempt ID and the user's answers.
 *
 * @param mysqli $conn The database connection object.
 * @param int $user_id The ID of the user submitting the MCQ answers.
 *
 * @throws Exception if there is a database error during the submission process.
 * 

/*******  db33f69c-ca7a-4af5-87ef-f8445236cda8  *******/
function submit_mcq_part($conn, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $attempt_id = $data['attempt_id'] ?? 0;
    $mcq_answers = $data['mcq_answers'] ?? [];

    if (empty($attempt_id) || empty($mcq_answers)) {
        echo json_encode(['success' => false, 'error' => 'Invalid submission data.']);
        exit();
    }

    // --- SCORE CALCULATION LOGIC ---
    $score = 0;
    $question_ids = array_keys($mcq_answers); // Get an array of question IDs that the user answered
    
    // Create placeholders for the SQL IN clause (e.g., ?,?,?)
    $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
    
    // Fetch the correct option IDs for all the questions the user answered
    $sql_correct = "SELECT question_id, id FROM gq_options WHERE question_id IN ($placeholders) AND is_correct = TRUE";
    $stmt_correct = mysqli_prepare($conn, $sql_correct);
    
    // Dynamically bind parameters for each question ID
    mysqli_stmt_bind_param($stmt_correct, str_repeat('i', count($question_ids)), ...$question_ids);
    mysqli_stmt_execute($stmt_correct);
    $result_correct = mysqli_stmt_get_result($stmt_correct);
    $correct_answers = mysqli_fetch_all($result_correct, MYSQLI_ASSOC);
    
    // Create a simple map of [question_id => correct_option_id] for easy lookup
    $correct_answers_map = [];
    foreach ($correct_answers as $row) {
        $correct_answers_map[$row['question_id']] = $row['id'];
    }

    // Compare user's answers to the correct answers
    foreach ($mcq_answers as $question_id => $user_option_id) {
        if (isset($correct_answers_map[$question_id]) && $correct_answers_map[$question_id] == $user_option_id) {
            $score++; // Increment score if the answer is correct
        }
    }
    // --- END OF SCORE CALCULATION ---

    mysqli_begin_transaction($conn);
    try {
        // Save the individual MCQ answers (no change here)
        $sql_save = "INSERT INTO gq_final_submissions (attempt_id, question_id, mcq_option_id) VALUES (?, ?, ?)";
        $stmt_save = mysqli_prepare($conn, $sql_save);
        foreach ($mcq_answers as $question_id => $mcq_option_id) {
            mysqli_stmt_bind_param($stmt_save, "iii", $attempt_id, $question_id, $mcq_option_id);
            mysqli_stmt_execute($stmt_save);
        }
        
        // Update the attempt with the final score and new status
        $end_time = date("Y-m-d H:i:s");
        $sql_update = "UPDATE gq_final_exam_attempts SET end_time = ?, status = 'mcq_completed', mcq_score = ? WHERE id = ? AND user_id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "siii", $end_time, $score, $attempt_id, $user_id);
        mysqli_stmt_execute($stmt_update);

        mysqli_commit($conn);
        echo json_encode(['success' => true]);
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'error' => 'Database error during MCQ submission.']);
        exit();
    }
}
function submit_text($conn, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $attempt_id = $data['attempt_id'] ?? 0;
    $mcq_answers = $data['mcq_answers'] ?? [];

    // header('Content-Type: application/json'); // ✅ Return JSON

    if (empty($attempt_id) || empty($mcq_answers)) {
        echo json_encode(['success' => false, 'error' => 'Invalid submission data.']);
        exit();
    }

    foreach ($mcq_answers as $question_id => $mcq_option_id) {
        if (isset($mcq_option_id) && is_string($mcq_option_id)) {
            // check if already exist or not
            

            $sql = "INSERT INTO gq_final_submissions (attempt_id, question_id, subjective_answer) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iis", $attempt_id, $question_id, $mcq_option_id);
            mysqli_stmt_execute($stmt);
        }
    }
         // Update the attempt with the final score and new status
        $end_time = date("Y-m-d H:i:s");
        $sql_update = "UPDATE gq_final_exam_attempts SET end_time = ?, status = 'upload', mcq_score = ? WHERE id = ? AND user_id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "siii", $end_time, $score, $attempt_id, $user_id);
        mysqli_stmt_execute($stmt_update);
    echo json_encode(['success' => true, 'attempt_id' => $data]);
    exit();
}

// --- NEW FUNCTION to handle PDF submission ---
function submit_pdf_part($conn, $user_id) {
    $attempt_id = $_POST['attempt_id'] ?? 0;
    $subjective_pdf = $_FILES['subjective_pdf'] ?? null;
    $db_pdf_path = null;


    if (empty($attempt_id)) {
        echo json_encode(['success' => false, 'error' => 'Invalid attempt ID.']);
        exit();
    }    
    // Handle the file upload
    if ($subjective_pdf && $subjective_pdf['error'] === UPLOAD_ERR_OK) {
 $upload_dir = __DIR__ . '/../uploads/exam/';
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
            
            $original_name = basename($subjective_pdf['name']);
            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
            
            // Create a unique, safe filename
            $safe_filename = "attempt{$attempt_id}_user{$user_id}_" . time() . "." . $file_extension;
            $destination = $upload_dir . $safe_filename;
            
            if (move_uploaded_file($subjective_pdf['tmp_name'], $destination)) {
                $db_pdf_path = "uploads/exam/" . $safe_filename;
            }    } else {
        $_SESSION['error_message'] = "File upload failed. Please try again.";
        header("Location: " . BASE_URL . "pages/gitas-quest/upload-paper.php?attempt=" . $attempt_id);
        exit();
    }

    // Update the final attempt record with the PDF path and set status to 'submitted'
    $sql_update = "UPDATE gq_final_exam_attempts SET subjective_pdf_path = ?, status = 'submitted' WHERE id = ? AND user_id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sii", $db_pdf_path, $attempt_id, $user_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        // Redirect to profile page on final success
        header("Location: " . BASE_URL . "pages/gitas-quest/result_page.php?attempt=". $attempt_id);
        exit();
    } else {
        $_SESSION['error_message'] = "Database error. Could not finalize submission.";
        header("Location: " . BASE_URL . "pages/gitas-quest/upload-paper.php?attempt=" . $attempt_id);
        exit();
    }
}
?>