<?php require_once 'session_check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya hospital-Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

</head>

<body>
    <div class="main-wrapper">

        <div class="header">
            <!--logo branding-->
            <div class="header-left">
                <a href="admindashboard.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>Afya Hospital</span>
                </a>
            </div>
            <!--toggle bar assisting with navigation-->
            <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <!--Header content notifications and profile-->
                <ul class="nav user-menu float-right">
                    <!--Notifications-->
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
                    <!--Profile-->
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
                <!--profile for mobile phones-->
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

        <!--sidebar menu-->
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
                        <li class="active">
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
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Add Appointment</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form id="addAppointmentForm" autocomplete="off">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Appointment ID</label>
                                        <input class="form-control" type="text" name="appointment_id" readonly="" value="APT-0001">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Patient Name</label>
                                        <select class="select" name="patient_id">
                                            <option value="">Select</option>
                                            <option value="1">Jennifer Robinson</option>
                                            <option value="2">Terry Baker</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select class="select" name="department">
                                            <option value="">Select</option>
                                            <option value="Dentists">Dentists</option>
                                            <option value="Neurology">Neurology</option>
                                            <option value="Opthalmology">Opthalmology</option>
                                            <option value="Orthopedics">Orthopedics</option>
                                            <option value="Cancer Department">Cancer Department</option>
                                            <option value="ENT Department">ENT Department</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Doctor</label>
                                        <select class="select" name="doctor_id">
                                            <option value="">Select</option>
                                            <option value="1">Cristina Groves</option>
                                            <option value="2">Marie Wells</option>
                                            <option value="3">Henry Daniels</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" name="appointment_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Time</label>
                                        <div class="time-icon">
                                            <input type="text" class="form-control" name="appointment_time" id="datetimepicker3">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea cols="30" rows="4" class="form-control" name="message"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Appointment Status</label>
                                <select class="form-control" name="status">
                                    <option value="Scheduled" selected>Scheduled</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="m-t-20 text-center">
                                <button type="submit" class="btn btn-primary submit-btn">Create Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <div id="successModal" class="modal fade delete-modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="assets/img/sent1.png" alt="" width="50" height="46">
                    <h3>The appointment was added successfully</h3>
                    <div class="m-t-20"> <a href="#" class="btn btn-white" data-dismiss="modal">Close</a></div>
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
    <script src="assets/js/add-appointments.js"></script>
    <script>
        $(function () {
            $('.select').select2();
            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD' // Date format (YYYY-MM-DD)
            });
            $('#datetimepicker3').datetimepicker({
                format: 'HH:mm' // 24-hour time format (HH:mm)
            });
        });
    </script>
</body>

</html>