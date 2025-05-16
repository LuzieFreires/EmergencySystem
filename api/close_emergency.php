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

    // Get and validate input
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['emergency_id'])) {
        throw new Exception('Emergency ID is required');
    }

    // Close the emergency
    $emergency = new Emergency();
    $success = $emergency->closeEmergency($data['emergency_id'], $responderId);

    if ($success) {
        // Get emergency details for SMS notification
        $details = $emergency->getEmergencyDetails($data['emergency_id']);
        
        // Send SMS notification
        $sms = new SMS();
        $message = "Your emergency has been resolved. Thank you for using our service.";
        $sms->send($details['phone'], $message);

        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to close emergency');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}