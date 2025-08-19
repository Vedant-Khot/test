<?php 
// view result of the test
require_once '../../config/db.php';
require_once '../../includes/header.php';
// require_once BASE_URL . 'includes/header.php';

// fetch user score according to get method
if (isset($_GET['id']) &&  is_numeric($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    // dont use pdo instance here, use mysqli   
    $sql = 'SELECT * FROM gq_scores WHERE user_id = '.$user_id;
    $result = mysqli_query($conn, $sql);
    // this i will fetch all the scores of the user (`id`, `user_id`, `session_id`, `score`, `created_at`) i have 10 sessions fixed fetch them from the sesssions table
    // if the user has not given the test, then show a message that the user has not given the test
    // fetch the session id from the gq_sessions table
    $session_sql = 'SELECT * FROM gq_sessions';
    $session_result = mysqli_query($conn, $session_sql);
    $session_row = mysqli_fetch_array($session_result);
// store all the session ids and names in an array
    $sessions = [];
    while ($session_row) {
        $sessions[$session_row['id']] = [
            'id' => $session_row['id'],
            'title' => $session_row['title']
        ];
        $session_row = mysqli_fetch_array($session_result);
    }
    // $session_id = $session_row['id'];
    // $session_name = $session_row['title'];
    if (!$result) {
        $_SESSION['error_message'] = "Error fetching results: " . mysqli_error($conn);
        header("Location: " . BASE_URL . "pages/gitas-quest/sessions.php");
        exit();
    }


  


} else {
    $_SESSION['error_message'] = "Invalid session ID.";
    header("Location: " . BASE_URL . "pages/gitas-quest/sessions.php");
    exit();
}

// print_r($sessions);
?>
<!-- <div class="table-card fade-in-up">
      <table class="leaderboard-table">
        <thead>
          <tr>
            <th>#</th><th>Name</th><th>Score</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($leaders as $row): ?>
          <tr<?= isset($_SESSION['user_id']) && $row['id'] == $_SESSION['user_id'] ? ' class="highlight"' : '' ?>>
            <td><?= $row['rank_pos'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['total_score'] ?> pts</td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div> -->
<div class="container ">
    <h1 class="mt-5">Test Results</h1>
    <div class="row mt-4">
        <div class="col-md-12 table-card fade-in-up">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="table  leaderboard-table">
                    <thead>
                        <tr>
                            <th>Session</th>
                            <th>Score</th>
                            <th>Date Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sessions[$row['session_id']]['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['score']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No test results found for this user.</p>
            <?php endif; ?>
        </div>
    </div>
