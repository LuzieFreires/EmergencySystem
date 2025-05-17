<?php
require_once '../classes/Database.php';
require_once '../classes/Auth.php';
require_once '../classes/Emergency.php';

header('Content-Type: application/json');

try {
    // Initialize database and auth
    $database = new Database();
    $db = $database->connect();
    $auth = new Auth($db);
    
    // Verify user is logged in
    if (!$auth->isLoggedIn()) {
        throw new Exception('Unauthorized access');
    }
    
    // Get current user
    $user = $auth->getCurrentUser();
    
    // Validate and sanitize input
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $required = ['type', 'location', 'severity', 'description'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        $emergency = new Emergency();
        $emergencyData = [
            'reported_by' => $user->id,
            'type' => $data['type'],
            'location' => $data['location'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'severity' => $data['severity'],
            'description' => $data['description'],
            'status' => 'active'
        ];
        
        $result = $emergency->createEmergency($emergencyData);
        
        if ($result) {
            $db->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Emergency reported successfully'
            ]);
        } else {
            throw new Exception('Failed to save emergency report');
        }
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}