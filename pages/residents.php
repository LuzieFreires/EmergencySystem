<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resident Dashboard | Community Emergency Alert System</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Resident Dashboard</h1>
                <p class="description">Manage your emergency alerts and medical details efficiently.</p>
            </header>

            <!-- Emergency Button -->
            <section class="feature-card emergency-section">
                <header class="emergency-header">
                    <h2>Emergency Assistance</h2>
                    <p>Need immediate help? Submit an emergency alert now.</p>
                </header>
                <a href="emergency-form.php" class="emergency-btn pulse">Report Emergency</a>
            </section>

            <div class="dashboard-grid">
                <!-- Medical Information Form -->
                <section class="feature-card medical-info-section">
                    <h2>Medical Information</h2>
                    <form id="updateMedicalInfo" class="medical-info-form">
                        <div class="form-group">
                            <label for="medicalCondition">Medical Conditions</label>
                            <textarea id="medicalCondition" name="medicalCondition" rows="4" 
                                      placeholder="List any current medical conditions..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="allergies">Allergies</label>
                            <input type="text" id="allergies" name="allergies" 
                                   placeholder="Enter any allergies..." />
                        </div>

                        <div class="form-group">
                            <label for="medications">Current Medications</label>
                            <textarea id="medications" name="medications" rows="3" 
                                      placeholder="List your current medications..."></textarea>
                        </div>

                        <button type="submit" class="update-btn">Update Medical Information</button>
                    </form>
                </section>

           <!-- Alert History -->
<section class="feature-card alert-history-section">
    <h2>Alert History</h2>
    <div id="alertHistory" class="history-list">
        <div class="loading-placeholder">Your alert history will appear here once you’ve submitted an emergency.</div>
    </div>
</section>

<!-- Available Responders -->
<section class="feature-card responders-section full-width">
    <h2>Available Responders</h2>
    <div id="availableResponders" class="responders-grid">
        <div class="loading-placeholder">No responders are currently available.</div>
    </div>
</section>

        </main>
    </div>

    <script src="../assets/js/residents.js"></script>
</body>
</html>
