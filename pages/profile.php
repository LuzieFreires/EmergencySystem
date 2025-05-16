<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Emergency Alert System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>
        <main class="main-content">
            <div class="dashboard-container">
                <section class="dashboard-header">
                    <h1>My Profile</h1>
                    <p class="description">View your profile information</p>
                </section>

                <div class="dashboard-main">
                    <div class="feature-card profile-info">
                        <?php
                        require_once '../classes/Auth.php';
                        $user = Auth::getCurrentUser();
                        ?>
                        <h3>Profile Details</h3>
                        <div class="profile-details">
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($user->username); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user->email); ?></p>
                            <p><strong>User Type:</strong> <?php echo ucfirst(htmlspecialchars($user->user_type)); ?></p>
                            
                            <?php if($user->user_type == 'resident'): ?>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($user->address); ?></p>
                            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user->contact_number); ?></p>
                            <?php endif; ?>
                            
                            <?php if($user->user_type == 'responder'): ?>
                            <p><strong>Specialization:</strong> <?php echo ucfirst(htmlspecialchars($user->specialization)); ?></p>
                            <p><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($user->responder_contact); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>