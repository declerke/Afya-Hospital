$(document).ready(function() {
    const appointmentId = $('input[name="appointment_id"]').val();

    // Fetch appointment details
    function loadAppointmentDetails() {
        $.ajax({
            url: 'fetch_appointment_details.php',
            method: 'GET',
            data: { id: appointmentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const appointment = response.appointment;

                    // Populate form fields
                    $('input[name="appointment_id_display"]').val('APT' + String(appointment.id).padStart(4, '0'));
                    $('select[name="patient_id"]').empty();
                    response.patients.forEach(patient => {
                        $('select[name="patient_id"]').append(
                            `<option value="${patient.id}" ${patient.id == appointment.patient_id ? 'selected' : ''}>${patient.name}</option>`
                        );
                    });
                    $('input[name="department"]').val(appointment.department);
                    $('input[name="doctor_name"]').val(appointment.doctor_name);
                    $('input[name="doctor_id"]').val(appointment.doctor_id);
                    $('input[name="appointment_date"]').val(appointment.appointment_date);
                    $('input[name="appointment_time"]').val(appointment.appointment_time);
                    // Message field is not fetched, so it remains blank
                    $(`input[name="status"][value="${appointment.status}"]`).prop('checked', true);

                    // Reinitialize Select2 after populating
                    $('.select').select2();
                } else {
                    alert('Error: ' + response.message);
                    window.location.href = 'appointments.php';
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching appointment details: ' + error);
                window.location.href = 'appointments.php';
            }
        });
    }

    // Load appointment details on page load
    loadAppointmentDetails();

    // Handle form submission
    $('#editAppointmentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: 'update_appointment.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error updating appointment: ' + error);
            }
        });
    });
});