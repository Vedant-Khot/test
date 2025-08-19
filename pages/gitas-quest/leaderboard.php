<?php
require_once __DIR__ . '/../../includes/header.php';

// 1. Rank the world
$sql = "
SELECT u.id,
       u.name,
       COALESCE(SUM(s.score),0) AS total_score,
       RANK() OVER (ORDER BY COALESCE(SUM(s.score),0) DESC) AS rank_pos
FROM   registrations u
LEFT JOIN gq_scores s ON s.user_id = u.id
GROUP  BY u.id
ORDER  BY total_score DESC
LIMIT  100";
$leaders = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// 2. Current user rank (even if >100)
$myRank = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("
        SELECT * FROM (
            SELECT u.id,
                   u.name,
                   COALESCE(SUM(s.score),0) AS total_score,
                   RANK() OVER (ORDER BY COALESCE(SUM(s.score),0) DESC) AS rank_pos
            FROM registrations u
            LEFT JOIN gq_scores s ON s.user_id = u.id
            GROUP BY u.id
        ) AS ranks
        WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $myRank = $stmt->get_result()->fetch_assoc();

} 
// echo " Debug: My Rank: " . print_r($myRank, true) . " -->";

?>
<section class="section">
  <div class="container leaderboard-wrapper">
    <h1 class="fade-in-up">🏆 Gita ConQuest Leaderboard</h1>
    <p class="section-intro fade-in-up">Top achievers and your standing in the quest.</p>

    <!-- Floating personal card -->
    <?php if ($myRank && (!isset($leaders[9]) || $myRank['rank_pos'] < 10)): ?>
      <aside class="my-rank-card floating">
        <h3>Your Rank</h3>
        <span class="rank-number"><?= $myRank['rank_pos'] ?></span>
        <span class="score"><?= $myRank['total_score'] ?> pts</span>
      </aside>
    <?php endif; ?>

    <!-- Main table -->
    <div class="table-card fade-in-up">
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
    </div>
    
  </div>
</section>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>