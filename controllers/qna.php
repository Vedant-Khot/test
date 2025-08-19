<?php
// controllers/qna_controller.php (AJAX Version)
session_start();
require_once '../config/db.php';

// Set the content type to JSON for the response
header('Content-Type: application/json');

// --- Create a response array to hold our data ---
$response = [
    'success' => false,
    'error' => 'An unknown error occurred.'
];

// --- Security & Validation ---
if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Authentication required. Please log in.';
    echo json_encode($response);
    exit();
}
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['error'] = 'Invalid request method.';
    echo json_encode($response);
        header('Location: ' . BASE_URL . 'pages/gitas-quest/sessions.php?id=' . $session_id);

    exit();
}

// --- Process the submitted data ---
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name']; // Get user's name from session
$session_id = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
$question_text = trim($_POST['question_text']);
$parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT);

if (empty($question_text) || !$session_id) {
    $response['error'] = 'Your question cannot be empty.';
     json_encode($response); 
    header('Location: ' . BASE_URL . 'pages/gitas-quest/sessions.php?id=' . $session_id);
    exit();
}

if ($parent_id === 0) { $parent_id = null; }

// --- Insert into the database ---
$sql = "INSERT INTO gq_qna (session_id, user_id, parent_id, question) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiis", $session_id, $user_id, $parent_id, $question_text);

if (mysqli_stmt_execute($stmt)) {
    // --- Success! Prepare a successful response ---
    $response['success'] = true;
    $response['error'] = ''; // Clear any default error
    
    // Send back the data for the new question so JavaScript can display it
    $response['question'] = [
        'name' => htmlspecialchars($user_name),
        'text' => nl2br(htmlspecialchars($question_text)),
        'timestamp' => date('M j, Y H:i') // Format the current time nicely
    ];

} else {
    $response['error'] = 'A database error occurred while posting.';
}

// --- Send the final JSON response back to the JavaScript ---
echo json_encode($response);
    header('Location: ' . BASE_URL . 'pages/gitas-quest/sessions.php?id=' . $session_id);

exit();
?>