<?php
// pages/gitas-quest/sessions.php
require_once __DIR__ . '/../../includes/header.php';

// Security: Kick users out if they are not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please log in to access Gita's Quest.";
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}
// 1. --- Fetch all sessions for the sidebar list ---
$all_sessions_sql = "SELECT id, session_number, title FROM gq_sessions ORDER BY session_number ASC";
$all_sessions_result = mysqli_query($conn, $all_sessions_sql);
$all_sessions = mysqli_fetch_all($all_sessions_result, MYSQLI_ASSOC);

// 2. --- Determine the current session ---
$current_session_id = 0;
if(isset($_SESSION['user_id'])) {
   $id = $_SESSION['user_id'];
}
else{
    $id = 0;
}
 if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $current_session_id = (int)$_GET['id'];
    } else {
        // If no ID is specified, default to the first session
        $current_session_id = $all_sessions[0]['id'] ?? 0;
    }
// if (isset($_GET['id']) && is_numeric($_GET['id'])) {
//     $current_session_id = (int)$_GET['id'];
// } else {
//     // If no ID is specified, default to the first session
//     $current_session_id = $all_sessions[0]['id'] ?? 0;
// }

// 3. --- Fetch the full details for the CURRENT session ---
$current_session = null;
if ($current_session_id > 0) {
    $current_session_sql = "SELECT * FROM gq_sessions WHERE id = ?";
    $stmt = mysqli_prepare($conn, $current_session_sql);
    mysqli_stmt_bind_param($stmt, "i", $current_session_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $current_session = mysqli_fetch_assoc($result);
}

// Helper function to extract YouTube video ID from URL
function getYouTubeEmbedUrl($url) {
    preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/', $url, $matches);
    return isset($matches[2]) ? 'https://www.youtube.com/embed/' . $matches[2] : '';
}

// 4. --- Check if the user has completed the quiz for this session ---
$stmt = $conn->prepare("SELECT id FROM gq_scores WHERE user_id = ? AND session_id = ?");
$stmt->bind_param('ii', $_SESSION['user_id'], $current_session_id);
$stmt->execute();
$quizDone = $stmt->get_result()->fetch_assoc() ? true : false;
// Debugging: Set a session variable to indicate they are registered
// $quizDone = true;
// echo $_SESSION['error_message'] ?? '';
$temp = $quizDone ? 'post_test_result.php' : 'post_tests.php';
$redirect_url = BASE_URL . 'pages/gitas-quest/'.$temp.'?session_id=' . $current_session_id . '&id=' . $_SESSION['user_id'];

// 5. --- Fetch all questions and answers for the current session ---
// This will include both questions and their replies using mysqli
$qna_list = [];
if($current_session_id > 0) {
    // echo "working";
    $qna_sql = "SELECT q.id, q.session_id, q.user_id, q.parent_id, q.question, q.created_at, u.name 
                FROM gq_qna q 
                JOIN registrations u ON q.user_id = u.id 
                WHERE q.session_id = ? 
                ORDER BY q.created_at ASC";
    $stmt = mysqli_prepare($conn, $qna_sql);
    mysqli_stmt_bind_param($stmt, "i", $current_session_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch all questions
    while ($row = mysqli_fetch_assoc($result)) {
        // Initialize the question if not already done
        if (!isset($qna_list[$row['id']])) {
            $qna_list[$row['id']] = [
                'id' => $row['id'],
                'session_id' => $row['session_id'],
                'user_id' => $row['user_id'],
                'parent_id' => $row['parent_id'],
                'question' => $row['question'],
                'created_at' => $row['created_at'],
                'name' => htmlspecialchars($row['name']),
                'replies' => []
            ];
        }

        // If it's a reply (parent_id is not null), add it to the replies array
        if ($row['parent_id'] !== null) {
            $qna_list[$row['parent_id']]['replies'][] = [
                'id' => $row['id'],
                'question' => nl2br(htmlspecialchars($row['question'])),
                'name' => htmlspecialchars($row['name'])
            ];
        }
    }
    // print_r($qna_list);
}
?>


<!-- Display info msg -->
<?php if (isset($_SESSION['info_message'])): ?>
    <div class="alert alert-info">
        <?php echo htmlspecialchars($_SESSION['info_message']); ?>
        <?php unset($_SESSION['info_message']); ?>
    </div>
<?php endif; ?>
<!-- Display error msg -->
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>
<!-- Display flash msg -->
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['flash']); ?>
        <?php unset($_SESSION['flash']); ?>
    </div>
<?php endif; ?>
<section class="section" style="padding: 20px;">
    <div class="container">
        <div class="gq-layout " style ="margin: 5px;">
            <!-- Main Content: Video Player and Description -->
            <div class="gq-main-content fade-in-up">
                
                <?php if ($current_session): ?>
                    <div class="video-responsive-wrapper">
                        <iframe src="<?php echo getYouTubeEmbedUrl($current_session['video_url']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <!-- Tab content for session details and question and  -->
                    
  <style>
      .tabs-container {
            background: rgba(255, 255, 255, 0.95);
            /* border-radius: 20px; */
            /* box-shadow: var(--shadow-card); */
            backdrop-filter: blur(20px);
            /* border: 1px solid var(--color-border); */
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            margin: 10px auto;
        }

        .tabs-header {
            display: flex;
            /* background: var(--color-bg); */
            position: relative;
            overflow: hidden;
            /* border-bottom: 1px solid var(--color-border); */
        }

        .tab-button {
            flex: 1;
            padding: 20px 30px;
            background: transparent;
            border: none;
            font-size: 16px;
            font-weight: 600;
            font-family: var(--font-family-sans);
            color: var(--color-text-body);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .tab-button:hover {
            color: var(--color-text-heading);
            background: rgba(217, 131, 46, 0.1);
        }

        .tab-button.active {
            color: var(--color-bg-alt);
        }

        .tab-indicator {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: var(--color-gradient-1);
            border-radius: 0;
            transition: var(--transition);
            z-index: 1;
            box-shadow: var(--shadow-glow);
        }

        .tabs-content {
            padding: 40px;
            min-height: 400px;
            background: var(--color-bg-alt);
        }

        .tab-panel {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }

        .tab-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-content {
            max-width: 600px;
        }

        .tab-content h2 {
            font-family: var(--font-family-serif);
            font-size: 2.5rem;
            color: var(--color-text-heading);
            margin-bottom: 1rem;
            background: var(--color-gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tab-content p {
            font-size: 1.1rem;
            line-height: var(--line-height);
            color: var(--color-text-body);
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
            margin: 2rem 0;
        }

        .feature-list li {
            padding: 0.5rem 0;
            color: var(--color-text-body);
            position: relative;
            padding-left: 2rem;
        }

        .feature-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--color-primary);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .cta-button {
            display: inline-block;
            padding: 12px 30px;
            background: var(--color-gradient-1);
            color: var(--color-bg-alt);
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-family: var(--font-family-sans);
            transition: var(--transition);
            box-shadow: var(--shadow-glow);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card-hover);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .stat {
            text-align: center;
            padding: 1.5rem;
            background: rgba(217, 131, 46, 0.08);
            border-radius: var(--border-radius);
            border: 1px solid var(--color-border);
            transition: var(--transition);
        }

        .stat:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-card);
        }

        .stat-number {
            font-family: var(--font-family-serif);
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-primary);
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--color-text-body);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .gallery-item {
            aspect-ratio: 4/3;
            background: var(--color-gradient-1);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-bg-alt);
            font-size: 3rem;
            transition: var(--transition);
            box-shadow: var(--shadow-card);
        }

        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-card-hover);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .tabs-container {
                margin: 10px;
                border-radius: var(--border-radius);
            }

            .tab-button {
                padding: 15px 20px;
                font-size: 14px;
            }

            .tabs-content {
                padding: 20px;
                min-height: 300px;
            }

            .tab-content h2 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .stat {
                padding: 1rem;
            }

            .stat-number {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .tab-button {
                padding: 12px 15px;
                font-size: 12px;
            }

            .tabs-content {
                padding: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Additional animations */
        .tab-button {
            position: relative;
            overflow: hidden;
        }

        .tab-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(217, 131, 46, 0.2), transparent);
            transition: left 0.5s;
        }

        .tab-button:hover::before {
            left: 100%;
        }/*
=================================================================
  Component: Q&A Section
=================================================================
*/
.qna-section {
  margin-top: 4rem;
  padding-top: 2rem;
  border-top: 1px solid var(--color-border);
}

.qna-post-box form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.qna-post-box textarea {
  width: 100%;
  min-height: 80px;
  padding: 0.8rem 1rem;
  border-radius: var(--border-radius);
  border: 1px solid var(--color-border);
  font-size: 1rem;
  font-family: var(--font-family);
  resize: vertical;
}
.qna-post-box button {
  align-self: flex-end; /* Pushes button to the right */
}

.qna-list {
  margin-top: 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.qna-item {
  background-color: var(--color-bg-light);
  padding: 1.5rem;
  border-radius: var(--border-radius);
}
.qna-author {
  font-weight: 700;
  color: var(--color-text);
  margin-bottom: 0.5rem;
}
.qna-text {
  line-height: 1.6;
}
.qna-meta {
  font-size: 0.85rem;
  color: var(--color-text-muted);
  margin-top: 1rem;
}
/* Style for replies/answers */
.qna-reply {
  background-color: var(--color-bg-white);
  border: 1px solid var(--color-border);
  margin-top: 1rem;
  margin-left: 2rem; /* Indent replies */
}
    </style> 
    
 <div class="tabs-container">
        <div class="tabs-header">
            <button class="tab-button active" data-tab="overview">Home</button>
            <button class="tab-button" data-tab="features">Q&A</button>
            <!-- <button class="tab-button" data-tab="analytics">About</button>
            <button class="tab-button" data-tab="gallery">Contact</button> -->
            <div class="tab-indicator"></div>
        </div>

        <div class="tabs-content">
            <div class="tab-panel active" id="overview">
                <div class="tab-content">
                    <h2><?php echo htmlspecialchars($current_session['title']); ?></h2>
                   <p class="text-muted"><?php echo htmlspecialchars($current_session['description']); ?></p>
                    
                </div>
            </div>

            <div class="tab-panel" id="features">
                <div class="tab-content">
                    <h2>Questions & Answers</h2>
                     <div class="qna-section" id="qna-section">
                        <h3></h3>
                        
                        <!-- Form to ask a new question -->
                        <div class="qna-post-box">
                            <form  id="qna-form" action="<?php echo BASE_URL; ?>controllers/qna.php" method="POST">
                                <input type="hidden" name="session_id" value="<?php echo $current_session_id; ?>">
                                <input type="hidden" name="parent_id" value="0">
                                
                                <textarea name="question_text" class="form-control" placeholder="Have a question? Ask here..." required></textarea><br>
                                <button type="submit" class="btn btn-primary">Post Question</button>
                                <br>
                            </form>
                        </div>

                        <!-- Display existing questions and answers -->
                        <div class="qna-list">
                            <?php if (empty($qna_list)): ?>
                                <p class="no-questions-message">No questions have been asked for this session yet. Be the first!</p>
                            <?php else: ?>
                                <?php foreach ($qna_list as $question): ?>
                                    <div class="qna-item qna-question">
                                        <div class="qna-author"><?php echo htmlspecialchars($question['name']); ?></div>
                                        <div class="qna-text"><?php echo nl2br(htmlspecialchars($question['question'])); ?></div>
                                        <div class="qna-meta"><?php echo date('M j, Y H:i', strtotime($question['created_at'])); ?></div>
                                        
                                        <!-- Replies/Answers -->
                                        <?php foreach ($question['replies'] as $reply): ?>
                                            <div class="qna-item qna-reply">
                                                <div class="qna-author"><?php echo htmlspecialchars($reply['name']); ?></div>
                                                <div class="qna-text"><?php echo nl2br(htmlspecialchars($reply['question'])); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    </form>
                </div>
            </div>

            <div class="tab-panel" id="analytics">
                <div class="tab-content">
                    <h2>About Us</h2>
                    <p>Learn more about our company and what makes us different.</p>
                    <div class="stats-grid">
                        <div class="stat">
                            <span class="stat-number">5+</span>
                            <span class="stat-label">Years</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">100+</span>
                            <span class="stat-label">Clients</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">20+</span>
                            <span class="stat-label">Team</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Support</span>
                        </div>
                    </div>
                    <p>We have been helping businesses succeed for over 5 years with our expert team.</p>
                    <a href="#" class="cta-button">Meet Our Team</a>
                </div>
            </div>

            <div class="tab-panel" id="gallery">
                <div class="tab-content">
                    <h2>Contact Us</h2>
                    <p>Get in touch with us today. We would love to hear from you.</p>
                    <div class="image-gallery">
                        <div class="gallery-item">📧</div>
                        <div class="gallery-item">📞</div>
                        <div class="gallery-item">📍</div>
                        <div class="gallery-item">💬</div>
                    </div>
                    <p>Email: info@company.com | Phone: (555) 123-4567</p>
                    <a href="#" class="cta-button">Send Message</a>
                </div>
            </div>
        </div>
    </div>
                    <a href="<?php echo $redirect_url;?>" class="btn btn-primary"    style="margin-top: 1rem;">
                        <i data-lucide="<?= $quizDone ? 'check-circle' : 'file-question' ?>"  style="display:inline-block; vertical-align: middle; margin-right: 0.5rem;"></i>
                        <?= $quizDone ? 'View Results' : 'Take Post-Test' ?>
                </a>
                   
                <?php else: ?>
                    <div class="alert alert-danger">No session selected or found.</div>
                <?php endif; ?>
            </div>

            <!-- Sidebar: List of all sessions -->
            <aside class="gq-sidebar fade-in-up" style="animation-delay: 0.2s;">
                <h3 style="margin-bottom: 1.5rem;">All Sessions</h3>
                <a href="<?php echo BASE_URL.'pages/gitas-quest/leaderboard.php';?>">Leaderboard</a>
                <ul class="gq-session-list">
                    <?php foreach ($all_sessions as $session): ?>
                        <li class="gq-session-item <?php if ($session['id'] == $current_session_id) echo 'active'; ?>">
                            <a href="sessions.php?id=<?php echo $session['id']; ?>">
                                <span class="session-number">Chapter <?php echo $session['session_number']; ?></span>
                                <span class="session-title"><?php echo htmlspecialchars($session['title']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </div>
    </div>
</section>
 <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanels = document.querySelectorAll('.tab-panel');
            const tabIndicator = document.querySelector('.tab-indicator');

            function setActiveTab(targetTab) {
                // Remove active class from all buttons and panels
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanels.forEach(panel => panel.classList.remove('active'));

                // Add active class to clicked button and corresponding panel
                const activeButton = document.querySelector(`[data-tab="${targetTab}"]`);
                const activePanel = document.getElementById(targetTab);
                
                activeButton.classList.add('active');
                activePanel.classList.add('active');

                // Move indicator
                updateIndicator(activeButton);
            }

            function updateIndicator(activeButton) {
                const buttonRect = activeButton.getBoundingClientRect();
                const headerRect = activeButton.parentElement.getBoundingClientRect();
                
                const left = buttonRect.left - headerRect.left;
                const width = buttonRect.width;

                tabIndicator.style.left = left + 'px';
                tabIndicator.style.width = width + 'px';
            }

            // Initialize indicator position
            const activeButton = document.querySelector('.tab-button.active');
            updateIndicator(activeButton);

            // Add click event listeners
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.getAttribute('data-tab');
                    setActiveTab(targetTab);
                });
            });

            // Update indicator on window resize
            window.addEventListener('resize', () => {
                const activeButton = document.querySelector('.tab-button.active');
                updateIndicator(activeButton);
            });
        });// assets/js/main.js

// Wait for the entire webpage to load before running the script
// assets/js/main.js

// document.addEventListener('DOMContentLoaded', function() {
//     const qnaForm = document.getElementById('qna-form');
//     if (qnaForm) {
//         console.log("SUCCESS: Q&A form found. JavaScript is ready.");

//         qnaForm.addEventListener('submit', function(event) {
//             event.preventDefault();
//             console.log("Step 1: Form submitted. Preventing page reload.");

//             const formData = new FormData(qnaForm);
//             const formAction = qnaForm.getAttribute('action');
//             const submitButton = qnaForm.querySelector('button[type="submit"]');
            
//             submitButton.disabled = true;
//             submitButton.textContent = 'Posting...';

//             console.log("Step 2: Sending data to:", formAction);

//             fetch(formAction, {
//                 method: 'POST',
//                 body: formData
//             })
//             .then(response => {
//                 console.log("Step 3: Received a response from the server.");
//                 // We first get the raw text to see exactly what the server sent
//                 return response.text().then(text => {
//                     console.log("Raw Response Text:", text); // VERY IMPORTANT FOR DEBUGGING
//                     try {
//                         // Then we try to parse it as JSON
//                         return JSON.parse(text);
//                     } catch (e) {
//                         // If parsing fails, we know the PHP sent invalid JSON
//                         throw new Error("Invalid JSON response from server.");
//                     }
//                 });
//             })
//             .then(data => {
//                 console.log("Step 4: Parsed JSON data:", data);

//                 if (data.success) {
//                     console.log("SUCCESS: Data indicates success. Preparing to update the page.");
//                     const qnaList = document.getElementById('qna-list-container');
//                     if (!qnaList) {
//                         console.error("CRITICAL ERROR: Could not find element with ID 'qna-list-container'");
//                         return;
//                     }

//                     const newQuestionHTML = createQuestionHTML(data.question);
//                     const noQuestionsMessage = qnaList.querySelector('.no-questions-message');
//                     if (noQuestionsMessage) {
//                         noQuestionsMessage.remove();
//                     }

//                     qnaList.insertAdjacentHTML('beforeend', newQuestionHTML);
//                     console.log("SUCCESS: Added new question to the page.");

//                     qnaForm.reset();
//                 } else {
//                     // The PHP script returned a controlled error
//                     console.error("SERVER-SIDE ERROR:", data.error);
//                     alert('Error: ' + data.error);
//                 }
//             })
//             .catch(error => {
//                 // This catches network errors OR the "Invalid JSON" error from above
//                 console.error("CRITICAL CATCH ERROR:", error);
//                 alert("An error occurred. Please check the console for details.");
//             })
//             .finally(() => {
//                 // This block runs whether the request succeeded or failed
//                 submitButton.disabled = false;
//                 submitButton.textContent = 'Post Question';
//             });
//         });
//     }
// });

// This helper function remains the same
function createQuestionHTML(question) {
    return `
        <div class="qna-item qna-question">
            <div class="qna-author">${question.name}</div>
            <div class="qna-text">${question.text}</div>
            <div class="qna-meta">${question.timestamp}</div>
        </div>
    `;
}
    
    
    </script>
<?php
require_once __DIR__ . '/../../includes/footer.php';
?>