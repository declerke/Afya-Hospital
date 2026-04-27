<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include 'db_connect.php';

$doctorId = $_SESSION['doctor_id'];

// Check for highlight_id to highlight the clicked notification
$highlightId = isset($_GET['highlight_id']) ? (int)$_GET['highlight_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .highlighted-row {
            background-color: #e0f7fa; /* Light cyan background for highlighted row */
        }
    </style>
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
                        <li><a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Schedule</span></a></li>
                        <li><a href="medicalrecords.php"><i class="fas fa-file-medical"></i> <span>Medical Records</span></a></li>
                        <li><a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a></li>
                        <li class="active"><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
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
                        <h4 class="page-title">Notifications</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="showAllNotifications">
                            <label class="form-check-label" for="showAllNotifications">Show All Notifications</label>
                        </div>
                        <input type="text" id="notificationSearchInput" class="form-control float-right mr-2" placeholder="Search notifications..." style="width: 200px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-border table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Message</th>
                                        <th>Notification Type</th>
                                        <th>Timestamp</th>
                                        <th class="text-right">Mark as Read</th>
                                    </tr>
                                </thead>
                                <tbody id="notificationsTableBody">
                                    <!-- Notifications will be loaded dynamically via JavaScript -->
                                </tbody>
                            </table>
                            <div class="pagination-controls mt-3" style="text-align: center;">
                                <button id="notificationPrevPage" class="btn btn-secondary" disabled>Previous</button>
                                <span id="notificationPageInfo">Page 1 of 1</span>
                                <button id="notificationNextPage" class="btn btn-secondary" disabled>Next</button>
                            </div>
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
    <script src="assets/js/app.js"></script>
    <script>
    $(document).ready(function() {
        let currentPage = 1;
        const perPage = 10;
        let totalPages = 1;
        let searchQuery = '';
        let showAll = false;
        const highlightId = <?php echo json_encode($highlightId); ?>;

        // Function to fetch notifications
        function fetchNotifications(page, search = '', showAllNotifications = false) {
            $.ajax({
                url: 'fetch-notifications-page.php',
                type: 'POST',
                data: {
                    doctor_id: <?php echo $doctorId; ?>,
                    page: page,
                    per_page: perPage,
                    search: search,
                    show_all: showAllNotifications ? 1 : 0
                },
                dataType: 'json',
                success: function(response) {
                    const tbody = $('#notificationsTableBody');
                    tbody.empty();

                    if (response.notifications.length === 0) {
                        tbody.append('<tr><td colspan="4" class="text-center">No notifications found.</td></tr>');
                    } else {
                        $.each(response.notifications, function(index, notification) {
                            const isHighlighted = highlightId && notification.id == highlightId;
                            const row = `
                                <tr data-id="${notification.id}" class="${isHighlighted ? 'highlighted-row' : ''}">
                                    <td>${notification.message}</td>
                                    <td>${notification.notification_type}</td>
                                    <td>${notification.created_at}</td>
                                    <td class="text-right">
                                        ${notification.is_read == 0 ? `<button class="btn btn-primary btn-sm">Mark as Read</button>` : 'Read'}
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);

                            // Scroll to the highlighted row if it exists
                            if (isHighlighted) {
                                $('html, body').animate({
                                    scrollTop: $(`tr[data-id="${highlightId}"]`).offset().top - 100
                                }, 500);
                            }
                        });
                    }

                    // Update pagination
                    totalPages = response.total_pages;
                    $('#notificationPageInfo').text(`Page ${page} of ${totalPages}`);
                    $('#notificationPrevPage').prop('disabled', page === 1);
                    $('#notificationNextPage').prop('disabled', page === totalPages);
                    currentPage = page;
                },
                error: function() {
                    $('#notificationsTableBody').html('<tr><td colspan="4" class="text-center">Error loading notifications.</td></tr>');
                }
            });
        }

        // Initial load
        fetchNotifications(currentPage);

        // Search functionality
        $('#notificationSearchInput').on('input', function() {
            searchQuery = $(this).val();
            currentPage = 1; // Reset to first page on search
            fetchNotifications(currentPage, searchQuery, showAll);
        });

        // Show all notifications toggle
        $('#showAllNotifications').on('change', function() {
            showAll = $(this).is(':checked');
            currentPage = 1; // Reset to first page
            fetchNotifications(currentPage, searchQuery, showAll);
        });

        // Pagination controls
        $('#notificationPrevPage').on('click', function() {
            if (currentPage > 1) {
                fetchNotifications(currentPage - 1, searchQuery, showAll);
            }
        });

        $('#notificationNextPage').on('click', function() {
            if (currentPage < totalPages) {
                fetchNotifications(currentPage + 1, searchQuery, showAll);
            }
        });

        // Mark as read functionality
        $(document).on('click', '#notificationsTableBody .btn-primary', function() {
            const row = $(this).closest('tr');
            const notificationId = row.data('id');

            $.ajax({
                url: 'mark-notification-read-page.php',
                type: 'POST',
                data: { notification_id: notificationId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        row.find('.text-right').html('Read');
                        fetchNotifications(currentPage, searchQuery, showAll);
                    } else {
                        alert('Error marking notification as read.');
                    }
                },
                error: function() {
                    alert('Error marking notification as read.');
                }
            });
        });
    });
    </script>
</body>
</html>