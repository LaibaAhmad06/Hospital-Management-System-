<?php
session_start();
require 'db.php';

// Redirect to login if not authenticated as patient
if (!isset($_SESSION['PatientID']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$patientId = $_SESSION['PatientID'];
$appointments = [];

// Fetch appointments for this patient
$query = "SELECT a.AppointmentID, a.AppointmentDate, a.Time, d.Name AS DoctorName 
          FROM appointments a
          JOIN doctors d ON a.DoctorID = d.DoctorID
          WHERE a.PatientID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booked Appointments</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .appointments-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }
        .appointment-item {
            border-bottom: 1px solid var(--light-color);
            padding: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .appointment-item:last-child {
            border-bottom: none;
        }
        .appointment-details p {
            margin-bottom: 5px;
        }
        .appointment-actions button {
            padding: 8px 12px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            margin-left: 10px;
        }
        .appointment-actions button.cancel {
            background-color: var(--accent-color);
            color: white;
        }
        .appointment-actions button.cancel:hover {
            background-color: #c0392b;
        }
        .appointment-actions button.reschedule {
            background-color: var(--secondary-color);
            color: white;
        }
        .appointment-actions button.reschedule:hover {
            background-color: #2980b9;
        }
        h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="appointments-container">
        <h2>Your Appointments</h2>

        <?php if (count($appointments) === 0): ?>
            <p>No upcoming appointments found.</p>
        <?php else: ?>
            <?php foreach ($appointments as $appt): ?>
                <div class="appointment-item">
                    <div class="appointment-details">
                        <p><strong>Date:</strong> <?= htmlspecialchars($appt['AppointmentDate']) ?></p>
                        <p><strong>Time:</strong> <?= htmlspecialchars($appt['Time']) ?></p>
                        <p><strong>Doctor:</strong> <?= htmlspecialchars($appt['DoctorName']) ?></p>
                    </div>
                    <div class="appointment-actions">
                        <form method="POST" action="cancel_appointment.php" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?= $appt['AppointmentID'] ?>">
                            <button class="cancel" type="submit">Cancel</button>
                        </form>
                        <form method="POST" action="reschedule_appointment.php" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?= $appt['AppointmentID'] ?>">
                            <button class="reschedule" type="submit">Reschedule</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
