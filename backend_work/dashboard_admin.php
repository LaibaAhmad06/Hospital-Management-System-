<?php
session_start();
if (!isset($_SESSION['AdminID']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard - HMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            box-shadow: var(--box-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            transition: background-color 0.3s;
            font-weight: 500;
        }

        nav a:hover, nav a.active {
            background-color: var(--secondary-color);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        h2 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        h3 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-color);
        }

        .welcome-section {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-message h2 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .welcome-message p {
            color: #666;
        }

        .date-time {
            background-color: var(--light-color);
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            color: var(--dark-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            text-align: center;
        }

        .dashboard-card h3 {
            color: var(--secondary-color);
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }

        .dashboard-card p {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .button-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .button-link:hover {
            background-color: #2980b9;
        }

        .recent-activity {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .recent-activity h3 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--light-color);
            padding-bottom: 0.5rem;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--light-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-time {
            color: #777;
            font-size: 0.9rem;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
        }

        footer p {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                flex-direction: column;
                margin-top: 1rem;
            }

            nav a {
                display: block;
                padding: 0.5rem;
            }

            .welcome-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1><i class="fas fa-shield-alt"></i> HMS - Admin Portal</h1>
        <nav>
            <ul>
                <li><a href="dashboard_admin.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="patient_registration.php"><i class="fas fa-user-plus"></i> Register Patient</a></li>
                <li><a href="doctor_management.php"><i class="fas fa-user-md"></i> Manage Doctors</a></li>
                <li><a href="booked_appointments.php"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
                <li><a href="inventory_management.php"><i class="fas fa-box-open"></i> Inventory</a></li>
                <li><a href="reports_analytics.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <section class="welcome-section">
        <div class="welcome-message">
            <h2>Admin Dashboard</h2>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['AdminUsername']) ?>! Here's what's happening today.</p>
        </div>
        <div class="date-time" id="currentDateTime">
            Loading date & time...
        </div>
    </section>

    <section class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Total Patients</h3>
            <p>1,234</p>
            <a href="patients.php" class="button-link">View Patients</a>
        </div>
        <div class="dashboard-card">
            <h3>Today's Appointments</h3>
            <p>45</p>
            <a href="booked_appointments.php" class="button-link">View Appointments</a>
        </div>
        <div class="dashboard-card">
            <h3>Available Doctors</h3>
            <p>15</p>
            <a href="doctor_management.php" class="button-link">Manage Doctors</a>
        </div>
        <div class="dashboard-card">
            <h3>Reports</h3>
            <p>7</p>
            <a href="reports_analytics.php" class="button-link">View Reports</a>
        </div>
    </section>

    <section class="recent-activity">
        <h3>Recent Activity</h3>
        <ul class="activity-list">
            <li class="activity-item">
                <span>New patient registered</span>
                <span class="activity-time">10 mins ago</span>
            </li>
            <li class="activity-item">
                <span>Dr. Ahmed updated availability</span>
                <span class="activity-time">30 mins ago</span>
            </li>
            <li class="activity-item">
                <span>New appointment scheduled</span>
                <span class="activity-time">1 hour ago</span>
            </li>
        </ul>
    </section>
</div>

<footer>
    <p>&copy; 2025 Hospital Management System. All rights reserved.</p>
</footer>

<script>
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateDateTime();
        setInterval(updateDateTime, 60000);

        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function() {
                const link = card.querySelector('a');
                if (link) {
                    window.location.href = link.href;
                }
            });
        });
    });
</script>
</body>
</html>
