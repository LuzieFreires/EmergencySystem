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

    // Get active emergencies
    $emergency = new Emergency();
    $emergencies = $emergency->getActiveEmergencies();

    // Format the response
    $formattedEmergencies = array_map(function($e) {
        return [
            'id' => $e['id'],
            'type' => $e['type'],
            'description' => $e['description'],
            'latitude' => floatval($e['latitude']),
            'longitude' => floatval($e['longitude']),
            'resident_name' => $e['resident_name'],
            'created_at' => $e['created_at'],
            'priority' => $e['priority'],
            'status' => $e['status']
        ];
    }, $emergencies);

    echo json_encode($formattedEmergencies);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}