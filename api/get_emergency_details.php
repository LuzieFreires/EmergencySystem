<?php
header('Content-Type: application/json');
require_once '../classes/Emergency.php';
require_once '../classes/Auth.php';

try {
    // Verify responder is logged in
    $auth = new Auth();
    $responderId = $auth->getLoggedInUserId();
    if (!$responderId) {
        throw new Exception('Unauthorized access');
    }

    // Validate input
    if (!isset($_GET['id'])) {
        throw new Exception('Emergency ID is required');
    }

    // Get emergency details
    $emergency = new Emergency();
    $details = $emergency->getEmergencyDetails($_GET['id']);

    if (!$details) {
        throw new Exception('Emergency not found');
    }

    echo json_encode($details);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}