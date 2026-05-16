<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include '../Backend/db_connect.php';

$doctorId = $_SESSION['doctor_id'];
$doctor   = null;
$success  = '';
$error    = '';

try {
    $stmt = $pdo->prepare("
        SELECT id, staff_id, first_name, last_name, department, date_of_birth, gender, address, email, contact_number, profile_pic
        FROM doctors WHERE id = ?
    ");
    $stmt->execute([$doctorId]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Doctor/edit-profile.php fetch: " . $e->getMessage());
}

if (!$doctor) {
    die("Doctor profile not found.");
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name     = strip_tags(trim($_POST['first_name'] ?? ''));
    $last_name      = strip_tags(trim($_POST['last_name'] ?? ''));
    $email          = trim($_POST['email'] ?? '');
    $contact_number = strip_tags(trim($_POST['contact_number'] ?? ''));
    $date_of_birth  = trim($_POST['date_of_birth'] ?? '');
    $gender         = trim($_POST['gender'] ?? '');
    $address        = strip_tags(trim($_POST['address'] ?? ''));
    $department     = strip_tags(trim($_POST['department'] ?? ''));

    if (empty($first_name) || empty($email) || empty($contact_number) || empty($department)) {
        $error = "First name, email, phone, and department are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/^[0-9+\- ]*$/', $contact_number)) {
        $error = "Invalid phone number format. Use digits, +, or - only.";
    } else {
        $current_pic = $doctor['profile_pic'];
        $new_pic     = $current_pic;

        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['profile_pic'];
            $allowed  = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024;

            if (!in_array($file['type'], $allowed)) {
                $error = "Only JPEG, PNG, and GIF images are allowed.";
            } elseif ($file['size'] > $max_size) {
                $error = "File size must be less than 5MB.";
            } else {
                $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_pic = 'doctor_' . $doctorId . '_' . time() . '.' . $ext;
                $dest    = __DIR__ . '/assets/img/' . $new_pic;
                if (!move_uploaded_file($file['tmp_name'], $dest)) {
                    $error   = "Failed to upload profile picture.";
                    $new_pic = $current_pic;
                }
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE doctors
                    SET first_name = ?, last_name = ?, email = ?, contact_number = ?,
                        date_of_birth = ?, gender = ?, address = ?, department = ?, profile_pic = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $first_name,
                    $last_name ?: null,
                    $email,
                    $contact_number,
                    $date_of_birth ?: null,
                    $gender ?: null,
                    $address ?: null,
                    $department,
                    $new_pic ?: null,
                    $doctorId
                ]);

                // Refresh
                $stmt = $pdo->prepare("SELECT id, staff_id, first_name, last_name, department, date_of_birth, gender, address, email, contact_number, profile_pic FROM doctors WHERE id = ?");
                $stmt->execute([$doctorId]);
                $doctor  = $stmt->fetch(PDO::FETCH_ASSOC);
                $success = "Profile updated successfully.";
            } catch (PDOException $e) {
                error_log("Doctor/edit-profile.php update: " . $e->getMessage());
                $error = "Could not update profile. Please try again.";
            }
        }
    }
}

// Resolve profile pic for display
$profile_pic_src = (!empty($doctor['profile_pic']) && file_exists(__DIR__ . '/assets/img/' . $doctor['profile_pic']))
    ? 'assets/img/' . htmlspecialchars($doctor['profile_pic'])
    : 'assets/img/user.jpg';

// Convert DB date (Y-m-d) to input[type=date] format
$dob_input = '';
if (!empty($doctor['date_of_birth'])) {
    $d = DateTime::createFromFormat('Y-m-d', $doctor['date_of_birth']);
    $dob_input = $d ? $d->format('Y-m-d') : $doctor['date_of_birth'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
<div class="main-wrapper">
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <a href="doctordashboard.php" class="logo">
                <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>Afya Hospital</span>
            </a>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
        <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
        <ul class="nav user-menu float-right">
            <li class="nav-item dropdown d-none d-sm-block">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                    <i class="fa fa-bell-o"></i>
                    <span class="badge badge-pill bg-danger float-right" id="notification-badge">0</span>
                </a>
                <div class="dropdown-menu notifications">
                    <div class="topnav-dropdown-header"><span>Notifications</span></div>
                    <div class="drop-scroll"><ul class="notification-list" id="notification-list"><li class="notification-message"><p class="text-center">Loading notifications...</p></li></ul></div>
                    <div class="topnav-dropdown-footer"><a href="notifications.php">View all Notifications</a></div>
                </div>
            </li>
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="<?php echo $profile_pic_src; ?>" width="24" alt="Doctor">
                        <span class="status online"></span>
                    </span>
                    <span>Doctor</span>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                    <a class="dropdown-item" href="../Backend/loginpage.php">Logout</a>
                </div>
            </li>
        </ul>
        <div class="dropdown mobile-user-menu float-right">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="profile.php">My Profile</a>
                <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                <a class="dropdown-item" href="../Backend/loginpage.php">Logout</a>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li><a href="doctordashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                    <li><a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
                    <li><a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Schedule</span></a></li>
                    <li><a href="medicalrecords.php"><i class="fas fa-file-medical"></i> <span>Medical Records</span></a></li>
                    <li><a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a></li>
                    <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
                    <li><a href="feedback.php"><i class="fa fa-comment"></i> <span>Feedback</span></a></li>
                    <li class="active"><a href="profile.php"><i class="fa fa-user"></i> <span>Profile</span></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-sm-7 col-6">
                    <h4 class="page-title">Edit Profile</h4>
                </div>
                <div class="col-sm-5 col-6 text-right m-b-30">
                    <a href="profile.php" class="btn btn-secondary btn-rounded"><i class="fa fa-arrow-left"></i> Back to Profile</a>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card-box">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Profile Picture -->
                        <div class="col-md-12 text-center mb-4">
                            <img id="preview-pic" src="<?php echo $profile_pic_src; ?>" alt="Profile"
                                 class="rounded-circle mb-2"
                                 style="width:100px;height:100px;object-fit:cover;">
                            <div>
                                <label class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-camera"></i> Change Photo
                                    <input type="file" name="profile_pic" accept="image/jpeg,image/png,image/gif"
                                           style="display:none;" onchange="previewImage(this)">
                                </label>
                            </div>
                            <small class="text-muted">JPEG, PNG or GIF — max 5MB</small>
                        </div>

                        <!-- First Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name"
                                       value="<?php echo htmlspecialchars($doctor['first_name']); ?>" required>
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="last_name"
                                       value="<?php echo htmlspecialchars($doctor['last_name'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email"
                                       value="<?php echo htmlspecialchars($doctor['email'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contact_number"
                                       value="<?php echo htmlspecialchars($doctor['contact_number'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth"
                                       value="<?php echo htmlspecialchars($dob_input); ?>">
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="">-- Select --</option>
                                    <option value="Male"   <?php echo ($doctor['gender'] ?? '') === 'Male'   ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($doctor['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other"  <?php echo ($doctor['gender'] ?? '') === 'Other'  ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="department"
                                       value="<?php echo htmlspecialchars($doctor['department'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" name="address"
                                       value="<?php echo htmlspecialchars($doctor['address'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-12 text-right">
                            <a href="profile.php" class="btn btn-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="sidebar-overlay" data-reff=""></div>
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/app.js"></script>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-pic').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
