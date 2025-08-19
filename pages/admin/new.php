<?php
require_once("../../config/db.php"); // adjust path if needed

// Get attempts where MCQ score is missing
$sql = "
    SELECT a.id AS attempt_id, a.user_id, r.name, r.email
    FROM gq_final_exam_attempts a
    JOIN registrations r ON r.id = a.user_id
    WHERE a.mcq_score IS NULL OR a.mcq_score = 0
";

$result = $conn->query($sql);

$attempts = [];

while ($row = $result->fetch_assoc()) {
    $attempt_id = $row['attempt_id'];
    $user_id = $row['user_id'];

    // Get all distinct MCQ answers for this attempt
    $submissions_sql = "
        SELECT DISTINCT fs.question_id, fs.mcq_option_id, qo.is_correct
        FROM gq_final_submissions fs
        JOIN gq_questions q ON q.id = fs.question_id
        JOIN gq_options qo ON qo.id = fs.mcq_option_id
        WHERE fs.attempt_id = $attempt_id
          AND fs.mcq_option_id IS NOT NULL
    ";

    $submission_result = $conn->query($submissions_sql);
    $total_mcqs = 0;
    $correct_mcqs = 0;

    while ($sub = $submission_result->fetch_assoc()) {
        $total_mcqs++;
        if ($sub['is_correct'] == 1) {
            $correct_mcqs++;
        }
    }

    $score = $correct_mcqs; // Can be changed to (correct/total * 100)

    $attempts[] = [
        'attempt_id' => $attempt_id,
        'name' => $row['name'],
        'email' => $row['email'],
        'total_mcqs' => $total_mcqs,
        'correct_mcqs' => $correct_mcqs,
        'score' => $score
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recalculate MCQ Scores</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: sans-serif;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        th {
            background: #f4f4f4;
        }
    </style>
</head>
<body>

<h2>Final Paper MCQ Score Recalculation</h2>
                        <button class="btn btn-primary" onclick="downloadTableAsCSV('table')">Export CSV</button>

<table id="table">
    <thead>
        <tr>
            <th>Attempt ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Total MCQs</th>
            <th>Correct Answers</th>
            <th>Recalculated Score</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attempts as $a): ?>
            <tr>
                <td><?= $a['attempt_id'] ?></td>
                <td><?= $a['name'] ?></td>
                <td><?= $a['email'] ?></td>
                <td><?= $a['total_mcqs'] ?></td>
                <td><?= $a['correct_mcqs'] ?></td>
                <td><?= $a['score'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
