<?php
require_once '../classes/Database.php';
require_once '../classes/Auth.php';

session_start();
$database = new Database();
$db = $database->connect();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Form - Emergency Alert System</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>
        <main class="main-content">
            <h1>Report Emergency</h1>
            <div class="emergency-form">
                <form id="emergency-form" method="POST" action="../api/save_alert.php">
                    <div class="form-group">
                        <label for="type">Emergency Type</label>
                        <select id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="medical">Medical Emergency</option>
                            <option value="fire">Fire</option>
                            <option value="police">Police</option>
                            <option value="disaster">Natural Disaster</option>
                        </select>
                    </div>

                    <div class="feature-card full-width map-section">
                        <h4>Emergency Location</h4>
                        <div id="emergencyMap" class="map-container"></div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <input type="hidden" id="location" name="location">
                    </div>

                    <div class="form-group">
                        <label for="severity">Severity Level</label>
                        <select id="severity" name="severity" required>
                            <option value="">Select Severity</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description (optional) </label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Report Emergency </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../assets/js/emergency-form.js"></script>
</body>
</html>