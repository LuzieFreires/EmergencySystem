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

$database = new Database();
$db = $database->connect();
$auth = new Auth($db);
$validator = new Validator();

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

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

try {
    if ($auth->register($username, $password, $email)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}