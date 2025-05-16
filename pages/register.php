<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Emergency Alert System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <h2>Create Account</h2>
            <div id="error-messages" class="alert alert-danger" style="display: none;"></div>
            <form id="register-form" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small class="form-text">Password must be at least 6 characters long</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="user_type">Register as</label>
                    <select id="user_type" name="user_type" required>
                        <option value="resident">Resident</option>
                        <option value="responder">Emergency Responder</option>
                    </select>
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

                <div id="responder_fields" class="additional-fields" style="display: none;">
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
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>

                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="../assets/js/register.js"></script>
</body>
</html>