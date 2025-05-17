<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fix the require paths
require_once '../classes/Database.php';
require_once '../classes/Auth.php';
require_once '../classes/Emergency.php';
require_once '../classes/Responder.php';

try {
    $database = new Database();
    $db = $database->connect();
    $auth = new Auth($db);
    $responder = new Responder();
    $user = $auth->getCurrentUser();

    if (!$user) {
        header("Location: login.php");
        exit;
    }

    // Handle emergency report submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $type = $_POST['type'] ?? '';
        $location = $_POST['location'] ?? '';
        $severity = $_POST['severity'] ?? '';
        $description = $_POST['description'] ?? '';

        if ($type && $location && $severity) {
            try {
                $emergency = new Emergency();
                $emergencyData = [
                    'reported_by' => $user->id,
                    'type' => $type,
                    'location' => $location,
                    'severity' => $severity,
                    'description' => $description,
                    'status' => 'active'
                ];
                
                if ($emergency->createEmergency($emergencyData)) {
                    $message = "Emergency reported successfully.";
                    $messageType = "success";
                } else {
                    $message = "Failed to report emergency.";
                    $messageType = "error";
                }
            } catch (Exception $e) {
                $message = "Error: Failed to report emergency.";
                $messageType = "error";
                error_log($e->getMessage());
            }
        } else {
            $message = "Please fill in all required fields.";
            $messageType = "warning";
        }
    }

    // Fetch active emergencies
    try {
        $emergency = new Emergency();
        $emergencies = $emergency->getActiveEmergencies();
    } catch (Exception $e) {
        $emergencies = [];
        $message = "Error: Failed to fetch emergencies.";
        $messageType = "error";
        error_log($e->getMessage());
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $message = "System error occurred. Please try again later.";
    $messageType = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Responders - Active Emergencies</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>
        <main class="main-content">
            <h1>Active Emergencies</h1>

            <?php if (!empty($message)): ?>
                <p class="alert <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <ul class="emergency-list">
                <?php if (empty($emergencies)): ?>
                    <li>No active emergencies at the moment.</li>
                <?php else: ?>
                    <?php foreach ($emergencies as $emergency): ?>
                        <li class="emergency-item severity-<?php echo htmlspecialchars($emergency['severity']); ?>">
                            <strong>Type:</strong> <?php echo htmlspecialchars($emergency['type']); ?><br>
                            <strong>Location:</strong> <?php echo htmlspecialchars($emergency['location']); ?><br>
                            <strong>Severity:</strong> <?php echo ucfirst(htmlspecialchars($emergency['severity'])); ?><br>
                            <strong>Description:</strong> <?php echo nl2br(htmlspecialchars($emergency['description'])); ?><br>
                            <strong>Reported by:</strong> <?php echo htmlspecialchars($emergency['reporter_name']); ?><br>
                            <strong>Reported at:</strong> <?php echo htmlspecialchars($emergency['created_at']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <div class="feature-card">
                <h4>Responder Status</h4>
                <div class="status-controls">
                    <select id="availabilityStatus" class="status-select">
                        <option value="available">Available</option>
                        <option value="on_duty">On Duty</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                    <button id="updateStatus" class="update-btn">Update Status</button>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/responders.js"></script>
</body>
</html>
