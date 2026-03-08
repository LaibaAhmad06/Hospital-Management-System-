<?php
session_start();
if (!isset($_SESSION['PatientID']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - HMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Your CSS remains the same (omitted here to save space) -->
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
        }

        .dashboard-card h3 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--light-color);
            padding-bottom: 0.5rem;
        }

        .dashboard-card p {
            color: #666;
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

        .appointments-list {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .appointments-list h3 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--light-color);
            padding-bottom: 0.5rem;
        }

        .appointment-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--light-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .appointment-item:last-child {
            border-bottom: none;
        }

        .appointment-info h4 {
            color: var(--secondary-color);
            margin-bottom: 0.3rem;
        }

        .appointment-info p {
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

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1><i class="fas fa-hospital-alt"></i> HMS - Patient Portal</h1>
        <nav>
            <ul>
                <li><a href="dashboard_patient.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="appointment_booking.php"><i class="fas fa-calendar-check"></i> Book Appointment</a></li>
                <li><a href="booked_appointments.php"><i class="fas fa-list"></i> My Appointments</a></li>
                <li><a href="consultation.php"><i class="fas fa-video"></i> My Consultations</a></li>
                <li><a href="medical_records.php"><i class="fas fa-file-medical"></i> Medical Records</a></li>
                <li><a href="billing_payment.php"><i class="fas fa-receipt"></i> Billing & Payment</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['PatientName']) ?> 👋</h2>

    <section class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Upcoming Appointments</h3>
            <p>You have <span id="upcoming-appointments-count">1</span> upcoming appointment.</p>
            <a href="booked_appointments.php" class="button-link">View Appointments</a>
        </div>

        <div class="dashboard-card">
            <h3>My Doctors</h3>
            <p>Access contact information for your healthcare providers.</p>
            <a href="my_doctors.php" class="button-link">See My Doctors</a>
        </div>

        <div class="dashboard-card">
            <h3>Billing & Payments</h3>
            <p>View and pay your medical bills online.</p>
            <a href="billing_payment.php" class="button-link">View Bills</a>
        </div>

        <div class="dashboard-card">
            <h3>Quick Actions</h3>
            <a href="appointment_booking.php" class="button-link" style="margin-bottom: 0.5rem;"><i class="fas fa-plus"></i> Book New Appointment</a>
            <a href="medical_records.php" class="button-link" style="margin-bottom: 0.5rem;"><i class="fas fa-file-medical"></i> View Medical Records</a>
            <a href="prescription_refill.php" class="button-link"><i class="fas fa-prescription-bottle-alt"></i> Request Prescription Refill</a>
        </div>
    </section>

    <section class="appointments-list">
        <h3>Today's Appointments</h3>
        <div class="appointment-item">
            <div class="appointment-info">
                <h4>Dr. Sarah Khan - Cardiology</h4>
                <p><i class="far fa-clock"></i> 2:30 PM | Follow-up Consultation</p>
            </div>
            <div class="appointment-actions">
                <a href="#" class="button-link" style="font-size: 0.9rem;"><i class="fas fa-video"></i> Join Now</a>
                <a href="#" class="button-link" style="background-color: var(--light-color); color: var(--dark-color); font-size: 0.9rem;"><i class="fas fa-info-circle"></i> Details</a>
            </div>
        </div>
        <div class="appointment-item">
            <div class="appointment-info">
                <h4>Dr. Ahmed Raza - General Medicine</h4>
                <p><i class="far fa-clock"></i> Tomorrow 10:00 AM | Annual Checkup</p>
            </div>
            <div class="appointment-actions">
                <a href="#" class="button-link" style="font-size: 0.9rem;"><i class="fas fa-edit"></i> Reschedule</a>
                <a href="#" class="button-link" style="background-color: var(--accent-color); font-size: 0.9rem;"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </div>
    </section>
</div>

<footer>
    <p>&copy; 2025 Hospital Management System. Lahore, Punjab, Pakistan. All rights reserved.</p>
    <p><i class="fas fa-phone"></i> Support: +92 300 1234567 | <i class="fas fa-envelope"></i> help@hms.com</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.getElementById('upcoming-appointments-count').textContent = '2';
        }, 500);
    });
</script>

</body>
</html>
