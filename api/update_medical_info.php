<?php
session_start();
require_once '../config/db.php'; // Your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Sanitize and fetch POST data
$medical_condition = trim($_POST['medicalCondition'] ?? '');
$allergies = trim($_POST['allergies'] ?? '');
$medications = trim($_POST['medications'] ?? '');

// Simple validation (optional)
if ($medical_condition === '' && $allergies === '' && $medications === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in at least one field.']);
    exit;
}

// Check if record exists
$stmt = $conn->prepare("SELECT id FROM medical_info WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update existing record
    $stmt = $conn->prepare("
        UPDATE medical_info 
        SET medical_condition = ?, allergies = ?, medications = ?, updated_at = NOW() 
        WHERE user_id = ?
    ");
    $stmt->bind_param("sssi", $medical_condition, $allergies, $medications, $user_id);
} else {
    // Insert new record
    $stmt = $conn->prepare("
        INSERT INTO medical_info (user_id, medical_condition, allergies, medications) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $user_id, $medical_condition, $allergies, $medications);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Medical information updated successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again later.']);
}

$stmt->close();
$conn->close();
?>
