<?php
// pages/profile/view.php
require_once '../../includes/header.php';

// Security: User must be logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch combined user and profile data
$sql = "SELECT * from registrations WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);

// A helper function to create the first letter for the avatar
function get_initial($name) {
    return strtoupper(substr($name, 0, 1));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($user_data['name']); ?></title>
    <style>
        /* Profile Page Styles - Using Warm & Grounded Theme */
        
        /* Root Variables */
        :root {
            --color-primary: #d9832e;
            --color-primary-dark: #b86e26;
            --color-accent: #d9832e;
            --color-bg: #f9f5f0;
            --color-bg-alt: #ffffff;
            --color-text-heading: #2a2a2a;
            --color-text-body: #606060;
            --color-border: #e8e1d9;
            --font-family-serif: 'Playfair Display', 'Georgia', serif;
            --font-family-sans: 'Poppins', 'Inter', sans-serif;
            --line-height: 1.75;
            --border-radius: 8px;
            --shadow-card: 0 5px 15px rgba(0, 0, 0, 0.04);
            --shadow-card-hover: 0 10px 30px rgba(42, 42, 42, 0.1);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family-sans);
            line-height: var(--line-height);
            color: var(--color-text-body);
            background-color: var(--color-bg);
            -webkit-font-smoothing: antialiased;
        }

        /* Alert Success Message */
        .alert-success {
            background: linear-gradient(135deg, #f0f8f0, #e8f5e8);
            border: 2px solid #4caf50;
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            color: #2e7d32;
            font-weight: 500;
            box-shadow: var(--shadow-card);
            margin-bottom: 2rem;
        }

        /* Profile Section Container */
        .profile-section {
            background: var(--color-bg);
            min-height: 100vh;
            padding: 3rem 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Profile Layout - Two Column Grid */
        .profile-layout {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            align-items: start;
        }

        /* Left Column - Profile Summary Card */
        .profile-summary-card {
            background: var(--color-bg-alt);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: var(--shadow-card);
            text-align: center;
            border: 1px solid var(--color-border);
            transition: var(--transition);
            position: sticky;
            top: 2rem;
        }

        .profile-summary-card:hover {
            box-shadow: var(--shadow-card-hover);
            transform: translateY(-2px);
        }

        /* Profile Avatar */
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 24px rgba(217, 131, 46, 0.25);
            border: 4px solid var(--color-bg);
        }

        .profile-avatar span {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            font-family: var(--font-family-serif);
        }

        /* Profile Name and Email */
        .profile-name {
            font-family: var(--font-family-serif);
            font-size: 2rem;
            color: var(--color-text-heading);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .profile-email {
            color: var(--color-text-body);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        /* Right Column - Profile Details */
        .profile-details-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Info Cards */
        .info-card {
            background: var(--color-bg-alt);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--color-border);
            overflow: hidden;
            transition: var(--transition);
        }

        .info-card:hover {
            box-shadow: var(--shadow-card-hover);
            transform: translateY(-2px);
        }

        /* Info Card Header */
        .info-card-header {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--color-border);
        }

        .info-card-header h3 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            font-family: var(--font-family-serif);
        }

        /* Info Card Body */
        .info-card-body {
            padding: 2rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem 2rem;
            align-items: center;
        }

        .info-grid > div:nth-child(odd) {
            font-weight: 600;
            color: var(--color-text-heading);
            font-size: 1rem;
        }

        .info-grid > div:nth-child(even) {
            color: var(--color-text-body);
            font-size: 1rem;
            padding: 0.75rem 1rem;
            background: var(--color-bg);
            border-radius: var(--border-radius);
            border: 1px solid var(--color-border);
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-family: var(--font-family-sans);
            line-height: 1.2;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(217, 131, 46, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--color-primary-dark), #9e5a1f);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 131, 46, 0.35);
            color: white;
            text-decoration: none;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .profile-summary-card {
                position: relative;
                top: 0;
                padding: 2rem;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .profile-avatar span {
                font-size: 2.5rem;
            }
            
            .profile-name {
                font-size: 1.75rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .info-grid > div:nth-child(odd) {
                font-size: 0.9rem;
            }
            
            .info-grid > div:nth-child(even) {
                font-size: 0.9rem;
                padding: 0.625rem 0.875rem;
            }
            
            .info-card-header {
                padding: 1.25rem 1.5rem;
            }
            
            .info-card-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .profile-section {
                padding: 2rem 0;
            }
            
            .profile-summary-card {
                padding: 1.5rem;
            }
            
            .profile-avatar {
                width: 80px;
                height: 80px;
            }
            
            .profile-avatar span {
                font-size: 2rem;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
            
            .info-card-header h3 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <section class="profile-section">
        <div class="container">
            
            <?php
            // Display any success messages after an update
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>

            <div class="profile-layout">
                <!-- Left Column: User Summary Card -->
                <aside class="profile-summary-card">
                    <div class="profile-avatar">
                        <span><?php echo get_initial($user_data['name']); ?></span>
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($user_data['name']); ?></h2>
                    <p class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></p>
                    <a href="edit.php" class="btn btn-primary">Edit Profile</a>
                </aside>

                <!-- Right Column: Detailed Information -->
                <div class="profile-details-content">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h3>Personal Information</h3>
                        </div>
                        <div class="info-card-body">
                            <div class="info-grid">
                                <div>Phone Number:</div>
                                <div><?php echo htmlspecialchars($user_data['phone'] ?? 'Not provided'); ?></div>
                                
                                <div>Age:</div>
                                <div><?php echo htmlspecialchars($user_data['age'] ?? 'Not provided'); ?></div>

                                <div>Gender:</div>
                                <div><?php echo htmlspecialchars($user_data['gender'] ?? 'Not provided'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <h3>Location Details</h3>
                        </div>
                        <div class="info-card-body">
                            <div class="info-grid">
                                <div>City:</div>
                                <div><?php echo htmlspecialchars($user_data['city'] ?? 'Not provided'); ?></div>

                                <div>State / Province:</div>
                                <div><?php echo htmlspecialchars($user_data['state'] ?? 'Not provided'); ?></div>

                                <div>Country:</div>
                                <div><?php echo htmlspecialchars($user_data['country'] ?? 'Not provided'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

<?php
require_once '../../includes/footer.php';
?>