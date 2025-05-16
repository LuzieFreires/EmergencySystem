<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responders - Emergency Alert System</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>
        <main class="main-content">
            <div class="dashboard-container">
                <section class="dashboard-header">
                    <h1>Responder Dashboard</h1>
                    <p class="description">Monitor and respond to community emergencies in real-time.</p>
                </section>

                <div class="dashboard-main">
                    <div class="features-grid">
                        <!-- Responder Status Section -->
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

                        <!-- Active Emergencies Section -->
                        <div class="feature-card">
                            <h4>Active Emergencies</h4>
                            <div id="activeEmergencies" class="emergency-list">
                                <!-- Emergency list will be loaded dynamically -->
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Map Section -->
                    <div class="feature-card full-width map-section">
                        <h4>Emergency Map</h4>
                        <div id="emergencyMap" class="map-container"></div>
                    </div>

                    <!-- Medical Information Section -->
                    <div class="feature-card full-width">
                        <h4>Medical Information</h4>
                        <div class="medical-tabs">
                            <div class="tab-header">
                                <button class="tab-btn active" data-tab="general">General Info</button>
                                <button class="tab-btn" data-tab="conditions">Medical Conditions</button>
                                <button class="tab-btn" data-tab="history">Medical History</button>
                            </div>
                            <div class="tab-content">
                                <div id="generalInfo" class="tab-panel active">
                                    <div class="info-grid">
                                        <div class="info-card">
                                            <h5>Basic Information</h5>
                                            <p>Patient's general health status and vital information.</p>
                                        </div>
                                        <div class="info-card">
                                            <h5>Emergency Contacts</h5>
                                            <p>List of primary and secondary emergency contacts.</p>
                                        </div>
                                        <div class="info-card">
                                            <h5>Blood Type</h5>
                                            <p>Patient's blood type and relevant transfusion information.</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="conditions" class="tab-panel">
                                    <div class="info-grid">
                                        <div class="info-card">
                                            <h5>Current Conditions</h5>
                                            <p>Active medical conditions requiring attention.</p>
                                        </div>
                                        <div class="info-card">
                                            <h5>Allergies</h5>
                                            <p>Known allergies and reaction severity.</p>
                                        </div>
                                        <div class="info-card">
                                            <h5>Medications</h5>
                                            <p>Current medication regimen and dosage information.</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="history" class="tab-panel">
                                    <div class="info-grid">
                                        <div class="info-card">
                                            <h5>Past Conditions</h5>
                                            <p>Historical medical conditions and treatments.</p>
                                        </div>
                                        <div class="info-card">
                                            <h5>Surgeries</h5>
                                            <p>Previous surgical procedures and dates.</p>
                                        </div>
                                        <div class="info-card">
                                            <h5>Family History</h5>
                                            <p>Relevant family medical history information.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../assets/js/responders.js"></script>
</body>
</html>