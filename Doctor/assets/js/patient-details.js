$(document).ready(function() {
    // Initialize DataTable and fetch visit history
    function loadVisitHistory() {
        const patientId = <?php echo $patientId; ?>;
        $.ajax({
            url: 'fetch_visit_history.php',
            method: 'GET',
            data: { patient_id: patientId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const tbody = $('#visitHistoryTable tbody');
                    tbody.empty();
                    response.visits.forEach(visit => {
                        const date = moment(visit.visit_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                        tbody.append(`
                            <tr>
                                <td>${date}</td>
                                <td>${visit.reason}</td>
                                <td>${visit.treatment || 'N/A'}</td>
                                <td>${visit.prescriptions || 'N/A'}</td>
                                <td>${visit.procedure || 'N/A'}</td>
                                <td>${visit.note || 'N/A'}</td>
                            </tr>
                        `);
                    });
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching visit history: ' + error);
            }
        });
    }
    loadVisitHistory();

    // Handle Visit Form Submission
    $('#visitForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: 'save_visit.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successMessage').text(response.message);
                    $('#successModal').modal('show');
                    $('#visitForm')[0].reset();
                    loadVisitHistory();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error saving visit: ' + error);
            }
        });
    });

    // Handle Payment Request Form Submission
    $('#paymentRequestForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&patient_id=<?php echo $patientId; ?>';
        $.ajax({
            url: 'request_payment.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successMessage').text(response.message);
                    $('#successModal').modal('show');
                    $('#paymentRequestForm')[0].reset();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error requesting payment: ' + error);
            }
        });
    });

    // Download Medical History as PDF
    $('#downloadMedicalHistory').on('click', function(e) {
        e.preventDefault();
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let y = 20;

        doc.setFontSize(18);
        doc.text('Medical History Report', 105, y, null, null, 'center');
        y += 10;
        doc.setFontSize(12);
        doc.text(`Patient: <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>`, 20, y);
        y += 10;
        doc.text(`Patient ID: <?php echo $patientId; ?>`, 20, y);
        y += 10;

        const patientInfo = [
            `Contact: <?php echo htmlspecialchars($patient['contact_number'] ?? 'N/A'); ?>`,
            `Email: <?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?>`,
            `Date of Birth: <?php echo htmlspecialchars($patient['date_of_birth']); ?>`,
            `Address: <?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?>`,
            `Gender: <?php echo htmlspecialchars($patient['gender'] ?? 'N/A'); ?>`
        ];
        doc.text(patientInfo, 20, y, { maxWidth: 180, align: 'left' });
        y += 40;

        doc.text('Visit History', 20, y);
        y += 10;
        const visits = <?php echo json_encode($patient['visits'] ?? []); ?>;
        visits.forEach((visit, index) => {
            y += 10;
            doc.text(`Visit ${index + 1} - Date: ${moment(visit.visit_date).format('DD/MM/YYYY')}`, 20, y);
            y += 5;
            doc.text(`Reason: ${visit.reason}`, 20, y);
            y += 5;
            doc.text(`Treatment: ${visit.treatment || 'N/A'}`, 20, y);
            y += 5;
            doc.text(`Prescriptions: ${visit.prescriptions || 'N/A'}`, 20, y);
            y += 5;
            doc.text(`Procedure: ${visit.procedure || 'N/A'}`, 20, y);
            y += 5;
            doc.text(`Note: ${visit.note || 'N/A'}`, 20, y);
            y += 10;
        });

        doc.save('medical_history_<?php echo $patientId; ?>.pdf');
    });
});