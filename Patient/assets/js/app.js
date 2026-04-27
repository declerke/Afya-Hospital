$(document).ready(function($) {
	
	// Variables declarations
	var $wrapper = $('.main-wrapper');
	var $pageWrapper = $('.page-wrapper');
	var $slimScrolls = $('.slimscroll');
	var $sidebarOverlay = $('.sidebar-overlay');
	
	// Sidebar
	var Sidemenu = function() {
		this.$menuItem = $('#sidebar-menu a');
	};

	function init() {
		var $this = Sidemenu;
		$('#sidebar-menu a').on('click', function(e) {
			if($(this).parent().hasClass('submenu')) {
				e.preventDefault();
			}
			if(!$(this).hasClass('subdrop')) {
				$('ul', $(this).parents('ul:first')).slideUp(350);
				$('a', $(this).parents('ul:first')).removeClass('subdrop');
				$(this).next('ul').slideDown(350);
				$(this).addClass('subdrop');
			} else if($(this).hasClass('subdrop')) {
				$(this).removeClass('subdrop');
				$(this).next('ul').slideUp(350);
			}
			// Add active class logic for sidebar highlighting
			$('#sidebar-menu li').removeClass('active'); // Remove active from all items
			$(this).parent('li').addClass('active'); // Add active to clicked item's parent <li>
		});

		// Set active class on page load based on current URL
		var currentPage = window.location.pathname.split('/').pop() || 'admindashboard.html';
		$('#sidebar-menu a').each(function() {
			var href = $(this).attr('href');
			if (href === currentPage) {
				$('#sidebar-menu li').removeClass('active'); // Clear all active classes
				$(this).parent('li').addClass('active'); // Set active on matching item
			}
		});

		$('#sidebar-menu ul li.submenu a.active').parents('li:last').children('a:first').addClass('active').trigger('click');
	}
	// Sidebar Initiate
	init();
	
	// Sidebar overlay
	function sidebar_overlay($target) {
		if($target.length) {
			$target.toggleClass('opened');
			$sidebarOverlay.toggleClass('opened');
			$('html').toggleClass('menu-opened');
			$sidebarOverlay.attr('data-reff', '#' + $target[0].id);
		}
	}
	
	// Mobile menu sidebar overlay
	$(document).on('click', '#mobile_btn', function() {
		var $target = $($(this).attr('href'));
		sidebar_overlay($target);
		$wrapper.toggleClass('slide-nav');
		$('#chat_sidebar').removeClass('opened');
		return false;
	});
	
	// Chat sidebar overlay
	$(document).on('click', '#task_chat', function() {
		var $target = $($(this).attr('href'));
		console.log($target);
		sidebar_overlay($target);
		return false;
	});
	
	// Sidebar overlay reset
	$sidebarOverlay.on('click', function() {
		var $target = $($(this).attr('data-reff'));
		if($target.length) {
			$target.removeClass('opened');
			$('html').removeClass('menu-opened');
			$(this).removeClass('opened');
			$wrapper.removeClass('slide-nav');
		}
		return false;
	});
	
	// Select 2
	if($('.select').length > 0) {
		$('.select').select2({
			minimumResultsForSearch: -1,
			width: '100%'
		});
	}
	
	// Floating Label
	if($('.floating').length > 0) {
		$('.floating').on('focus blur', function(e) {
			$(this).parents('.form-focus').toggleClass('focused', (e.type === 'focus' || this.value.length > 0));
		}).trigger('blur');
	}
	
	// Right Sidebar Scroll
	if($('#msg_list').length > 0) {
		$('#msg_list').slimscroll({
			height: '100%',
			color: '#878787',
			disableFadeOut: true,
			borderRadius: 0,
			size: '4px',
			alwaysVisible: false,
			touchScrollStep: 100
		});
		var msgHeight = $(window).height() - 124;
		$('#msg_list').height(msgHeight);
		$('.msg-sidebar .slimScrollDiv').height(msgHeight);
		$(window).resize(function() {
			var msgrHeight = $(window).height() - 124;
			$('#msg_list').height(msgrHeight);
			$('.msg-sidebar .slimScrollDiv').height(msgrHeight);
		});
	}
	
	// Left Sidebar Scroll
	if($slimScrolls.length > 0) {
		$slimScrolls.slimScroll({
			height: 'auto',
			width: '100%',
			position: 'right',
			size: '7px',
			color: '#ccc',
			wheelStep: 10,
			touchScrollStep: 100
		});
		var wHeight = $(window).height() - 60;
		$slimScrolls.height(wHeight);
		$('.sidebar .slimScrollDiv').height(wHeight);
		$(window).resize(function() {
			var rHeight = $(window).height() - 60;
			$slimScrolls.height(rHeight);
			$('.sidebar .slimScrollDiv').height(rHeight);
		});
	}
	
	// Page wrapper height
	var pHeight = $(window).height();
	$pageWrapper.css('min-height', pHeight);
	$(window).resize(function() {
		var prHeight = $(window).height();
		$pageWrapper.css('min-height', prHeight);
	});
	
	// Datetimepicker
	if($('.datetimepicker').length > 0) {
		$('.datetimepicker').datetimepicker({
			format: 'DD/MM/YYYY'
		});
	}
	
	// Datatable
	if($('.datatable').length > 0) {
		$('.datatable').DataTable({
			"bFilter": false,
		});
	}
	
	// Bootstrap Tooltip
	if($('[data-toggle="tooltip"]').length > 0) {
		$('[data-toggle="tooltip"]').tooltip();
	}
	
	// Mobile Menu
	$(document).on('click', '#open_msg_box', function() {
		$wrapper.toggleClass('open-msg-box');
		return false;
	});
	
	// Lightgallery
	if($('#lightgallery').length > 0) {
		$('#lightgallery').lightGallery({
			thumbnail: true,
			selector: 'a'
		});
	}
	
	// Incoming call popup
	if($('#incoming_call').length > 0) {
		$('#incoming_call').modal('show');
	}
	
	// Summernote
	if($('.summernote').length > 0) {
		$('.summernote').summernote({
			height: 200,
			minHeight: null,
			maxHeight: null,
			focus: false
		});
	}
	
	// Check all email
	$(document).on('click', '#check_all', function() {
		$('.checkmail').click();
		return false;
	});
	if($('.checkmail').length > 0) {
		$('.checkmail').each(function() {
			$(this).on('click', function() {
				if($(this).closest('tr').hasClass('checked')) {
					$(this).closest('tr').removeClass('checked');
				} else {
					$(this).closest('tr').addClass('checked');
				}
			});
		});
	}
	
	// Mail important
	$(document).on('click', '.mail-important', function() {
		$(this).find('i.fa').toggleClass('fa-star').toggleClass('fa-star-o');
	});
	
	// Dropfiles
	if($('#drop-zone').length > 0) {
		var dropZone = document.getElementById('drop-zone');
		var uploadForm = document.getElementById('js-upload-form');
		var startUpload = function(files) {
			console.log(files);
		};
		uploadForm.addEventListener('submit', function(e) {
			var uploadFiles = document.getElementById('js-upload-files').files;
			e.preventDefault();
			startUpload(uploadFiles);
		});
		dropZone.ondrop = function(e) {
			e.preventDefault();
			this.className = 'upload-drop-zone';
			startUpload(e.dataTransfer.files);
		};
		dropZone.ondragover = function() {
			this.className = 'upload-drop-zone drop';
			return false;
		};
		dropZone.ondragleave = function() {
			this.className = 'upload-drop-zone';
			return false;
		};
	}
	
	// Small Sidebar
	if(screen.width >= 992) {
		$(document).on('click', '#toggle_btn', function() {
			if($('body').hasClass('mini-sidebar')) {
				$('body').removeClass('mini-sidebar');
				$('.subdrop + ul').slideDown();
			} else {
				$('body').addClass('mini-sidebar');
				$('.subdrop + ul').slideUp();
			}
			return false;
		});
		$(document).on('mouseover', function(e) {
			e.stopPropagation();
			if($('body').hasClass('mini-sidebar') && $('#toggle_btn').is(':visible')) {
				var targ = $(e.target).closest('.sidebar').length;
				if(targ) {
					$('body').addClass('expand-menu');
					$('.subdrop + ul').slideDown();
				} else {
					$('body').removeClass('expand-menu');
					$('.subdrop + ul').slideUp();
				}
				return false;
			}
		});
	}
});




$(document).ready(function () {
    function loadNotifications() {
        $.ajax({
            url: "fetch_notifications.php",
            method: "GET",
            dataType: "json",
            success: function (data) {
                let notifications = data.notifications;
                let notificationList = $("#notification-list");
                let notificationBadge = $("#notification-badge");

                notificationList.empty(); // Clear old notifications
                notificationBadge.text(data.unread_count); // Update badge count

                if (notifications.length > 0) {
                    notifications.forEach(function (notification) {
                        let item = `
                            <li class="notification-message" data-notification-id="${notification.id}">
                                <a href="#" class="notification-link">
                                    <div class="media">
                                        <span class="avatar"><i class="fa fa-bell"></i></span>
                                        <div class="media-body">
                                            <p class="noti-details">${notification.message}</p>
                                            <p class="noti-time"><span class="notification-time">${notification.created_at}</span></p>
                                        </div>
                                    </div>
                                </a>
                            </li>`;
                        notificationList.append(item);
                    });
                } else {
                    notificationList.append(
                        '<li class="notification-message text-center">' +
                        '<p class="font-weight-bold">No new notifications</p>' +
                        '</li>'
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    // Handle notification click to mark as read and redirect
    $(document).on("click", ".notification-link", function (e) {
        e.preventDefault(); // Prevent default anchor behavior

        const notificationId = $(this).closest('.notification-message').data('notification-id');
        if (!notificationId) {
            console.error('Notification ID not found');
            return;
        }

        // Mark the specific notification as read
        $.ajax({
            url: "mark_notifications_read.php", // New endpoint for marking a single notification
            method: "POST",
            data: { notification_id: notificationId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    // Update the badge count after marking as read
                    loadNotifications(); // Reload notifications to update the badge
                    // Redirect to activities.html
                    window.location.href = "activities.html";
                } else {
                    console.error('Error marking notification as read:', response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    });

    // Refresh notifications every 5 seconds
    setInterval(loadNotifications, 5000);
    loadNotifications(); // Initial load
});





// Function to get URL parameter
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Function to fetch and display profile data
function loadProfile() {
    var userType = getUrlParameter('type') || 'admin'; // Default to admin if no type specified
    var userId = getUrlParameter('id'); // For doctors

    // Update the page title based on userType
    if (userType === 'admin') {
        $('#page-title').text('My Profile');
    } else {
        $('#page-title').text('Profile');
    }

    var url = 'fetch_profile.php?type=' + userType;
    if (userType === 'doctor' && userId) {
        url += '&id=' + userId;
    }

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var data = response.data;

                // Update profile header
                $('#user-name').text(data.first_name + ' ' + data.last_name);
                $('#user-role').text(data.role);
                $('#staff-id').text('Staff ID: ' + data.staff_id);
                $('#user-phone').text(data.phone).attr('href', 'tel:' + data.phone);
                $('#user-email').text(data.email).attr('href', 'mailto:' + data.email);
                $('#user-birthday').text(data.birthday);
                $('#user-address').text(data.address);
                $('#user-gender').text(data.gender);
                $('#profile-pic').attr('src', 'assets/img/' + data.profile_pic);

                // Update Edit Profile button link
                if (userType === 'admin') {
                    $('#edit-profile-btn').attr('href', 'edit-profile.html');
                } else if (userType === 'doctor') {
                    $('#edit-profile-btn').attr('href', 'edit-doctor.html?id=' + userId);
                }

                // Load education and experience using staff_id
                loadEducation(data.staff_id);
                loadExperience(data.staff_id);
            } else {
                console.error('Failed to load profile:', response.error || 'Unknown error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching profile:', error);
        }
    });
}

// Function to fetch and display education data
function loadEducation(staffId) {
    $.ajax({
        url: 'fetch_education.php?staff_id=' + staffId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var educationList = $('#education-list');
                educationList.empty();

                response.data.forEach(function(edu) {
                    var li = `
                        <li>
                            <div class="experience-user">
                                <div class="before-circle"></div>
                            </div>
                            <div class="experience-content">
                                <div class="timeline-content">
                                    <a href="#/" class="name">${edu.institution} (${edu.degree})</a>
                                    <div>${edu.subject}</div>
                                    <span class="time">${edu.time}</span>
                                </div>
                            </div>
                        </li>`;
                    educationList.append(li);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching education:', error);
        }
    });
}

// Function to fetch and display experience data
function loadExperience(staffId) {
    $.ajax({
        url: 'fetch_experience.php?staff_id=' + staffId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var experienceList = $('#experience-list');
                experienceList.empty();

                response.data.forEach(function(exp) {
                    var li = `
                        <li>
                            <div class="experience-user">
                                <div class="before-circle"></div>
                            </div>
                            <div class="experience-content">
                                <div class="timeline-content">
                                    <a href="#/" class="name">${exp.position}</a>
                                    <span class="time">${exp.time}</span>
                                </div>
                            </div>
                        </li>`;
                    experienceList.append(li);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching experience:', error);
        }
    });
}

// Load profile when the page loads
$(document).ready(function() {
    loadProfile();
});




$(document).ready(function() {
    function updateCounts() {
        $.ajax({
            url: 'get_counts.php', // Ensure the path is correct
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error(response.error);
                } else {
                    $(".doctor-count").text(response.doctor_count);
                    $(".nurse-count").text(response.nurse_count);
                    $(".patient-count").text(response.patient_count);
					$(".staff-count").text(response.staff_count);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + error);
            }
        });
    }

    updateCounts(); // Call function when page loads
    setInterval(updateCounts, 10000); // Update every 10 seconds
});




$(document).ready(function() {
    // Function to fetch and update the patient total line graph (real-time from database)
    function updatePatientGraph() {
        $.ajax({
            url: 'chart_data_fetcher.php', // Your PHP endpoint for real-time data
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    console.error('Server error:', data.error);
                    return;
                }

                // Extract patient totals data
                const currentData = data.patientTotals.current || [];
                const lastYearData = data.patientTotals.lastYear || [];

                // Generate labels (months for the last 12 months)
                const months = [];
                for (let i = 11; i >= 0; i--) {
                    const date = new Date();
                    date.setMonth(date.getMonth() - i);
                    months.push(date.toLocaleString('default', { month: 'short', year: 'numeric' }));
                }

                // Map data to ensure all months are represented, default to 0
                const currentTotals = months.map(month => {
                    const match = currentData.find(item => item.month === month);
                    return match ? parseInt(match.total) || 0 : 0;
                });

                const lastYearTotals = months.map(month => {
                    const match = lastYearData.find(item => item.month === month);
                    return match ? parseInt(match.total) || 0 : 0;
                });

                // Log data to debug
                console.log('Current Totals:', currentTotals);
                console.log('Last Year Totals:', lastYearTotals);

                // Sanitize data to fit 0–120 range
                const sanitizeData = (values) => {
                    return values.map(value => {
                        if (value === null || isNaN(value) || value === undefined) return 0;
                        return Math.min(Math.max(parseInt(value), 0), 120);
                    });
                };

                const sanitizedCurrent = sanitizeData(currentTotals);
                const sanitizedLastYear = sanitizeData(lastYearTotals);

                // Get canvas context for patient total line graph
                const ctxLine = document.getElementById('linegraph').getContext('2d');

                // Destroy existing chart to avoid overlap or caching
                if (window.patientChart) {
                    window.patientChart.destroy();
                }

                // Create the real-time line graph with two datasets (matching your screenshot design)
                window.patientChart = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Current Patients',
                                data: sanitizedCurrent,
                                borderColor: '#4bc0c0', // Teal blue (matches screenshot)
                                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Light teal fill
                                fill: true,
                                lineTension: 0.4, // Use lineTension instead of tension for 2.7.2
                                pointStyle: 'circle', // Dotted points
                                pointRadius: 5,
                                pointBorderWidth: 2,
                                pointBackgroundColor: '#4bc0c0',
                                borderWidth: 2
                            },
                            {
                                label: 'Last Year',
                                data: sanitizedLastYear,
                                borderColor: '#ffce56', // Yellow (matches screenshot)
                                backgroundColor: 'rgba(255, 206, 86, 0.2)', // Light yellow fill
                                fill: true,
                                lineTension: 0.4,
                                pointStyle: 'circle',
                                pointRadius: 5,
                                pointBorderWidth: 2,
                                pointBackgroundColor: '#ffce56',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{ // Use yAxes array for 2.7.2
                                ticks: {
                                    beginAtZero: true,
                                    min: 0, // Explicitly set minimum
                                    max: 120, // Match screenshot max
                                    stepSize: 20 // Match screenshot increments (0, 20, 40, ..., 120)
                                }
                            }],
                            xAxes: [{ // Use xAxes array for 2.7.2
                                ticks: {
                                    autoSkip: true
                                }
                            }]
                        },
                        title: {
                            display: true,
                            text: 'Patient Total',
                            fontSize: 16, // Use fontSize instead of font.size for 2.7.2
                            fontStyle: 'bold'
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 20,
                                padding: 20
                            }
                        },
                        tooltips: { // Use tooltips instead of tooltip for 2.7.2
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)', // Dark semi-transparent background
                            titleFontColor: '#fff', // White title (month)
                            bodyFontColor: '#fff', // White body (values)
                            borderColor: '#4bc0c0', // Teal border to match design
                            borderWidth: 1,
                            caretSize: 5,
                            cornerRadius: 4,
                            padding: 8,
                            displayColors: false, // Hide color boxes next to labels
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    const label = data.datasets[tooltipItem.datasetIndex].label || '';
                                    const value = tooltipItem.yLabel || 0;
                                    return `${label}: ${value} patients`;
                                },
                                title: function(tooltipItems, data) {
                                    return tooltipItems[0].xLabel; // Show just the month
                                }
                            }
                        }
                    }
                });

                // Dynamically calculate percentage change from previous month
                let percentageText = 'No Change';
                if (sanitizedCurrent.length > 1) {
                    const currentMonthCount = sanitizedCurrent[sanitizedCurrent.length - 1]; // Current month
                    const previousMonthCount = sanitizedCurrent[sanitizedCurrent.length - 2] || 0; // Previous month (default 0 if none)

                    if (previousMonthCount > 0) { // Avoid division by zero
                        const difference = currentMonthCount - previousMonthCount;
                        const percentageChange = (difference / currentMonthCount) * 100;

                        if (percentageChange > 0) {
                            percentageText = `<i class="fa fa-caret-up" aria-hidden="true"></i> ${percentageChange.toFixed(0)}% Higher than Last Month`;
                        } else if (percentageChange < 0) {
                            percentageText = `<i class="fa fa-caret-down" aria-hidden="true"></i> ${Math.abs(percentageChange).toFixed(0)}% Lower than Last Month`;
                        }
                    } else if (currentMonthCount > 0) {
                        percentageText = `<i class="fa fa-caret-up" aria-hidden="true"></i> % Higher than Last Month`; // If previous was 0, and current > 0
                    }
                }

                // Update the chart title with dynamic percentage
                const chartAreaLine = ctxLine.canvas.parentElement;
                $(chartAreaLine).find('.chart-title').html(`
                    <h4>Patient Total</h4>
                    <span class="float-right">${percentageText}</span>
                `);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for patient totals:', error);
                alert('Yo, bro, couldn’t grab the patient total data!');
            }
        });
    }
    

    // Function to fetch and update the patient in bar graph (real-time from database)
    function updatePatientInGraph() {
        $.ajax({
            url: 'chart_data_fetcher.php', 
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    console.error('Server error:', data.error);
                    return;
                }

                // Extract patient in data
                const months = data.patientIn.months || [];
                const icuData = data.patientIn.icu || [];
                const opdData = data.patientIn.opd || [];

                // Log data to debug
                console.log('Months:', months);
                console.log('ICU Data:', icuData);
                console.log('OPD Data:', opdData);

                // Sanitize data to fit 0–90 range
                const sanitizeData = (values) => {
                    return values.map(value => {
                        if (value === null || isNaN(value) || value === undefined) return 0;
                        return Math.min(Math.max(parseInt(value), 0), 90);
                    });
                };

                const sanitizedICU = sanitizeData(icuData);
                const sanitizedOPD = sanitizeData(opdData);

                // Generate fixed months for last 6 months to ensure consistent labels
                const fixedMonths = [];
                for (let i = 5; i >= 0; i--) {
                    const date = new Date();
                    date.setMonth(date.getMonth() - i);
                    fixedMonths.push(date.toLocaleString('default', { month: 'short' }));
                }

                // Map data to ensure all months are represented, default to 0
                const finalICU = fixedMonths.map(month => {
                    const index = months.indexOf(month);
                    return index !== -1 ? sanitizedICU[index] || 0 : 0;
                });

                const finalOPD = fixedMonths.map(month => {
                    const index = months.indexOf(month);
                    return index !== -1 ? sanitizedOPD[index] || 0 : 0;
                });
                

                // Get canvas context for patient in bar graph
                const ctxBar = document.getElementById('bargraph').getContext('2d');

                // Destroy existing chart to avoid overlap or caching
                if (window.patientInChart) {
                    window.patientInChart.destroy();
                }
                

                // Create the real-time bar graph (matching your screenshot design)
                window.patientInChart = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: fixedMonths,
                        datasets: [
                            {
                                label: 'ICU',
                                data: finalICU,
                                backgroundColor: '#4bc0c0', // Blue (matches screenshot)
                                borderColor: '#4bc0c0',
                                borderWidth: 1
                            },
                            {
                                label: 'OPD',
                                data: finalOPD,
                                backgroundColor: '#ffce56', // Orange (matches screenshot)
                                borderColor: '#ffce56',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{ // Use yAxes array for 2.7.2
                                ticks: {
                                    beginAtZero: true,
                                    min: 0, // Explicitly set minimum
                                    max: 90, // Match screenshot max
                                    stepSize: 10 // Match screenshot increments (0, 10, 20, ..., 90)
                                }
                            }],
                            xAxes: [{ // Use xAxes array for 2.7.2
                                ticks: {
                                    autoSkip: true
                                }
                            }]
                        },
                        title: {
                            display: true,
                            text: 'Patients In',
                            fontSize: 16, // Use fontSize instead of font.size for 2.7.2
                            fontStyle: 'bold'
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 20,
                                padding: 20
                            }
                        },
                        tooltips: { // Use tooltips instead of tooltip for 2.7.2
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)', // Dark semi-transparent background
                            titleFontColor: '#fff', // White title (month)
                            bodyFontColor: '#fff', // White body (values)
                            borderColor: '#4bc0c0', // Teal border to match design
                            borderWidth: 1,
                            caretSize: 5,
                            cornerRadius: 4,
                            padding: 8,
                            displayColors: false, // Hide color boxes next to labels
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    const label = data.datasets[tooltipItem.datasetIndex].label || '';
                                    const value = tooltipItem.yLabel || 0;
                                    return `${label}: ${value} patients`;
                                },
                                title: function(tooltipItems, data) {
                                    return tooltipItems[0].xLabel; // Show just the month
                                }
                            }
                        }
                    }
                });

                // Add the legend text "ICU OPD" as in the screenshot
                const chartAreaBar = ctxBar.canvas.parentElement;
                $(chartAreaBar).find('.chart-title').html(`
                    <h4>Patients In</h4>
                    <div class="float-right">
                        <ul class="chat-user-total">
                            <li><i class="fa fa-circle" style="color: #4bc0c0;" aria-hidden="true"></i> ICU</li>
                            <li><i class="fa fa-circle" style="color: #ffce56;" aria-hidden="true"></i> OPD</li>
                        </ul>
                    </div>
                `);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for patient in data:', error);
                alert('Yo, bro, couldn’t grab the patient in data!');
            }
        });
    }

    // Function to fetch and update the upcoming appointments table (real-time from database)
    function updateAppointmentsTable() {
        $.ajax({
            url: 'chart_data_fetcher.php', // Your PHP endpoint for real-time data
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    console.error('Server error:', data.error);
                    return;
                }

                // Extract appointments data
                const appointments = data.appointments || [];

                // Limit to first 5 appointments
                const limitedAppointments = appointments.slice(0, 5);

                // Build table rows
                let html = '';
                limitedAppointments.forEach(appointment => {
                    // Patient name and location
                    const patientName = `${appointment.patient_first_name} ${appointment.patient_last_name}`;
                    const patientLocation = appointment.patient_location || 'Location N/A';
                    const patientAvatar = appointment.patient_first_name ? appointment.patient_first_name.charAt(0).toUpperCase() : 'P'; // First letter of first name

                    // Doctor name (combined first and last)
                    const doctorName = `Dr. ${appointment.doctor_first_name} ${appointment.doctor_last_name}`;

                    // Timing (combine date and time, format to 12-hour)
                    const date = new Date(`${appointment.appointment_date} ${appointment.appointment_time}`);
                    const options = { 
                        hour: 'numeric', 
                        minute: '2-digit', 
                        hour12: true 
                    };
                    const timing = date.toLocaleTimeString('en-US', options);

                    // Appointment details page URL (e.g., appointments.html?id={appointment_id})
                    const detailsUrl = `appointments.html?id=${appointment.appointment_id}`;

                    html += `
                        <tr>
                            <td style="min-width: 200px;">
                                <a class="avatar" href="profile.html">${patientAvatar}</a>
                                <h2><a href="profile.html">${patientName} <span>${patientLocation}</span></a></h2>
                            </td>
                            <td>
                                <h5 class="time-title p-0">Appointment With</h5>
                                <p>${doctorName}</p>
                            </td>
                            <td>
                                <h5 class="time-title p-0">Timing</h5>
                                <p>${timing}</p>
                            </td>
                            <td class="text-right">
                                <a href="${detailsUrl}" class="btn btn-outline-primary take-btn">Scheduled</a>
                            </td>
                        </tr>`;
                });

                // Update the table body
                $('#upcoming-appointments-table tbody').html(html);

                // Remove scrolling CSS since we’re limiting to 5 rows
                $('#upcoming-appointments-table').css({
                    'max-height': '', // Reset max-height
                    'overflow-y': '' // Reset overflow
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for appointments:', error);
                alert('Yo, bro, couldn’t grab the appointment data!');
            }
        });
    }

    // Run all updates on page load
    updatePatientGraph();
    updatePatientInGraph();
    updateAppointmentsTable();

    // Refresh all updates every 60 seconds for real-time updates
    setInterval(updatePatientGraph, 60000);
    setInterval(updatePatientInGraph, 60000);
    setInterval(updateAppointmentsTable, 60000);
});




$(document).ready(function() {
    // Function to fetch and update doctors data
    function updateDoctors() {
        $.ajax({
            url: 'fetch_doctors.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error fetching doctors:', response.error);
                    return;
                }

                // Update doctors list in the dashboard
                let doctorList = '';
                response.doctors.forEach(function(doctor) {
                    doctorList += `
                        <li>
                            <div class="contact-cont">
                                <div class="float-left user-img m-r-10">
                                    <a href="doctors.html?id=${doctor.id}" title="${doctor.full_name}">
                                        <img src="${doctor.profile_pic || 'assets/img/user.jpg'}" alt="" class="w-40 rounded-circle">
                                        <span class="status ${doctor.status_class}"></span>
                                    </a>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-name text-ellipsis">${doctor.full_name}</span>
                                    <span class="contact-date">${doctor.dept_initial}, MD</span>
                                </div>
                            </div>
                        </li>`;
                });
                $('.contact-list').html(doctorList);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for doctors:', error);
            }
        });
    }

    // Initial load
    updateDoctors();

    // Refresh every 10 seconds (sync with existing updateCounts interval)
    setInterval(updateDoctors, 10000);




    let currentOffset = 0;
    const doctorsPerPage = 12;

    // Function to fetch and update doctors list on doctors.html
    function updateDoctorsList() {
        $.ajax({
            url: 'fetch_doctors_list.php',
            type: 'GET',
            data: { offset: currentOffset, limit: doctorsPerPage },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error fetching doctors list:', response.error);
                    $('#doctor-grid').html('<div class="col-md-12 text-center"><p>Error loading doctors...</p></div>');
                    return;
                }
    
                // Clear existing content only on initial load
                if (currentOffset === 0) {
                    $('#doctor-grid').html('');
                }
    
                // Build dynamic cards
                let doctorHtml = '';
                response.doctors.forEach(function(doctor) {
                    doctorHtml += `
                        <div class="col-md-4 col-sm-4 col-lg-3" data-doctor-id="${doctor.id}">
                            <div class="profile-widget">
                                <div class="doctor-img">
                                    <a class="avatar" href="profile.html?type=doctor&id=${doctor.id}"><img alt="" src="${doctor.profile_pic || 'assets/img/default-avatar.jpg'}"></a>
                                </div>
                                <div class="dropdown profile-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="edit-doctor.html?id=${doctor.id}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item delete-doctor" href="#" data-toggle="modal" data-target="#delete_doctor" data-doctor-id="${doctor.id}"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                                <h4 class="doctor-name text-ellipsis"><a href="profile.html?type=doctor&id=${doctor.id}">${doctor.first_name} ${doctor.last_name}</a></h4>
                                <div class="doc-prof">${doctor.department}</div>
                                <div class="user-country">
                                    <i class="fa fa-map-marker"></i> ${doctor.address || 'Location N/A'}
                                </div>
                            </div>
                        </div>`;
                });
    
                // Append new doctors to the grid
                $('#doctor-grid').append(doctorHtml);
    
                // Check if there are more doctors to load
                if (currentOffset + response.doctors.length >= response.total) {
                    $('.see-all-btn').hide(); // Hide "Load More" button if no more doctors
                } else {
                    $('.see-all-btn').show(); // Show button if more doctors exist
                }
    
                // Update the offset for the next batch
                currentOffset += response.doctors.length;
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for doctors list:', error);
                $('#doctor-grid').html('<div class="col-md-12 text-center"><p>Error loading doctors...</p></div>');
            }
        });
    }

    // Function to handle "Load More" button click
    function loadMoreDoctors() {
        updateDoctorsList(); // Fetch the next batch
    }

    // Initial load for doctors.html
    if (window.location.pathname.includes('doctors.html')) {
        currentOffset = 0; // Reset offset on page load
        updateDoctorsList();
    }

    // Bind "Load More" button click
    $('.see-all-btn').on('click', function(e) {
        e.preventDefault();
        loadMoreDoctors();
    });

    // Set doctor_id and doctor name on modal open
    $(document).on('click', '.delete-doctor', function() {
        const doctorId = $(this).data('doctor-id');
        const doctorName = $(this).closest('.profile-widget').find('.doctor-name').text().trim(); // Get the doctor's name
        $('#delete_doctor .confirm-delete').attr('data-doctor-id', doctorId); // Set the doctor_id
        $('#doctor-name-to-delete').text(doctorName); // Set the doctor's name in the modal
        console.log('Setting doctor_id for deletion:', doctorId, 'Doctor name:', doctorName); // Debug log
    });

    // Handle delete confirmation
    $('#delete_doctor').on('click', '.confirm-delete', function() {
        const doctorId = $(this).attr('data-doctor-id');
        console.log('Confirming deletion for doctor_id:', doctorId);

        if (!doctorId) {
            alert('Error: No doctor ID set for deletion.');
            return;
        }

        $.ajax({
            url: 'delete_doctor.php',
            type: 'POST',
            data: { doctor_id: doctorId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(`#doctor-grid [data-doctor-id="${doctorId}"]`).remove();
                    $('#delete_doctor').modal('hide');
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for deleting doctor:', error);
                alert('Error deleting doctor. Please try again.');
            }
        });
    });

    // Refresh every 5 minutes (300,000 ms)
    setInterval(function() {
        if (window.location.pathname.includes('doctors.html')) {
            currentOffset = 0; // Reset offset on refresh
            updateDoctorsList();
        }
    }, 300000);

    // Edit Doctor Page Logic
    if (window.location.pathname.includes('edit-doctor.html')) {
        // Get doctor_id from URL query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const doctorId = urlParams.get('id');

        if (!doctorId) {
            alert('Error: No doctor ID provided.');
            window.location.href = 'doctors.html'; // Redirect if no ID
            return;
        }

        // Set the doctor_id in the hidden input
        $('#doctor_id').val(doctorId);

        // Fetch the doctor's current data to pre-fill the form
        $.ajax({
            url: 'fetch_doctor.php',
            type: 'GET',
            data: { doctor_id: doctorId },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error fetching doctor data:', response.error);
                    alert('Error loading doctor data: ' + response.error);
                    window.location.href = 'doctors.html';
                    return;
                }

                const doctor = response.doctor;
                if (doctor) {
                    // Pre-fill the form fields
                    $('#staff_id').val(doctor.staff_id || 'N/A');
                    const profilePicSrc = doctor.profile_pic || 'assets/img/default-avatar.jpg';
                    $('#profile-pic-preview').attr('src', profilePicSrc);
                    $('#current_profile_pic').val(doctor.profile_pic || ''); // Store full path
                    $('#first_name').val(doctor.first_name);
                    $('#last_name').val(doctor.last_name);
                    $('#email').val(doctor.email || '');
                    $('#date_of_birth').val(doctor.date_of_birth || '');
                    if (doctor.gender) {
                        $(`input[name="gender"][value="${doctor.gender}"]`).prop('checked', true);
                    }
                    // Populate department dropdown
                    $.ajax({
                        url: 'fetch_departments.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function(deptResponse) {
                            if (deptResponse.success) {
                                const departmentSelect = $('#department');
                                departmentSelect.empty();
                                departmentSelect.append('<option value="">Select Department</option>');
                                deptResponse.departments.forEach(function(dept) {
                                    const selected = dept === doctor.department ? 'selected' : '';
                                    departmentSelect.append(`<option value="${dept}" ${selected}>${dept}</option>`);
                                });
                            } else {
                                console.error('Error fetching departments:', deptResponse.error);
                                alert('Error loading departments: ' + deptResponse.error);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error for fetching departments:', error);
                            alert('Error loading departments. Please try again.');
                        }
                    });
                    $('#department').val(doctor.department);
                    $('#address').val(doctor.address || '');
                    $('#phone').val(doctor.contact_number || '');
                } else {
                    alert('Doctor not found.');
                    window.location.href = 'doctors.html';
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for fetching doctor:', error);
                alert('Error loading doctor data. Please try again.');
                window.location.href = 'doctors.html';
            }
        });

        // Image preview on file selection
        $('#profile_pic_file').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-pic-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle form submission with file upload and validation
        $('#edit-doctor-form').on('submit', function(e) {
            e.preventDefault();

            // Validate contact number
            const contactNumber = $('#phone').val().trim();
            if (contactNumber && !/^[0-9+\- ]*$/.test(contactNumber)) {
                alert('Invalid phone number format. Use digits, +, or - only.');
                $('#phone').focus();
                return;
            }

            const formData = new FormData(this);

            $.ajax({
                url: 'update_doctor.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        if (response.profile_pic) {
                            $('#current_profile_pic').val(response.profile_pic); // Update with new full path
                            $('#profile-pic-preview').attr('src', response.profile_pic);
                        }
                        window.location.href = 'doctors.html';
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error for updating doctor:', error);
                    alert('Error updating doctor. Please try again.');
                }
            });
        });
    }
});


// Function to fetch and update new patients data
function updateNewPatients() {
    $.ajax({
        url: 'fetch_new_patients.php',
        type: 'GET',
        dataType: 'json',
        data: { t: new Date().getTime() }, // Cache-busting parameter
        success: function(response) {
            if (response.error) {
                console.error('Error fetching patients:', response.error);
                return;
            }

            // Update new patients table
            let patientRows = '';
            if (response.patients.length === 0) {
                patientRows = `
                    <tr>
                        <td colspan="4" class="text-center">No new patients found.</td>
                    </tr>`;
            } else {
                response.patients.forEach(function(patient, index) {
                    const buttonClass = `btn btn-primary btn-primary-${index + 1}`; // btn-primary-1, btn-primary-2, etc.
                    patientRows += `
                        <tr>
                            <td class="avatar-cell">
                                <img width="28" height="28" class="rounded-circle" src="assets/img/user.jpg" alt="">
                            </td>
                            <td class="name-cell">
                                <h2>${patient.full_name}</h2>
                            </td>
                            <td class="email-cell">${patient.email}</td>
                            <td class="contact-cell">${patient.contact_number}</td>
                            <td class="condition-cell">
                                <a href="patients.html?id=${patient.id}" class="${buttonClass} float-right">${patient.medical_history}</a>
                            </td>
                        </tr>`;
                });
            }
            $('.new-patient-table tbody').html(patientRows);
        },
        error: function(xhr, status, error) {
            console.error('AJAX error for patients:', error);
        }
    });
}

// Initial load
updateNewPatients();

// Refresh every 10 seconds (sync with existing updateCounts interval)
setInterval(function() {
    updateNewPatients();
}, 10000);




// Function to calculate age from date of birth using real-time date
function calculateAge(dateOfBirth) {
    const dob = new Date(dateOfBirth);
    const today = new Date(); // Real-time system date
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    return age;
}

// Function to render table rows
function renderTable(patients, page = 1, searchTerm = '') {
    const rowsPerPage = 10;
    const startIndex = (page - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;

    // Filter patients based on search term
    let filteredPatients = patients.filter(patient =>
        patient.full_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        patient.address.toLowerCase().includes(searchTerm.toLowerCase()) ||
        patient.contact_number.includes(searchTerm) ||
        patient.email.toLowerCase().includes(searchTerm.toLowerCase())
    );

    // Slice for pagination
    const paginatedPatients = filteredPatients.slice(startIndex, endIndex);

    // Generate table rows
    let patientRows = '';
    if (paginatedPatients.length === 0) {
        patientRows = `
            <tr>
                <td colspan="6" class="text-center">No patients found.</td>
            </tr>`;
    } else {
        paginatedPatients.forEach(function(patient) {
            const age = calculateAge(patient.date_of_birth);
            patientRows += `
                <tr>
                    <td>
                        <img width="28" height="28" src="assets/img/user.jpg" class="rounded-circle m-r-5" alt="">
                        <span>${patient.full_name}</span>
                    </td>
                    <td>${age}</td>
                    <td>${patient.address}</td>
                    <td>${patient.contact_number}</td>
                    <td>${patient.email}</td>
                    <td class="text-right">
                        <div class="dropdown dropdown-action">
                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="edit-patient.html?id=${patient.id}">
                                    <i class="fa fa-pencil m-r-5"></i> Edit
                                </a>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_patient" data-id="${patient.id}">
                                    <i class="fa fa-trash-o m-r-5"></i> Delete
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>`;
        });
    }

    // Update table
    $('#patientTableBody').html(patientRows);

    // Update pagination info
    const totalPages = Math.ceil(filteredPatients.length / rowsPerPage);
    $('#pageInfo').text(`Page ${page} of ${totalPages}`);
    $('#prevPage').prop('disabled', page === 1);
    $('#nextPage').prop('disabled', page === totalPages);
}

// Function to fetch and update patients data
function updatePatients() {
    $.ajax({
        url: 'fetch_patients.php',
        type: 'GET',
        dataType: 'json',
        data: { t: new Date().getTime() }, // Cache-busting parameter
        success: function(response) {
            console.log('Fetch patients response:', response); // Debug log

            if (response.error) {
                console.error('Error fetching patients:', response.error);
                $('#patientTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center">Error loading patients: ${response.error}</td>
                    </tr>
                `);
                return;
            }

            // Store patients and render initial page
            window.patientData = response.patients || [];
            renderTable(window.patientData, 1, $('#searchInput').val() || '');
        },
        error: function(xhr, status, error) {
            console.error('AJAX error for patients:', status, error);
            $('#patientTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center">Failed to load patients. Check console for details.</td>
                </tr>
            `);
        }
    });
}

// Handle search input
$('#searchInput').on('input', function() {
    const searchTerm = $(this).val();
    renderTable(window.patientData || [], 1, searchTerm);
});

// Handle pagination
let currentPage = 1;
$('#prevPage').on('click', function() {
    if (currentPage > 1) {
        currentPage--;
        renderTable(window.patientData || [], currentPage, $('#searchInput').val() || '');
    }
});
$('#nextPage').on('click', function() {
    const totalPages = Math.ceil((window.patientData || []).length / 10);
    if (currentPage < totalPages) {
        currentPage++;
        renderTable(window.patientData || [], currentPage, $('#searchInput').val() || '');
    }
});

// Handle delete confirmation
$('#delete_patient').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget); // Button that triggered the modal
    const patientId = button.data('id'); // Extract patient ID from data-id
    const modal = $(this);
    modal.find('.btn-danger').off('click').on('click', function() {
        $.ajax({
            url: 'delete_patient.php',
            type: 'POST',
            dataType: 'json',
            data: { id: patientId },
            success: function(response) {
                if (response.success) {
                    modal.modal('hide');
                    updatePatients(); // Refresh the table
                    alert(response.message);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for delete:', error);
                alert('An error occurred while deleting the patient.');
            }
        });
    });
});

// Initial load and refresh
$(document).ready(function() {
    updatePatients(); // Initial load
    setInterval(function() {
        updatePatients();
    }, 100000);
});




// Doctor Schedule Table
$(document).ready(function() {
    let currentPage = 1;
    const recordsPerPage = 10;

    // Function to fetch schedules
    function fetchSchedules(page, search = '') {
        $.ajax({
            url: 'get_schedules.php', // Removed api/ prefix
            method: 'GET',
            data: {
                page: page,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#scheduleTableBody').html('<tr><td colspan="6" class="text-center">Error loading schedules</td></tr>');
                    return;
                }

                const schedules = response.schedules;
                const totalPages = response.totalPages;
                currentPage = response.currentPage;

                // Update table body
                let html = '';
                if (schedules.length === 0) {
                    html = '<tr><td colspan="6" class="text-center">No schedules found</td></tr>';
                } else {
                    schedules.forEach(schedule => {
                        // Determine status badge color
                        let statusClass = '';
                        switch (schedule.status.toLowerCase()) {
                            case 'available':
                                statusClass = 'status-green';
                                break;
                            case 'blocked':
                                statusClass = 'status-red';
                                break;
                            case 'busy':
                                statusClass = 'status-orange';
                                break;
                            case 'on-call':
                                statusClass = 'status-blue';
                                break;
                            default:
                                statusClass = 'status-grey';
                        }

                        html += `
                            <tr>
                                <td>
                                    <img width="28" height="28" src="assets/img/user.jpg" class="rounded-circle m-r-5" alt=""> 
                                    ${schedule.doctor_name}
                                </td>
                                <td>${schedule.department}</td>
                                <td>${schedule.schedule_date}</td>
                                <td><span class="custom-badge ${statusClass}">${schedule.status}</span></td>
                                <td>${schedule.available_time}</td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="edit-schedule.php?id=${schedule.id}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_schedule" data-id="${schedule.id}"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#scheduleTableBody').html(html);

                // Update pagination
                $('#schedulePageInfo').text(`Page ${currentPage} of ${totalPages}`);
                $('#schedulePrevPage').prop('disabled', currentPage === 1);
                $('#scheduleNextPage').prop('disabled', currentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#scheduleTableBody').html('<tr><td colspan="6" class="text-center">Error loading schedules</td></tr>');
            }
        });
    }

    // Initial load
    fetchSchedules(currentPage);

    // Search functionality
    $('#scheduleSearchInput').on('input', function() {
        const searchTerm = $(this).val();
        currentPage = 1; // Reset to first page on search
        fetchSchedules(currentPage, searchTerm);
    });

    // Pagination controls
    $('#schedulePrevPage').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchSchedules(currentPage, $('#scheduleSearchInput').val());
        }
    });

    $('#scheduleNextPage').on('click', function() {
        currentPage++;
        fetchSchedules(currentPage, $('#scheduleSearchInput').val());
    });

    // Delete functionality
    $('#scheduleTableBody').on('click', '[data-target="#delete_schedule"]', function() {
        const scheduleId = $(this).data('id');
        $('#delete_schedule').data('schedule-id', scheduleId);
    });

    $('#delete_schedule').on('click', '.btn-danger', function() {
        const scheduleId = $('#delete_schedule').data('schedule-id');
        $.ajax({
            url: 'delete_schedule.php', // Removed api/ prefix
            method: 'POST',
            data: { id: scheduleId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    fetchSchedules(currentPage, $('#scheduleSearchInput').val());
                    $('#delete_schedule').modal('hide');
                } else {
                    alert('Error deleting schedule: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete Error:', status, error);
                alert('Error deleting schedule');
            }
        });
    });
});




// Audit Logs Table
$(document).ready(function() {
    let auditLogCurrentPage = 1;
    const auditLogRecordsPerPage = 10;

    // Function to fetch audit logs
    function fetchAuditLogs(page, search = '') {
        $.ajax({
            url: 'get_audit_logs.php',
            method: 'GET',
            data: {
                page: page,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#auditLogsTableBody').html('<tr><td colspan="5" class="text-center">Error loading audit logs</td></tr>');
                    return;
                }

                const logs = response.logs;
                const totalPages = response.totalPages;
                auditLogCurrentPage = response.currentPage;

                // Update table body
                let html = '';
                if (logs.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No audit logs found</td></tr>';
                } else {
                    logs.forEach(log => {
                        html += `
                            <tr>
                                <td>${log.user_name}</td>
                                <td>${log.action}</td>
                                <td>${log.table_name}</td>
                                <td>${log.record_id}</td>
                                <td>${log.timestamp}</td>
                            </tr>
                        `;
                    });
                }
                $('#auditLogsTableBody').html(html);

                // Update pagination
                $('#auditLogPageInfo').text(`Page ${auditLogCurrentPage} of ${totalPages}`);
                $('#auditLogPrevPage').prop('disabled', auditLogCurrentPage === 1);
                $('#auditLogNextPage').prop('disabled', auditLogCurrentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#auditLogsTableBody').html('<tr><td colspan="5" class="text-center">Error loading audit logs</td></tr>');
            }
        });
    }

    // Initial load
    fetchAuditLogs(auditLogCurrentPage);

    // Search functionality
    $('#auditLogSearchInput').on('input', function() {
        const searchTerm = $(this).val();
        auditLogCurrentPage = 1; // Reset to first page on search
        fetchAuditLogs(auditLogCurrentPage, searchTerm);
    });

    // Pagination controls
    $('#auditLogPrevPage').on('click', function() {
        if (auditLogCurrentPage > 1) {
            auditLogCurrentPage--;
            fetchAuditLogs(auditLogCurrentPage, $('#auditLogSearchInput').val());
        }
    });

    $('#auditLogNextPage').on('click', function() {
        auditLogCurrentPage++;
        fetchAuditLogs(auditLogCurrentPage, $('#auditLogSearchInput').val());
    });

    // Download functionality
    $('#downloadAuditLogs').on('click', function() {
        window.location.href = 'download_audit_logs.php';
    });
});




// Chatbot Logs Table
$(document).ready(function() {
    let chatbotLogCurrentPage = 1;
    const chatbotLogRecordsPerPage = 10;

    // Function to fetch chatbot logs
    function fetchChatbotLogs(page, search = '') {
        $.ajax({
            url: 'get_chatbot_logs.php',
            method: 'GET',
            data: {
                page: page,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#chatbotLogsTableBody').html('<tr><td colspan="4" class="text-center">Error loading chatbot logs</td></tr>');
                    return;
                }

                const logs = response.logs;
                const totalPages = response.totalPages;
                chatbotLogCurrentPage = response.currentPage;

                // Update table body
                let html = '';
                if (logs.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">No chatbot logs found</td></tr>';
                } else {
                    logs.forEach(log => {
                        html += `
                            <tr>
                                <td>${log.user_name}</td>
                                <td>${log.message}</td>
                                <td>${log.response}</td>
                                <td>${log.timestamp}</td>
                            </tr>
                        `;
                    });
                }
                $('#chatbotLogsTableBody').html(html);

                // Update pagination
                $('#chatbotLogPageInfo').text(`Page ${chatbotLogCurrentPage} of ${totalPages}`);
                $('#chatbotLogPrevPage').prop('disabled', chatbotLogCurrentPage === 1);
                $('#chatbotLogNextPage').prop('disabled', chatbotLogCurrentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#chatbotLogsTableBody').html('<tr><td colspan="4" class="text-center">Error loading chatbot logs</td></tr>');
            }
        });
    }

    // Initial load
    fetchChatbotLogs(chatbotLogCurrentPage);

    // Search functionality
    $('#chatbotLogSearchInput').on('input', function() {
        const searchTerm = $(this).val();
        chatbotLogCurrentPage = 1; // Reset to first page on search
        fetchChatbotLogs(chatbotLogCurrentPage, searchTerm);
    });

    // Pagination controls
    $('#chatbotLogPrevPage').on('click', function() {
        if (chatbotLogCurrentPage > 1) {
            chatbotLogCurrentPage--;
            fetchChatbotLogs(chatbotLogCurrentPage, $('#chatbotLogSearchInput').val());
        }
    });

    $('#chatbotLogNextPage').on('click', function() {
        chatbotLogCurrentPage++;
        fetchChatbotLogs(chatbotLogCurrentPage, $('#chatbotLogSearchInput').val());
    });

    // Download functionality
    $('#downloadChatbotLogs').on('click', function() {
        window.location.href = 'download_chatbot_logs.php';
    });
});




// Employees Table
$(document).ready(function() {
    let employeeCurrentPage = 1;
    const employeeRecordsPerPage = 10;

    // Function to fetch employees
    function fetchEmployees(page, search = '', employeeId = '', employeeName = '', role = '') {
        $.ajax({
            url: 'get_employees.php',
            method: 'GET',
            data: {
                page: page,
                search: search,
                employee_id: employeeId,
                employee_name: employeeName,
                role: role
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#employeesTableBody').html('<tr><td colspan="7" class="text-center">Error loading employees</td></tr>');
                    return;
                }

                const employees = response.employees;
                const totalPages = response.totalPages;
                employeeCurrentPage = response.currentPage;

                // Update table body
                let html = '';
                if (employees.length === 0) {
                    html = '<tr><td colspan="7" class="text-center">No employees found</td></tr>';
                } else {
                    employees.forEach(employee => {
                        // Determine role badge color
                        let roleClass = '';
                        switch (employee.role.toLowerCase()) {
                            case 'nurse':
                                roleClass = 'status-green';
                                break;
                            case 'doctor':
                                roleClass = 'status-blue';
                                break;
                            default:
                                roleClass = 'status-grey';
                        }

                        html += `
                            <tr>
                                <td>
                                    <img width="28" height="28" src="assets/img/user.jpg" class="rounded-circle m-r-5" alt="">
                                    <span>${employee.full_name}</span>
                                </td>
                                <td>${employee.staff_id}</td>
                                <td>${employee.email}</td>
                                <td>${employee.contact_number}</td>
                                <td>${employee.created_at}</td>
                                <td>
                                    <span class="custom-badge ${roleClass}">${employee.role}</span>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="edit-employee.html?id=${employee.id}&role=${employee.role}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_employee" data-id="${employee.id}" data-role="${employee.role}"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#employeesTableBody').html(html);

                // Update pagination
                $('#employeePageInfo').text(`Page ${employeeCurrentPage} of ${totalPages}`);
                $('#employeePrevPage').prop('disabled', employeeCurrentPage === 1);
                $('#employeeNextPage').prop('disabled', employeeCurrentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#employeesTableBody').html('<tr><td colspan="7" class="text-center">Error loading employees</td></tr>');
            }
        });
    }

    // Initial load
    fetchEmployees(employeeCurrentPage);

    // General search functionality (main search bar)
    $('#employeeSearchInput').on('input', function() {
        const searchTerm = $(this).val();
        employeeCurrentPage = 1; // Reset to first page on search
        fetchEmployees(employeeCurrentPage, searchTerm, $('#employeeIdFilter').val(), $('#employeeNameFilter').val(), $('#employeeRoleFilter').val());
    });

    // Filter input fields (real-time updates)
    $('#employeeIdFilter, #employeeNameFilter, #employeeRoleFilter').on('input change', function() {
        const employeeId = $('#employeeIdFilter').val();
        const employeeName = $('#employeeNameFilter').val();
        const role = $('#employeeRoleFilter').val();
        employeeCurrentPage = 1; // Reset to first page on filter change
        fetchEmployees(employeeCurrentPage, $('#employeeSearchInput').val(), employeeId, employeeName, role);
    });

    // Filter search button (still functional for manual trigger)
    $('#employeeFilterSearch').on('click', function(e) {
        e.preventDefault();
        const employeeId = $('#employeeIdFilter').val();
        const employeeName = $('#employeeNameFilter').val();
        const role = $('#employeeRoleFilter').val();
        employeeCurrentPage = 1; // Reset to first page on filter
        fetchEmployees(employeeCurrentPage, $('#employeeSearchInput').val(), employeeId, employeeName, role);
    });

    // Pagination controls
    $('#employeePrevPage').on('click', function() {
        if (employeeCurrentPage > 1) {
            employeeCurrentPage--;
            fetchEmployees(employeeCurrentPage, $('#employeeSearchInput').val(), $('#employeeIdFilter').val(), $('#employeeNameFilter').val(), $('#employeeRoleFilter').val());
        }
    });

    $('#employeeNextPage').on('click', function() {
        employeeCurrentPage++;
        fetchEmployees(employeeCurrentPage, $('#employeeSearchInput').val(), $('#employeeIdFilter').val(), $('#employeeNameFilter').val(), $('#employeeRoleFilter').val());
    });

    // Delete functionality
    $('#employeesTableBody').on('click', '[data-target="#delete_employee"]', function() {
        const employeeId = $(this).data('id');
        const employeeRole = $(this).data('role');
        $('#delete_employee').data('employee-id', employeeId);
        $('#delete_employee').data('employee-role', employeeRole);
    });

    $('#delete_employee').on('click', '.btn-danger', function() {
        const employeeId = $('#delete_employee').data('employee-id');
        const employeeRole = $('#delete_employee').data('employee-role');
        $.ajax({
            url: 'delete_employee.php',
            method: 'POST',
            data: { id: employeeId, role: employeeRole },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    fetchEmployees(employeeCurrentPage, $('#employeeSearchInput').val(), $('#employeeIdFilter').val(), $('#employeeNameFilter').val(), $('#employeeRoleFilter').val());
                    $('#delete_employee').modal('hide');
                } else {
                    alert('Error deleting employee: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete Error:', status, error);
                alert('Error deleting employee');
            }
        });
    });
});




$(document).ready(function() {
    let dataAccessLogCurrentPage = 1;
    const recordsPerPage = 10;

    // Function to fetch data access logs
    function fetchDataAccessLogs(page, search = '') {
        console.log('Fetching data access logs for page:', page, 'with search:', search);
        $.ajax({
            url: 'get_data_access_logs.php',
            method: 'GET',
            data: {
                page: page,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                console.log('Received response:', response);
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#dataAccessLogsTableBody').html('<tr><td colspan="4" class="text-center">Error loading data access logs: ' + response.error + '</td></tr>');
                    return;
                }

                const logs = response.logs || [];
                const totalPages = response.totalPages || 1;
                dataAccessLogCurrentPage = response.currentPage || 1;

                // Update table body
                let html = '';
                if (logs.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">No data access logs found</td></tr>';
                } else {
                    logs.forEach(log => {
                        html += `
                            <tr>
                                <td>${log.user_name || 'Unknown User'}</td>
                                <td>${log.patient_name || 'Unknown Patient'}</td>
                                <td>${log.access_time || 'N/A'}</td>
                                <td>${log.action || 'N/A'}</td>
                            </tr>
                        `;
                    });
                }
                $('#dataAccessLogsTableBody').html(html);

                // Update pagination
                $('#dataAccessLogPageInfo').text(`Page ${dataAccessLogCurrentPage} of ${totalPages}`);
                $('#dataAccessLogPrevPage').prop('disabled', dataAccessLogCurrentPage === 1);
                $('#dataAccessLogNextPage').prop('disabled', dataAccessLogCurrentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error('Server Error Details:', response.error);
                } catch (e) {
                    console.error('Failed to parse error response:', xhr.responseText);
                }
                $('#dataAccessLogsTableBody').html('<tr><td colspan="4" class="text-center">Error loading data access logs</td></tr>');
            }
        });
    }

    // Initial load and event handlers (only for dataaccesslogs.html)
    if (window.location.pathname.includes('dataaccesslogs.html')) {
        // Initial fetch
        fetchDataAccessLogs(dataAccessLogCurrentPage);

        // Search functionality
        $('#dataAccessLogSearchInput').on('input', function() {
            const searchTerm = $(this).val();
            dataAccessLogCurrentPage = 1; // Reset to first page on search
            fetchDataAccessLogs(dataAccessLogCurrentPage, searchTerm);
        });

        // Pagination controls
        $('#dataAccessLogPrevPage').on('click', function() {
            if (dataAccessLogCurrentPage > 1) {
                dataAccessLogCurrentPage--;
                fetchDataAccessLogs(dataAccessLogCurrentPage, $('#dataAccessLogSearchInput').val());
            }
        });

        $('#dataAccessLogNextPage').on('click', function() {
            dataAccessLogCurrentPage++;
            fetchDataAccessLogs(dataAccessLogCurrentPage, $('#dataAccessLogSearchInput').val());
        });

        // Download functionality (optional, if you want to add it)
        $('#downloadDataAccessLogs').on('click', function() {
            window.location.href = 'download_data_access_logs.php';
        });
    }
});



// Feedback Table
$(document).ready(function() {
    let feedbackCurrentPage = 1;
    const feedbackRecordsPerPage = 10;

    // Function to fetch feedback
    function fetchFeedback(page, search = '') {
        console.log('Fetching feedback for page:', page, 'with search:', search);
        $.ajax({
            url: 'get_feedback.php',
            method: 'GET',
            data: {
                page: page,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                console.log('Received feedback response:', response);
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#feedbackTableBody').html('<tr><td colspan="4" class="text-center">Error loading feedback</td></tr>');
                    return;
                }

                const feedbacks = response.feedbacks;
                const totalPages = response.totalPages;
                feedbackCurrentPage = response.currentPage;

                // Update table body
                let html = '';
                if (feedbacks.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">No feedback found</td></tr>';
                } else {
                    feedbacks.forEach(feedback => {
                        // Format the rating as X/5
                        const ratingFormatted = `${feedback.rating}/5`;
                        html += `
                            <tr>
                                <td>${feedback.patient_name}</td>
                                <td>${feedback.feedback_text}</td>
                                <td>${ratingFormatted}</td>
                                <td>${feedback.submitted_at}</td>
                            </tr>
                        `;
                    });
                }
                $('#feedbackTableBody').html(html);

                // Update pagination
                $('#feedbackPageInfo').text(`Page ${feedbackCurrentPage} of ${totalPages}`);
                $('#feedbackPrevPage').prop('disabled', feedbackCurrentPage === 1);
                $('#feedbackNextPage').prop('disabled', feedbackCurrentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error('Server Error Details:', response.error);
                } catch (e) {
                    console.error('Failed to parse error response:', xhr.responseText);
                }
                $('#feedbackTableBody').html('<tr><td colspan="4" class="text-center">Error loading feedback</td></tr>');
            }
        });
    }

    // Initial load
    fetchFeedback(feedbackCurrentPage);

    // Search functionality
    $('#feedbackSearchInput').on('input', function() {
        const searchTerm = $(this).val();
        feedbackCurrentPage = 1; // Reset to first page on search
        fetchFeedback(feedbackCurrentPage, searchTerm);
    });

    // Pagination controls
    $('#feedbackPrevPage').on('click', function() {
        if (feedbackCurrentPage > 1) {
            feedbackCurrentPage--;
            fetchFeedback(feedbackCurrentPage, $('#feedbackSearchInput').val());
        }
    });

    $('#feedbackNextPage').on('click', function() {
        feedbackCurrentPage++;
        fetchFeedback(feedbackCurrentPage, $('#feedbackSearchInput').val());
    });
});




// Users Table
$(document).ready(function() {
    let userCurrentPage = 1;
    const userRecordsPerPage = 10;

    // Function to fetch users
    function fetchUsers(page, search = '') {
        console.log('Fetching users for page:', page, 'with search:', search);
        $.ajax({
            url: 'get_users.php',
            method: 'GET',
            data: {
                page: page,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                console.log('Received users response:', response);
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#usersTableBody').html('<tr><td colspan="5" class="text-center">Error loading users</td></tr>');
                    return;
                }

                const users = response.users;
                const totalPages = response.totalPages;
                userCurrentPage = response.currentPage;

                // Update table body
                let html = '';
                if (users.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No users found</td></tr>';
                } else {
                    users.forEach(user => {
                        html += `
                            <tr>
                                <td>
                                    <img width="28" height="28" src="assets/img/user.jpg" class="rounded-circle m-r-5" alt="">
                                    <span>${user.full_name}</span>
                                </td>
                                <td>${user.role}</td>
                                <td>${user.contact_number}</td>
                                <td><a href="mailto:${user.email}">${user.email}</a></td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="edit-user.html?id=${user.id}">
                                                <i class="fa fa-pencil m-r-5"></i> Edit
                                            </a>
                                            <a class="dropdown-item delete-user" href="#" data-toggle="modal" data-target="#delete_user" data-id="${user.id}">
                                                <i class="fa fa-trash-o m-r-5"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#usersTableBody').html(html);

                // Update pagination
                $('#userPageInfo').text(`Page ${userCurrentPage} of ${totalPages}`);
                $('#userPrevPage').prop('disabled', userCurrentPage === 1);
                $('#userNextPage').prop('disabled', userCurrentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error('Server Error Details:', response.error);
                } catch (e) {
                    console.error('Failed to parse error response:', xhr.responseText);
                }
                $('#usersTableBody').html('<tr><td colspan="5" class="text-center">Error loading users</td></tr>');
            }
        });
    }

    // Initial load
    fetchUsers(userCurrentPage);

    // Search functionality
    $('#userSearchInput').on('input', function() {
        const searchTerm = $(this).val();
        userCurrentPage = 1; // Reset to first page on search
        fetchUsers(userCurrentPage, searchTerm);
    });

    // Pagination controls
    $('#userPrevPage').on('click', function() {
        if (userCurrentPage > 1) {
            userCurrentPage--;
            fetchUsers(userCurrentPage, $('#userSearchInput').val());
        }
    });

    $('#userNextPage').on('click', function() {
        userCurrentPage++;
        fetchUsers(userCurrentPage, $('#userSearchInput').val());
    });

    // Delete user functionality
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        $('#delete_user').data('user-id', userId); // Store the user ID in the modal
    });

    // Handle delete confirmation
    $('#delete_user').on('click', '.btn-danger', function() {
        const userId = $('#delete_user').data('user-id');
        if (!userId) {
            alert('No user ID specified for deletion.');
            return;
        }

        $.ajax({
            url: 'delete_user.php',
            method: 'POST',
            data: {
                user_id: userId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('User deleted successfully!');
                    fetchUsers(userCurrentPage, $('#userSearchInput').val()); // Refresh the table
                } else {
                    alert('Failed to delete user: ' + response.message);
                }
                $('#delete_user').modal('hide');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('An error occurred while deleting the user.');
                $('#delete_user').modal('hide');
            }
        });
    });
});




// Header Notifications (for all pages)
$(document).ready(function() {
    // Function to fetch and update header notifications
    function updateHeaderNotifications() {
        $.ajax({
            url: 'get_notifications.php',
            method: 'GET',
            data: {
                header: 'true'
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error loading header notifications:', response.error);
                    $('#notification-list').html('<li class="notification-message"><p class="text-center">Error loading notifications</p></li>');
                    $('#notification-badge').text('0');
                    return;
                }

                const notifications = response.notifications;
                const unreadCount = response.unreadCount;

                // Update the badge
                $('#notification-badge').text(unreadCount);

                // Update the dropdown list
                let html = '';
                if (notifications.length === 0) {
                    html = '<li class="notification-message"><p class="text-center">No new notifications</p></li>';
                } else {
                    notifications.forEach(notification => {
                        html += `
                            <li class="notification-message">
                                <a href="activities.html">
                                    <div class="media">
                                        <span class="avatar">
                                            <img alt="" src="assets/img/user.jpg" class="rounded-circle">
                                        </span>
                                        <div class="media-body">
                                            <p class="noti-details">
                                                <span class="noti-title">${notification.user_name}</span> 
                                                ${notification.message}
                                            </p>
                                            <p class="noti-time">
                                                <span class="notification-time">${notification.created_at}</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        `;
                    });
                }
                $('#notification-list').html(html);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error loading header notifications:', status, error);
                $('#notification-list').html('<li class="notification-message"><p class="text-center">Error loading notifications</p></li>');
                $('#notification-badge').text('0');
            }
        });
    }

    // Initial load for header notifications
    updateHeaderNotifications();

    // Optionally, refresh header notifications every 30 seconds
    setInterval(updateHeaderNotifications, 30000);
});

// Notifications Table (for activities.html)
$(document).ready(function() {
    let notificationCurrentPage = 1;
    const notificationRecordsPerPage = 10;

    // Function to fetch notifications
    function fetchNotifications(page, search = '') {
        console.log('Fetching notifications for page:', page, 'with search:', search);
        $.ajax({
            url: 'get_notifications.php',
            method: 'GET',
            data: {
                page: page,
                search: search
            },
            dataType: 'json',
            success: function(response) {
                console.log('Received notifications response:', response);
                if (response.error) {
                    console.error('Error:', response.error);
                    $('#notificationsTableBody').html('<tr><td colspan="5" class="text-center">Error loading notifications</td></tr>');
                    return;
                }

                const notifications = response.notifications;
                const totalPages = response.totalPages;
                notificationCurrentPage = response.currentPage;

                // Update table body
                let html = '';
                if (notifications.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No notifications found</td></tr>';
                } else {
                    notifications.forEach(notification => {
                        const markAsReadButton = notification.is_read === 0 ? 
                            `<button class="btn btn-sm btn-primary mark-as-read" data-id="${notification.id}">Mark as Read</button>` : 
                            '<span class="text-muted">Read</span>';
                        html += `
                            <tr>
                                <td>${notification.user_name}</td>
                                <td>${notification.message}</td>
                                <td>${notification.notification_type}</td>
                                <td>${notification.created_at}</td>
                                <td class="text-right">${markAsReadButton}</td>
                            </tr>
                        `;
                    });
                }
                $('#notificationsTableBody').html(html);

                // Update pagination
                $('#notificationPageInfo').text(`Page ${notificationCurrentPage} of ${totalPages}`);
                $('#notificationPrevPage').prop('disabled', notificationCurrentPage === 1);
                $('#notificationNextPage').prop('disabled', notificationCurrentPage === totalPages);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error('Server Error Details:', response.error);
                } catch (e) {
                    console.error('Failed to parse error response:', xhr.responseText);
                }
                $('#notificationsTableBody').html('<tr><td colspan="5" class="text-center">Error loading notifications</td></tr>');
            }
        });
    }

    // Initial load
    fetchNotifications(notificationCurrentPage);

    // Search functionality
    $('#notificationSearchInput').on('input', function() {
        const searchTerm = $(this).val();
        notificationCurrentPage = 1; // Reset to first page on search
        fetchNotifications(notificationCurrentPage, searchTerm);
    });

    // Pagination controls
    $('#notificationPrevPage').on('click', function() {
        if (notificationCurrentPage > 1) {
            notificationCurrentPage--;
            fetchNotifications(notificationCurrentPage, $('#notificationSearchInput').val());
        }
    });

    $('#notificationNextPage').on('click', function() {
        notificationCurrentPage++;
        fetchNotifications(notificationCurrentPage, $('#notificationSearchInput').val());
    });

    // Mark as read functionality
    $(document).on('click', '.mark-as-read', function() {
        const notificationId = $(this).data('id');
        $.ajax({
            url: 'mark_notification_read.php',
            method: 'POST',
            data: {
                notification_id: notificationId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Notification marked as read!');
                    fetchNotifications(notificationCurrentPage, $('#notificationSearchInput').val()); // Refresh the table
                    updateHeaderNotifications(); // Update the header
                } else {
                    alert('Failed to mark notification as read: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('An error occurred while marking the notification as read.');
            }
        });
    });
});