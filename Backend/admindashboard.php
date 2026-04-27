<?php require_once 'session_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya hospital-Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <!--Header section-->
        <div class="header">
            <!--logo branding-->
            <div class="header-left">
                <a href="admindashboard.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>Afya Hospital</span>
                </a>
            </div>
            <!--toggle bar assisting with navigagtion-->
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

        <!--dashboard wrapper-->
        <div class="page-wrapper">
            <div class="content">
                <!--1st row users-->
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg1"><i class="fa fa-user-md" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3 class="doctor-count">Loading...</h3>
                                <span class="widget-title1">Doctors <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg2"><i class="fa fa-user-o"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3 class="patient-count">Loading...</h3>
                                <span class="widget-title2">Patients <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg3"><i class="fa fa-stethoscope" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right"> 
                                <h3 class="nurse-count">Loading...</h3>
                                <span class="widget-title3">Nurses <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg4"><i class="fa fa-user-plus" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3 class="staff-count">Loading...</h3>   
                                <span class="widget-title4">Hospital Staff <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--2nd row graphs-->
                <div class="row">
                    <!--1st graph card-->
                    <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="chart-title">
                                    <h4>Patient Total</h4>
                                    <span class="float-right"><i class="fa fa-caret-up" aria-hidden="true"></i> 15% Higher than Last Month</span>
                                </div>    
                                <canvas id="linegraph" height="300" width="600"></canvas> 
                            </div>
                        </div>
                    </div>
                    <!--2st graph card-->
                    <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="chart-title">
                                    <h4>Patients In</h4>
                                    <div class="float-right">
                                        <ul class="chat-user-total">
                                            <li><i class="fa fa-circle" style="color: #4bc0c0;" aria-hidden="true"></i> ICU</li>
                                            <li><i class="fa fa-circle" style="color: #ffce56;" aria-hidden="true"></i> OPD</li>
                                        </ul>
                                    </div>
                                </div>    
                                <canvas id="bargraph" height="300" width="600"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!--3rd row Appointments and Doctors-->
                <div class="row">
                    <!--Card Appointments-->
                    <div class="col-12 col-md-6 col-lg-8 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">Upcoming Appointments</h4> <a href="appointments.php" class="btn btn-primary float-right">View all</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="upcoming-appointments-table">
                                        <tbody>
                                            <!-- Dynamic rows will be inserted here by app.js -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Card Doctors-->
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                        <div class="card member-panel">
                            <div class="card-header bg-white">
                                <h4 class="card-title mb-0">Doctors</h4>
                            </div>
                            <div class="card-body">
                                <ul class="contact-list">
                                    <!-- Dynamic content will be inserted here by app.js -->
                                    <li class="notification-message">
                                        <p class="text-center">Loading doctors...</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer text-center bg-white">
                                <a href="doctors.php" class="text-muted">View all Doctors</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!--4th row New Patients and Hospital Management-->
                <div class="row">
                    <!--New Patients-->
                    <div class="col-12 col-md-6 col-lg-8 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">New Patients </h4> <a href="patients.php" class="btn btn-primary float-right">View all</a>
                            </div>
                            <div class="card-block">
                                <div class="table-responsive">
                                    <table class="table mb-0 new-patient-table">
                                        <tbody>
                                            <!-- Dynamic rows will be inserted here by app.js -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Hospital Management-->
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                        <div class="hospital-barchart">
                            <h4 class="card-title d-inline-block">Hospital Management</h4>
                        </div>
                        <div class="bar-chart">
                            <div class="legend">
                                <div class="item">
                                    <h4>Level1</h4>
                                </div>
                                <div class="item">
                                    <h4>Level2</h4>
                                </div>
                                <div class="item text-right">
                                    <h4>Level3</h4>
                                </div>
                                <div class="item text-right">
                                    <h4>Level4</h4>
                                </div>
                            </div>
                            <div class="chart clearfix">
                                <div class="item">
                                    <div class="bar">
                                        <span class="percent">16%</span>
                                        <div class="item-progress" data-percent="16">
                                            <span class="title">OPD Patient</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="bar">
                                        <span class="percent">71%</span>
                                        <div class="item-progress" data-percent="71">
                                            <span class="title">New Patient</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="bar">
                                        <span class="percent">82%</span>
                                        <div class="item-progress" data-percent="82">
                                            <span class="title">Laboratory Test</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="bar">
                                        <span class="percent">67%</span>
                                        <div class="item-progress" data-percent="67">
                                            <span class="title">Treatment</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="bar">
                                        <span class="percent">30%</span>                                    
                                        <div class="item-progress" data-percent="30">
                                            <span class="title">Discharge</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/Chart.bundle.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        (function(){function c(){var b=a.contentDocument||a.contentWindow.document;
        if(b){var d=b.createElement('script');
        d.innerHTML="window.__CF$cv$params={r:'91c3e5f09d5a0dec',t:'MTc0MTI4NTU1Mi4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";
        b.getElementsByTagName('head')[0].appendChild(d)}}
        if(document.body){var a=document.createElement('iframe');
        a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';
        a.style.visibility='hidden';document.body.appendChild(a);
        if('loading'!==document.readyState)c();
        else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);
        else{var e=document.onreadystatechange||function(){};
        document.onreadystatechange=function(b){e(b);
        'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();
    </script>
</body>
</html>