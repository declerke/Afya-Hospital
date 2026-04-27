<?php
require_once 'session_check.php';
require_once 'config/db.php';

$session_patient_id = $_SESSION['patient_id'];
$error_message      = '';
$success_message    = '';

// Fetch invoices for this patient
$invoices = [];
try {
    $stmt = $pdo->prepare(
        "SELECT i.*, CONCAT(d.first_name,' ',d.last_name) AS doctor_name
         FROM invoices i
         LEFT JOIN appointments a ON i.appointment_id = a.id
         LEFT JOIN doctors d ON a.doctor_id = d.id
         WHERE i.patient_id = ?
         ORDER BY i.transaction_date DESC"
    );
    $stmt->execute([$session_patient_id]);
    $invoices = $stmt->fetchAll();
} catch (PDOException $e) {
    // Non-fatal — table may not exist yet
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Form</title>
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
      --blue-50: #eff6ff;
      --blue-100: #dbeafe;
      --blue-500: #3b82f6;
      --blue-800: #1e40af;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
      --red-500: #ef4444;
      --red-600: #dc2626;
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
    }

    h4 {
      font-size: 1rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    a {
      color: var(--healthcare-500);
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
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
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 0.625rem;
      border: 1px solid var(--gray-200);
      border-radius: 0.375rem;
      font-size: 0.875rem;
      transition: border-color 0.2s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: var(--healthcare-500);
      box-shadow: 0 0 0 1px var(--healthcare-500);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .terms-checkbox {
      display: flex;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .terms-checkbox input {
      margin-top: 0.25rem;
      margin-right: 0.5rem;
    }

    .terms-checkbox label {
      font-size: 0.875rem;
      margin-bottom: 0;
    }

    /* Buttons */
    .btn-primary {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 1rem;
      background-color: var(--healthcare-500);
      color: white;
      border: none;
      border-radius: 0.375rem;
      font-size: 1.125rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .btn-primary:hover {
      background-color: var(--healthcare-600);
    }

    .btn-primary:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }

    .btn-primary i {
      margin-left: 0.5rem;
    }

    .btn-outline {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem 1.5rem;
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

    /* Payment form */
    .payment-form {
      background-color: white;
      border-radius: 0.75rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--gray-100);
      overflow: hidden;
      animation: fadeInUp 0.4s ease-out;
    }

    .form-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--gray-100);
      background-color: var(--healthcare-50);
    }

    .form-content {
      padding: 1.5rem;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }

    @media (min-width: 992px) {
      .form-grid {
        grid-template-columns: 2fr 3fr;
      }
    }

    /* Invoice summary */
    .summary-box {
      background-color: var(--gray-50);
      border-radius: 0.5rem;
      border: 1px solid var(--gray-100);
      padding: 1rem;
      margin-bottom: 1rem;
    }

    .invoice-details,
    .invoice-items,
    .invoice-totals {
      margin-bottom: 1rem;
    }

    .invoice-items {
      border-top: 1px solid var(--gray-200);
      padding-top: 0.75rem;
    }

    .invoice-totals {
      border-top: 1px solid var(--gray-200);
      padding-top: 0.75rem;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
    }

    .detail-row.total {
      font-weight: 600;
      font-size: 1rem;
      padding-top: 0.5rem;
    }

    .label {
      color: var(--gray-600);
    }

    .total-amount {
      color: var(--healthcare-700);
    }

    #invoice-items-list {
      list-style: none;
      margin-bottom: 1rem;
    }

    /* Info box */
    .info-box {
      background-color: var(--blue-50);
      border-radius: 0.5rem;
      border: 1px solid var(--blue-100);
      padding: 1rem;
    }

    .info-content {
      display: flex;
      gap: 0.5rem;
    }

    .info-icon {
      color: var(--blue-500);
      flex-shrink: 0;
    }

    .info-title {
      font-weight: 500;
      margin-bottom: 0.25rem;
      color: var(--blue-800);
    }

    .info-content p {
      font-size: 0.875rem;
      color: var(--blue-800);
    }

    /* Tabs */
    .tabs {
      margin-bottom: 1.5rem;
    }

    .tab-list {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
      margin-bottom: 1.5rem;
    }

    .tab-trigger {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem;
      background-color: var(--gray-100);
      border: 1px solid var(--gray-200);
      border-radius: 0.375rem;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .tab-trigger i {
      margin-right: 0.5rem;
    }

    .tab-trigger.active {
      background-color: white;
      border-color: var(--healthcare-200);
      color: var(--healthcare-600);
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .tab-pane {
      display: none;
    }

    .tab-pane.active {
      display: block;
      animation: fadeIn 0.3s ease-out;
    }

    /* Alerts */
    .alert {
      border-radius: 0.5rem;
      padding: 1rem;
      margin-bottom: 1rem;
      display: flex;
      gap: 0.5rem;
    }

    .alert-icon {
      flex-shrink: 0;
    }

    .alert-title {
      font-weight: 500;
      margin-bottom: 0.25rem;
    }

    .alert p, .alert ol {
      font-size: 0.875rem;
    }

    .alert ol {
      margin-left: 1.5rem;
    }

    .alert-warning {
      background-color: var(--yellow-50);
      border: 1px solid var(--yellow-100);
    }

    .alert-warning .alert-icon {
      color: var(--yellow-500);
    }

    .alert-warning .alert-title {
      color: var(--yellow-800);
    }

    .alert-info {
      background-color: var(--blue-50);
      border: 1px solid var(--blue-100);
    }

    .alert-info .alert-icon {
      color: var(--blue-500);
    }

    .alert-info .alert-title {
      color: var(--blue-800);
    }

    .bank-details {
      margin-top: 0.5rem;
    }

    .bank-details p {
      margin-bottom: 0.25rem;
    }

    .bank-details .label {
      font-weight: 500;
    }

    /* Success screen */
    .success-screen {
      background-color: white;
      border-radius: 0.75rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--gray-100);
      overflow: hidden;
      animation: scaleIn 0.4s ease-out;
    }

    .success-header {
      padding: 1.5rem;
      text-align: center;
      background-color: var(--green-50);
      border-bottom: 1px solid var(--green-100);
    }

    .success-icon-wrapper {
      width: 4rem;
      height: 4rem;
      background-color: var(--green-100);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      animation: scaleIn 0.4s ease-out 0.2s both;
    }

    .success-icon {
      width: 2rem;
      height: 2rem;
      color: var(--green-600);
    }

    .success-content {
      padding: 1.5rem;
    }

    .success-details {
      margin-bottom: 1.5rem;
    }

    .details-box {
      background-color: var(--gray-50);
      border-radius: 0.5rem;
      border: 1px solid var(--gray-100);
      padding: 1rem;
    }

    .success-amount {
      color: var(--green-600);
      font-weight: 600;
    }

    .whats-next {
      margin-bottom: 1.5rem;
    }

    .check-list {
      list-style: none;
    }

    .check-list li {
      display: flex;
      align-items: flex-start;
      margin-bottom: 0.5rem;
    }

    .check-icon {
      color: var(--green-500);
      margin-right: 0.5rem;
      flex-shrink: 0;
      margin-top: 0.125rem;
    }

    .success-actions {
      display: flex;
      justify-content: center;
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

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes scaleIn {
      from {
        opacity: 0;
        transform: scale(0.8);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .hidden {
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div id="payment-form" class="payment-form">
      <div class="form-header">
        <h2>Payment Details</h2>
        <p>Complete your payment to confirm your appointment</p>
      </div>
      
      <div class="form-content">
        <div class="form-grid">
          <!-- Left column: Invoice summary -->
          <div class="invoice-summary">
            <div class="summary-box">
              <h3>Invoice Summary</h3>
              
              <div class="invoice-details">
                <div class="detail-row">
                  <span class="label">Invoice Number:</span>
                  <span id="invoice-number">INV-1234-2025</span>
                </div>
                <div class="detail-row">
                  <span class="label">Date:</span>
                  <span id="invoice-date">April 4, 2025</span>
                </div>
              </div>
              
              <div class="invoice-items">
                <h4>Items</h4>
                <ul id="invoice-items-list">
                  <li class="detail-row">
                    <span>Appointment with Specialist</span>
                    <span>KES 2,500.00</span>
                  </li>
                </ul>
              </div>
              
              <div class="invoice-totals">
                <div class="detail-row">
                  <span class="label">Subtotal:</span>
                  <span id="subtotal">KES 2,500.00</span>
                </div>
                <div class="detail-row">
                  <span class="label">VAT (16%):</span>
                  <span id="tax">KES 400.00</span>
                </div>
                <div class="detail-row total">
                  <span>Total:</span>
                  <span id="total" class="total-amount">KES 2,900.00</span>
                </div>
              </div>
            </div>
            
            <div class="info-box">
              <div class="info-content">
                <i data-lucide="info" class="info-icon"></i>
                <div>
                  <p class="info-title">Payment Note</p>
                  <p>Payment is required to confirm your appointment. Your slot will be reserved for 30 minutes to complete the payment.</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right column: Payment form -->
          <div class="payment-methods">
            <div class="tabs">
              <div class="tab-list">
                <button class="tab-trigger active" data-tab="card">
                  <i data-lucide="credit-card"></i>
                  Card
                </button>
                <button class="tab-trigger" data-tab="mpesa">
                  <i data-lucide="smartphone"></i>
                  M-Pesa
                </button>
                <button class="tab-trigger" data-tab="bank">
                  <i data-lucide="building-2"></i>
                  Bank
                </button>
              </div>
              
              <div class="tab-content">
                <!-- Card payment form -->
                <div class="tab-pane active" id="card-tab">
                  <div class="form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456">
                  </div>
                  
                  <div class="form-group">
                    <label for="cardHolder">Cardholder Name</label>
                    <input type="text" id="cardHolder" name="cardHolder" placeholder="John Doe">
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group">
                      <label for="expiryDate">Expiry Date</label>
                      <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY">
                    </div>
                    
                    <div class="form-group">
                      <label for="cvv">CVV</label>
                      <input type="password" id="cvv" name="cvv" placeholder="123" maxlength="4">
                    </div>
                  </div>
                </div>
                
                <!-- M-Pesa payment form -->
                <div class="tab-pane" id="mpesa-tab">
                  <div class="form-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" placeholder="e.g. 254712345678">
                  </div>
                  
                  <div class="alert alert-warning">
                    <i data-lucide="alert-circle" class="alert-icon"></i>
                    <div>
                      <p class="alert-title">M-Pesa Payment Instructions</p>
                      <ol>
                        <li>Enter your M-Pesa registered phone number above.</li>
                        <li>Click on the "Pay Now" button.</li>
                        <li>You will receive a prompt on your phone to enter your M-Pesa PIN.</li>
                        <li>Enter your PIN to complete the payment.</li>
                      </ol>
                    </div>
                  </div>
                </div>
                
                <!-- Bank transfer form -->
                <div class="tab-pane" id="bank-tab">
                  <div class="alert alert-info">
                    <i data-lucide="info" class="alert-icon"></i>
                    <div>
                      <p class="alert-title">Bank Transfer Details</p>
                      <p>Please make a transfer to the following account:</p>
                      <div class="bank-details">
                        <p><span class="label">Bank Name:</span> Healthcare Bank</p>
                        <p><span class="label">Account Name:</span> Healthcare Ltd</p>
                        <p><span class="label">Account Number:</span> 1234567890</p>
                        <p><span class="label">Branch:</span> Main Branch</p>
                        <p><span class="label">Reference:</span> <span id="bank-reference">INV-1234-2025</span></p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="accountName">Account Holder Name</label>
                    <input type="text" id="accountName" name="accountName" placeholder="Enter the name on your account">
                  </div>
                  
                  <div class="form-group">
                    <label for="bankName">Bank Name</label>
                    <input type="text" id="bankName" name="bankName" placeholder="Enter your bank name">
                  </div>
                  
                  <div class="form-group">
                    <label for="transactionRef">Transaction Reference</label>
                    <input type="text" id="transactionRef" name="transactionRef" placeholder="Enter the transaction reference/number">
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-footer">
              <div class="terms-checkbox">
                <input type="checkbox" id="terms" name="terms" checked>
                <label for="terms">
                  I agree to the <a href="#">terms and conditions</a> and <a href="#">privacy policy</a>
                </label>
              </div>
              
              <button id="submit-payment" class="btn-primary">
                Pay KES 2,900.00
                <i data-lucide="arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Success screen (hidden by default) -->
    <div id="success-screen" class="success-screen hidden">
      <div class="success-header">
        <div class="success-icon-wrapper">
          <i data-lucide="check-circle-2" class="success-icon"></i>
        </div>
        <h2>Payment Successful!</h2>
        <p>Thank you for your payment. Your transaction has been completed successfully.</p>
      </div>
      
      <div class="success-content">
        <div class="success-details">
          <h3>Invoice Details</h3>
          <div class="details-box">
            <div class="detail-row">
              <span class="label">Invoice Number:</span>
              <span id="success-invoice-number">INV-1234-2025</span>
            </div>
            <div class="detail-row">
              <span class="label">Date:</span>
              <span id="success-date">April 4, 2025</span>
            </div>
            <div class="detail-row">
              <span class="label">Amount Paid:</span>
              <span id="success-amount" class="success-amount">KES 2,900.00</span>
            </div>
            <div class="detail-row">
              <span class="label">Payment Method:</span>
              <span id="success-method">Credit/Debit Card</span>
            </div>
          </div>
        </div>
        
        <div class="whats-next">
          <h3>What's Next?</h3>
          <ul class="check-list">
            <li>
              <i data-lucide="check" class="check-icon"></i>
              <span>An email confirmation has been sent to your registered email address.</span>
            </li>
            <li>
              <i data-lucide="check" class="check-icon"></i>
              <span>You can view your appointment details and invoice in your account dashboard.</span>
            </li>
            <li>
              <i data-lucide="check" class="check-icon"></i>
              <span>Please arrive 15 minutes before your scheduled appointment time.</span>
            </li>
          </ul>
        </div>
        
        <div class="success-actions">
          <button class="btn-outline" id="back-home">Back to Home</button>
          <button class="btn-primary" id="download-invoice">
            <i data-lucide="file-text"></i>
            Download Invoice
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Lucide icons
      const lucide = window.lucide;
      lucide.createIcons();
      
      // Get DOM elements
      const paymentForm = document.getElementById('payment-form');
      const successScreen = document.getElementById('success-screen');
      const submitButton = document.getElementById('submit-payment');
      const backHomeButton = document.getElementById('back-home');
      const downloadInvoiceButton = document.getElementById('download-invoice');
      const tabTriggers = document.querySelectorAll('.tab-trigger');
      const tabPanes = document.querySelectorAll('.tab-pane');
      
      // Current payment method
      let currentPaymentMethod = 'card';
      
      // Generate invoice data
      const invoice = generateInvoice();
      
      // Populate invoice data in the UI
      populateInvoiceData(invoice);
      
      // Tab switching functionality
      tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
          const tabId = trigger.getAttribute('data-tab');
          
          // Update active tab trigger
          tabTriggers.forEach(t => t.classList.remove('active'));
          trigger.classList.add('active');
          
          // Update active tab pane
          tabPanes.forEach(pane => pane.classList.remove('active'));
          document.getElementById(`${tabId}-tab`).classList.add('active');
          
          // Update current payment method
          currentPaymentMethod = tabId;
          
          // Update payment button text
          updatePaymentButtonText(invoice.total);
        });
      });
      
      // Form submission
      submitButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
          return;
        }
        
        // Show processing state
        submitButton.disabled = true;
        submitButton.textContent = 'Processing Payment...';
        
        // Simulate payment processing
        setTimeout(() => {
          // Show success screen
          paymentForm.style.display = 'none';
          successScreen.classList.remove('hidden');
          
          // Update success screen data
          document.getElementById('success-invoice-number').textContent = invoice.invoiceNumber;
          document.getElementById('success-date').textContent = formatDate(invoice.date);
          document.getElementById('success-amount').textContent = formatCurrency(invoice.total);
          document.getElementById('success-method').textContent = getPaymentMethodName(currentPaymentMethod);
          
          // Reset form state
          submitButton.disabled = false;
          updatePaymentButtonText(invoice.total);
        }, 2000);
      });
      
      // Back to home button
      backHomeButton.addEventListener('click', function() {
        // In a real app, this would navigate to the home page
        // For demo purposes, we'll just reset the form
        successScreen.classList.add('hidden');
        paymentForm.style.display = 'block';
        resetForm();
      });
      
      // Download invoice button
      downloadInvoiceButton.addEventListener('click', function() {
        // In a real app, this would generate and download a PDF
        showToast('Invoice Downloaded', 'Your invoice has been downloaded successfully.');
      });
      
      // Helper functions
      function generateInvoice() {
        // In a real app, this data would come from the server or previous page
        const isNewPatient = Math.random() > 0.5;
        const items = [
          {
            id: '1',
            description: 'Appointment with Specialist',
            amount: 2500,
          }
        ];
        
        // Add registration fee for new patients
        if (isNewPatient) {
          items.push({
            id: '2',
            description: 'New Patient Registration',
            amount: 500,
          });
        }
        
        // Calculate total
        const subtotal = items.reduce((sum, item) => sum + item.amount, 0);
        const tax = subtotal * 0.16; // 16% tax
        const total = subtotal + tax;
        
        return {
          items,
          subtotal,
          tax,
          total,
          invoiceNumber: `INV-${Math.floor(Math.random() * 10000)}-${new Date().getFullYear()}`,
          date: new Date(),
        };
      }
      
      function populateInvoiceData(invoice) {
        // Update invoice number and date
        document.getElementById('invoice-number').textContent = invoice.invoiceNumber;
        document.getElementById('invoice-date').textContent = formatDate(invoice.date);
        document.getElementById('bank-reference').textContent = invoice.invoiceNumber;
        
        // Update invoice items
        const itemsList = document.getElementById('invoice-items-list');
        itemsList.innerHTML = '';
        
        invoice.items.forEach(item => {
          const li = document.createElement('li');
          li.className = 'detail-row';
          li.innerHTML = `
            <span>${item.description}</span>
            <span>${formatCurrency(item.amount)}</span>
          `;
          itemsList.appendChild(li);
        });
        
        // Update totals
        document.getElementById('subtotal').textContent = formatCurrency(invoice.subtotal);
        document.getElementById('tax').textContent = formatCurrency(invoice.tax);
        document.getElementById('total').textContent = formatCurrency(invoice.total);
        
        // Update payment button text
        updatePaymentButtonText(invoice.total);
      }
      
      function updatePaymentButtonText(amount) {
        submitButton.innerHTML = `
          Pay ${formatCurrency(amount)}
          <i data-lucide="arrow-right"></i>
        `;
        lucide.createIcons();
      }
      
      function validateForm() {
        if (currentPaymentMethod === 'card') {
          const cardNumber = document.getElementById('cardNumber').value;
          const cardHolder = document.getElementById('cardHolder').value;
          const expiryDate = document.getElementById('expiryDate').value;
          const cvv = document.getElementById('cvv').value;
          
          if (!cardNumber || !cardHolder || !expiryDate || !cvv) {
            showToast('Missing information', 'Please fill in all required card details');
            return false;
          }
        } else if (currentPaymentMethod === 'mpesa') {
          const phoneNumber = document.getElementById('phoneNumber').value;
          
          if (!phoneNumber) {
            showToast('Missing information', 'Please enter your phone number');
            return false;
          }
        } else if (currentPaymentMethod === 'bank') {
          const accountName = document.getElementById('accountName').value;
          const bankName = document.getElementById('bankName').value;
          const transactionRef = document.getElementById('transactionRef').value;
          
          if (!accountName || !bankName || !transactionRef) {
            showToast('Missing information', 'Please fill in all required bank transfer details');
            return false;
          }
        }
        
        // Check terms agreement
        const termsChecked = document.getElementById('terms').checked;
        if (!termsChecked) {
          showToast('Terms and Conditions', 'Please agree to the terms and conditions');
          return false;
        }
        
        return true;
      }
      
      function resetForm() {
        // Reset form fields
        document.getElementById('cardNumber').value = '';
        document.getElementById('cardHolder').value = '';
        document.getElementById('expiryDate').value = '';
        document.getElementById('cvv').value = '';
        document.getElementById('phoneNumber').value = '';
        document.getElementById('accountName').value = '';
        document.getElementById('bankName').value = '';
        document.getElementById('transactionRef').value = '';
        
        // Reset to card tab
        tabTriggers[0].click();
      }
      
      function formatCurrency(amount) {
        return new Intl.NumberFormat('en-KE', {
          style: 'currency',
          currency: 'KES',
          minimumFractionDigits: 2,
        }).format(amount);
      }
      
      function formatDate(date) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
      }
      
      function getPaymentMethodName(method) {
        switch (method) {
          case 'card':
            return 'Credit/Debit Card';
          case 'mpesa':
            return 'M-Pesa';
          case 'bank':
            return 'Bank Transfer';
          default:
            return 'Unknown';
        }
      }
      
      function showToast(title, message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
          <div class="toast-header">
            <strong>${title}</strong>
            <button type="button" class="toast-close">&times;</button>
          </div>
          <div class="toast-body">${message}</div>
        `;
        
        // Add to document
        document.body.appendChild(toast);
        
        // Add styles
        toast.style.position = 'fixed';
        toast.style.bottom = '1rem';
        toast.style.right = '1rem';
        toast.style.backgroundColor = type === 'info' ? 'white' : '#FEF2F2';
        toast.style.color = type === 'info' ? 'var(--gray-800)' : '#B91C1C';
        toast.style.padding = '0.75rem 1rem';
        toast.style.borderRadius = '0.375rem';
        toast.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
        toast.style.maxWidth = '24rem';
        toast.style.zIndex = '50';
        toast.style.animation = 'fadeInUp 0.3s ease-out';
        toast.style.border = type === 'info' ? '1px solid var(--gray-200)' : '1px solid #FEE2E2';
        
        // Close button functionality
        const closeButton = toast.querySelector('.toast-close');
        closeButton.addEventListener('click', () => {
          document.body.removeChild(toast);
        });
        
        // Auto-dismiss after 4 seconds
        setTimeout(() => {
          if (document.body.contains(toast)) {
            document.body.removeChild(toast);
          }
        }, 4000);
      }
    });
  </script>
</body>
</html>