$(document).ready(function() {
    const doctorId = window.doctorId || 1; // Fallback if session fails

    function loadDashboardStats() {
        $.ajax({
            url: 'fetch_doctor_stats.php', // Same directory as doctordashboard.php
            method: 'GET',
            data: { doctor_id: doctorId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.appointment-count').text(response.data.appointments);
                    $('.schedule-count').text(response.data.schedule);
                    $('.records-count').text(response.data.medical_records);
                    $('.billing-count').text(response.data.billing);
                } else {
                    console.error('Failed to load stats:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching stats:', error);
            }
        });
    }

    function loadAppointments() {
        $.ajax({
            url: 'fetch_doctor_appointments.php',
            method: 'GET',
            data: { doctor_id: doctorId, limit: 9 },
            dataType: 'json',
            success: function(response) {
                const tbody = $('#upcoming-appointments-table tbody');
                tbody.empty();
                if (response.success && response.data.length > 0) {
                    response.data.forEach(appointment => {
                        tbody.append(`
                            <tr>
                                <td>${appointment.patient_name}</td>
                                <td>${appointment.date}</td>
                                <td>${appointment.time}</td>
                                <td>${appointment.status}</td>
                                <td><a href="appointment-details.php?id=${appointment.id}" class="btn btn-sm btn-info">View</a></td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="5" class="text-center">No upcoming appointments</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching appointments:', error);
            }
        });
    }

    function loadSchedule() {
        $.ajax({
            url: 'fetch_doctor_schedule.php',
            method: 'GET',
            data: { doctor_id: doctorId, limit: 5 },
            dataType: 'json',
            success: function(response) {
                const list = $('#schedule-list');
                list.empty();
                if (response.success && response.data.length > 0) {
                    response.data.forEach(schedule => {
                        // Format the date and time
                        const timeRange = schedule.start_time && schedule.end_time !== 'N/A'
                            ? `${schedule.start_time} - ${schedule.end_time}`
                            : schedule.start_time || 'N/A';
                        list.append(`
                            <li class="d-flex align-items-center mb-3">
                                <div class="avatar mr-3">
                                    <i class="fa fa-calendar fa-2x text-muted"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${schedule.reason}</h6>
                                    <p class="mb-1 text-muted">${schedule.schedule_date || 'N/A'}</p>
                                    <p class="mb-1 text-muted">Time: ${timeRange}</p>
                                    <p class="mb-0 text-muted">Status: ${schedule.status}</p>
                                </div>
                            </li>
                        `);
                    });
                } else {
                    list.append('<li><p class="text-center">No schedule available</p></li>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching schedule:', error);
            }
        });
    }

    function loadMedicalRecords() {
        $.ajax({
            url: 'fetch_doctor_medical_records.php',
            method: 'GET',
            data: { doctor_id: doctorId, limit: 5 },
            dataType: 'json',
            success: function(response) {
                const list = $('#medical-records-list');
                list.empty();
                if (response.success && response.data.length > 0) {
                    response.data.forEach(record => {
                        // Format the visit date (remove time if it's 00:00:00)
                        const visitDate = record.visit_date.includes('00:00:00')
                            ? record.visit_date.split(' ')[0]
                            : record.visit_date;
                        // Truncate long text
                        const truncate = (str, len) => str.length > len ? str.substring(0, len) + '...' : str;
                        const reason = truncate(record.reason, 50);
                        const notes = truncate(record.notes, 50);
                        list.append(`
                            <li class="mb-3">
                                <a href="patient_details.php?patient_id=${record.patient_id}" class="record-link d-flex align-items-center text-decoration-none">
                                    <div class="avatar mr-3">
                                        <img src="assets/img/user.jpg" alt="Patient" class="rounded-circle" width="40" height="40">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${record.patient_name}</h6>
                                        <p class="mb-1 text-muted">${visitDate}</p>
                                        <p class="mb-1 text-muted"><strong>Reason:</strong> ${reason}</p>
                                        <p class="mb-0 text-muted"><strong>Notes:</strong> ${notes}</p>
                                    </div>
                                </a>
                            </li>
                        `);
                    });
                } else {
                    list.append('<li><p class="text-center">No recent records</p></li>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching medical records:', error);
            }
        });
    }

    function loadBilling() {
        $.ajax({
            url: 'fetch_doctor_billing.php',
            method: 'GET',
            data: { doctor_id: doctorId, limit: 9 },
            dataType: 'json',
            success: function(response) {
                const tbody = $('#billing-table tbody');
                tbody.empty();
                if (response.success && response.data.length > 0) {
                    response.data.forEach(billing => {
                        tbody.append(`
                            <tr>
                                <td>${billing.invoice_number}</td>
                                <td>${billing.patient_name}</td>
                                <td>${billing.amount}</td>
                                <td>${billing.status}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center">No recent billing</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching billing:', error);
            }
        });
    }

    // Load all data on page load
    loadDashboardStats();
    loadAppointments();
    loadSchedule();
    loadMedicalRecords();
    loadBilling();

    // Refresh periodically
    setInterval(() => {
        loadDashboardStats();
        loadAppointments();
        loadSchedule();
        loadMedicalRecords();
        loadBilling();
    }, 30000);
});



