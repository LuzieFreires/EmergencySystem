<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'emergency_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// System Configuration
define('SITE_NAME', 'Community Emergency Alert System');
define('SITE_URL', 'http://localhost/EmergencySystem');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('HASH_COST', 10); // Password hashing cost

// Emergency Types
define('EMERGENCY_TYPES', [
    'medical' => 'Medical Emergency',
    'fire' => 'Fire Emergency',
    'police' => 'Police Emergency',
    'disaster' => 'Natural Disaster',
    'accident' => 'Accident'
]);

// Alert Severity Levels
define('ALERT_LEVELS', [
    'low' => 'Low Priority',
    'medium' => 'Medium Priority',
    'high' => 'High Priority',
    'critical' => 'Critical Priority'
]);

// System Status
define('SYSTEM_TIMEZONE', 'America/New_York');
date_default_timezone_set(SYSTEM_TIMEZONE);

// Error Reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}

// Session Configuration
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true
]);

// System Paths
define('ROOT_PATH', __DIR__);
define('CLASSES_PATH', ROOT_PATH . '/classes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('API_PATH', ROOT_PATH . '/api');
define('ASSETS_PATH', ROOT_PATH . '/assets');