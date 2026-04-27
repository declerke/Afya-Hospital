$(document).ready(function() {
    // Store all doctors globally for filtering
    let allDoctors = [];

    // Function to fetch and populate form data
    function loadAppointmentData() {
        const urlParams = new URLSearchParams(window.location.search);
        const appointmentId = urlParams.get('id');

        if (!appointmentId || isNaN(appointmentId) || parseInt(appointmentId) <= 0) {
            alert('Yo, bro, no valid appointment ID provided! ID received: ' + (appointmentId || 'null'));
            console.error('Invalid appointment ID:', appointmentId);
            return;
        }

        $.ajax({
            url: 'fetch_form_data.php',
            method: 'GET',
            data: { id: appointmentId },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error fetching appointment:', response.error);
                    alert('Yo, bro, couldn’t load the appointment data: ' + response.error);
                    return;
                }

                const appointment = response.appointment;
                const patients = response.patients;
                const departments = response.departments;
                const doctors = response.doctors;
                console.log('Fetched Appointment:', appointment);
                console.log('Fetched Patients:', patients);
                console.log('Fetched Departments:', departments);
                console.log('Fetched Doctors:', doctors);

                // Store all doctors for filtering
                allDoctors = doctors;

                // Populate form fields
                $('input[name="appointment_id"]').val(`APT${String(appointment.appointment_id).padStart(4, '0')}`);
                $(`input[name="status"][value="${appointment.status}"]`).prop('checked', true);

                // Populate Patient Name dropdown
                $('select[name="patient_id"]').empty().append('<option value="">Select</option>');
                patients.forEach(patient => {
                    $('select[name="patient_id"]').append(`<option value="${patient.id}">${patient.first_name} ${patient.last_name}</option>`);
                });
                if (appointment.patient_id) {
                    $('select[name="patient_id"]').val(appointment.patient_id).trigger('change');
                }

                // Populate Department dropdown
                $('select[name="department"]').empty().append('<option value="">Select</option>');
                departments.forEach(department => {
                    $('select[name="department"]').append(`<option value="${department}">${department}</option>`);
                });
                if (appointment.department) {
                    $('select[name="department"]').val(appointment.department).trigger('change');
                }

                // Populate Doctor dropdown (initially all doctors)
                $('select[name="doctor_id"]').empty().append('<option value="">Select</option>');
                doctors.forEach(doctor => {
                    $('select[name="doctor_id"]').append(`<option value="${doctor.id}">${doctor.first_name} ${doctor.last_name}</option>`);
                });
                if (appointment.doctor_id) {
                    $('select[name="doctor_id"]').val(appointment.doctor_id).trigger('change');
                }

                // Convert date from YYYY-MM-DD to DD/MM/YYYY for display
                const dateMoment = moment(appointment.appointment_date, 'YYYY-MM-DD');
                $('input[name="appointment_date"]').val(dateMoment.format('DD/MM/YYYY'));

                // Populate Time (trim seconds if present)
                const time = appointment.appointment_time.split(':').slice(0, 2).join(':');
                $('input[name="appointment_time"]').val(time);

                // Initialize Message textarea as empty
                $('textarea[name="message"]').val('');

                // Reinitialize Select2 after population
                $('.select').select2();

                // Log the selected values after Select2 initialization
                console.log('Selected Patient ID after Select2:', $('select[name="patient_id"]').val());
                console.log('Selected Doctor ID after Select2:', $('select[name="doctor_id"]').val());
                console.log('Selected Department after Select2:', $('select[name="department"]').val());

                // Reinitialize Datetimepicker after population
                $('.datetimepicker').datetimepicker({
                    format: 'DD/MM/YYYY'
                });
                $('#datetimepicker3').datetimepicker({
                    format: 'HH:mm'
                });

                console.log('Appointment ID populated:', $('input[name="appointment_id"]').val());
                console.log('Patient Name dropdown populated:', $('select[name="patient_id"]').html());
                console.log('Department dropdown populated:', $('select[name="department"]').html());
                console.log('Doctor dropdown populated:', $('select[name="doctor_id"]').html());
                console.log('Date populated:', $('input[name="appointment_date"]').val());
                console.log('Time populated:', $('input[name="appointment_time"]').val());
                console.log('Message initialized:', $('textarea[name="message"]').val());

                // Link Department to Doctor (narrow down doctors based on department)
                $('select[name="department"]').on('change', function() {
                    const selectedDepartment = $(this).val();
                    const currentDoctorId = $('select[name="doctor_id"]').val(); // Store the currently selected doctor

                    // Reset Doctor dropdown
                    $('select[name="doctor_id"]').empty().append('<option value="">Select</option>');

                    // Filter doctors based on the selected department
                    allDoctors.forEach(doctor => {
                        if (!selectedDepartment || doctor.department === selectedDepartment) {
                            $('select[name="doctor_id"]').append(`<option value="${doctor.id}">${doctor.first_name} ${doctor.last_name}</option>`);
                        }
                    });

                    // Reinitialize Select2 after filtering
                    $('select[name="doctor_id"]').select2();

                    // If the previously selected doctor is still in the filtered list, keep them selected
                    const doctorStillExists = allDoctors.some(doctor => doctor.id == currentDoctorId && doctor.department === selectedDepartment);
                    if (doctorStillExists) {
                        $('select[name="doctor_id"]').val(currentDoctorId).trigger('change');
                    } else {
                        // Clear the selection if the doctor is not in the filtered list
                        $('select[name="doctor_id"]').val('').trigger('change');
                    }
                });

                // Trigger the department change event to filter doctors based on the initial department
                $('select[name="department"]').trigger('change');
            },
            error: function(xhr, status, error) {
                console.error('AJAX error fetching appointment:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error,
                    url: xhr.responseURL
                });
                alert('Yo, bro, couldn’t load the appointment data due to an AJAX error! Status: ' + xhr.status + ', Error: ' + error);
            }
        });
    }

    // Function to save form data (updated to show new success modal)
    $('#editAppointmentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            appointment_id: $('input[name="appointment_id"]').val().replace('APT', ''),
            patient_id: $('select[name="patient_id"]').val(),
            department: $('select[name="department"]').val(),
            doctor_id: $('select[name="doctor_id"]').val(),
            appointment_date: $('input[name="appointment_date"]').val(),
            appointment_time: $('input[name="appointment_time"]').val(),
            status: $('input[name="status"]:checked').val(),
            message: $('textarea[name="message"]').val()
        };

        // Log the form data for debugging
        console.log('Raw Form Data:', formData);

        // Stricter validation for patient_id and doctor_id
        if (!formData.patient_id || isNaN(formData.patient_id) || parseInt(formData.patient_id) <= 0) {
            alert('Yo, bro, please select a valid patient!');
            return;
        }
        if (!formData.doctor_id || isNaN(formData.doctor_id) || parseInt(formData.doctor_id) <= 0) {
            alert('Yo, bro, please select a valid doctor!');
            return;
        }
        if (!formData.department) {
            alert('Yo, bro, please select a department!');
            return;
        }
        if (!formData.appointment_date || !formData.appointment_time || !formData.status) {
            alert('Yo, bro, please fill in all required fields!');
            return;
        }

        // Convert date from DD/MM/YYYY to YYYY-MM-DD
        const dateMoment = moment(formData.appointment_date, 'DD/MM/YYYY');
        if (!dateMoment.isValid()) {
            alert('Yo, bro, invalid date format! Please use DD/MM/YYYY (e.g., 31/03/2025).');
            return;
        }
        formData.appointment_date = dateMoment.format('YYYY-MM-DD');

        // Trim seconds from time (e.g., "09:15:00" to "09:15")
        formData.appointment_time = formData.appointment_time.split(':').slice(0, 2).join(':');

        // Validate date and time together
        const dateTime = moment(`${formData.appointment_date} ${formData.appointment_time}`, 'YYYY-MM-DD HH:mm');
        if (!dateTime.isValid()) {
            alert('Yo, bro, invalid date or time format! Date must be YYYY-MM-DD and time must be HH:mm (e.g., 09:15).');
            return;
        }

        console.log('Processed Form Data:', formData);

        $.ajax({
            url: 'update_appointment.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successModal').modal('show');
                    setTimeout(function() {
                        $('#successModal').modal('hide');
                    }, 3000);
                    loadAppointmentData();
                } else {
                    alert('Error updating appointment: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error updating appointment:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                alert('Yo, bro, couldn’t update the appointment due to an AJAX error! Status: ' + xhr.status + ', Error: ' + error + ', Details: ' + (xhr.responseText || 'No details available'));
            }
        });
    });

    // Load initial data
    loadAppointmentData();

    // Reinitialize Select2 on page load
    $('.select').select2();
});