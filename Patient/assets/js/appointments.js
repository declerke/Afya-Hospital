$(document).ready(function() {
    // Function to fetch and update the appointments table (real-time from database)
    function updateAppointmentsTable() {
        $.ajax({
            url: 'fetch_tables_data.php', // New PHP endpoint for table data
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    console.error('Server error:', data.error);
                    return;
                }

                // Log data to debug (check sorting, statuses, and real-time updates)
                console.log('Appointments Data (Real-Time):', data);

                // Build table rows
                let html = '';
                data.forEach(appointment => {
                    // Appointment ID (formatted as "APT" + padded zeros)
                    const appointmentId = `APT${String(appointment.appointment_id).padStart(4, '0')}`;

                    // Patient name and avatar
                    const patientName = `${appointment.patient_first_name} ${appointment.patient_last_name}`;
                    const patientAvatar = appointment.patient_first_name ? appointment.patient_first_name.charAt(0).toUpperCase() : 'P'; // First letter of first name

                    // Age calculation from date_of_birth
                    const dob = new Date(appointment.patient_dob);
                    const today = new Date(); // Use client's current date for consistency with server
                    let age = today.getFullYear() - dob.getFullYear();
                    const monthDiff = today.getMonth() - dob.getMonth();
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                        age--;
                    }

                    // Doctor name (combined first and last)
                    const doctorName = `${appointment.doctor_first_name} ${appointment.doctor_last_name}`;

                    // Department
                    const department = appointment.department || 'N/A';

                    // Appointment date (format "DD MMM YYYY")
                    const date = new Date(appointment.appointment_date);
                    const dateOptions = { day: 'numeric', month: 'short', year: 'numeric' };
                    const appointmentDate = date.toLocaleDateString('en-US', dateOptions);

                    // Appointment time (format 12-hour range, assuming 1-hour slots for simplicity)
                    const time = new Date(`1970-01-01T${appointment.appointment_time}`);
                    const timeOptions = { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        hour12: true 
                    };
                    const startTime = time.toLocaleTimeString('en-US', timeOptions).toLowerCase(); // e.g., "2:00pm"
                    const endTime = new Date(time.getTime() + 3600000).toLocaleTimeString('en-US', timeOptions).toLowerCase(); // +1 hour, e.g., "3:00pm"
                    const appointmentTime = `${startTime} - ${endTime}`;

                    // Status with color-coded badge
                    let statusClass = 'custom-badge status-red'; // Default to red (Cancelled)
                    if (appointment.status === 'Scheduled') {
                        statusClass = 'custom-badge status-blue'; // Blue for Scheduled
                    } else if (appointment.status === 'Completed') {
                        statusClass = 'custom-badge status-green'; // Green for Completed
                    }
                    const statusText = appointment.status;

                    // Action links with delete functionality
                    const editUrl = `edit-appointment.html?id=${appointment.appointment_id}`;
                    const deleteModal = '#delete_appointment';

                    html += `
                        <tr>
                            <td>${appointmentId}</td>
                            <td><img width="28" height="28" src="assets/img/user.jpg" class="rounded-circle m-r-5" alt=""> ${patientName}</td>
                            <td>${age}</td>
                            <td>${doctorName}</td>
                            <td>${department}</td>
                            <td>${appointmentDate}</td>
                            <td>${appointmentTime}</td>
                            <td><span class="${statusClass}">${statusText}</span></td>
                            <td class="text-right">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="${editUrl}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                        <a class="dropdown-item show-delete-modal" data-id="${appointment.appointment_id}" href="#"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                });

                // Update the table body
                $('#appointments-table tbody').html(html);

                // Enable scrolling if needed (ensure table is scrollable)
                $('#appointments-table').css({
                    'max-height': '600px', // Adjust as needed for your layout
                    'overflow-y': 'auto'
                });

                // Add delete button click handler to show modal
                $('.show-delete-modal').on('click', function(e) {
                    e.preventDefault();
                    const appointmentId = $(this).data('id');
                    $('#delete_appointment').data('appointment-id', appointmentId).modal('show');
                });

                // Handle delete confirmation in the modal
                $('#delete_appointment .btn-danger').off('click').on('click', function() {
                    const appointmentId = $('#delete_appointment').data('appointment-id');

                    $.ajax({
                        url: 'delete_appointment.php',
                        method: 'POST',
                        data: { id: appointmentId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Show aesthetic success modal instead of alert
                                $('#successModal').modal('show');
                                setTimeout(function() {
                                    $('#successModal').modal('hide'); // Auto-close after 3 seconds
                                }, 3000);
                                $('#delete_appointment').modal('hide'); // Close the delete modal
                                updateAppointmentsTable(); // Refresh the table to reflect the deletion
                            } else {
                                alert('Error deleting appointment: ' + (response.error || 'Unknown error'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error deleting appointment:', error);
                            alert('Yo, bro, couldn’t delete the appointment!');
                        }
                    });
                });

                // Log completion of update for debugging
                console.log('Appointments table updated at:', new Date().toLocaleString());
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for appointments:', error);
                alert('Yo, bro, couldn’t grab the appointment data!');
            }
        });
    }

    // Run on page load
    updateAppointmentsTable();

    // Refresh every 60 seconds for real-time updates
    setInterval(updateAppointmentsTable, 60000);
});