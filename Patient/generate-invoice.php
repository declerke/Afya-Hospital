<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    http_response_code(403);
    die("Access denied.");
}
require_once 'config/db.php';
require_once 'vendor/autoload.php';

if (empty($_GET['id'])) {
    die("Invoice ID is required.");
}

$invoice_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT i.*, p.first_name AS patient_first_name, p.last_name AS patient_last_name,
               p.email AS patient_email, p.contact_number AS patient_phone,
               a.appointment_date, a.appointment_time,
               d.first_name AS doctor_first_name, d.last_name AS doctor_last_name, d.department
        FROM invoices i
        LEFT JOIN patients p ON i.patient_id = p.id
        LEFT JOIN appointments a ON i.appointment_id = a.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        WHERE i.id = ? AND i.patient_id = ?
    ");
    $stmt->execute([$invoice_id, $_SESSION['patient_id']]);
    $invoice = $stmt->fetch();

    if (!$invoice) {
        die("Invoice not found.");
    }

    $items = !empty($invoice['items']) ? json_decode($invoice['items'], true) : [
        ['description' => $invoice['description'] ?? 'Medical Services', 'amount' => $invoice['total_amount']]
    ];
} catch (PDOException $e) {
    die("Error retrieving invoice.");
}

class PDF extends \FPDF {
    function Header() {
        $this->Image('img/logo.png', 10, 10, 50);
        $this->Ln(20);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function InvoiceTitle($title) {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, $title, 0, 1, 'C');
        $this->Ln(5);
    }

    function InvoiceInfo($invoice) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50, 10, 'Invoice #: ' . $invoice['invoice_number'], 0, 0);
        $this->Cell(50, 10, 'Date: ' . date('F j, Y', strtotime($invoice['invoice_date'])), 0, 0);
        $this->Cell(50, 10, 'Due Date: ' . date('F j, Y', strtotime($invoice['due_date'])), 0, 1);
        $this->Cell(50, 10, 'Status: ' . $invoice['status'], 0, 1);
        $this->Ln(5);
    }

    function InvoiceItems($items) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Invoice Items:', 0, 1);

        $this->SetFillColor(200, 220, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(120, 8, 'Description', 1, 0, 'L', true);
        $this->Cell(70, 8, 'Amount', 1, 1, 'R', true);

        $this->SetFont('Arial', '', 10);
        $total = 0;

        foreach ($items as $item) {
            $this->Cell(120, 8, $item['description'], 1, 0, 'L');
            $this->Cell(70, 8, 'KES ' . number_format($item['amount'], 2), 1, 1, 'R');
            $total += $item['amount'];
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(120, 8, 'Total', 1, 0, 'L', true);
        $this->Cell(70, 8, 'KES ' . number_format($total, 2), 1, 1, 'R', true);
        $this->Ln(5);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->InvoiceTitle('INVOICE');
$pdf->InvoiceInfo($invoice);
$pdf->InvoiceItems($items);
$pdf->Output('Invoice-' . $invoice['invoice_number'] . '.pdf', 'I');
exit;
?>