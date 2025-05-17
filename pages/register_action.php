<?php
require_once '../classes/Database.php';
require_once '../classes/Auth.php';
require_once '../classes/Validator.php';
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Initialize database connection
    $database = new Database();
    $db = $database->connect();
    $auth = new Auth($db);
    $validator = new Validator();

    // Collect and sanitize form data
    $username = trim($_POST['username'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';

    // Validate basic fields
    $validator->validateUsername($username);
    $validator->validateEmail($email);
    $validator->validatePassword($password);

    if ($validator->hasErrors()) {
        throw new Exception(implode(', ', $validator->getErrors()));
    }

    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }

    // Start transaction
    $db->beginTransaction();

    // Register base user account
    $userId = $auth->register($username, $password, $email);

    if (!$userId) {
        throw new Exception('Failed to create user account');
    }

    // Handle specific user type registration
    if ($user_type === 'resident') {
        $address = trim($_POST['address'] ?? '');
        $contact_number = trim($_POST['contact_number'] ?? '');

        if (empty($address) || empty($contact_number)) {
            throw new Exception('Address and contact number are required for residents');
        }

        $stmt = $db->prepare("INSERT INTO residents (user_id, address, contact_number) VALUES (?, ?, ?)");
        if (!$stmt->execute([$userId, $address, $contact_number])) {
            throw new Exception('Failed to create resident profile');
        }
    } elseif ($user_type === 'responder') {
        $specialization = trim($_POST['specialization'] ?? '');
        $responder_contact = trim($_POST['responder_contact'] ?? '');

        if (empty($specialization) || empty($responder_contact)) {
            throw new Exception('Specialization and contact number are required for responders');
        }

        $stmt = $db->prepare("INSERT INTO responders (user_id, specialization, contact_number) VALUES (?, ?, ?)");
        if (!$stmt->execute([$userId, $specialization, $responder_contact])) {
            throw new Exception('Failed to create responder profile');
        }
    } else {
        throw new Exception('Invalid user type');
    }

    // Commit transaction
    $db->commit();

    echo json_encode(['success' => true, 'message' => 'Registration successful']);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log('Registration error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed: ' . $e->getMessage()
    ]);
}