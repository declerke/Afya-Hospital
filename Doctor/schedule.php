<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include '../Backend/db_connect.php';

// Fetch the doctor's staff_id and doctor_id using the user_id from the session
$userId = $_SESSION['user_id']; // This is users.id
$doctorScheduleId = $_SESSION['doctor_id']; // This is doctors.id (already set during login)
$staffId = null;

try {
    // Step 1: Fetch staff_id from users table using users.id
    $stmt = $pdo->prepare("SELECT staff_id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $staffId = $stmt->fetchColumn();

    if (!$staffId) {
        error_log("No staff_id found for user_id: $userId");
        $staffId = 'N/A';
        die("Error: Staff ID not found for this user.");
    }

    // Step 2: No need to fetch doctor_id since it's already in $_SESSION['doctor_id']
    if (!$doctorScheduleId) {
        error_log("No doctor_id found in session for user_id: $userId");
        $doctorScheduleId = 'N/A';
        die("Error: Doctor ID not found in session.");
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Error: Unable to fetch doctor information.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Doctor Schedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
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
                        <i class="fa fa-bell-o"></i> <span class="badge badge-pill bg-danger float-right" id="notification-badge">0</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header"><span>Notifications</span></div>
                        <div class="drop-scroll"><ul class="notification-list" id="notification-list"><li class="notification-message"><p class="text-center">Loading notifications...</p></li></ul></div>
                        <div class="topnav-dropdown-footer"><a href="notifications.php">View all Notifications</a></div>
                    </div>
                </li>
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img"><img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Doctor"><span class="status online"></span></span>
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
                        <li class="active"><a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Schedule</span></a></li>
                        <li><a href="medicalrecords.php"><i class="fas fa-file-medical"></i> <span>Medical Records</span></a></li>
                        <li><a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a></li>
                        <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
                        <li><a href="feedback.php"><i class="fa fa-comment"></i> <span>Feedback</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">My Schedule</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="add-schedule.php" class="btn btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Add Schedule</a>
                        <input type="text" id="scheduleSearchInput" class="form-control float-right mr-2" placeholder="Search schedules..." style="width: 200px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-border table-striped custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Notes</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                                <tbody id="scheduleTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center">Loading schedules...</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="pagination-controls mt-3" style="text-align: center;">
                                <button id="schedulePrevPage" class="btn btn-secondary">Previous</button>
                                <span id="schedulePageInfo"></span>
                                <button id="scheduleNextPage" class="btn btn-secondary">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="delete_schedule" class="modal fade delete-modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <img src="assets/img/sent.png" alt="" width="50" height="46">
                        <h3>Are you sure you want to delete this Schedule?</h3>
                        <div class="m-t-20">
                            <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                            <button type="button" id="confirmDeleteSchedule" class="btn btn-danger">Delete</button>
                        </div>
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
            let currentPage = 1;
            const itemsPerPage = 8;
            let schedules = [];
            let filteredSchedules = [];
            let scheduleToDelete = null;

            // Fetch schedules for the logged-in doctor
            function fetchSchedules() {
                $.ajax({
                    url: 'fetch_doctor_schedules.php',
                    method: 'GET',
                    data: { doctor_id: '<?php echo $doctorScheduleId; ?>' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            schedules = response.schedules;
                            filteredSchedules = schedules;
                            renderSchedules();
                        } else {
                            console.error('Error fetching schedules:', response.message);
                            $('#scheduleTableBody').html('<tr><td colspan="4" class="text-center">Error loading schedules.</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        $('#scheduleTableBody').html('<tr><td colspan="4" class="text-center">Error loading schedules.</td></tr>');
                    }
                });
            }

            // Render schedules with pagination
            function renderSchedules() {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const paginatedSchedules = filteredSchedules.slice(start, end);

                let html = '';
                if (paginatedSchedules.length === 0) {
                    html = '<tr><td colspan="6" class="text-center">No schedules found.</td></tr>';
                } else {
                    paginatedSchedules.forEach(schedule => {
                        html += `
                            <tr>
                                <td>${schedule.date}</td>
                                <td>${schedule.status}</td>
                                <td>${schedule.available_time}</td>
                                <td>${schedule.patient_name}</td>
                                <td>${schedule.notes}</td>
                                <td class="text-right">
                                    <a class="btn btn-primary btn-sm" href="edit-schedule.php?id=${schedule.id}"><i class="fa fa-edit"></i> Edit</a>
                                    <a class="btn btn-danger btn-sm delete-schedule" data-id="${schedule.id}" data-toggle="modal" data-target="#delete_schedule"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#scheduleTableBody').html(html);
                updatePagination();
            }

            // Update pagination controls
            function updatePagination() {
                const totalPages = Math.ceil(filteredSchedules.length / itemsPerPage);
                $('#schedulePageInfo').text(`Page ${currentPage} of ${totalPages}`);
                $('#schedulePrevPage').prop('disabled', currentPage === 1);
                $('#scheduleNextPage').prop('disabled', currentPage === totalPages || totalPages === 0);
            }

            // Search functionality
            $('#scheduleSearchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                filteredSchedules = schedules.filter(schedule =>
                    schedule.date.toLowerCase().includes(searchTerm) ||
                    schedule.status.toLowerCase().includes(searchTerm) ||
                    schedule.available_time.toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderSchedules();
            });

            // Pagination controls
            $('#schedulePrevPage').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderSchedules();
                }
            });

            $('#scheduleNextPage').on('click', function() {
                const totalPages = Math.ceil(filteredSchedules.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderSchedules();
                }
            });

            // Delete schedule
            $(document).on('click', '.delete-schedule', function() {
                scheduleToDelete = $(this).data('id');
            });

            $('#confirmDeleteSchedule').on('click', function() {
                if (scheduleToDelete) {
                    $.ajax({
                        url: 'delete_schedule.php',
                        method: 'POST',
                        data: { id: scheduleToDelete },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#delete_schedule').modal('hide');
                                fetchSchedules();
                            } else {
                                console.error('Error deleting schedule:', response.message);
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('Error deleting schedule.');
                        }
                    });
                }
            });

            // Initial fetch
            fetchSchedules();
        });
    </script>
</body>
</html>