-- Main users table to store all members and admins
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    age INT,
    location VARCHAR(100),
    center_affiliation VARCHAR(100),
    role ENUM('youth', 'congregation', 'admin') NOT NULL DEFAULT 'youth',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration VARCHAR(50),
    start_date DATE,
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    price DECIMAL(10, 2) DEFAULT 0.00,
    is_free BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Linking table for user enrollments in courses
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date DATE,
    progress INT DEFAULT 0, -- e.g., percentage completion
    payment_status ENUM('pending', 'completed', 'na') DEFAULT 'na',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Gita's Quest video sessions
CREATE TABLE gq_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_number INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(255) NOT NULL,
    release_date DATE
);

-- Gita's Quest questions (for both post-tests and final paper)
CREATE TABLE gq_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT, -- NULL if it's for the final paper
    question_text TEXT NOT NULL,
    question_type ENUM('mcq', 'subjective') NOT NULL,
    marks INT NOT NULL DEFAULT 10,
    is_final_paper BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (session_id) REFERENCES gq_sessions(id) ON DELETE SET NULL
);

-- Options for MCQ questions
CREATE TABLE gq_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES gq_questions(id) ON DELETE CASCADE
);

-- User progress and scores for Gita's Quest post-tests
CREATE TABLE gq_post_test_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id INT NOT NULL,
    score INT,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES gq_sessions(id) ON DELETE CASCADE
);

-- User submissions for the Final Paper
CREATE TABLE gq_final_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    mcq_option_id INT, -- For MCQ answers
    subjective_answer_path VARCHAR(255), -- For subjective file uploads
    subjective_marks INT, -- Manually graded by admin
    is_graded BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES gq_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (mcq_option_id) REFERENCES gq_options(id) ON DELETE SET NULL
);

-- Testimonials from users
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);