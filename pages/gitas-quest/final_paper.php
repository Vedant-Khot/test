<?php
ob_start();
// error_reporting(E_ALL);
// error_reporting(0); i want ot see the errors

// pages/gitas-quest/final-paper.php
require_once '../../includes/header.php';
// Security: User must be logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/account/login.php");
    exit();
} else {
    $user = $_SESSION["user_id"];
}
// check if he has submitted the final paper
if (isset($_GET['type'])) {
    $status = $_GET['type'] == 'mcq' ? 'mcq_completed' : 'submitted';

    // check in table gq_final_exam_attempts
    $sql = "SELECT * FROM gq_final_exam_attempts WHERE user_id = '$user' AND status = '$status'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // get the last attempt id
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_all($result);
        // print_r($row);
        $attempt_id = $row[$count - 1][0];
        // echo ''. $attempt_id .'';
        $status= $row[$count - 1][5];
        
?>
       <script>
        switch(<?= $status ?>){
            case 'mcq_completed':
                    setInterval(() => {
                ok()
                },1000)
                break;
            case 'upload': 
                setInterval(() => {
                    work();
                }, 1000);
                break;
            case 'submitted':
                setInterval(() => {
                    done();
                }, 1000);
                break;
        }
        if(window.location.search.includes('type=text')) {
                setInterval(() => {
                    work();
                }, 1000);
            }else{ 
                
            }
                function ok(){
                    showModal("Success", "You have Already submitted the final paper MCQs.", [{
                    text: "View Result",
                    action: () => {
                        window.location.href = "<?php echo BASE_URL; ?>pages/gitas-quest/result_page.php?attempt=<?= $attempt_id ?>";
                    }
                }])
            }
            function work(){
                showModal("Submit Pdf", "You have Already submitted the final paper.<br> If you want to submit pdf you can", [{
                    text: "Upload",
                    action: () => {
                        window.location.href = "<?php echo BASE_URL; ?>pages/gitas-quest/upload_paper.php?attempt=<?= $attempt_id ?>";
                    }
                }])
            }
            function done(){
                 showModal("Success", "You have Already submitted the final paper.", [{
                    text: "View Result",
                    action: () => {
                        window.location.href = "<?php echo BASE_URL; ?>pages/gitas-quest/result_page.php?attempt=<?= $attempt_id ?>";
                    }
                }])
            }
           
        </script>
        </script>
<?php
        // header("Location: " . BASE_URL . "pages/gitas-quest/result_page.php?attempt=".$attempt_id);
        // exit();
    }
}
?>
<style>
    /* assets/css/exam.css */
    #exam-environment {
        height: 100vh;
        width: 100vw;
        background-color: var(--color-bg-light);
    }

    .exam-screen {
        display: flex;
        width: 100%;
        height: 100%;
    }

    #exam-start-screen {
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .instructions-card {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-border);
        margin: 2rem 0;
        text-align: left;
        max-width: 500px;
    }

    .instructions-card ul {
        list-style-position: inside;
    }

    /* Main Exam Layout */
    .exam-sidebar {
        width: 250px;
        background: white;
        border-right: 1px solid var(--color-border);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .exam-content {
        flex-grow: 1;
        /*padding: 2rem 3rem;*/
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    /* Timer */
    .timer-box {
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
        padding: 1rem;
        margin-bottom: 2rem;
        border-bottom: 1px solid var(--color-border);
    }

    /* Question Navigator */
    .question-nav-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        overflow-Y: scroll;
        padding : 10px;
    }

    .nav-item-exam {
        padding: 0.75rem;
        text-align: center;
        border: 1px solid var(--color-border);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .nav-item-exam:hover {
        background: var(--color-bg-light);
    }

    .nav-item-exam.active {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    .nav-item-exam.answered {
        background: #e0f2fe;
        border-color: #7dd3fc;
    }

    /* Question Display */
    #question-display-area {
        flex-grow: 1;
    }

    #question-display-area h3 {
        font-size: 1.25rem;
    }

    .options-container {
        margin-top: 1.5rem;
    }

    .option {
        margin-bottom: 1rem;
    }

    .option input {
        margin-right: 0.75rem;
    }

    .subjective-answer {
        width: 100%;
        min-height: 200px;
        padding: 1rem;
        font-size: 1rem;
        border-radius: var(--border-radius);
        border: 1px solid var(--color-border);
    }

    /* Navigation Buttons */
    .exam-navigation {
        display: flex;
        justify-content: space-between;
        padding-top: 1.5rem;
        border-top: 1px solid var(--color-border);
    }

    .nav-item-exam {
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        border-radius: 6px;
        padding: 8px 12px;
        margin: 4px;
        display: inline-block;
        cursor: pointer;
    }

    .nav-item-exam.answered {
        background-color: #c8f7c5;
        /* light green */
        border: 1px solid green;
    }

    .nav-item-exam.not-answered {
        background-color: #f7c5c5;
        /* light red */
        border: 1px solid red;
    }

    .nav-item-exam.active_index {
        border: 2px solid blue;
        font-weight: bold;
        transform: scale(1.05);
        /* subtle zoom effect */
    }#exam-environment {
    /* On mobile, allow the content to scroll naturally */
    height: auto;
    min-height: 100vh;
    width: 100vw;
    background-color: var(--color-bg-light);
}
.exam-screen { display: flex; width: 100%; min-height: 100vh; }
#exam-start-screen { flex-direction: column; align-items: center; justify-content: center; }
.instructions-card { background: white; padding: 2rem; border-radius: var(--border-radius); border: 1px solid var(--color-border); margin: 2rem 0; text-align: left; max-width: 500px; }

/*
--- MOBILE FIRST LAYOUT (Default for all screen sizes) ---
Everything is stacked vertically.
*/
#exam-main-screen {
    flex-direction: column;
}
.exam-sidebar {
    width: 100%; /* Take full width on mobile */
    background: white;
    border-bottom: 1px solid var(--color-border); /* Border is at the bottom now */
    padding: 1rem;
    order: 1; /* Sidebar comes first */
}
.exam-content {
    width: 100%;
    padding: 1.5rem;
    order: 2; /* Main content comes second */
    flex-grow: 1;
}

/* Timer */
.timer-box {
    font-size: 1.75rem;
    font-weight: 700;
    text-align: center;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--color-border);
}

/* Question Navigator on Mobile */
.question-nav-list {
    display: grid;
    /* This will create as many columns as fit, with a minimum size of 45px */
    grid-template-columns: repeat(auto-fit, minmax(45px, 1fr));
    gap: 0.5rem;
}
.nav-item {
    padding: 0.75rem 0.25rem; /* More vertical padding for tapping */
    text-align: center;
    border: 1px solid var(--color-border);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: var(--transition);
}
.nav-item.active { background: var(--color-primary); color: white; border-color: var(--color-primary); }
.nav-item.answered { background: #e0f2fe; border-color: #7dd3fc; }

/* Question Display */
#question-display-area { flex-grow: 1; }
#question-display-area h3 { font-size: 1.25rem; }
.subjective-answer { width: 100%; min-height: 150px; }

/* Navigation Buttons */
.exam-navigation {
    display: flex;
    justify-content: space-between;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid var(--color-border);
}

/*
--- DESKTOP LAYOUT (For screens 992px and wider) ---
We switch to the two-column layout.
*/
@media (min-width: 992px) {
    .collapse{
    display: none;
}
    #exam-environment {
        height: 100vh; /* Lock height on desktop for app-like feel */
    }
    #exam-main-screen {
        flex-direction: row; /* Go back to side-by-side */
    }
    .exam-sidebar {
        width: 280px; /* Give it a fixed width */
        flex-shrink: 0; /* Prevent it from shrinking */
        border-right: 1px solid var(--color-border); /* Border is on the right */
        border-bottom: none;
        /*padding: 1.5rem;*/
    }
    .exam-content {
        /*padding: 2rem 3rem;*/
    }
    .timer-box {
        font-size: 2rem;
    }
    .question-nav-list {
        grid-template-columns: repeat(3, 1fr); /* Back to 4 columns on desktop */
    }
}
.btn{
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    margin: 10px;
    /* font-size: 1rem;
    font-weight: 500;
    line-height: 1.5;
    color: #fff;
    background-color: #007bff; */
}
.flex{
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.collapse i{
    font-size: 1rem;
    font-weight: 500;
    line-height: 1.5;
    color: #000;
    background-color: #007bff;
}
</style>
<!-- This is the container for our JavaScript exam app -->
<div id="exam-environment">
    <!-- 1. The Pre-Exam "Start" Screen -->
    <div id="exam-start-screen" class="exam-screen">
        <div class="container" style="text-align: center;">
            <h1>Gita ConQuest Final Paper</h1>
            <p class="text-muted">This is the final step in your quest. Please read the instructions carefully.</p>
            <div class="instructions-card">
                <h3>Instructions</h3>
                <ul>
                    <li>You will have **60 minutes** to complete the exam.</li>
                    <li>The exam will enter **full-screen mode**.</li>
                    <li>Switching tabs or leaving the page will be monitored.</li>
                    <li>Your answers will be submitted automatically when the timer ends.</li>
                </ul>
            </div>
            <button id="start-exam-btn" class="btn btn-primary">Begin the Final Paper</button>
        </div>
    </div>

    <!-- 2. The Main Exam Interface (Initially Hidden) -->
    <div id="exam-main-screen" class="exam-screen" style="display: none;">
        <div class="exam-sidebar">
            <div class="timer-box">
                <span id="timer-display">60:00</span>
            </div>

           <div class="flex"> <h4>Questions</h4>
        <!-- Toggler for mobile -->
        <!-- add a toggle button -->
        <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </button> -->
    </div>
            <div id="question-navigator" class="question-nav-list">
                <!-- Question numbers will be injected here by JavaScript -->
            </div>
        </div>
        <div class="exam-content">
            <div id="question-display-area">
                <!-- The current question will be injected here by JavaScript -->
            </div>
            <div class="exam-navigation">
                <button id="prev-question-btn" class="btn btn-secondary">Previous</button>
                <button id="next-question-btn" class="btn btn-primary">Next</button>
                <button id="submit-exam-btn" class="btn" style="background-color: #22c55e; color: white;">Submit Exam</button>
            </div>
        </div>
    </div>
</div>


<!-- We will create this new JS file -->
<script src="<?php echo BASE_URL; ?>assets/js/exam.js"></script>

<?php
// We don't include a footer here to provide a more immersive "app-like" experience
// You can add it back if you prefer.
require '../../includes/footer.php';
?>