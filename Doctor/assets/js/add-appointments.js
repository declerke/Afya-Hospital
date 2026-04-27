$(document).ready(function() {
    // Fetch patients for the dropdown
    function loadPatients() {
        $.ajax({
            url: 'fetch_patients.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const patientSelect = $('select[name="patient_id"]');
                    patientSelect.empty();
                    patientSelect.append('<option value="">Select</option>');
                    response.patients.forEach(patient => {
                        patientSelect.append(
                            `<option value="${patient.id}">${patient.name}</option>`
                        );
                    });
                    // Reinitialize Select2 after populating
                    $('.select').select2();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching patients: ' + error);
            }
        });
    }

    // Load patients on page load
    loadPatients();

    // Handle form submission
    $('#addAppointmentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: 'save_appointment.php',
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
                alert('Error saving appointment: ' + error);
            }
        });
    });
});