<footer class="footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> ISKCON Mumbai. All rights reserved.</p>
    </div>
</footer>
<div class="modal-overlay" id="registrationModal">
    <div class="modal-container">
        <!-- Modal Header -->
        <div class="modal-header" style="display: block !important;">
            <button class="close-btn" onclick="hideModal()">✕</button>
            <h2> Gita Conquest Registration</h2>
            <p>Join the spiritual journey and unlock ancient wisdom</p>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <form class="auth-form fade-in-up" method="POST" id="registrationForm">

                <p style="text-align:center;width:100%;font-size:1.5rem;padding:0;margin:0;" class="form-group span-2">Create an account for future access</p>
                <!-- Hidden input to specify the action -->
                <input type="hidden" name="action" value="register">
                <div class="form-group span-2">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password_reg" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password_reg" name="confirm_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>



                <div class="form-group">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="state" class="form-label">State / Province</label>
                    <input type="text" id="state" name="state" class="form-control" required>
                </div>

                <!--<div class="form-group span-2">-->
                <!--    <label for="country" class="form-label">Country</label>-->
                <!--    <input type="text" id="country" name="country" class="form-control" required>-->
                <!--</div>-->



                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number </label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">Gender</label>
                    <select name="gender" class="form-select" id="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" id="age" name="age" class="form-control" min="10" max="100" required>
                </div>

                <div class="form-group span-2">
                    <label for="college_name" class="form-label">College/Company Name</label>
                    <input type="text" id="college_name" placeholder="e.g., New College of Engineering" name="college_name" class="form-control" required>
                </div>

                <div class="form-group span-2">
                    <label for="standard" class="form-label">Stream and Year of Study/ Job Designation</label>
                    <input type="text" placeholder="e.g., 11th Science, First Year BE, MBA" id="standard" name="standard" class="form-control" required>
                </div>

                <div class="form-group span-2">
                    <label for="iskconYears" class="form-label"> How long have you been in touch with ISKCON?</label>
                    <input type="text" class="form-control" id="iskconYears" name="iskconYears" placeholder="e.g., 6 months, 2 years, just started">
                </div>

                <div class="form-group span-2">
                    <label class="form-label">🧘 Are you doing any spiritual practices?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="chanting" name="spiritualPractices[]" value="Chanting">
                        <label class="form-check-label" for="chanting">Chanting</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="reading" name="spiritualPractices[]" value="Reading Gita">
                        <label class="form-check-label" for="reading">Reading Gita</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="templeVisit" name="spiritualPractices[]" value="Temple Visit">
                        <label class="form-check-label" for="templeVisit">Temple Visit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="otherPractice" name="spiritualPractices[]" value="Other">
                        <label class="form-check-label" for="otherPractice">Other</label>
                    </div>
                </div>

                <div class="form-group span-2">
                    <label for="kcLevel" class="form-label">🌿 Your Connection to Krishna Consciousness</label>
                    <select class="form-select" id="kcLevel" name="kcLevel" required>
                        <option value="">Select your connection</option>
                        <option value="New">New</option>
                        <option value="Occasional">Occasional</option>
                        <option value="Regular">Regular</option>
                    </select>
                </div>
                <div class="form-group span-2">
                    <label for="source" class="form-label">How did you get to know about us?</label>
                    <select id="source" name="source" class="form-control" required>
                        <option value="" disabled selected>Select an option</option>
                        <option value="classroom">Classroom announcement</option>
                        <option value="whatsapp">College WhatsApp group</option>
                        <option value="instagram">Instagram story</option>
                        <option value="facebook">Facebook ads</option>
                        <option value="friends">Friends and family</option>
                    </select>
                </div>

                <!--<div class="form-group span-2">-->
                <!--    <div class="terms-group">-->
                <!--        <input type="checkbox" name="terms" id="terms" required>-->
                <!--        <label for="terms">-->
                <!--            I agree to the <a href="#" target="_blank">Terms and Conditions</a> and understand that this registration is for the Gita ConQuest spiritual learning event.-->
                <!--        </label>-->
                <!--    </div>-->
                <!--</div>-->

                <div class="form-group span-2">
                    <button type="submit" class="btn btn-primary" style="width: 100%;"> Create Account & Register Gita ConQuest</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-overlay" id="succesModal">
    <div class="modal-container">
        <!-- Modal Header -->
        <div class="modal-header" style="display: block !important;">
            <button class="close-btn" onclick="hideModalSuccess()">✕</button>
            <h2> Gita ConQuest Registration</h2>
            <p>Join the spiritual journey and unlock ancient wisdom</p>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <h1>Registration Successfull</h1>
            <p>Thank you for registering for Gita ConQuest! </p>
            <p>You will receive a confirmation email shortly with event details.</p>
            <p>We look forward to seeing you at the event!</p>
            <!-- join our whatsapp group -->
            <!--<p>Join our WhatsApp group for updates: <a href="https://chat.whatsapp.com/KvybmzEXA838yqZA1q5ask?mode=ac_ts" target="_blank">Join WhatsApp Group</a></p>-->
            <a href="https://chat.whatsapp.com/KvybmzEXA838yqZA1q5ask?mode=ac_ts" class="whatsapp-btn floating-btn" target="_blank">
                💬 Join WhatsApp Group
            </a>
            <br>
            <br>

            <button class="btn btn-primary" onclick="hideModalSuccess()">Close</button>
        </div>
    </div>
</div>

<!-- At the end of pages/gitas-quest/final-paper.php -->

<!-- =================================================================== -->
<!-- NEW: DYNAMIC MODAL (Initially Hidden) -->
<!-- =================================================================== -->
<div class="modal-overlay" id="dynamic-modal-overlay" >
    <div class="modal-container">
        <div class="modal-header">
            <h2 id="modal-title">Default Title</h2>
        </div>
        <div class="modal-body" id="modal-body">
            <p>Default content...</p>
        </div>
        <div class="modal-footer" id="modal-footer">
            <!-- Buttons will be injected here by JavaScript -->
        </div>
    </div>
</div>
<script>
    // Inside assets/js/exam.js

    // --- DYNAMIC MODAL ENGINE ---

const dynamicModal = document.getElementById('dynamic-modal-overlay');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        const modalFooter = document.getElementById('modal-footer');
    /**
     * A powerful function to show a modal with any content and buttons.
     * @param {string} title - The text for the modal's title.
     * @param {string} contentHTML - The HTML content for the modal's body.
     * @param {Array<object>} buttons - An array of button objects, e.g., [{ text: 'OK', class: 'btn-primary', action: () => {...} }]
     */
    function showModal(title, contentHTML, buttons = []) {
        // console.log('Dynamic modal overlay:', dynamicModal);
        // console.log('Showing modal with title:', title, 'and content:', contentHTML);
        modalTitle.textContent = title;
        modalBody.innerHTML = contentHTML;
        modalFooter.innerHTML = ''; // Clear old buttons

        // Create and add the new buttons
        buttons.forEach(btnInfo => {
            const button = document.createElement('button');
            button.textContent = btnInfo.text;
            button.className = `btn ${btnInfo.class || 'btn-secondary'}`;
            button.addEventListener('click', btnInfo.action);
            modalFooter.appendChild(button);
        });
        dynamicModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        dynamicModal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // --- End of Dynamic Modal Engine ---
    let pass = document.getElementById('password_reg');
    let confirmPass = document.getElementById('confirm_password_reg');
    if (pass && confirmPass) {
        console.log('Password fields found', pass, confirmPass);
        pass.addEventListener('input', function() {
            if (pass.value !== confirmPass.value) {
                confirmPass.setCustomValidity("Passwords do not match");
            } else {
                confirmPass.setCustomValidity("");
            }
        });

        confirmPass.addEventListener('input', function() {
            if (pass.value !== confirmPass.value) {
                confirmPass.setCustomValidity("Passwords do not match");
            } else {
                confirmPass.setCustomValidity("");
            }
        });
    }

    // in get method if i use header('Location: gita_conquest_php.php?success=1'); then it will not work
    // so i will use echo json_encode(['status' => 'success']); and then in javascript i will handle the response
    // and show the success message
    // but in post method i will use header('Location: gita_conquest_php.php?success=1'); to redirect to the same page  
    // Show modal function
    // I want to show the modal after 3 seconds of page load
    if (window.location.search.includes('success=1')) {
        setTimeout(() => {
            showModalSuccess();
            // alert('Registration successful! Welcome to Gita Conquest. \n\nYou will receive a confirmation email shortly with event details.');
        }, 1000);
    }
    
    if (window.location.search.includes('name = parth')) {
        showModalR()
    }
    function showModalSuccess() {
        const modal = document.getElementById('succesModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function hideModalSuccess() {
        const modal = document.getElementById('succesModal');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function showModalR() {
        const modal = document.getElementById('registrationModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function hideModalR() {
        const modal = document.getElementById('registrationModal');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function handleSubmit(event) {
        event.preventDefault();

        // Get form data
        const formData = new FormData(event.target);
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');

        // Validate passwords match
        if (password !== confirmPassword) {
            alert('Passwords do not match. Please try again.');
            return;
        }

        // Simulate form submission
        alert('Registration successful! Welcome to Gita ConQuest. \n\nYou will receive a confirmation email shortly with event details.');
        hideModal();

        // Here you would typically send the data to your server
        console.log('Form data:', Object.fromEntries(formData));
    }

    // Close modal when clicking outside
    document.getElementById('registrationModal').addEventListener('click', function(event) {
        if (event.target === this) {
            hideModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideModal();
        }
    });

    // Auto-show modal after 3 seconds (for demo)
    // setTimeout(showModal, 3000);
</script>

<!-- Load Lucide Icons -->
<script>
    lucide.createIcons();
</script>