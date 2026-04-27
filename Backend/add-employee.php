<?php require_once 'session_check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Add Employee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>

<body>
    <div class="main-wrapper">
        
        <div class="header">
            <div class="header-left">
                <a href="admindashboard.php" class="logo">
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
                        <div class="topnav-dropdown-header">
                            <span>Notifications</span>
                        </div>
                        <div class="drop-scroll">
                            <ul class="notification-list" id="notification-list">
                                <li class="notification-message">
                                    <p class="text-center">Loading notifications...</p>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="activities.php">View all Notifications</a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                            <span class="status online"></span>
                        </span>
                        <span>Admin</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php">My Profile</a>
                        <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                    <a class="dropdown-item" href="profile.php">Settings</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li>
                            <a href="admindashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
                        <li>
                            <a href="doctors.php"><i class="fa fa-user-md"></i> <span>Doctors</span></a>
                        </li>
                        <li>
                            <a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a>
                        </li>
                        <li>
                            <a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a>
                        </li>
                        <li>
                            <a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Doctor Schedule</span></a>
                        </li>
                        <li>
                            <a href="auditlogs.php"><i class="fa fa-clipboard"></i> <span>Audit Logs</span></a>
                        </li>
                        <li>
                            <a href="chatbotlogs.php"><i class="fas fa-robot"></i> <span>Chatbot Logs</span></a>
                        </li>
                        <li class="active">
                            <a href="employees.php"><i class="fa fa-user"></i> <span>Employees</span></a>
                        </li>
                        <li>
                            <a href="dataaccesslogs.php"><i class="fa fa-database"></i> <span>Data Access Logs</span></a>
                        </li>
                        <li>
                            <a href="feedback.php"><i class="fa fa-star"></i> <span>Feedback</span></a>
                        </li>
                        <li>
                            <a href="users.php"><i class="fa fa-users"></i> <span>Users</span></a>
                        </li>
                        <li>
                            <a href="activities.php"><i class="fa fa-bell-o"></i> <span>Notifications</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Add Employee</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form id="add-employee-form" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Profile Picture -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Profile Picture</label>
                                        <div class="profile-upload">
                                            <div class="upload-img">
                                                <img id="profile-pic-preview" src="assets/img/user.jpg" alt="Profile Picture" class="rounded-circle" width="100" height="100">
                                            </div>
                                            <div class="upload-input">
                                                <input type="file" class="form-control" id="profile-pic" name="profile_pic" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role (New, Required) -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select class="select" id="role" name="role">
                                            <option value="">Select Role</option>
                                            <option value="Admin">Admin (e.g., AD-01-001-2025)</option>
                                            <option value="Doctor">Doctor (e.g., DR-01-001-2025)</option>
                                            <option value="Nurse">Nurse (e.g., NR-01-001-2025)</option>
                                            <option value="Receptionist">Receptionist (e.g., REC-01-001-2025)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Staff ID (Autogenerated) -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Staff ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="staff-id" name="staff_id" readonly>
                                        <small class="form-text text-muted">Select a role first to autogenerate Staff ID.</small>
                                    </div>
                                </div>

                                <!-- First Name -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="first-name" name="first_name">
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input class="form-control" type="text" id="last-name" name="last_name">
                                    </div>
                                </div>

                                <!-- Department (Dynamic for Doctor) -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select class="select" id="department" name="department" disabled>
                                            <option value="">Select Department</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" id="email" name="email">
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input class="form-control" type="text" id="phone" name="contact_number">
                                    </div>
                                </div>

                                <!-- Date of Birth -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date of Birth</label>
                                        <input class="form-control" type="text" id="dob" name="date_of_birth" placeholder="DD-MM-YYYY">
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select class="select" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit">Create Employee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select').select2();

            // Initialize Datepicker
            $('#dob').datetimepicker({
                format: 'DD-MM-YYYY',
                maxDate: moment(),
                useCurrent: false
            });

            $('#role').on('change', function() {
                const role = $(this).val();
                const $staffId = $('#staff-id');
                const $department = $('#department');

                if (!role) {
                    $staffId.val('');
                    $department.prop('disabled', true).val('').trigger('change');
                    alert('Please select a role first before proceeding.');
                    return;
                }

                // Fetch next Staff ID
                $.ajax({
                    url: 'get_next_staff_id.php',
                    method: 'GET',
                    data: { role: role },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Staff ID Response for ' + role + ':', response);
                        if (response.error) {
                            alert('Error: ' + response.error);
                            $staffId.val('');
                        } else {
                            $staffId.val(response.staff_id);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error for ' + role + ':', status, error);
                        console.log('Response Text:', xhr.responseText);
                        alert('Failed to generate Staff ID');
                        $staffId.val('');
                    }
                });

    


                // Handle Department for Doctor
                if (role === 'Doctor') {
                    $department.prop('disabled', false);
                    $.ajax({
                        url: 'get_departments.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.error) {
                                alert('Error: ' + response.error);
                                return;
                            }
                            $department.empty();
                            $department.append('<option value="">Select Department</option>');
                            response.departments.forEach(function(dept) {
                                $department.append(`<option value="${dept}">${dept}</option>`);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            alert('Failed to load departments');
                        }
                    });
                } else {
                    $department.prop('disabled', true).val('').trigger('change');
                }
            });

            // Profile picture preview
            $('#profile-pic').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile-pic-preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Form submission
            $('#add-employee-form').on('submit', function(e) {
                e.preventDefault();
                const role = $('#role').val();
                if (!role) {
                    alert('Please select a role before submitting.');
                    return;
                }

                const formData = new FormData(this);

                $.ajax({
                    url: 'add_employee.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Employee added successfully');
                            window.location.href = 'employees.php';
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('Failed to add employee');
                    }
                });
            });
        });
    </script>
</body>

</html>