<?php
require_once 'session_check.php';
require_once 'config/db.php';

$patient_id           = $_SESSION['patient_id'];
$error_message        = '';
$success_message      = '';
$patient_info         = null;
$medical_records      = [];
$appointments         = [];

// Handle "retrieve records" lookup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retrieve_records'])) {
    $lookup_email = trim($_POST['email'] ?? '');
    $lookup_phone = trim($_POST['phone'] ?? '');

    if (!empty($lookup_email) || !empty($lookup_phone)) {
        try {
            if (!empty($lookup_email)) {
                $stmt = $pdo->prepare("SELECT * FROM patients WHERE email = ? AND id = ?");
                $stmt->execute([$lookup_email, $patient_id]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM patients WHERE contact_number = ? AND id = ?");
                $stmt->execute([$lookup_phone, $patient_id]);
            }
            $patient_info = $stmt->fetch();

            if ($patient_info) {
                $recStmt = $pdo->prepare(
                    "SELECT vr.*, d.first_name AS doctor_first_name, d.last_name AS doctor_last_name, d.department
                     FROM visit_records vr
                     JOIN doctors d ON vr.doctor_id = d.id
                     WHERE vr.patient_id = ?
                     ORDER BY vr.visit_date DESC"
                );
                $recStmt->execute([$patient_info['id']]);
                $medical_records = $recStmt->fetchAll();

                $apptStmt = $pdo->prepare(
                    "SELECT a.*, d.first_name AS doctor_first_name, d.last_name AS doctor_last_name, d.department
                     FROM appointments a
                     JOIN doctors d ON a.doctor_id = d.id
                     WHERE a.patient_id = ?
                     ORDER BY a.appointment_date DESC"
                );
                $apptStmt->execute([$patient_info['id']]);
                $appointments = $apptStmt->fetchAll();
            } else {
                $error_message = "No records found matching the provided details.";
            }
        } catch (PDOException $e) {
            $error_message = "Could not retrieve records. Please try again.";
        }
    } else {
        $error_message = "Please provide an email or phone number.";
    }
}

// Handle new medical history submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_medical_history'])) {
    $medical_history_text = strip_tags(trim($_POST['medical_history_text'] ?? ''));

    if (empty($medical_history_text)) {
        $error_message = "Please enter medical history.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO patient_records (patient_id, medical_history_text) VALUES (?, ?)"
            );
            $stmt->execute([$patient_id, $medical_history_text]);
            $success_message = "Medical history saved successfully!";
        } catch (PDOException $e) {
            $error_message = "Could not save medical history. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medical History Form</title>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  
  <style>
    /* Base styles */
    :root {
      --healthcare-50: #f0f7ff;
      --healthcare-100: #e0f0ff;
      --healthcare-200: #bae0ff;
      --healthcare-500: #0080ff;
      --healthcare-600: #0066cc;
      --healthcare-700: #0052a3;
      --green-50: #f0fff4;
      --green-100: #dcfce7;
      --green-500: #22c55e;
      --green-600: #16a34a;
      --yellow-50: #fffbeb;
      --yellow-100: #fef3c7;
      --yellow-500: #eab308;
      --yellow-800: #854d0e;
      --red-50: #fef2f2;
      --red-100: #fee2e2;
      --red-500: #ef4444;
      --red-600: #dc2626;
      --blue-500: #3b82f6;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
      line-height: 1.5;
      color: var(--gray-800);
      background-color: #f5f5f7;
      padding: 2rem 1rem;
    }

    .container {
      max-width: 1024px;
      margin: 0 auto;
    }

    h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    h3 {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--gray-900);
    }

    .space-y {
      margin-bottom: 2rem;
    }

    .section-description {
      color: var(--gray-600);
      margin-bottom: 1rem;
      font-size: 0.875rem;
    }

    .required {
      color: var(--red-500);
    }

    /* Form elements */
    .form-group {
      margin-bottom: 1rem;
    }

    label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--gray-700);
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    input[type="number"],
    select,
    textarea {
      width: 100%;
      padding: 0.625rem;
      border: 1px solid var(--gray-200);
      border-radius: 0.375rem;
      font-size: 0.875rem;
      transition: border-color 0.2s;
      background-color: white;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    input[type="date"]:focus,
    input[type="number"]:focus,
    select:focus,
    textarea:focus {
      outline: none;
      border-color: var(--healthcare-500);
      box-shadow: 0 0 0 1px var(--healthcare-500);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.5rem center;
      background-size: 1rem;
      padding-right: 2rem;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem;
    }

    @media (min-width: 640px) {
      .form-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    /* Toggle Switch */
    .switch-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 44px;
      height: 24px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: var(--gray-300);
      transition: .4s;
      border-radius: 24px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: var(--healthcare-500);
    }

    input:focus + .slider {
      box-shadow: 0 0 1px var(--healthcare-500);
    }

    input:checked + .slider:before {
      transform: translateX(20px);
    }

    /* Checkbox styles */
    .checkbox-container {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
    }

    .checkbox-container input[type="checkbox"] {
      height: 16px;
      width: 16px;
      margin-right: 0.5rem;
      accent-color: var(--healthcare-500);
    }

    .checkbox-container label {
      margin-bottom: 0;
      cursor: pointer;
    }

    .conditions-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 0.5rem;
    }

    @media (min-width: 640px) {
      .conditions-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (min-width: 768px) {
      .conditions-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    /* Medical form */
    .medical-form {
      background-color: white;
      border-radius: 0.75rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--gray-100);
      overflow: hidden;
      animation: fadeInUp 0.4s ease-out;
      margin-bottom: 2rem;
    }

    .form-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--gray-100);
      background-color: var(--healthcare-50);
    }

    .form-content {
      padding: 1.5rem;
    }

    .form-content section {
      margin-bottom: 2rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid var(--gray-100);
    }

    .form-content section:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    /* File upload */
    .file-upload-container {
      border: 2px dashed var(--gray-200);
      border-radius: 0.5rem;
      padding: 2rem;
      text-align: center;
      margin-bottom: 1.5rem;
      transition: border-color 0.3s;
    }

    .file-upload-container:hover {
      border-color: var(--healthcare-500);
    }

    .file-upload-label {
      display: flex;
      flex-direction: column;
      align-items: center;
      cursor: pointer;
    }

    .upload-icon {
      width: 2.5rem;
      height: 2.5rem;
      color: var(--gray-400);
      margin-bottom: 0.5rem;
    }

    .upload-title {
      font-size: 1.125rem;
      font-weight: 500;
      color: var(--gray-700);
      margin-bottom: 0.25rem;
    }

    .upload-subtitle {
      font-size: 0.875rem;
      color: var(--gray-500);
      margin-bottom: 0.75rem;
    }

    .upload-formats {
      font-size: 0.75rem;
      color: var(--gray-500);
      margin-top: 0.75rem;
    }

    .hidden {
      display: none;
    }

    .uploaded-files-title {
      font-weight: 500;
      color: var(--gray-900);
      margin-bottom: 0.5rem;
    }

    .uploaded-files-list {
      list-style: none;
      border: 1px solid var(--gray-200);
      border-radius: 0.5rem;
      overflow: hidden;
    }

    .file-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem;
      border-bottom: 1px solid var(--gray-100);
    }

    .file-item:last-child {
      border-bottom: none;
    }

    .file-info {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .file-icon {
      color: var(--green-500);
    }

    .file-icon.uploading {
      color: var(--blue-500);
      animation: spin 1s linear infinite;
    }

    .file-icon.error {
      color: var(--red-500);
    }

    .file-details {
      display: flex;
      flex-direction: column;
    }

    .file-name {
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--gray-900);
    }

    .file-meta {
      font-size: 0.75rem;
      color: var(--gray-500);
    }

    .file-actions {
      display: flex;
      align-items: center;
    }

    .progress-bar {
      width: 6rem;
      height: 0.375rem;
      background-color: var(--gray-100);
      border-radius: 1rem;
      overflow: hidden;
      margin-right: 0.75rem;
    }

    .progress-fill {
      height: 100%;
      background-color: var(--healthcare-500);
      border-radius: 1rem;
      transition: width 0.3s ease;
    }

    .remove-file-btn {
      background: none;
      border: none;
      cursor: pointer;
      color: var(--gray-500);
      display: flex;
      align-items: center;
      justify-content: center;
      width: 1.5rem;
      height: 1.5rem;
      border-radius: 0.25rem;
      transition: background-color 0.2s;
    }

    .remove-file-btn:hover {
      background-color: var(--gray-100);
      color: var(--gray-700);
    }

    /* Notice box */
    .notice-box {
      background-color: var(--yellow-50);
      border: 1px solid var(--yellow-100);
      border-radius: 0.5rem;
      padding: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      gap: 0.5rem;
    }

    .notice-icon {
      color: var(--yellow-500);
      flex-shrink: 0;
      margin-top: 0.125rem;
    }

    .notice-title {
      font-weight: 500;
      color: var(--yellow-800);
      margin-bottom: 0.25rem;
    }

    .notice-text {
      font-size: 0.875rem;
      color: var(--yellow-800);
    }

    /* Buttons */
    .btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.625rem 1.25rem;
      background-color: var(--healthcare-500);
      color: white;
      border: none;
      border-radius: 0.375rem;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
      min-width: 8rem;
    }

    .btn-primary:hover {
      background-color: var(--healthcare-600);
    }

    .btn-primary:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }

    .btn-outline {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem 1rem;
      background-color: white;
      color: var(--gray-700);
      border: 1px solid var(--gray-200);
      border-radius: 0.375rem;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .btn-outline:hover {
      background-color: var(--gray-50);
    }

    .btn-icon {
      margin-right: 0.375rem;
      width: 1rem;
      height: 1rem;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
    }

    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    /* Toast notification */
    .toast {
      position: fixed;
      bottom: 1rem;
      right: 1rem;
      padding: 1rem;
      background-color: white;
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      max-width: 24rem;
      z-index: 50;
      animation: fadeInUp 0.3s ease-out;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
    }

    .toast-success {
      border-left: 4px solid var(--green-500);
    }

    .toast-error {
      border-left: 4px solid var(--red-500);
    }

    .toast-icon {
      flex-shrink: 0;
    }

    .toast-success .toast-icon {
      color: var(--green-500);
    }

    .toast-error .toast-icon {
      color: var(--red-500);
    }

    .toast-content {
      flex: 1;
    }

    .toast-title {
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .toast-message {
      font-size: 0.875rem;
      color: var(--gray-600);
    }

    .toast-close {
      background: none;
      border: none;
      color: var(--gray-400);
      cursor: pointer;
      padding: 0.25rem;
      margin-left: 0.5rem;
      border-radius: 0.25rem;
      transition: background-color 0.2s;
    }

    .toast-close:hover {
      background-color: var(--gray-100);
      color: var(--gray-700);
    }

    /* Tabs */
    .tabs {
      display: flex;
      border-bottom: 1px solid var(--gray-200);
      margin-bottom: 2rem;
    }

    .tab {
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      color: var(--gray-600);
      cursor: pointer;
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
    }

    .tab.active {
      color: var(--healthcare-600);
      border-bottom-color: var(--healthcare-500);
    }

    .tab:hover:not(.active) {
      color: var(--gray-900);
      border-bottom-color: var(--gray-300);
    }

    /* Alert messages */
    .alert {
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background-color: var(--green-50);
      border: 1px solid var(--green-100);
      color: var(--green-600);
    }

    .alert-error {
      background-color: var(--red-50);
      border: 1px solid var(--red-100);
      color: var(--red-600);
    }

    /* Medical records display */
    .records-container {
      margin-top: 2rem;
    }

    .record-card {
      background-color: white;
      border-radius: 0.5rem;
      border: 1px solid var(--gray-200);
      padding: 1.5rem;
      margin-bottom: 1rem;
    }

    .record-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--gray-100);
    }

    .record-title {
      font-weight: 600;
      color: var(--gray-900);
    }

    .record-date {
      color: var(--gray-500);
      font-size: 0.875rem;
    }

    .record-doctor {
      color: var(--healthcare-600);
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    .record-department {
      color: var(--gray-600);
      font-size: 0.875rem;
      margin-bottom: 1rem;
    }

    .record-content {
      color: var(--gray-800);
      margin-bottom: 1rem;
    }

    .record-meta {
      display: flex;
      gap: 1rem;
      font-size: 0.875rem;
      color: var(--gray-500);
    }

    .record-meta-item {
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    /* Appointments */
    .appointment-card {
      background-color: white;
      border-radius: 0.5rem;
      border: 1px solid var(--gray-200);
      padding: 1rem;
      margin-bottom: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .appointment-info {
      flex: 1;
    }

    .appointment-date-time {
      font-weight: 600;
      color: var(--gray-900);
      margin-bottom: 0.25rem;
    }

    .appointment-doctor {
      color: var(--healthcare-600);
      font-weight: 500;
      margin-bottom: 0.25rem;
    }

    .appointment-department {
      color: var(--gray-600);
      font-size: 0.875rem;
    }

    .appointment-status {
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .status-scheduled {
      background-color: var(--yellow-50);
      color: var(--yellow-800);
    }

    .status-completed {
      background-color: var(--green-50);
      color: var(--green-600);
    }

    .status-cancelled {
      background-color: var(--red-50);
      color: var(--red-600);
    }

    /* Patient info */
    .patient-info {
      background-color: white;
      border-radius: 0.5rem;
      border: 1px solid var(--gray-200);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .patient-name {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--gray-900);
      margin-bottom: 0.5rem;
    }

    .patient-details {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
    }

    .patient-detail {
      margin-bottom: 0.5rem;
    }

    .detail-label {
      font-size: 0.75rem;
      color: var(--gray-500);
      margin-bottom: 0.25rem;
    }

    .detail-value {
      font-weight: 500;
      color: var(--gray-800);
    }

    /* Retrieve records form */
    .retrieve-form {
      max-width: 600px;
      margin: 0 auto 2rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Tabs for switching between forms -->
    <div class="tabs">
      <div class="tab active" id="tab-new">Submit New Medical History</div>
      <div class="tab" id="tab-retrieve">Retrieve Medical Records</div>
    </div>

    <!-- Alert messages -->
    <?php if (!empty($error_message)): ?>
      <div class="alert alert-error">
        <i data-lucide="alert-circle"></i>
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
      <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        <?php echo htmlspecialchars($success_message); ?>
      </div>
    <?php endif; ?>

    <!-- Retrieve Records Form -->
    <div id="retrieve-records-section" class="retrieve-form" style="display: none;">
      <div class="medical-form">
        <div class="form-header">
          <h2>Retrieve Medical Records</h2>
          <p>Enter your email or phone number to retrieve your medical history</p>
        </div>
        
        <div class="form-content">
          <form method="POST" action="">
            <div class="form-grid">
              <div class="form-group">
                <label for="retrieve-email">Email</label>
                <input
                  type="email"
                  id="retrieve-email"
                  name="email"
                  placeholder="Enter your email"
                />
              </div>
              
              <div class="form-group">
                <label for="retrieve-phone">Phone Number</label>
                <input
                  type="tel"
                  id="retrieve-phone"
                  name="phone"
                  placeholder="Enter your phone number"
                />
              </div>
            </div>
            
            <div class="form-actions">
              <button
                type="submit"
                name="retrieve_records"
                class="btn-primary"
              >
                Retrieve Records
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Display retrieved patient information -->
      <?php if ($patient_info): ?>
        <div class="patient-info">
          <h3 class="patient-name"><?php echo htmlspecialchars($patient_info['first_name'] . ' ' . $patient_info['last_name']); ?></h3>
          
          <div class="patient-details">
            <div class="patient-detail">
              <div class="detail-label">Date of Birth</div>
              <div class="detail-value"><?php echo htmlspecialchars($patient_info['date_of_birth'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="patient-detail">
              <div class="detail-label">Gender</div>
              <div class="detail-value"><?php echo htmlspecialchars($patient_info['gender'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="patient-detail">
              <div class="detail-label">Email</div>
              <div class="detail-value"><?php echo htmlspecialchars($patient_info['email'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="patient-detail">
              <div class="detail-label">Phone</div>
              <div class="detail-value"><?php echo htmlspecialchars($patient_info['contact_number'] ?? 'N/A'); ?></div>
            </div>
            
            <div class="patient-detail">
              <div class="detail-label">Blood Type</div>
              <div class="detail-value"><?php echo htmlspecialchars($patient_info['blood_type'] ?? 'N/A'); ?></div>
            </div>
          </div>
        </div>

        <!-- Display medical records -->
        <?php if (!empty($medical_records)): ?>
          <div class="records-container">
            <h3>Medical Records</h3>
            
            <?php foreach ($medical_records as $record): ?>
              <div class="record-card">
                <div class="record-header">
                  <div class="record-title"><?php echo htmlspecialchars($record['diagnosis'] ?? 'Medical Visit'); ?></div>
                  <div class="record-date"><?php echo htmlspecialchars($record['record_date']); ?></div>
                </div>
                
                <div class="record-doctor">
                  Dr. <?php echo htmlspecialchars($record['doctor_first_name'] . ' ' . $record['doctor_last_name']); ?>
                </div>
                
                <div class="record-department">
                  <?php echo htmlspecialchars($record['department']); ?>
                </div>
                
                <div class="record-content">
                  <?php echo nl2br(htmlspecialchars($record['notes'])); ?>
                </div>
                
                <div class="record-meta">
                  <div class="record-meta-item">
                    <i data-lucide="pill" style="width: 16px; height: 16px;"></i>
                    <?php echo htmlspecialchars($record['medications'] ?? 'No medications'); ?>
                  </div>
                  
                  <div class="record-meta-item">
                    <i data-lucide="activity" style="width: 16px; height: 16px;"></i>
                    <?php echo htmlspecialchars($record['treatment'] ?? 'No treatment specified'); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Display appointments -->
        <?php if (!empty($appointments)): ?>
          <div class="records-container">
            <h3>Appointments</h3>
            
            <?php foreach ($appointments as $appointment): ?>
              <div class="appointment-card">
                <div class="appointment-info">
                  <div class="appointment-date-time">
                    <?php 
                      $date = new DateTime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
                      echo $date->format('F j, Y \a\t g:i A'); 
                    ?>
                  </div>
                  
                  <div class="appointment-doctor">
                    Dr. <?php echo htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?>
                  </div>
                  
                  <div class="appointment-department">
                    <?php echo htmlspecialchars($appointment['department']); ?>
                  </div>
                </div>
                
                <div class="appointment-status status-<?php echo strtolower($appointment['status']); ?>">
                  <?php echo htmlspecialchars($appointment['status']); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- Medical History Form -->
    <div id="new-medical-form" class="medical-form">
      <div class="form-header">
        <h2>Medical History Form</h2>
        <p>Please provide your medical information to help us provide better care</p>
      </div>
      
      <div class="form-content">
        <form id="medical-history-form" class="space-y" method="POST" action="">
          <!-- Personal Information -->
          <section>
            <h3>Personal Information</h3>
            <div class="form-grid">
              <div class="form-group">
                <label for="fullName">
                  Full Name <span class="required">*</span>
                </label>
                <input
                  type="text"
                  id="fullName"
                  name="fullName"
                  placeholder="Enter your full name"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="dateOfBirth">
                  Date of Birth <span class="required">*</span>
                </label>
                <input
                  type="date"
                  id="dateOfBirth"
                  name="dateOfBirth"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="gender">
                  Gender <span class="required">*</span>
                </label>
                <select id="gender" name="gender" required>
                  <option value="" disabled selected>Select gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                  <option value="prefer-not-to-say">Prefer not to say</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="bloodType">Blood Type</label>
                <select id="bloodType" name="bloodType">
                  <option value="" disabled selected>Select blood type</option>
                  <option value="A+">A+</option>
                  <option value="A-">A-</option>
                  <option value="B+">B+</option>
                  <option value="B-">B-</option>
                  <option value="AB+">AB+</option>
                  <option value="AB-">AB-</option>
                  <option value="O+">O+</option>
                  <option value="O-">O-</option>
                  <option value="unknown">Unknown</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="height">Height (cm)</label>
                <input
                  type="number"
                  id="height"
                  name="height"
                  placeholder="Enter height in cm"
                />
              </div>
              
              <div class="form-group">
                <label for="weight">Weight (kg)</label>
                <input
                  type="number"
                  id="weight"
                  name="weight"
                  placeholder="Enter weight in kg"
                />
              </div>
            </div>
          </section>
          
          <!-- Contact Information -->
          <section>
            <h3>Contact Information</h3>
            <div class="form-grid">
              <div class="form-group">
                <label for="email">
                  Email <span class="required">*</span>
                </label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="Enter your email"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="phone">
                  Phone Number <span class="required">*</span>
                </label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  placeholder="Enter your phone number"
                  required
                />
              </div>
              
              <div class="form-group">
                <label for="emergencyContact">Emergency Contact Name</label>
                <input
                  type="text"
                  id="emergencyContact"
                  name="emergencyContact"
                  placeholder="Name of emergency contact"
                />
              </div>
              
              <div class="form-group">
                <label for="emergencyPhone">Emergency Contact Phone</label>
                <input
                  type="tel"
                  id="emergencyPhone"
                  name="emergencyPhone"
                  placeholder="Phone number of emergency contact"
                />
              </div>
            </div>
          </section>
          
          <!-- Medical Information -->
          <section>
            <h3>Medical Information</h3>
            <div class="space-y">
              <div class="form-group">
                <label for="allergies">Known Allergies</label>
                <textarea
                  id="allergies"
                  name="allergies"
                  placeholder="List any allergies (medications, food, environmental)"
                  rows="3"
                ></textarea>
              </div>
              
              <div class="form-group">
                <label for="currentMedications">Current Medications</label>
                <textarea
                  id="currentMedications"
                  name="currentMedications"
                  placeholder="List any medications you are currently taking, including dosage"
                  rows="3"
                ></textarea>
              </div>
              
              <div class="form-group">
                <label for="pastSurgeries">Past Surgeries</label>
                <textarea
                  id="pastSurgeries"
                  name="pastSurgeries"
                  placeholder="List any previous surgeries and approximate dates"
                  rows="3"
                ></textarea>
              </div>
              
              <div class="form-group">
                <label for="chronicConditions">Chronic Conditions</label>
                <textarea
                  id="chronicConditions"
                  name="chronicConditions"
                  placeholder="Describe any chronic conditions you have"
                  rows="3"
                ></textarea>
              </div>
              
              <div class="form-group">
                <label for="familyHistory">Family Medical History</label>
                <textarea
                  id="familyHistory"
                  name="familyHistory"
                  placeholder="Describe any significant family medical history"
                  rows="3"
                ></textarea>
              </div>
            </div>
          </section>
          
          <!-- Medical Conditions -->
          <section>
            <h3>Medical Conditions</h3>
            <p class="section-description">Select any conditions that you have been diagnosed with:</p>
            <div class="conditions-grid" id="medical-conditions">
              <!-- Conditions will be added here via JavaScript -->
            </div>
          </section>
          
          <!-- Lifestyle Information -->
          <section>
            <h3>Lifestyle Information</h3>
            <div class="form-grid">
              <div class="form-group">
                <div class="switch-container">
                  <label for="smoker">Do you smoke?</label>
                  <div class="toggle-switch">
                    <input type="checkbox" id="smoker" name="smoker">
                    <span class="slider"></span>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label for="alcohol">Alcohol Consumption</label>
                <select id="alcohol" name="alcohol">
                  <option value="" disabled selected>Select option</option>
                  <option value="none">None</option>
                  <option value="occasional">Occasional</option>
                  <option value="moderate">Moderate</option>
                  <option value="heavy">Heavy</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="exercise">Exercise Frequency</label>
                <select id="exercise" name="exercise">
                  <option value="" disabled selected>Select option</option>
                  <option value="none">None</option>
                  <option value="rarely">Rarely</option>
                  <option value="occasionally">1-2 times per week</option>
                  <option value="regularly">3-4 times per week</option>
                  <option value="daily">Daily</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="diet">Diet</label>
                <select id="diet" name="diet">
                  <option value="" disabled selected>Select option</option>
                  <option value="regular">Regular</option>
                  <option value="vegetarian">Vegetarian</option>
                  <option value="vegan">Vegan</option>
                  <option value="gluten-free">Gluten-Free</option>
                  <option value="keto">Keto</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
          </section>
          
          <!-- File Upload -->
          <section>
            <h3>Medical Records Upload</h3>
            <p class="section-description">Upload any relevant medical records, test results, or previous diagnosis documents.</p>
            
            <div class="file-upload-container">
              <input
                type="file"
                id="file-upload"
                multiple
                class="hidden"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
              />
              <label for="file-upload" class="file-upload-label">
                <i data-lucide="upload" class="upload-icon"></i>
                <p class="upload-title">Drag and drop files here</p>
                <p class="upload-subtitle">or click to browse files</p>
                <button type="button" class="btn-outline">
                  <i data-lucide="plus" class="btn-icon"></i>
                  Select Files
                </button>
              </label>
              <p class="upload-formats">
                Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB per file)
              </p>
            </div>
            
            <!-- File list -->
            <div id="uploaded-files-container" class="hidden">
              <p class="uploaded-files-title">Uploaded Files</p>
              <ul id="uploaded-files-list" class="uploaded-files-list">
                <!-- Uploaded files will be added here via JavaScript -->
              </ul>
            </div>
          </section>
          
          <!-- Submission -->
          <section>
            <div class="notice-box">
              <i data-lucide="alert-circle" class="notice-icon"></i>
              <div>
                <p class="notice-title">Important Notice</p>
                <p class="notice-text">
                  The information provided in this form will be kept confidential and used only for medical purposes. 
                  By submitting this form, you confirm that the information provided is accurate to the best of your knowledge.
                </p>
              </div>
            </div>
            
            <div class="form-actions">
              <button
                type="submit"
                id="submit-button"
                name="save_medical_history"
                class="btn-primary"
              >
                Save Medical History
              </button>
            </div>
          </section>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Lucide icons
      const lucide = window.lucide;
      lucide.createIcons();
      
      // Get DOM elements
      const form = document.getElementById('medical-history-form');
      const submitButton = document.getElementById('submit-button');
      const fileUploadInput = document.getElementById('file-upload');
      const uploadedFilesContainer = document.getElementById('uploaded-files-container');
      const uploadedFilesList = document.getElementById('uploaded-files-list');
      const medicalConditionsContainer = document.getElementById('medical-conditions');
      
      // Tab elements
      const tabNew = document.getElementById('tab-new');
      const tabRetrieve = document.getElementById('tab-retrieve');
      const newMedicalForm = document.getElementById('new-medical-form');
      const retrieveRecordsSection = document.getElementById('retrieve-records-section');
      
      // Add tab switching functionality
      tabNew.addEventListener('click', function() {
        tabNew.classList.add('active');
        tabRetrieve.classList.remove('active');
        newMedicalForm.style.display = 'block';
        retrieveRecordsSection.style.display = 'none';
      });
      
      tabRetrieve.addEventListener('click', function() {
        tabRetrieve.classList.add('active');
        tabNew.classList.remove('active');
        retrieveRecordsSection.style.display = 'block';
        newMedicalForm.style.display = 'none';
      });
      
      // Check if we should show the retrieve tab based on PHP variables
      <?php if (!empty($medical_records) || !empty($error_message) && isset($_POST['retrieve_records'])): ?>
        tabRetrieve.click();
      <?php endif; ?>
      
      // Medical conditions list
      const medicalConditions = [
        'Diabetes',
        'Hypertension',
        'Asthma',
        'Heart Disease',
        'Cancer',
        'Arthritis',
        'Kidney Disease',
        'Liver Disease',
        'Thyroid Disorder',
        'Mental Health Condition',
        'Stroke',
        'Epilepsy',
      ];
      
      // Populate medical conditions checkboxes
      populateMedicalConditions();
      
      // Track uploaded files
      let uploadedFiles = [];
      
      // Add event listeners
      form.addEventListener('submit', handleSubmit);
      fileUploadInput.addEventListener('change', handleFileUpload);
      
      // Populate medical conditions checkboxes
      function populateMedicalConditions() {
        medicalConditions.forEach(condition => {
          const checkboxContainer = document.createElement('div');
          checkboxContainer.className = 'checkbox-container';
          
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.id = `condition-${condition}`;
          checkbox.name = 'conditions';
          checkbox.value = condition;
          
          const label = document.createElement('label');
          label.htmlFor = `condition-${condition}`;
          label.textContent = condition;
          
          checkboxContainer.appendChild(checkbox);
          checkboxContainer.appendChild(label);
          medicalConditionsContainer.appendChild(checkboxContainer);
        });
      }
      
      // Handle file upload
      function handleFileUpload(e) {
        const files = e.target.files;
        if (!files || files.length === 0) return;
        
        // Process each selected file
        Array.from(files).forEach(file => {
          // Check file size (max 10MB)
          if (file.size > 10 * 1024 * 1024) {
            showToast('File too large', `${file.name} exceeds the maximum file size of 10MB.`, 'error');
            return;
          }
          
          // Create a new file record
          const newFile = {
            id: Date.now().toString() + Math.random().toString(36).substring(2, 9),
            name: file.name,
            size: file.size,
            type: file.type,
            progress: 0,
            status: 'uploading',
          };
          
          uploadedFiles.push(newFile);
          
          // Show uploaded files container if it's hidden
          if (uploadedFilesContainer.classList.contains('hidden')) {
            uploadedFilesContainer.classList.remove('hidden');
          }
          
          // Add file to the UI
          addFileToUI(newFile);
          
          // Simulate upload progress
          simulateFileUpload(newFile.id, file);
        });
        
        // Reset the file input
        e.target.value = '';
      }
      
      // Add file to UI
      function addFileToUI(file) {
        const fileItem = document.createElement('li');
        fileItem.className = 'file-item';
        fileItem.id = `file-${file.id}`;
        
        const iconClass = file.status === 'completed' ? 'check-circle-2' : 
                          file.status === 'error' ? 'alert-circle' : 'loader-2';
        const iconColorClass = file.status === 'completed' ? '' : 
                              file.status === 'error' ? 'error' : 'uploading';
        
        fileItem.innerHTML = `
          <div class="file-info">
            <i data-lucide="${iconClass}" class="file-icon ${iconColorClass}"></i>
            <div class="file-details">
              <span class="file-name">${file.name}</span>
              <span class="file-meta">
                ${formatFileSize(file.size)} • ${file.status === 'completed' ? 'Uploaded' : 
                                                file.status === 'error' ? 'Failed' : 
                                                `Uploading ${Math.round(file.progress)}%`}
              </span>
            </div>
          </div>
          
          <div class="file-actions">
            ${file.status === 'uploading' ? `
              <div class="progress-bar">
                <div class="progress-fill" style="width: ${file.progress}%"></div>
              </div>
            ` : ''}
            
            <button type="button" class="remove-file-btn" data-file-id="${file.id}">
              <i data-lucide="x" class="remove-icon"></i>
            </button>
          </div>
        `;
        
        uploadedFilesList.appendChild(fileItem);
        
        // Initialize Lucide icons in the new element
        lucide.createIcons({
          attrs: {
            class: ["file-icon", iconColorClass]
          },
          elements: [fileItem]
        });
        
        // Add event listener to remove button
        const removeButton = fileItem.querySelector('.remove-file-btn');
        removeButton.addEventListener('click', () => removeFile(file.id));
      }
      
      // Simulate file upload
      function simulateFileUpload(fileId, file) {
        let progress = 0;
        
        // Create a URL for the file
        const fileUrl = URL.createObjectURL(file);
        
        // Simulate upload progress with intervals
        const interval = setInterval(() => {
          progress += Math.random() * 15;
          
          if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            
            // Update file status to completed after a delay
            setTimeout(() => {
              const fileIndex = uploadedFiles.findIndex(f => f.id === fileId);
              if (fileIndex !== -1) {
                uploadedFiles[fileIndex].progress = 100;
                uploadedFiles[fileIndex].status = 'completed';
                uploadedFiles[fileIndex].url = fileUrl;
                
                // Update UI
                updateFileUI(uploadedFiles[fileIndex]);
              }
            }, 500);
          }
          
          // Update progress
          const fileIndex = uploadedFiles.findIndex(f => f.id === fileId);
          if (fileIndex !== -1) {
            uploadedFiles[fileIndex].progress = Math.min(progress, 100);
            
            // Update progress bar in UI
            const progressFill = document.querySelector(`#file-${fileId} .progress-fill`);
            if (progressFill) {
              progressFill.style.width = `${Math.min(progress, 100)}%`;
            }
            
            // Update progress text
            const fileMeta = document.querySelector(`#file-${fileId} .file-meta`);
            if (fileMeta) {
              fileMeta.textContent = `${formatFileSize(uploadedFiles[fileIndex].size)} • Uploading ${Math.round(progress)}%`;
            }
          }
        }, 300);
      }
      
      // Update file UI after status change
      function updateFileUI(file) {
        const fileItem = document.getElementById(`file-${file.id}`);
        if (!fileItem) return;
        
        const iconClass = file.status === 'completed' ? 'check-circle-2' : 
                          file.status === 'error' ? 'alert-circle' : 'loader-2';
        const iconColorClass = file.status === 'completed' ? '' : 
                              file.status === 'error' ? 'error' : 'uploading';
        
        // Update icon
        const iconContainer = fileItem.querySelector('.file-icon').parentNode;
        iconContainer.innerHTML = `<i data-lucide="${iconClass}" class="file-icon ${iconColorClass}"></i>`;
        
        // Update meta text
        const fileMeta = fileItem.querySelector('.file-meta');
        fileMeta.textContent = `${formatFileSize(file.size)} • ${file.status === 'completed' ? 'Uploaded' : 
                                                                file.status === 'error' ? 'Failed' : 
                                                                `Uploading ${Math.round(file.progress)}%`}`;
        
        // Remove progress bar if completed or error
        if (file.status !== 'uploading') {
          const progressBar = fileItem.querySelector('.progress-bar');
          if (progressBar) {
            progressBar.remove();
          }
        }
        
        // Initialize Lucide icons in the updated element
        lucide.createIcons({
          attrs: {
            class: ["file-icon", iconColorClass]
          },
          elements: [fileItem]
        });
      }
      
      // Remove file
      function removeFile(fileId) {
        // Remove from array
        uploadedFiles = uploadedFiles.filter(file => file.id !== fileId);
        
        // Remove from UI
        const fileItem = document.getElementById(`file-${fileId}`);
        if (fileItem) {
          fileItem.remove();
        }
        
        // Hide container if no files
        if (uploadedFiles.length === 0) {
          uploadedFilesContainer.classList.add('hidden');
        }
      }
      
      // Format file size
      function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
      }
      
      // Handle form submission
      function handleSubmit(e) {
        // Don't prevent default as we want the form to submit to PHP
        // e.preventDefault();
        
        if (!validateForm()) {
          e.preventDefault();
          return;
        }
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';
        
        // The form will be submitted to the server
      }
      
      // Validate form
      function validateForm() {
        const requiredFields = ['fullName', 'dateOfBirth', 'gender', 'email', 'phone'];
        let isValid = true;
        
        requiredFields.forEach(field => {
          const input = document.getElementById(field);
          if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
          } else {
            input.classList.remove('error');
          }
        });
        
        if (!isValid) {
          showToast('Missing Required Fields', 'Please fill in all required fields before submitting.', 'error');
        }
        
        return isValid;
      }
      
      // Show toast notification
      function showToast(title, message, type = 'success') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const iconName = type === 'success' ? 'check-circle-2' : 'alert-circle';
        
        toast.innerHTML = `
          <i data-lucide="${iconName}" class="toast-icon"></i>
          <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
          </div>
          <button type="button" class="toast-close">
            <i data-lucide="x"></i>
          </button>
        `;
        
        // Add to document
        document.body.appendChild(toast);
        
        // Initialize Lucide icons in the toast
        lucide.createIcons({
          elements: [toast]
        });
        
        // Add event listener to close button
        const closeButton = toast.querySelector('.toast-close');
        closeButton.addEventListener('click', () => {
          document.body.removeChild(toast);
        });
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
          if (document.body.contains(toast)) {
            document.body.removeChild(toast);
          }
        }, 5000);
      }
    });
  </script>
</body>
</html>