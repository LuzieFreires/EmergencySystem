<?php
require_once '../classes/Auth.php';
require_once '../classes/User.php';
require_once '../classes/Validator.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = Auth::getCurrentUser();
$data = $_POST;

// Validate current password
if (!password_verify($data['current_password'], $user->password)) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    exit;
}

// Validate email if changed
if ($data['email'] !== $user->email) {
    if (!Validator::isValidEmail($data['email'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
}

// Update user data
$requiresRelogin = false;
$updates = ['email' => $data['email']];

// Handle password update
if (!empty($data['new_password'])) {
    if (strlen($data['new_password']) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    $updates['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
    $requiresRelogin = true;
}

// Update type-specific fields
if ($user->user_type === 'resident') {
    $updates['address'] = $data['address'];
    $updates['contact_number'] = $data['contact_number'];
} else if ($user->user_type === 'responder') {
    $updates['specialization'] = $data['specialization'];
    $updates['responder_contact'] = $data['responder_contact'];
}

// Perform update
try {
    $user->update($updates);
    echo json_encode([
        'success' => true,
        'requiresRelogin' => $requiresRelogin,
        'message' => 'Profile updated successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update profile'
    ]);
}