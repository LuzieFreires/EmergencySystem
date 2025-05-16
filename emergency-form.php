<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Form - Emergency Alert System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>
        <main class="main-content">
            <h1>Report Emergency</h1>
            <div class="emergency-form">
                <form id="emergency-form" method="POST">
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

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Report Emergency</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="../assets/js/emergency-form.js"></script>
</body>
</html>