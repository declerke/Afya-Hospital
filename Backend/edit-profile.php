<?php require_once 'session_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <!-- Header section -->
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
                        <li class="active">
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
                        <li>
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
                    <div class="col-sm-12">
                        <h4 class="page-title">Edit Profile</h4>
                    </div>
                </div>
                <form id="edit-profile-form" enctype="multipart/form-data">
                    <div class="card-box">
                        <h3 class="card-title">Basic Information</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="profile-img-wrap">
                                    <img class="inline-block" id="profile-picture" src="assets/img/user.jpg" alt="Profile Picture">
                                    <div class="fileupload btn">
                                        <span class="btn-text">Edit</span>
                                        <input class="upload" type="file" name="profile_picture" id="profile-picture-input" title="Upload Profile Picture" accept="image/*">
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <input type="hidden" name="user_id" id="user_id">
                                        <input type="hidden" name="role" id="role">
                                        <input type="hidden" name="staff_id" id="staff_id">
                                        <input type="hidden" name="patient_id" id="patient_id">

                                        <div class="col-md-6">
                                            <div class="form-group form-focus">
                                                <label class="focus-label" for="first_name">First Name</label>
                                                <input type="text" id="first_name" name="first_name" class="form-control floating" placeholder="Enter First Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-focus">
                                                <label class="focus-label" for="last_name">Last Name</label>
                                                <input type="text" id="last_name" name="last_name" class="form-control floating" placeholder="Enter Last Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-focus">
                                                <label class="focus-label" for="birth_date">Birth Date</label>
                                                <div class="cal-icon">
                                                    <input id="birth_date" class="form-control floating datetimepicker" type="text" name="birth_date" placeholder="DD/MM/YYYY">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-focus select-focus">
                                                <label class="focus-label" for="gender">Gender</label>
                                                <select id="gender" name="gender" class="select form-control floating">
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-box">
                        <h3 class="card-title">Contact Information</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-focus">
                                    <label class="focus-label" for="address">Address</label>
                                    <input type="text" id="address" name="address" class="form-control floating" placeholder="Enter Address">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-focus">
                                    <label class="focus-label" for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control floating" placeholder="Enter Email Address">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-focus">
                                    <label class="focus-label" for="contact_number">Contact Number</label>
                                    <input type="text" id="contact_number" name="contact_number" class="form-control floating" placeholder="Enter Contact Number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center m-t-20">
                        <button class="btn btn-primary submit-btn" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" data-reff=""></div>
    <div id="successModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="assets/img/sent1.png" alt="" width="50" height="46">
                    <h3>Profile updated successfully!</h3>
                    <div class="m-t-20">
                        <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            // Initialize datetimepicker
            $('.datetimepicker').datetimepicker({
                format: 'DD/MM/YYYY'
            });

            // Add image preview functionality
            $('#profile-picture-input').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Yo, bro, please upload a valid image file (JPEG, PNG, or GIF).');
                        $(this).val(''); // Clear the input
                        return;
                    }

                    // Create a FileReader to read the image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile-picture').attr('src', e.target.result);
                    };
                    reader.onerror = function() {
                        alert('Yo, bro, there was an error reading the image file.');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Function to load user data
            function loadUserData() {
                $.ajax({
                    url: 'fetch_user_data.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            console.error('Error fetching user data:', response.error);
                            alert('Yo, bro, couldn’t load the user data: ' + response.error);
                            return;
                        }

                        const user = response.user;

                        // Log the user ID for debugging
                        console.log('Fetched User ID:', user.id);

                        // Populate form fields
                        $('#user_id').val(user.id);
                        $('#role').val(user.role);
                        $('#staff_id').val(user.staff_id);
                        $('#patient_id').val(user.patient_id);
                        $('#first_name').val(user.first_name);
                        $('#last_name').val(user.last_name);
                        $('#email').val(user.email);
                        $('#contact_number').val(user.contact_number);
                        $('#address').val(user.address);
                        $('#gender').val(user.gender.toLowerCase());

                        // Convert birth date from YYYY-MM-DD to DD/MM/YYYY
                        if (user.date_of_birth) {
                            const birthDate = moment(user.date_of_birth, 'YYYY-MM-DD');
                            $('#birth_date').val(birthDate.format('DD/MM/YYYY'));
                        }

                        // Set profile picture
                        if (user.profile_pic) {
                            $('#profile-picture').attr('src', user.profile_pic);
                        }

                        console.log('User data loaded:', user);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching user data:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                        alert('Yo, bro, couldn’t load the user data due to an AJAX error! Status: ' + xhr.status + ', Error: ' + error);
                    }
                });
            }

            // Load user data on page load
            loadUserData();

            // Handle form submission
            $('#edit-profile-form').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Convert birth date from DD/MM/YYYY to YYYY-MM-DD
                const birthDate = moment(formData.get('birth_date'), 'DD/MM/YYYY');
                if (birthDate.isValid()) {
                    formData.set('birth_date', birthDate.format('YYYY-MM-DD'));
                } else {
                    alert('Yo, bro, invalid birth date format! Please use DD/MM/YYYY (e.g., 21/05/2003).');
                    return;
                }

                $.ajax({
                    url: 'update-adminprofile.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            $('#successModal').modal('show');
                            setTimeout(function() {
                                $('#successModal').modal('hide');
                            }, 3000);
                            loadUserData(); // Reload the updated data
                        } else {
                            alert('Error updating profile: ' + (response.error || 'Unknown error'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error updating profile:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                        alert('Yo, bro, couldn’t update the profile due to an AJAX error! Status: ' + xhr.status + ', Error: ' + error + ', Details: ' + (xhr.responseText || 'No details available'));
                    }
                });
            });
        });
    </script>
</body>
</html>