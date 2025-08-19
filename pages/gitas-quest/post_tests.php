<?php
// pages/gitas-quest/quiz.php
ob_start();
require_once __DIR__ . '/../../includes/header.php';

// 1. Security
if (!isset($_SESSION['user_id'])) {
  $_SESSION['error_message'] = 'Please log in to take the quiz.';
  header('Location: ' . BASE_URL . 'pages/accountlogin.php');
  exit;
}

$session_id = (int)($_GET['session_id'] ?? 0);
if (!$session_id) {
  $_SESSION['error_message'] = 'Invalid session.';
  header('Location: sessions.php');
  exit;
}

// 2. Has user already taken it?
$stmt = $conn->prepare("SELECT id FROM gq_scores WHERE user_id = ? AND session_id = ?");
$stmt->bind_param('ii', $_SESSION['user_id'], $session_id);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
  $_SESSION['info_message'] = 'You have already completed this quiz.';
  header("Location: sessions.php?id=$session_id");
  exit;
}

// 3. Fetch 5 random MCQs
$stmt = $conn->prepare(
  "SELECT 
        q.id, 
        q.question_text, 
        q.question_type, 
        q.marks,
        GROUP_CONCAT(o.id ORDER BY o.id SEPARATOR ',') AS option_ids,
        GROUP_CONCAT(o.option_text ORDER BY o.id SEPARATOR '|') AS option_texts
     FROM 
        gq_questions q
     JOIN 
        gq_options o ON o.question_id = q.id
     WHERE 
        q.session_id = ? 
     GROUP BY 
        q.id
     ORDER BY 
        RAND()
     LIMIT 20"
);

$stmt->bind_param('i', $session_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
print_r($questions); // Debugging line, remove in production
if (!$questions) {
  $_SESSION['error_message'] = 'No questions found for this session.';
  header("Location: sessions.php?id=$session_id");
  exit;
}

// 4. Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $score = 0;

  foreach ($questions as $q) {

    $correct_options_result = $conn->query(
      "SELECT id FROM gq_options WHERE question_id = {$q['id']} AND is_correct = 1"
    );

    // Handle MCQ and Checkbox
    $correct_ids = array_map(fn($row) => $row['id'], $correct_options_result->fetch_all(MYSQLI_ASSOC));

    if ($q['question_type'] === 'checkbox') {
      // Get user's selected checkbox answers
      $selected = $_POST['answer_box'][$q['id']] ?? [];

      // Sort both arrays before comparison (optional but good practice)
      sort($selected);
      sort($correct_ids);

      if ($selected === $correct_ids) {
        $score += $q['marks'];
      }
    } elseif ($q['question_type'] === 'text') {
      $answer = trim($_POST['answer'][$q['id']] ?? '');
      if (!empty($answer)) {
        $score += $q['marks'];
        $stmt = $conn->prepare("INSERT INTO gq_text_answers (user_id, question_id, answer) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $_SESSION['user_id'], $q['id'], $answer);
        $stmt->execute();
      }
    } else {
      // Default: MCQ or radio type
      $selected = $_POST['answer'][$q['id']] ?? null;
      if ($selected && in_array($selected, $correct_ids)) {
        $score += $q['marks'];
      }
    }
  }

  $stmt = $conn->prepare("INSERT INTO gq_scores (user_id, session_id, score) VALUES (?,?,?)");
  $stmt->bind_param('iii', $_SESSION['user_id'], $session_id, $score);
  $stmt->execute();

  $_SESSION['flash'] = "🎉 You scored {$score} points!";
  header("Location: sessions.php?id=$session_id");
  exit;
}


?>
<section class="section">
  <div class="container">
    <h2>Post-Test – Session <?= $session_id ?></h2>

    <!-- Replace the old <form> with this silky version -->
    <!-- Replace the old <form> with this silky version -->
    <form method="post" class="quiz-wrapper">
      <?php foreach ($questions as $index => $q):
        $opts  = explode('|', $q['option_texts']);
        $optIds = explode(',', $q['option_ids']); ?>
        <div class="question-card">
          <p class="question-title">
            <?= $index + 1 ?>. <?= htmlspecialchars($q['question_text']) ?>
            <small><?= $q['marks'] ?> mark<?= $q['marks'] > 1 ? 's' : '' ?></small>
          </p>

          <?php
          if ($q['question_type'] === 'text') {
            // For text questions, we can use a textarea
            echo '<textarea class="form-control" style="margin-bottom:10px" name="answer[' . $q['id'] . ']" required></textarea>';
            continue;
          } else if ($q['question_type'] === 'mcq') {
            // For MCQ questions, we can use radio buttons

            foreach ($opts as $i => $text): ?>
              <label class="radio-pill">
                <input type="radio"
                  name="answer[<?= $q['id'] ?>]"
                  value="<?= $optIds[$i] ?>" required>
                <span><?= htmlspecialchars($text) ?></span>
              </label>
            <?php endforeach;
          } else if ($q['question_type'] === 'checkbox') {
            // For checkbox questions, we can use checkboxes
            foreach ($opts as $i => $text): ?>
              <label class="checkbox-pill">
                <input type="checkbox" class="form-check-input"
                  name="answer_box[<?= $q['id'] ?>][]"
                  value="<?= $optIds[$i] ?>">
                <span><?= htmlspecialchars($text) ?></span>
              </label>
              <br>
          <?php endforeach;
          } else {
            echo '<p class="error">Unknown question type.</p>';
          }
          ?>
        </div>
      <?php endforeach; ?>

      <button type="submit" class="btn-glow-submit">
        Submit Answers
      </button>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>