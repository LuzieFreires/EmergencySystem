<?php
require_once '../classes/Auth.php';
require_once '../classes/User.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = Auth::getCurrentUser();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Return user data without sensitive information
$userData = [
    'email' => $user->email,
    'user_type' => $user->user_type
];

if ($user->user_type === 'resident') {
    $userData['address'] = $user->address;
    $userData['contact_number'] = $user->contact_number;
} else if ($user->user_type === 'responder') {
    $userData['specialization'] = $user->specialization;
    $userData['responder_contact'] = $user->responder_contact;
}

echo json_encode([
    'success' => true,
    'user' => $userData
]);