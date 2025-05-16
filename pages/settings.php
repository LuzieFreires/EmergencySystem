<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Emergency Alert System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>
        <main class="main-content">
            <div class="dashboard-container">
                <section class="dashboard-header">
                    <h1>Settings</h1>
                    <p class="description">Customize your application preferences</p>
                </section>

                <div class="dashboard-main">
                    <!-- Theme Settings Card -->
                    <div class="feature-card">
                        <h4>Theme Settings</h4>
                        <div class="theme-toggle">
                            <label for="themeSelect">Color Theme:</label>
                            <select id="themeSelect" class="theme-select">
                                <option value="light">Light Mode</option>
                                <option value="dark">Dark Mode</option>
                            </select>
                        </div>
                    </div>

                    <!-- Profile Settings Card -->
                    <div class="feature-card">
                        <h4>Profile Settings</h4>
                        <div id="error-messages" class="alert alert-danger" style="display: none;"></div>
                        <div id="success-message" class="alert alert-success" style="display: none;"></div>
                        
                        <form id="profile-update-form" method="POST">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password">
                                <small class="form-text">Required to save changes</small>
                            </div>

                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password">
                                <small class="form-text">Leave blank to keep current password</small>
                            </div>

                            <div id="resident_fields" class="additional-fields">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" id="address" name="address">
                                </div>
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="tel" id="contact_number" name="contact_number">
                                </div>
                            </div>

                            <div id="responder_fields" class="additional-fields">
                                <div class="form-group">
                                    <label for="specialization">Specialization</label>
                                    <select id="specialization" name="specialization">
                                        <option value="medical">Medical</option>
                                        <option value="fire">Fire Fighter</option>
                                        <option value="police">Police</option>
                                        <option value="rescue">Rescue</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="responder_contact">Emergency Contact Number</label>
                                    <input type="tel" id="responder_contact" name="responder_contact">
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/settings.js"></script>
    <script>
    // Apply saved theme immediately to prevent flash
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>