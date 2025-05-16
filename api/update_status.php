<?php
header('Content-Type: application/json');
require_once '../classes/Responder.php';
require_once '../classes/Auth.php';

try {
    // Verify responder is logged in
    $auth = new Auth();
    $responderId = $auth->getLoggedInUserId();
    if (!$responderId) {
        throw new Exception('Unauthorized access');
    }

    // Get and validate input
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['status'])) {
        throw new Exception('Status is required');
    }

    // Update status
    $responder = new Responder();
    $success = $responder->updateStatus($responderId, $data['status']);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to update status');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}