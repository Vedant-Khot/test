<?php
// controllers/recalculate_score.php (or a general functions file)

/**
 * Calculates the MCQ score for a given exam attempt and updates the database.
 *
 * @param mysqli $conn The database connection object.
 * @param int $attempt_id The ID of the exam attempt to grade.
 * @return int|false The calculated score on success, or false on failure.
 */
function calculate_and_save_mcq_score($conn, $attempt_id) {
    // 1. Fetch all of the user's submitted MCQ answers for this attempt.
    $sql_user_answers = "SELECT question_id, mcq_option_id FROM gq_final_submissions WHERE attempt_id = ? AND mcq_option_id IS NOT NULL";
    $stmt_user = mysqli_prepare($conn, $sql_user_answers);
    mysqli_stmt_bind_param($stmt_user, "i", $attempt_id);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user_answers_raw = mysqli_fetch_all($result_user, MYSQLI_ASSOC);
    
    // Create a simple map of [question_id => user's_option_id]
    $user_answers_map = [];
    foreach ($user_answers_raw as $row) {
        $user_answers_map[$row['question_id']] = $row['mcq_option_id'];
    }

    if (empty($user_answers_map)) {
        // No MCQ answers to score.
        return 0;
    }

    // 2. Fetch the correct option IDs for all the questions the user answered.
    $question_ids = array_keys($user_answers_map);
    $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
    $sql_correct = "SELECT question_id, id FROM gq_options WHERE question_id IN ($placeholders) AND is_correct = TRUE";
    $stmt_correct = mysqli_prepare($conn, $sql_correct);
    mysqli_stmt_bind_param($stmt_correct, str_repeat('i', count($question_ids)), ...$question_ids);
    mysqli_stmt_execute($stmt_correct);
    $result_correct = mysqli_stmt_get_result($stmt_correct);
    $correct_answers_raw = mysqli_fetch_all($result_correct, MYSQLI_ASSOC);

    // Create a map of [question_id => correct_option_id]
    $correct_answers_map = [];
    foreach ($correct_answers_raw as $row) {
        $correct_answers_map[$row['question_id']] = $row['id'];
    }

    // 3. Compare the maps and calculate the score.
    $score = 0;
    foreach ($user_answers_map as $question_id => $user_option_id) {
        if (isset($correct_answers_map[$question_id]) && $correct_answers_map[$question_id] == $user_option_id) {
            $score++;
        }
    }

    // 4. Update the gq_final_exam_attempts table with the new score.
    $sql_update = "UPDATE gq_final_exam_attempts SET mcq_score = ? WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ii", $score, $attempt_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        return $score; // Return the new score on success
    } else {
        return false; // Return false on failure
    }
}
?>