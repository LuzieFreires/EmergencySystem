<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Community Emergency Alert System</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <div class="container">
        <?php include 'components/sidebar.php'; ?>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Community Emergency Alert System (CEAS)</h1>
                <p class="description">
                    Empowering communities through real-time emergency awareness and coordinated response.
                </p>
            </header>

            <section class="dashboard-main">
                <!-- About CEAS -->
                <article class="feature-card full-width">
                    <h2>About CEAS</h2>
                    <p>
                        CEAS is a web-based platform designed to <strong>connect residents and emergency responders</strong> 
                        for swift, informed, and unified action.
                    </p>
                    <p>
                        Whether you're a <strong>resident reporting incidents</strong> or a <strong>responder ready to assist</strong>, 
                        CEAS ensures that critical information reaches the right people at the right time.
                    </p>
                </article>

                <!-- System Features and Live Data -->
                <div class="features-grid">
                    <!-- System Features -->
                    <section class="feature-card">
                        <h2>System Features</h2>
                        <ul class="feature-list">
                            <li>Secure registration for residents and responders</li>
                            <li>Role-based access with personalized dashboards</li>
                            <li>Real-time alerts and emergency updates</li>
                            <li>Comprehensive alert logs for review and analysis</li>
                            <li>Verified responder profiles and contact details</li>
                        </ul>
                    </section>

                    <!-- Active Alerts -->
                    <section class="feature-card">
                        <h2>Active Alerts</h2>
                        <p>Monitor live alerts submitted by residents, including emergency type, location, time, and status.</p>
                        <div id="activeAlerts" class="alerts-container"></div>
                    </section>

                    <!-- Available Responders -->
                    <section class="feature-card">
                        <h2>Available Responders</h2>
                        <p>Access a real-time list of verified responders with their specialization, availability, and contact details.</p>
                        <div id="respondersList" class="responders-container"></div>
                    </section>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
