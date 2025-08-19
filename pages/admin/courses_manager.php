<?php
// pages/admin/courses_manager.php
require_once __DIR__ . '/../../config/db.php'; // Ensure the BASE_URL constant is defined
// if (!($_SESSION['user_id_admin'] ?? false)) { die('Access denied'); }
$video= '';
// Initialize variables
$number = '';
$title = '';
$desc = '';
$release = 'yyyy-MM-dd'; // Default date format
if (isset($_GET['edit'])&& $_GET['edit'] == '1' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM gq_sessions WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $session = $result->fetch_assoc();
        $number = $session['session_number'];
        $title = $session['title'];
        $desc = $session['description'];
        $video = $session['video_url'];
        $release = $session['release_date'];
      
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('addForm').style.display = 'block';
            });
        </script>
        <?php
    } else {
        die('Session not found');
    }
 
}
// CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_GET['edit']) && $_GET['edit'] == '1' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $conn->prepare(
            "UPDATE gq_sessions SET session_number=?, title=?, description=?, video_url=?, release_date=? WHERE id=?"
        );
        $stmt->bind_param(
            'issssi',
            $_POST['number'],
            $_POST['title'],
            $_POST['desc'],
            $_POST['video'],
            $_POST['release'],
            $id
        );
        $stmt->execute();
        $result = $stmt->get_result();
        header('Location: courses_manager.php');
    } else {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare(
            "INSERT INTO gq_sessions (session_number, title, description, video_url, release_date)
             VALUES (?,?,?,?,?)"
        );
        $stmt->bind_param(
            'issss',
            $_POST['number'],
            $_POST['title'],
            $_POST['desc'],
            $_POST['video'],
            $_POST['release']
        );
        $stmt->execute();
    }
    if (isset($_POST['delete'])) {
        $conn->query("DELETE FROM gq_sessions WHERE id=" . (int)$_POST['delete']);
        // Delete related options and questions
       // before deleting question fetch its id 
       $id= $conn->query('Select id from gq_questions where session_id=' . (int)($_POST['delete']));
        $conn->query("DELETE FROM gq_questions WHERE session_id=" . (int)($_POST["delete"]));
        // Delete all options related to the question
        
        
        $conn->query("DELETE FROM gq_options WHERE question_id=" . (int)$id);
        header("Location: courses_manager.php");
        exit();
    }}
}

$rows = $conn->query("SELECT * FROM gq_sessions ORDER BY session_number")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

</head>

<body>
    <div class="admin-container">
        <?php require_once __DIR__ . '/header.php'; ?>
        <section class="grid-content">
            <h1>Manage Sessions</h1>
            <button class="btn btn-primary"  onclick="toggleForm()">+ Add Session</button>
            <br>
            <br>
            <div class="card">
            <form id="addForm" method="post" class="admin-form glass-card" style="display:none">
                <input name="number" value="<?php echo $number;?>" type="number" placeholder="Session #" required>
                <input name="title" value="<?php echo $title;?>" placeholder="Title" required>
                <textarea name="desc" value=" " placeholder="Description" rows="3" required><?php echo $desc;?></textarea>
                <input name="video" value="<?php echo $video;?>" placeholder="YouTube URL" required>
                <input name="release" value="<?php echo $release;?>" type="date" required>
                <br>
                <button name="add" class="btn btn-primary">Save</button>
            </form>
            </div>
            <br><br>
            <div class="content-grid">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Participants</h3>
                        <button class="btn btn-primary" onclick="downloadTableAsCSV('table')">Export CSV</button>
                    </div>
                    <div class="card-body">
                        <table class="admin-table" id="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Video</th>
                                    <th>Release</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= $r['session_number'] ?></td>
                                        <td><?= $r['title'] ?></td>
                                        <td><a href="<?= $r['video_url'] ?>" target="_blank">Watch</a></td>
                                        <td><?= $r['release_date'] ?></td>
                                        <td>
                                            <form method="post" onsubmit="return confirm('Delete?')">
                                                <button name="delete" value="<?= $r['id'] ?>" class="btn btn-danger">×</button>
                                            </form>
                                            <form method="post" >
                                               <a href="<?= BASE_URL ?>pages/admin/courses_manager.php?edit=1&id=<?= $r['id'] ?>" class="btn btn-primary">📝</a>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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

        </section>
        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>
    <script>
        if (window.location.search.includes('add=1')) {
            toggleForm();
        }
        function toggleForm() {
            //if i get edit =1 then redirect it to normal add form
            if (window.location.search.includes('edit=1')) {
                window.location.href = '<?= BASE_URL ?>pages/admin/courses_manager.php?add=1';
            } else {
            document.getElementById('addForm').style.display = 'block';
            }
        }
    </script>