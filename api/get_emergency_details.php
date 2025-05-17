<?php
header('Content-Type: application/json');

require_once '../classes/Database.php';
require_once '../classes/Emergency.php';
require_once '../classes/Auth.php';

try {
    // Set up database connection
    $database = new Database();
    $db = $database->connect();

    // Auth with database dependency
    $auth = new Auth($db);
    $responderId = $auth->getLoggedInUserId();
    if (!$responderId) {
        http_response_code(401);
        throw new Exception('Unauthorized access');
    }

    // Validate input
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(422);
        throw new Exception('Valid emergency ID is required');
    }

    $emergencyId = (int) $_GET['id'];

    // Get emergency details
    $emergency = new Emergency($db);
    $details = $emergency->getEmergencyDetails($emergencyId);

    if (!$details) {
        http_response_code(404);
        throw new Exception('Emergency not found');
    }

    echo json_encode($details);
} catch (Exception $e) {
    if (http_response_code() === 200) {
        http_response_code(400);
    }
    echo json_encode(['error' => $e->getMessage()]);
}
