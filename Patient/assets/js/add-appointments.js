$(document).ready(function() {
    // Function to fetch and populate form data
    function loadFormData() {
        $.ajax({
            url: 'fetch_add_form_data.php', 
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error fetching form data:', response.error);
                    alert('Yo, bro, couldn’t load the form data: ' + response.error);
                    return;
                }

                const patients = response.patients;
                const departments = response.departments;
                const doctors = response.doctors;
                console.log('Fetched Patients:', patients);
                console.log('Fetched Departments:', departments);
                console.log('Fetched Doctors:', doctors);

                // Populate Patient Name dropdown
                $('select[name="patient_id"]').empty().append('<option value="">Select</option>');
                patients.forEach(patient => {
                    $('select[name="patient_id"]').append(`<option value="${patient.id}">${patient.first_name} ${patient.last_name}</option>`);
                });

                // Populate Department dropdown
                $('select[name="department"]').empty().append('<option value="">Select</option>');
                departments.forEach(department => {
                    $('select[name="department"]').append(`<option value="${department}">${department}</option>`);
                });

                // Populate Doctor dropdown (store all doctors initially)
                const allDoctors = doctors; // Keep all doctors for filtering
                $('select[name="doctor_id"]').empty().append('<option value="">Select</option>');
                doctors.forEach(doctor => {
                    $('select[name="doctor_id"]').append(`<option value="${doctor.id}">${doctor.first_name} ${doctor.last_name}</option>`);
                });

                // Reinitialize Select2 after population
                $('.select').select2();

                // Link Department to Doctor (narrow down doctors based on department)
                $('select[name="department"]').on('change', function() {
                    const selectedDepartment = $(this).val();
                    $('select[name="doctor_id"]').empty().append('<option value="">Select</option>'); // Reset Doctor dropdown
                    allDoctors.forEach(doctor => {
                        if (!selectedDepartment || doctor.department === selectedDepartment) {
                            $('select[name="doctor_id"]').append(`<option value="${doctor.id}">${doctor.first_name} ${doctor.last_name}</option>`);
                        }
                    });
                    // Reinitialize Select2 after filtering
                    $('select[name="doctor_id"]').select2();
                    // Clear any previously selected doctor if not in the filtered list
                    $('select[name="doctor_id"]').val('').trigger('change');
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX error fetching form data:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    url: xhr.responseURL // Added to check the exact URL
                });
                alert('Yo, bro, couldn’t load the form data due to an AJAX error! Status: ' + xhr.status + ', Error: ' + error);
            }
        });
    }

    // Function to auto-generate Appointment ID
    function generateAppointmentId() {
        $.ajax({
            url: 'add_appointment.php', // Use add_appointment.php to get the next ID
            method: 'GET',
            dataType: 'json',
            data: { action: 'get_next_id' },
            success: function(response) {
                if (response.success && response.nextId) {
                    const numericId = response.nextId; // e.g., 2
                    const displayId = 'APT-' + String(numericId).padStart(4, '0'); // e.g., "APT-0002"
                    $('input[name="appointment_id"]').val(displayId); // Show "APT-0002" in UI
                    $('input[name="appointment_id"]').data('numericId', numericId); // Store numeric ID for submission
                } else {
                    console.error('Error generating Appointment ID:', response.error);
                    alert('Yo, bro, couldn’t generate the Appointment ID: ' + (response.error || 'Unknown error'));
                    $('input[name="appointment_id"]').val('APT-0001'); // Fallback to default display
                    $('input[name="appointment_id"]').data('numericId', 1); // Fallback numeric ID
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error generating Appointment ID:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                alert('Yo, bro, couldn’t generate the Appointment ID due to an AJAX error! Status: ' + xhr.status + ', Error: ' + error);
                $('input[name="appointment_id"]').val('APT-0001'); // Fallback to default display
                $('input[name="appointment_id"]').data('numericId', 1); // Fallback numeric ID
            }
        });
    }

    // Load initial data and generate Appointment ID
    loadFormData();
    generateAppointmentId();

    // Function to save form data via AJAX
    $('#addAppointmentForm').on('submit', function(e) {
        e.preventDefault();
    
        const formData = {
            appointment_id: $('input[name="appointment_id"]').data('numericId'),
            patient_id: $('select[name="patient_id"]').val(),
            doctor_id: $('select[name="doctor_id"]').val(),
            appointment_date: $('input[name="appointment_date"]').val(),
            appointment_time: $('input[name="appointment_time"]').val().padStart(5, '0'),
            status: $('input[name="status"]:checked').val(),
            message: $('textarea[name="message"]').val()
            // No department here—it’s UI-only
        };
    
        console.log('Form Data Sent:', formData);
    
        if (!formData.patient_id || !formData.doctor_id || !formData.appointment_date || !formData.appointment_time || !formData.status) {
            alert('Yo, bro, please fill in all required fields!');
            return;
        }
    
        const dateTime = moment(`${formData.appointment_date} ${formData.appointment_time}`, 'YYYY-MM-DD HH:mm');
        if (!dateTime.isValid()) {
            alert('Yo, bro, invalid date or time format! Use YYYY-MM-DD and HH:mm (e.g., 09:00).');
            return;
        }
    
        $.ajax({
            url: 'add_appointment.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Success Response:', response);
                if (response.success) {
                    $('#successModal').modal('show');
                    setTimeout(function() { $('#successModal').modal('hide'); }, 3000);
                    $('#addAppointmentForm')[0].reset();
                    loadFormData();
                    generateAppointmentId();
                } else {
                    alert('Error adding appointment: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                alert('Yo, bro, couldn’t add the appointment! Status: ' + xhr.status + ', Error: ' + error + ', Details: ' + xhr.responseText);
            }
        });
    });

    // Reinitialize Select2 on page load
    $('.select').select2();
});