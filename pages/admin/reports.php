<?php
require_once __DIR__ . '/header.php';
// if (!($_SESSION['is_admin'] ?? false)) { die('Access denied'); }

// Reset score
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $conn->query("DELETE FROM gq_scores WHERE id=" . (int)$_POST['reset']);
}

// Search filter
$where = '';
if (!empty($_GET['user'])) {
    $where = "WHERE u.name LIKE '%" . $conn->real_escape_string($_GET['user']) . "%'";
}
$scores = $conn->query("
  SELECT sc.id, u.name, s.title, sc.score, sc.created_at
  FROM gq_scores sc
  JOIN users u ON u.id = sc.user_id
  JOIN gq_sessions s ON s.id = sc.session_id
  $where
  ORDER BY sc.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<section class="admin-section">
    <h1>Scores & Export</h1>

    <form method="get" class="search-card">
        <div class="search-box">
        <input name="user" placeholder="Search by name" id="searchInput" onkeyup="searchTable()" value="<?= htmlspecialchars($_GET['user'] ?? '') ?>">
        <button class="btn btn-primary">Search</button>
        </div>
        <!-- <button type="button" class="btn btn-secondary" onclick="downloadCSV()">Export CSV</button> -->
    </form>
    <br><br>
    <div class="content-grid">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Participants</h3>
                <button class="btn btn-primary" onclick="downloadCSV('table')">Export CSV</button>
            </div>
            <div class="card-body">
                <div class="table-card">
                    <table class="admin-table" id="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Session</th>
                                <th>Score</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scores as $sc): ?>
                                <tr>
                                    <td><?= $sc['name'] ?></td>
                                    <td><?= $sc['title'] ?></td>
                                    <td><?= $sc['score'] ?></td>
                                    <td><?= $sc['created_at'] ?></td>
                                    <td>
                                        <form method="post" onsubmit="return confirm('Reset score?')">
                                            <button name="reset" value="<?= $sc['id'] ?>" class="btn btn-danger">Reset</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activity</h3>
                        </div>
                        <div class="card-body">
                            <div class="activity-item">
                                <div class="activity-icon">📊</div>
                                <div class="activity-content">
                                    <div class="activity-title">New report generated</div>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">👤</div>
                                <div class="activity-content">
                                    <div class="activity-title">New user registered</div>
                                    <div class="activity-time">4 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">🛒</div>
                                <div class="activity-content">
                                    <div class="activity-title">Order #12349 placed</div>
                                    <div class="activity-time">6 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">💰</div>
                                <div class="activity-content">
                                    <div class="activity-title">Payment received</div>
                                    <div class="activity-time">8 hours ago</div>
                                </div>
                            </div>
                        </div>
                    </div> -->
    </div>
<?php require_once 'footer.php'; ?>
</section>
<script>
    function downloadCSV() {
        const rows = Array.from(document.querySelectorAll('#scoreTable tr'));
        const csv = rows.map(r => Array.from(r.querySelectorAll('td,th')).map(c => `"${c.innerText}"`).join(',')).join('\n');
        const blob = new Blob([csv], {
            type: 'text/csv'
        });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'scores.csv';
        a.click();
    }
</script>