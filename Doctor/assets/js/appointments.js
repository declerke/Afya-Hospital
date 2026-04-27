$(document).ready(function() {
    const doctorId = window.doctorId || 1;

    function loadAppointments(searchQuery = '') {
        $.ajax({
            url: 'fetch_doctor_appointments.php',
            method: 'GET',
            data: { doctor_id: doctorId },
            dataType: 'json',
            success: function(response) {
                const tbody = $('#appointments-table tbody');
                tbody.empty();
                if (response.success && response.data.length > 0) {
                    let filteredData = response.data;
                    if (searchQuery) {
                        searchQuery = searchQuery.toLowerCase();
                        filteredData = response.data.filter(appointment =>
                            appointment.formatted_id.toLowerCase().includes(searchQuery) ||
                            appointment.patient_name.toLowerCase().includes(searchQuery) ||
                            appointment.date.toLowerCase().includes(searchQuery) ||
                            appointment.status.toLowerCase().includes(searchQuery)
                        );
                    }
                    filteredData.forEach(appointment => {
                        // Map status to badge class
                        let badgeClass;
                        switch (appointment.status) {
                            case 'Scheduled':
                                badgeClass = 'warning'; // Keep the current color (yellow/orange)
                                break;
                            case 'Cancelled':
                                badgeClass = 'danger'; // Red for Cancelled
                                break;
                            case 'Completed':
                                badgeClass = 'success'; // Green for Completed
                                break;
                            default:
                                badgeClass = 'secondary'; // Fallback for unexpected statuses
                        }

                        tbody.append(`
                            <tr data-id="${appointment.id}">
                                <td>${appointment.formatted_id}</td>
                                <td>${appointment.patient_name}</td>
                                <td>${appointment.age}</td>
                                <td>${appointment.date}</td>
                                <td>${appointment.time}</td>
                                <td><span class="badge badge-${badgeClass}">${appointment.status}</span></td>
                                <td>
                                    <a href="patient_details.php?patient_id=${appointment.patient_id}" class="btn btn-sm btn-info view-btn">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="edit-appointment.php?id=${appointment.id}"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                            <a class="dropdown-item delete-appointment" href="#" data-id="${appointment.id}" data-toggle="modal" data-target="#delete_appointment"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="8" class="text-center">No upcoming appointments</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching appointments:', error);
            }
        });
    }

    // Load appointments on page load
    loadAppointments();

    // Search functionality
    $('#search-appointments').on('input', function() {
        const searchQuery = $(this).val();
        loadAppointments(searchQuery);
    });

    // Delete functionality
    let appointmentIdToDelete = null;
    $(document).on('click', '.delete-appointment', function() {
        appointmentIdToDelete = $(this).data('id');
    });

    $('#confirm-delete').on('click', function() {
        if (appointmentIdToDelete) {
            $.ajax({
                url: 'delete_appointment.php',
                method: 'POST',
                data: { appointment_id: appointmentIdToDelete },
                dataType: 'json',
                success: function(response) {
                    $('#delete_appointment').modal('hide');
                    if (response.success) {
                        // Show success message
                        alert(response.message);
                        // Reload appointments
                        loadAppointments();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#delete_appointment').modal('hide');
                    alert('Error deleting appointment: ' + error);
                }
            });
        }
    });

    // Refresh periodically
    setInterval(() => loadAppointments(), 30000);
});