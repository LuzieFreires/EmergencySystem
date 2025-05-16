<?php
require_once '../classes/Resident.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

// Verify authentication
$auth = new Auth();
if (!$auth->isAuthenticated() || !$auth->hasRole('responder')) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$residentId = $_GET['resident_id'] ?? 0;

if (!$residentId) {
    http_response_code(400);
    echo json_encode(['error' => 'Resident ID is required']);
    exit;
}

try {
    $resident = new Resident();
    $medicalInfo = $resident->getMedicalInfo($residentId);
    echo json_encode($medicalInfo);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}