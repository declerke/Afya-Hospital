$(document).ready(function() {
    // Initialize datetimepicker for Date of Birth
    $('#date_of_birth').datetimepicker({
        format: 'YYYY-MM-DD',
        maxDate: moment().subtract(18, 'years') // Ensure doctor is at least 18 years old
    });

    // Fetch departments dynamically
    function fetchDepartments() {
        $.ajax({
            url: 'fetch_departments.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const departmentSelect = $('#department');
                    departmentSelect.empty();
                    departmentSelect.append('<option value="">Select Department</option>');
                    response.departments.forEach(function(dept) {
                        departmentSelect.append(`<option value="${dept}">${dept}</option>`);
                    });
                } else {
                    console.error('Error fetching departments:', response.error);
                    alert('Error loading departments: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for fetching departments:', error);
                alert('Error loading departments. Please try again.');
            }
        });
    }

    // Fetch latest staff ID and generate the next one
    function generateNextStaffID() {
        $.ajax({
            url: 'fetch_latest_staff_id.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const latestStaffId = response.latest_staff_id || 'DR-01-001-2025';
                    const currentYear = new Date().getFullYear(); // 2025
                    const [prefix, group, seq, year] = latestStaffId.split('-');

                    let newGroup = parseInt(group, 10);
                    let newSeq = parseInt(seq, 10) + 1;

                    if (newSeq > 999) {
                        newSeq = 1;
                        newGroup += 1;
                    }

                    const newStaffId = `DR-${String(newGroup).padStart(2, '0')}-${String(newSeq).padStart(3, '0')}-${currentYear}`;
                    $('#staff_id').val(newStaffId);
                } else {
                    console.error('Error fetching latest staff ID:', response.message);
                    alert('Error generating Staff ID. Defaulting to DR-01-001-2025.');
                    $('#staff_id').val('DR-01-001-2025');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for fetching latest staff ID:', error);
                alert('Error generating Staff ID. Defaulting to DR-01-001-2025.');
                $('#staff_id').val('DR-01-001-2025');
            }
        });
    }

    // Avatar preview and validation on file selection
    $('#profile_pic_file').on('change', function() {
        const file = this.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (file) {
            if (!allowedTypes.includes(file.type)) {
                alert('Only JPEG, PNG, and GIF files are allowed.');
                $(this).val(''); // Clear the input
                $('#avatar-preview').attr('src', 'assets/img/user.jpg');
                return;
            }
            if (file.size > maxSize) {
                alert('File size must be less than 5MB.');
                $(this).val(''); // Clear the input
                $('#avatar-preview').attr('src', 'assets/img/user.jpg');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatar-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle form submission with validation
    $('#add-doctor-form').on('submit', function(e) {
        e.preventDefault();

        // Validate required fields
        const firstName = $('#first_name').val().trim();
        const department = $('#department').val().trim();
        const dateOfBirth = $('#date_of_birth').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        const profilePicFile = $('#profile_pic_file')[0].files[0];
        const gender = $('input[name="gender"]:checked').val();

        if (!firstName) {
            alert('First Name is required.');
            $('#first_name').focus();
            return;
        }
        if (!department || department === '') {
            alert('Department is required.');
            $('#department').focus();
            return;
        }
        if (!dateOfBirth) {
            alert('Date of Birth is required.');
            $('#date_of_birth').focus();
            return;
        }
        if (!email) {
            alert('Email is required.');
            $('#email').focus();
            return;
        }
        if (!phone) {
            alert('Phone number is required.');
            $('#phone').focus();
            return;
        }
        if (!profilePicFile) {
            alert('Avatar is required.');
            $('#profile_pic_file').focus();
            return;
        }
        if (!gender) {
            alert('Gender is required.');
            return; // No focus needed since it's a radio group
        }
        if (!/^[0-9+\- ]*$/.test(phone)) {
            alert('Invalid phone number format. Use digits, +, or - only.');
            $('#phone').focus();
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert('Invalid email format.');
            $('#email').focus();
            return;
        }

        const formData = new FormData(this);

        $.ajax({
            url: 'add_doctor.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#add-doctor-form')[0].reset(); // Reset form
                    $('#avatar-preview').attr('src', 'assets/img/user.jpg'); // Reset avatar preview
                    generateNextStaffID(); // Generate new staff ID after successful add
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error for adding doctor:', error);
                alert('Error adding doctor. Please try again.');
            }
        });
    });

    // Refetch departments and staff ID if needed
    fetchDepartments();
    generateNextStaffID();
});