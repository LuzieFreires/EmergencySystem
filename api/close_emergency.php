<?php
header('Content-Type: application/json');

require_once '../classes/Emergency.php';
require_once '../classes/Auth.php';
require_once '../classes/SMS.php';

try {
    // Verify responder is logged in
    $auth = new Auth();
    $responderId = $auth->getLoggedInUserId();
    if (!$responderId) {
        throw new Exception('Unauthorized access');
    }

    // Get and validate input data
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['emergency_id'])) {
        throw new Exception('Emergency ID is required');
    }

    // Close the emergency
    $emergency = new Emergency();
    $closed = $emergency->closeEmergency($input['emergency_id'], $responderId);

    if (!$closed) {
        throw new Exception('Failed to close emergency');
    }

    // Send SMS notification to the emergency contact
    $details = $emergency->getEmergencyDetails($input['emergency_id']);
    if (!empty($details['phone'])) {
        $sms = new SMS();
        $message = "Your emergency has been resolved. Thank you for using our service.";
        $sms->send($details['phone'], $message);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
