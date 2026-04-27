<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$json_data = file_get_contents('php://input');
$data      = json_decode($json_data, true);
$message   = isset($data['message']) ? trim($data['message']) : '';
$user_id   = $_SESSION['user_id'] ?? null;

if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'No message provided']);
    exit;
}

$response = processMessage($message);

if ($user_id) {
    try {
        $stmt = $pdo->prepare("INSERT INTO chatbot_logs (user_id, message, response) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $message, $response]);
    } catch (PDOException $e) {
        // Non-fatal: log failure does not break chat
    }
}

echo json_encode(['status' => 'success', 'response' => $response]);
exit;

function processMessage(string $message): string {
    $m = strtolower(trim($message));

    // Greetings
    if (preg_match('/\b(hello|hi|hey|good morning|good afternoon|good evening|howdy)\b/', $m)) {
        return "Hello! Welcome to Afya Hospital. I'm your virtual assistant. How can I help you today?";
    }

    // Goodbye
    if (preg_match('/\b(bye|goodbye|see you|take care|farewell)\b/', $m)) {
        return "Thank you for chatting with us. Take care, and don't hesitate to reach out if you need anything. Goodbye!";
    }

    // Thanks
    if (preg_match('/\b(thank you|thanks|thank u|appreciate|cheers)\b/', $m)) {
        return "You're very welcome! Is there anything else I can help you with today?";
    }

    // Booking / appointments
    if (preg_match('/\b(book|appointment|schedule|reserve|consult)\b/', $m)) {
        return "You can book an appointment online by clicking the 'Book Appointment' button at the top of the page. Alternatively, call our reception at +254 712 345 678 or dial 1-800-AFYA. We recommend booking at least 24 hours in advance.";
    }

    // Cancel / reschedule appointment
    if (preg_match('/\b(cancel|reschedule|change appointment|move appointment)\b/', $m)) {
        return "To cancel or reschedule an appointment, please log in to the patient portal and visit your 'Appointments' section. You can also call us at +254 712 345 678. Please give at least 24 hours' notice if possible.";
    }

    // Doctors / specialists
    if (preg_match('/\b(doctor|doctors|specialist|physician|surgeon|cardiologist|neurologist|pediatrician|orthopedist|gynecologist)\b/', $m)) {
        return "Afya Hospital has specialists across six departments: Cardiology, Neurology, Pediatrics, Orthopedics, General Medicine, and Surgery. You can view our full team on the Doctors page. Would you like to book an appointment with a specific specialist?";
    }

    // Cardiology
    if (str_contains($m, 'cardiology') || str_contains($m, 'heart') || str_contains($m, 'cardiac')) {
        return "Our Cardiology department offers expert care for heart conditions including ECG, echocardiography, arrhythmia management, and heart failure treatment. To book a cardiology appointment, click 'Book Appointment' and select the Cardiology department.";
    }

    // Neurology
    if (str_contains($m, 'neurology') || str_contains($m, 'brain') || str_contains($m, 'neuro') || str_contains($m, 'seizure') || str_contains($m, 'headache') || str_contains($m, 'migraine')) {
        return "Our Neurology department treats conditions of the brain, spine, and nervous system — including epilepsy, stroke, migraines, and multiple sclerosis. Book a neurology appointment through the portal or call us directly.";
    }

    // Pediatrics
    if (str_contains($m, 'pediatric') || str_contains($m, 'child') || str_contains($m, 'children') || str_contains($m, 'baby') || str_contains($m, 'infant') || str_contains($m, 'kid')) {
        return "Our Pediatrics department provides compassionate care for children from newborns to adolescents — including vaccinations, well-child visits, and management of childhood illnesses. Book a pediatrics appointment online.";
    }

    // Orthopedics
    if (str_contains($m, 'orthopedic') || str_contains($m, 'bone') || str_contains($m, 'joint') || str_contains($m, 'fracture') || str_contains($m, 'knee') || str_contains($m, 'hip') || str_contains($m, 'spine')) {
        return "Our Orthopedics department handles bone and joint conditions including fractures, sports injuries, arthritis, and joint replacements. Contact us to schedule an orthopedics consultation.";
    }

    // Surgery
    if (str_contains($m, 'surgery') || str_contains($m, 'operation') || str_contains($m, 'surgical') || str_contains($m, 'operate')) {
        return "Our surgical team performs a range of procedures including laparoscopic, abdominal, and emergency surgeries using modern minimally invasive techniques. All surgeries require a prior consultation — book an appointment to get started.";
    }

    // General Medicine
    if (str_contains($m, 'general') || str_contains($m, 'checkup') || str_contains($m, 'check-up') || str_contains($m, 'flu') || str_contains($m, 'fever') || str_contains($m, 'sick') || str_contains($m, 'unwell')) {
        return "Our General Medicine department provides primary care for patients of all ages — from routine check-ups to managing chronic conditions like diabetes and hypertension. Walk-ins and scheduled appointments are both welcome.";
    }

    // Location / address / directions
    if (preg_match('/\b(location|address|where|directions|find you|get to|situated)\b/', $m)) {
        return "Afya Hospital is located at 123 Hospital Road, Nairobi, Kenya. We're easily accessible by public transport. Our emergency department is open 24/7, and general services run Monday–Friday 8 AM–8 PM, Saturday 9 AM–5 PM.";
    }

    // Working hours / opening times
    if (preg_match('/\b(hour|hours|open|opening|closing|time|when|schedule)\b/', $m)) {
        return "Our working hours are: Monday–Friday: 8:00 AM – 8:00 PM, Saturday: 9:00 AM – 5:00 PM, Sunday: Emergency services only (24/7). The pharmacy and laboratory are open during the same hours.";
    }

    // Emergency
    if (preg_match('/\b(emergency|urgent|critical|serious|ambulance|accident)\b/', $m)) {
        return "For medical emergencies, please call +254 712 345 678 immediately or proceed to our Emergency Department — it is open 24 hours a day, 7 days a week. Do not use this chat for life-threatening situations — call for help right away.";
    }

    // Insurance
    if (preg_match('/\b(insurance|cover|coverage|nhif|aar|jubilee|britam|insured)\b/', $m)) {
        return "We accept most major insurance providers including NHIF, AAR, Jubilee, Britam, CIC, and Resolution Health. Please bring your insurance card and national ID. For queries about specific cover, contact our billing office at billing@afyahospital.com.";
    }

    // Billing / payments / invoices
    if (preg_match('/\b(bill|billing|invoice|pay|payment|cost|fee|charge|price|mpesa|m-pesa|card)\b/', $m)) {
        return "You can view your bills and invoices in the 'Billing' section of the patient portal. We accept M-Pesa (Paybill: 400200), Visa/Mastercard, and cash at the cashier. For billing queries, call +254 712 345 679 or email billing@afyahospital.com.";
    }

    // Medical records / history
    if (preg_match('/\b(medical record|medical history|records|history|report|results|lab|test results)\b/', $m)) {
        return "Your medical records and test results are available in the 'Medical History' section of the patient portal. If you need physical copies or have trouble accessing them, contact our records office at records@afyahospital.com.";
    }

    // Feedback / complaints / suggestions
    if (preg_match('/\b(feedback|complaint|complain|suggestion|review|rate|experience)\b/', $m)) {
        return "We value your feedback! Please visit the 'Feedback' page in the portal to share your experience, suggestions, or concerns. Your input helps us improve our services.";
    }

    // Contact information
    if (preg_match('/\b(contact|phone|call|email|reach|number|whatsapp)\b/', $m)) {
        return "You can reach Afya Hospital at:\n📞 +254 712 345 678 (General)\n📞 +254 712 345 679 (Billing)\n📧 info@afyahospital.com\n📍 123 Hospital Road, Nairobi\n\nOur team is available during working hours, with 24/7 emergency support.";
    }

    // Services
    if (preg_match('/\b(service|services|offer|provide|available|department)\b/', $m)) {
        return "Afya Hospital offers: Cardiology, Neurology, Pediatrics, Orthopedics, General Medicine, and Surgery. We also have a fully equipped laboratory, pharmacy, and radiology unit. Visit our Services page for full details.";
    }

    // COVID / vaccine
    if (preg_match('/\b(covid|coronavirus|vaccine|vaccination|pcr|antigen)\b/', $m)) {
        return "We offer COVID-19 PCR testing, rapid antigen tests, and vaccinations. Testing is available Monday–Saturday during working hours. Please book in advance through the portal to avoid waiting.";
    }

    // Password / login / account
    if (preg_match('/\b(password|login|account|sign in|forgot|reset|username)\b/', $m)) {
        return "If you're having trouble logging in or need to reset your password, click the 'Forgot Password' link on the login page. For account issues, contact our support team at support@afyahospital.com.";
    }

    // Default fallback
    return "Thank you for your message. I'm not sure I fully understood that — for specific enquiries, please call us at 1-800-AFYA (+254 712 345 678) or email info@afyahospital.com. Is there anything else I can help you with?";
}
