
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




// Function to fetch and update notifications
function fetchNotifications() {
    fetch('fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotifications(data.notifications);
            } else {
                console.error('Failed to fetch notifications:', data.message);
                updateNotifications([]); // Display "No notifications" if fetch fails
            }
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            updateNotifications([]); // Display "No notifications" on error
        });
}

// Function to update the UI with notifications
function updateNotifications(notifications) {
    const notificationBadge = document.getElementById("notification-badge");
    const notificationList = document.getElementById("notification-list");

    if (notificationBadge && notificationList) {
        // Update badge count
        notificationBadge.textContent = notifications.length;

        // Clear existing content
        notificationList.innerHTML = "";

        // Populate notifications
        if (notifications.length === 0) {
            notificationList.innerHTML = '<li class="notification-message"><p class="text-center">No new notifications</p></li>';
        } else {
            notifications.forEach(notification => {
                const li = document.createElement("li");
                li.className = "notification-message";
                li.innerHTML = `
                    <a href="notifications.php?id=${notification.id}" class="notification-link" data-notification-id="${notification.id}">
                        <div class="media">
                            <span class="avatar"><i class="fa fa-bell-o"></i></span>
                            <div class="media-body">
                                <p class="noti-details">${notification.message}</p>
                                <p class="noti-time"><span>${notification.created_at}</span></p>
                            </div>
                        </div>
                    </a>
                `;
                notificationList.appendChild(li);
            });
        }
    }
}

// Function to mark notification as read (optional for now)
function markNotificationAsRead(notificationId) {
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh notifications after marking as read
            fetchNotifications(); // Re-fetch notifications without reloading the page
        } else {
            console.error('Failed to mark notification as read:', data.message);
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

// Initialize notifications on page load
document.addEventListener("DOMContentLoaded", () => {
    fetchNotifications();

    // Add event listeners to notification links (optional for now)
    document.addEventListener('click', (e) => {
        const link = e.target.closest('.notification-link');
        if (link) {
            const notificationId = link.getAttribute('data-notification-id');
            markNotificationAsRead(notificationId);
        }
    });
});

// Periodically refresh notifications (e.g., every 30 seconds)
setInterval(fetchNotifications, 30000);




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
