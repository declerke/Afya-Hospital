<?php
require_once 'session_check.php';
require_once 'config/db.php';

$patient_id    = $_SESSION['patient_id'];
$error_message = '';
$success_message = '';

// Load departments and doctors for the form dropdowns
$departments = [];
$doctors     = [];
try {
    $deptStmt = $pdo->query("SELECT DISTINCT department FROM doctors WHERE department IS NOT NULL ORDER BY department");
    $departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);

    $docStmt = $pdo->query("SELECT id, first_name, last_name, department FROM doctors ORDER BY first_name");
    $doctors = $docStmt->fetchAll();
} catch (PDOException $e) {
    // Non-fatal
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_text = strip_tags(trim($_POST['message'] ?? ''));
    $rating        = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $feedback_type = strip_tags(trim($_POST['feedback_type'] ?? 'General'));

    if (empty($feedback_text)) {
        $error_message = "Please provide your feedback.";
    } else {
        $rating = ($rating && $rating >= 1 && $rating <= 5) ? $rating : null;
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO feedback (patient_id, feedback_text, rating) VALUES (?, ?, ?)"
            );
            $stmt->execute([$patient_id, $feedback_text, $rating]);
            $success_message = "Thank you for your feedback!";
        } catch (PDOException $e) {
            $error_message = "Could not submit feedback. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Afya Hospital</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0087ff;
            --primary-dark: #0066cc;
            --secondary-color: #00b8d4;
            --text-color: #333333;
            --light-text: #666666;
            --white: #ffffff;
            --light-gray: #f5f5f5;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;
            --blue-700: #1d4ed8;
            --red-100: #fee2e2;
            --red-500: #ef4444;
            --green-100: #d1fae5;
            --green-500: #10b981;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 0.375rem;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            color: var(--text-color);
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header Styles */
        header {
            background-color: var(--white);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
        }

        .logo a {
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo-blue {
            color: var(--primary-color);
        }

        .logo-dark {
            color: var(--gray-800);
        }

        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 1.5rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--blue-50);
        }

        .phone-number {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-700);
            font-weight: 500;
        }

        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            gap: 0.25rem;
            cursor: pointer;
        }

        .mobile-menu-btn span {
            display: block;
            width: 1.5rem;
            height: 0.125rem;
            background-color: var(--gray-700);
            transition: all 0.3s;
        }

        /* Page Section Styles */
        .page-section {
            padding: 4rem 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2rem;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--gray-600);
            font-size: 1.125rem;
        }

        /* Feedback Form Styles */
        .feedback-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 0.5rem;
            box-shadow: var(--shadow-md);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: var(--green-100);
            color: #065f46;
        }
        
        .alert-danger {
            background-color: var(--red-100);
            color: #b91c1c;
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--gray-700);
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-700);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.375rem;
            font-family: inherit;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--blue-500);
            box-shadow: 0 0 0 3px rgba(0, 135, 255, 0.2);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }
        
        .rating-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .rating-stars {
            display: flex;
            gap: 0.5rem;
        }
        
        .rating-stars i {
            font-size: 1.5rem;
            color: var(--gray-300);
            cursor: pointer;
        }
        
        .rating-stars i.active {
            color: #fbbf24;
        }
        
        .rating-text {
            font-size: 0.875rem;
            color: var(--gray-500);
        }
        
        .conditional-field {
            display: none;
        }
        
        .conditional-field.active {
            display: block;
        }

        /* Footer Styles */
        footer {
            background-color: var(--gray-800);
            color: var(--white);
            padding: 4rem 0 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-logo .logo-white {
            color: var(--white);
        }

        .footer-about p {
            color: var(--gray-400);
            margin-bottom: 1.5rem;
        }

        .footer-social {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: var(--white);
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .social-link:hover {
            background-color: var(--primary-color);
        }

        .footer-links h3,
        .footer-services h3,
        .footer-contact h3 {
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
            color: var(--white);
        }

        .footer-links ul,
        .footer-services ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li,
        .footer-services li {
            margin-bottom: 0.75rem;
        }

        .footer-links a,
        .footer-services a {
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover,
        .footer-services a:hover {
            color: var(--primary-color);
        }

        .contact-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .contact-item i {
            color: var(--primary-color);
        }

        .contact-item p {
            color: var(--gray-400);
            margin: 0;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom p {
            color: var(--gray-400);
            margin: 0;
        }

        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-bottom-links a {
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-bottom-links a:hover {
            color: var(--primary-color);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-links, .header-right {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .form-row {
                flex-direction: column;
                gap: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .footer-bottom-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.html">
                    <span class="logo-blue">Afya</span>
                    <span class="logo-dark">Hospital</span>
                </a>
            </div>
            
            <nav>
                <ul class="nav-links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="medical-history.php">Medical History</a></li>
                    <li><a href="billing.php">Billing</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                </ul>
            </nav>
            
            <div class="header-right">
                <a href="book-appointment.php" class="btn btn-primary">Book Appointment</a>
                <div class="phone-number">
                    <i class="fas fa-phone-alt"></i>
                    <span>1-800-AFYA</span>
                </div>
            </div>
            
            <div class="mobile-menu-btn">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>
    
    <!-- Feedback Section -->
    <section class="page-section">
        <div class="container">
            <div class="section-header">
                <h2>Feedback</h2>
                <p>We value your opinion and would love to hear from you</p>
            </div>
            
            <div class="feedback-container">
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <form action="feedback.php" method="POST">
                    <h3 class="form-title">Share Your Experience</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="feedback_type">Feedback Type <span class="text-red-500">*</span></label>
                            <select id="feedback_type" name="feedback_type" required>
                                <option value="">Select Feedback Type</option>
                                <option value="General" <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'General') ? 'selected' : ''; ?>>General Feedback</option>
                                <option value="Compliment" <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'Compliment') ? 'selected' : ''; ?>>Compliment</option>
                                <option value="Complaint" <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'Complaint') ? 'selected' : ''; ?>>Complaint</option>
                                <option value="Suggestion" <?php echo (isset($_POST['feedback_type']) && $_POST['feedback_type'] == 'Suggestion') ? 'selected' : ''; ?>>Suggestion</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row conditional-field" id="department-field">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select id="department" name="department">
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept; ?>" <?php echo (isset($_POST['department']) && $_POST['department'] == $dept) ? 'selected' : ''; ?>><?php echo $dept; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="doctor_id">Doctor</label>
                            <select id="doctor_id" name="doctor_id">
                                <option value="">Select Doctor</option>
                                <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['id']; ?>" data-department="<?php echo $doctor['department']; ?>" <?php echo (isset($_POST['doctor_id']) && $_POST['doctor_id'] == $doctor['id']) ? 'selected' : ''; ?>>Dr. <?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group conditional-field" id="rating-field">
                        <label>Rating</label>
                        <div class="rating-container">
                            <div class="rating-stars">
                                <i class="fas fa-star" data-rating="1"></i>
                                <i class="fas fa-star" data-rating="2"></i>
                                <i class="fas fa-star" data-rating="3"></i>
                                <i class="fas fa-star" data-rating="4"></i>
                                <i class="fas fa-star" data-rating="5"></i>
                            </div>
                            <div class="rating-text">Click to rate</div>
                            <input type="hidden" name="rating" id="rating" value="<?php echo isset($_POST['rating']) ? $_POST['rating'] : '0'; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Feedback</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-logo">
                        <span class="logo-blue">Afya</span>
                        <span class="logo-white">Hospital</span>
                    </div>
                    <p>Providing quality healthcare services with a patient-centered approach. Our team of experts is dedicated to your well-being.</p>
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="services.html">Services</a></li>
                        <li><a href="doctors.html">Doctors</a></li>
                        <li><a href="book-appointment.php">Appointments</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-services">
                    <h3>Our Services</h3>
                    <ul>
                        <li><a href="services.html#cardiology">Cardiology</a></li>
                        <li><a href="services.html#neurology">Neurology</a></li>
                        <li><a href="services.html#pediatrics">Pediatrics</a></li>
                        <li><a href="services.html#orthopedics">Orthopedics</a></li>
                        <li><a href="services.html#general-medicine">General Medicine</a></li>
                        <li><a href="services.html#surgery">Surgery</a></li>
                    </ul>
                </div>
                
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>123 Hospital Road, Nairobi, Kenya</p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <p>+254 712 345 678</p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <p>info@afyahospital.com</p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Afya Hospital. All Rights Reserved.</p>
                <div class="footer-bottom-links">
                    <a href="privacy-policy.html">Privacy Policy</a>
                    <a href="terms-of-service.html">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const navLinks = document.querySelector('.nav-links');
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                });
            }
            
            // Feedback type conditional fields
            const feedbackType = document.getElementById('feedback_type');
            const departmentField = document.getElementById('department-field');
            const ratingField = document.getElementById('rating-field');
            
            feedbackType.addEventListener('change', function() {
                if (this.value === 'Compliment' || this.value === 'Complaint') {
                    departmentField.classList.add('active');
                    ratingField.classList.add('active');
                } else {
                    departmentField.classList.remove('active');
                    ratingField.classList.remove('active');
                }
            });
            
            // Initialize conditional fields based on initial value
            if (feedbackType.value === 'Compliment' || feedbackType.value === 'Complaint') {
                departmentField.classList.add('active');
                ratingField.classList.add('active');
            }
            
            // Department and doctor filtering
            const departmentSelect = document.getElementById('department');
            const doctorSelect = document.getElementById('doctor_id');
            
            departmentSelect.addEventListener('change', function() {
                const selectedDepartment = this.value;
                
                // Reset doctor selection
                doctorSelect.value = '';
                
                // Show/hide doctors based on department
                Array.from(doctorSelect.options).forEach(option => {
                    if (option.value === '') return; // Skip the placeholder option
                    
                    const doctorDepartment = option.dataset.department;
                    
                    if (!selectedDepartment || selectedDepartment === doctorDepartment) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
            
            // Star rating
            const stars = document.querySelectorAll('.rating-stars i');
            const ratingInput = document.getElementById('rating');
            const ratingText = document.querySelector('.rating-text');
            
            // Rating text options
            const ratingTexts = [
                'Click to rate',
                'Poor',
                'Fair',
                'Good',
                'Very Good',
                'Excellent'
            ];
            
            // Set initial rating if available
            if (ratingInput.value > 0) {
                updateStars(ratingInput.value);
            }
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;
                    updateStars(rating);
                });
                
                star.addEventListener('mouseover', function() {
                    const rating = this.dataset.rating;
                    highlightStars(rating);
                });
                
                star.addEventListener('mouseout', function() {
                    resetStars();
                    if (ratingInput.value > 0) {
                        updateStars(ratingInput.value);
                    }
                });
            });
            
            function updateStars(rating) {
                stars.forEach(star => {
                    if (star.dataset.rating <= rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
                
                ratingText.textContent = ratingTexts[rating];
            }
            
            function highlightStars(rating) {
                stars.forEach(star => {
                    if (star.dataset.rating <= rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
                
                ratingText.textContent = ratingTexts[rating];
            }
            
            function resetStars() {
                stars.forEach(star => {
                    star.classList.remove('active');
                });
                
                ratingText.textContent = ratingTexts[0];
            }
        });
    </script>
</body>
</html>