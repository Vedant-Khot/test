<?php
// require_once __DIR__ . '/config.php';
require_once '../../config/db.php';
// if (isset($_SESSION['user_id_admin'])) {
//     die('Access denied');
// }


// initialize variables
$question = '';
$type = 'MCQ';
$marks = '';
$final = 0;
$options = [['','',''],['','',''],['','',''],['','','']];
$choices = [['','',''],['','',''],['','',''],['','','']];
$options_ids = [];
$sess_id = '';
$correct = 3;
if (isset($_GET['edit']) && $_GET['edit'] == '1' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM gq_questions WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $questionData = $result->fetch_assoc();
        $question = $questionData['question_text'];
        $type = $questionData['question_type'];
        $marks = $questionData['marks'];
        $final = $questionData['is_final_paper'];
        $sess_id = $questionData['session_id'];
        //fetch options
        // echo $id;
        $i = 0;
        $optionsResult = $conn->query("SELECT * FROM gq_options WHERE question_id=$id");
        $options=[];
        while ($opt = $optionsResult->fetch_assoc()) {
            // print_r($opt);
            
            if (count($options) < 4) {
                
                $options[] = [$opt['option_text'], $opt['is_correct'], $opt['id']];
                // $options_ids[] = $opt['id'];
            }
            if ($opt['is_correct']) {
                $correct = $i;
            }
            // echo $correct . " is correct<br>";
            $i++;
        }
        // print_r($options);
        // echo "<br> correct:" . $correct . "<br>";
        // echo $correct == 0 ? ' 0' : ' ';
        // echo $correct == 1 ? ' 1' : ' ';
        // echo $correct == 2 ? ' 2' : ' ';
        // echo $correct == 3 ? ' 3' : ' ';
        $stmt = $conn->prepare('SELECT session_id FROM gq_questions WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $sessionData = $result->fetch_assoc();
            $sessionId = $sessionData['session_id'];
        } else {
            die('Session not found');
        }


        $optionsResult = $conn->query("SELECT * FROM gq_options WHERE question_id=" . $questionData['id'] . "");

        // $i = 0;



    ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('addForm').style.display = 'block';
                });
            </script>
    <?php
    } else {
        die('Question not found');
    }
}
// CRUD

require_once 'header.php';
$questions = $conn->query("SELECT q.*, s.title AS session_title
                           FROM gq_questions q
                           JOIN gq_sessions s ON s.id = q.session_id
                           ORDER BY s.session_number, q.id")->fetch_all(MYSQLI_ASSOC);
$sessions  = $conn->query("SELECT id, title FROM gq_sessions ORDER BY session_number")->fetch_all(MYSQLI_ASSOC);
?>
<section class="admin-section">
    <h1>Manage Questions & Options</h1>
    <button class="btn btn-primary" onclick="toggleForm()">+ Add Question</button>
    <br>
    <br>
    <form id="addForm" method="post" action="<?php echo BASE_URL.'controllers/questions.php';?>" class="admin-form glass-card" style="display:none">
        <select name="session_id" required>
            <?php foreach ($sessions as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $sess_id == $s['id'] ? 'selected' : ''; ?>><?= $s['title'] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($_GET['edit']) && $_GET['edit'] == '1'): ?>
            <input type="hidden" name="edit" value="1">
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>
        <input name="text" value="<?php echo $question; ?>" placeholder="Question text" required>
        <select name="type">
            <option value="MCQ" <?= $type == 'MCQ' ? 'selected' : ''; ?>>MCQ</option>
            <option value="Check Box" <?= $type == 'Check Box' ? 'selected' : ''; ?>>Check Box</option>
            <option value="Match The following" <?= $type == 'Match The following' ? 'selected' : ''; ?>>Match The following</option>
            <option value="TEXT" <?= $type == 'TEXT' ? 'selected' : ''; ?>>TEXT</option>
        </select>
        <input name="marks" type="number" value="<?php echo $marks; ?>" placeholder="Marks" required>
        <label>Final paper?
            <input name="final" type="checkbox" <?php $final ? 'checked' : ''; ?> value="1">
        </label>
        <!-- // for checkbox  -->
        <fieldset class="hide">
            <legend>ADD choices:</legend>
            <div id="choices-container">
               
            <input name="choices[]" value="<?php echo $choices[0][0]; ?>" placeholder="Option 1" required>
            </div>
            <br>
            <a id="btn-add" onclick="add_choices()" class="btn btn-primary">ADD choice</a>
        </fieldset>
        <fieldset class="hide special" style="padding: 10px;">
            <legend>Options (for MCQ)</legend>
            <input name="options[]" value="<?php echo $options[0][0]; ?>" placeholder="Option 1" required>
            <input name="options[]" value="<?php echo $options[1][0]; ?>" placeholder="Option 2" required>
            <input name="options[]" value="<?php echo $options[2][0]; ?>" placeholder="Option 3">
            <input name="options[]" value="<?php echo $options[3][0]; ?>" placeholder="Option 4">
            Correct index:
            <select name="correct" required>
                <?php 
                // echo is_int($correct); // Ensure $correct is an integer

                for ($i = 0; $i < 4; $i++): 
                    // echo "<br>Correct: " . $correct. "<br>";
                ?>

                    <option value="<?= $i ?>" <?= ($correct == $i) ? 'selected' : '' ?>><?= $i + 1 ?></option>
                <?php endfor; ?>
            </select>

        </fieldset>

        <button name="add" class="btn btn-primary">Save</button>
    </form>
    <div class="content-grid">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Participants</h3>
                <button class="btn btn-primary" onclick="downloadTableAsCSV('table')">Export CSV</button>
            </div>
            <div class="card-body">
                <div class="table-card">
                    <table class="admin-table" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Session</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $q): ?>
                                <tr>
                                    <td><?= $q['id'] ?></td>
                                    <td><?= $q['session_title'] ?></td>
                                    <td><?= mb_substr($q['question_text'], 0, 50) ?>…</td>
                                    <td><?= $q['question_type'] ?></td>
                                    <td><?= $q['marks'] ?></td>
                                    <td>
                                        <form method="post" onsubmit="return confirm('Delete?')" action="<?= BASE_URL ?>controllers/questions.php">
                                            <button name="delete" value="<?= $q['id'] ?>" class="btn btn-danger">×</button>
                                        </form>
                                        <form method="post" >
                                            <a href="<?= BASE_URL ?>pages/admin/questions_manager.php?edit=1&id=<?= $q['id'] ?>" class="btn btn-primary">📝</a>
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

</section>
<?php require_once __DIR__ . '/footer.php'; ?>
<script>
    let choicesCount = 1; // Start with one choice
    function add_choices() {
       // add choice input fields dynamically and br tag as well
        // document.preventDefault(); // Prevent form submission
        choicesCount++;
        const container = document.getElementById('choices-container');
        const input = document.createElement('input');
        input.name = 'choices[]';
        input.placeholder = 'Option ' + choicesCount;
        input.required = true;
        input.className = 'form-control';
        container.appendChild(input);
        
    }
    function toggleForm() {
        document.getElementById('addForm').style.display = 'block';
    }
</script>