<?php
require_once '../databases/Database.php';
require_once '../classes/Auth.php';
require_once '../classes/Validator.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $auth = new Auth($db);
    $validator = new Validator();

    // Get and validate basic user data
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';

    $validator->validateUsername($username);
    $validator->validateEmail($email);
    $validator->validatePassword($password);

    if ($validator->hasErrors()) {
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->getErrors()
        ]);
        exit;
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
        $address = $_POST['address'] ?? '';
        $contact_number = $_POST['contact_number'] ?? '';

        if (empty($address) || empty($contact_number)) {
            throw new Exception('Address and contact number are required for residents');
        }

        $stmt = $db->prepare("INSERT INTO residents (user_id, address, contact_number) VALUES (?, ?, ?)");
        if (!$stmt->execute([$userId, $address, $contact_number])) {
            throw new Exception('Failed to create resident profile');
        }
    } elseif ($user_type === 'responder') {
        $specialization = $_POST['specialization'] ?? '';
        $responder_contact = $_POST['responder_contact'] ?? '';

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