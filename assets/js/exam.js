
document.addEventListener('DOMContentLoaded', function () {

    // --- Get all required elements ---
    const startBtn = document.getElementById('start-exam-btn');
    const startScreen = document.getElementById('exam-start-screen');
    const mainScreen = document.getElementById('exam-main-screen');
    const examEnvironment = document.getElementById('exam-environment');
    const fullscreenModal = document.getElementById('fullscreen-modal-overlay');
    const reEnterBtn = document.getElementById('re-enter-fullscreen-btn');
    const submitFromModalBtn = document.getElementById('submit-from-modal-btn');
    const EXAM_CONTROLLER_URL = "http://localhost/Frontend%201/iskcon-website/controllers/exam_controller.php";
    const HOME_URL = "http://localhost/Frontend%201/iskcon-website/";
    // --- State variables ---
    let questions = [];
    let questionIds = [];
    let questionTypes = [];
    let userAnswers = localStorage.getItem('userAnswers') ? JSON.parse(localStorage.getItem('userAnswers')) : {};
    let currentQuestionIndex = 0;
    let timerInterval;
    
   
    let attemptId = 0;
    let visited = [];
    let voilations = 0;
    const type = window.location.search.includes('type=text') ? 'text' : 'mcq';
    let time = type=='text'? 5400: 3600;
    let remainingTime = time;
    // --- Event Listeners ---
    if (startBtn) startBtn.addEventListener('click', startExam);
    if (document.getElementById('next-question-btn')) document.getElementById('next-question-btn').addEventListener('click', nextQuestion);
    if (document.getElementById('prev-question-btn')) document.getElementById('prev-question-btn').addEventListener('click', prevQuestion);
    if (document.getElementById('submit-exam-btn')) document.getElementById('submit-exam-btn').addEventListener('click', submitExam);
    if (reEnterBtn) reEnterBtn.addEventListener('click', reEnterFullscreen);
    if (submitFromModalBtn) submitFromModalBtn.addEventListener('click', submitExam);
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('visibilitychange', handleVisibilityChange);
    window.addEventListener('blur', handleFocusLoss);
    window.addEventListener('beforeunload', handleBeforeUnload);
    let isModalVisible = false;

    // --- CORE FUNCTIONS ---
    async function startExam() {
        try {
            const response = await fetch('../../controllers/exam_controller.php?action=start&type=' + type);
            const data = await response.json();

            if (!data.success) {
                alert('Error: ' + data.error);
                return;
            }

            questions = data.questions;
            questionIds = questions.map(q => q.id);
            questionTypes = questions.map(q => q.question_type);
            attemptId = data.attempt_id;

            await examEnvironment.requestFullscreen();

            startScreen.style.display = 'none';
            mainScreen.style.display = 'flex';
            buildNavigator();
            renderQuestion(currentQuestionIndex);

            startTimer(remainingTime);

        } catch (err) {
            console.error("Failed to start exam:", err);
            alert("Could not start the exam. Please check the console for details.");
        }
    }
    function handleBeforeUnload(event) {
        // This proctoring feature should ONLY be active when the exam is in progress.
        if (mainScreen.style.display === 'flex') {

            // To trigger the browser's confirmation dialog, you must call preventDefault()
            // and set a returnValue. This is a requirement for modern browsers.
            // event.preventDefault();

            // Some older browsers require the return value to be set directly on the event.
            // Modern browsers will show a generic message like "Changes you made may not be saved."
            // You cannot customize this message for security reasons.
            // event.returnValue = 'Are you sure you want to leave? Your exam progress will be lost.';

            // // You can return the string as well for maximum compatibility.
            // return 'Are you sure you want to leave? Your exam progress will be lost.';
        }

        // If the exam is not in progress (e.g., on the start screen or after submission),
        // the function does nothing, allowing the user to refresh or leave freely.
    }
    function handleFocusLoss() {
        console.log("Focus loss detected");
        // Check if the page is hidden OR if the window has lost focus.
        // Also check if the exam is in progress AND our modal isn't already visible.
        if ((document.hidden || !document.hasFocus()) && mainScreen.style.display === 'flex' && !isModalVisible) {
            console.log("Focus loss detected");
            // 1. Set the flag to true to prevent this from firing again.
            isModalVisible = true;

            // 2. Stop the timer immediately.
            clearInterval(timerInterval);
            if (document.fullscreenElement) {
                document.exitFullscreen();
            }
            // 3. Show our dynamic modal with the warning.
            showModal(
                "Warning: Focus Lost",
                "<p>You have navigated away from the exam window. This action is monitored.</p><p>To continue, you must re-enter the fullscreen exam environment.</p>",
                [
                    {
                        text: 'Resume Exam',
                        class: 'btn-primary',
                        action: reEnterFullscreen // We reuse the same function
                    }
                ]
            );
        }
        isModalVisible = false; // Reset the flag after handling focus loss
    }

    function handleVisibilityChange() {
        voilations++;
        // Check if the page is now hidden AND the exam is in progress.
        // 'document.hidden' is a boolean property that tells us if the page is the active tab.
        if (document.hidden && mainScreen.style.display === 'flex') {

            // 1. Stop the timer immediately.
            clearInterval(timerInterval);

            // 2. Show our dynamic modal with a specific warning message and a single action.
            showModal(
                "Warning: Tab Switch Detected",
                "<p>You have navigated away from the exam tab. This action may be monitored and could affect your final score.</p><p>To continue, you must re-enter the fullscreen exam environment.</p>",
                [
                    {
                        text: 'Resume Exam',
                        class: 'btn-primary',
                        action: reEnterFullscreen // We can reuse our existing function!
                    }
                ]
            );
        }
    }



    function buildNavigator() {
        const navList = document.getElementById('question-navigator');
        let navHTML = '';
        questions.forEach((q, index) => {
            navHTML += `<div class="nav-item-exam " data-index="${index}">Q${index + 1}</div>`;
        });
        navList.innerHTML = navHTML;
        console.log(navList.querySelectorAll('.nav-item-exam')[0].classList)
        navList.querySelectorAll('.nav-item-exam')[0].classList.add('active_index'); // Set first question as active
        navList.querySelectorAll('.nav-item-exam').forEach(item => {
            item.addEventListener('click', () => {
                renderQuestion(parseInt(item.dataset.index));
            });
        });
    }

    function updateNavigator() {
        const navItems = document.querySelectorAll('.nav-item-exam');
        console.log(userAnswers)
        navItems.forEach((item, index) => {
            item.classList.remove('active_index', 'answered', 'not-answered');
            
            console.log(visited.indexOf(index), 'visited', visited.indexOf(index) > -1,questionIds[index],userAnswers.hasOwnProperty(questionIds[index]))
            if (userAnswers.hasOwnProperty(questionIds[index]) && visited.indexOf(index) > -1&& type !='text') {



                item.classList.add('answered');

            } else {
                if (visited.indexOf(index) > -1 && type !='text') {
                    item.classList.add('not-answered');
                }
            }
            console.log(currentQuestionIndex)
            if (index === currentQuestionIndex) {
                item.classList.add('active_index');
            }
        });
    }

    // Inside exam.js, near your other variable declarations

    const pdfUploadInput = document.getElementById('pdf-upload-input');
    const pdfFileNameDisplay = document.getElementById('pdf-file-name-display');
    let subjectivePdfFile = null; // This will hold our single file

    // --- Add this to your EVENT LISTENERS section ---
    if (pdfUploadInput) {
        pdfUploadInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                subjectivePdfFile = file;
                pdfFileNameDisplay.innerHTML = `File: <strong>${file.name}</strong>`;
            } else {
                subjectivePdfFile = null;
                pdfFileNameDisplay.textContent = 'No file chosen.';
            }
        });
    }
    function nextQuestion() { renderQuestion(currentQuestionIndex + 1); }
    function prevQuestion() { renderQuestion(currentQuestionIndex - 1); }

    function handleFullscreenChange() {
        voilations++;
        console.log("Fullscreen change detected");
        if (!document.fullscreenElement && mainScreen.style.display === 'flex') {

            clearInterval(timerInterval);
            console.log("Fullscreen change detectessd");
            showModal(
                "You Have Exited Fullscreen",
                "<p>To ensure exam integrity, you must continue in fullscreen mode. Please choose an option below.</p>",
                [
                    {
                        text: 'Resume in Fullscreen',
                        class: 'btn-primary',
                        action: reEnterFullscreen // Calls the function below
                    },
                    {
                        text: 'Submit Exam Now',
                        class: 'btn-secondary',
                        action: submitExam // Calls your existing submit function
                    }
                ]
            );
        }
    }
    function startTimer(durationInSeconds) {
        let timer = durationInSeconds;
        const display = document.getElementById('timer-display');
        timerInterval = setInterval(() => {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;
            display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            if (--timer < 0) {
                clearInterval(timerInterval);
                // Auto-submit logic here
                alert("Time's up! Submitting your exam.");
            }
            remainingTime = timer; // Update remaining time
            localStorage.setItem('remainingTime', remainingTime);
        }, 1000);
    }

    function reEnterFullscreen() {
        examEnvironment.requestFullscreen().then(() => {
            hideModal();
            startTimer(remainingTime);
        }).catch(err => {
            alert("Could not re-enter full-screen. Your exam will be submitted.");
            submitExam();
        });
    }

    // Inside exam.js -> REPLACE this function

    // Inside assets/js/exam.js

    // 1. REPLACE the renderQuestion function
    function renderQuestion(index) {
        if (index < 0 || index >= questions.length) return;
        visited.push(index);
        currentQuestionIndex = index;
        const question = questions[index];
        const displayArea = document.getElementById('question-display-area');

        // This exam now ONLY shows MCQs.
        if (question.question_type === 'mcq') {
            let questionHTML = `<h3>Question ${index + 1}</h3><p>${question.question_text}</p>`;
            questionHTML += '<div class="options-container">';
            question.options.forEach(opt => {
                const isChecked = userAnswers[question.id] == opt.id ? 'checked' : '';
                questionHTML += `
                <div class="option">
                    <input type="radio" onchange="saveAnswer(event)" name="q${question.id}" id="opt${opt.id}" value="${opt.id}" data-question-id="${question.id}" ${isChecked}>
                    <label for="opt${opt.id}">${opt.option_text}</label>
                </div>`;
            });
            questionHTML += '</div>';
            displayArea.innerHTML = questionHTML;
        } else {
            // If a subjective question is somehow loaded, we'll just show a message.
            displayArea.innerHTML = `<h3>Question ${index + 1}</h3><p>${question.question_text}</p>
            <textarea class="form-control" onchange="saveAnswer(event)" style="margin-bottom:10px" name="answer[${question.id}]" data-question-id="${question.id}" required>${userAnswers[question.id]==undefined?'':userAnswers[question.id]}</textarea>
            `;
        }
        updateNavigator();
    }

    // 2. REPLACE the saveAnswer function
    window.saveAnswer = function (event) {
        const inputElement = event.target;
        const questionId = inputElement.dataset.questionId;
        // We only save the value (the option ID)
        console.log(inputElement.value,'inputElement.value')
        userAnswers[questionId] = inputElement.value;

        localStorage.setItem('userAnswers', JSON.stringify(userAnswers));
        updateNavigator();
    }

    // 3. REPLACE the submitExam function 
    async function submitExam() {
        clearInterval(timerInterval);
        hideModal();
        console.log(userAnswers,'userAnswers')
        // We now only send the attempt_id and the MCQ answers.
        const submissionData = {
            attempt_id: attemptId,
            mcq_answers: userAnswers
        };
        const file = type == 'text'? 'submit_text': 'submit_mcq';
        try {
            const response = await fetch(`${EXAM_CONTROLLER_URL}?action=${file}`, { // Note the new action name
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(submissionData)
            });
            const data = await response.json();
            console.log(data['attempt_id'],'data');
            if (data.success) {
                // SUCCESS! Redirect to the new PDF upload page.
                if (document.fullscreenElement) document.exitFullscreen();
                
                if(type == 'text') {
                    setInterval(() => { showModal("Subjective Question", "Please upload your subjective answer in the PDF format."); }, 1000);
                    
                    setInterval(() => { window.location.href = `${HOME_URL}pages/gitas-quest/upload_paper.php?attempt=${attemptId}`; }, 5000);
                    // return;
                    // window.location.href = `${HOME_URL}pages/gitas-quest/upload_paper.php?attempt=${attemptId}`;
                }else{
                setInterval(() => { showSuccessAndRedirect(attemptId);}, 1000);
                // alert("MCQ section submitted successfully! You will now be taken to the PDF upload page.");
                
                setInterval(() => { window.location.href = `${HOME_URL}pages/gitas-quest/index.php?attempt=${attemptId}`; }, 5000);
                     }
                // window.location.href = `${HOME_URL}pages/gitas-quest/upload_paper.php?attempt=${attemptId}`;
            } else {
                alert("Error submitting exam: " + data.error);
            }
        } catch (err) {
            alert("A network error occurred during submission.");
            console.error("Submission Error:", err);
        }
    }
    // Example of how to use the dynamic modal for another purpose
    function showSuccessAndRedirect(attemptId) {
        showModal(
            "Submission Successful!",
            "<p>Your answers have been recorded.</p>"
        );
    }
});