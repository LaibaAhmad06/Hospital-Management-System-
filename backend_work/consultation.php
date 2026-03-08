<?php
session_start();
require 'db.php';

// ✅ Make sure doctor is logged in
if (!isset($_SESSION['DoctorID']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

// ✅ Safely access session variable
$doctorName = isset($_SESSION['name']) ? $_SESSION['name'] : "Unknown Doctor";
$doctorID = $_SESSION['DoctorID'];

$success = $error = "";

// ✅ Only read form values when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patientID = $_POST['PatientID'] ?? '';
    $diagnosis = $_POST['Diagnosis'] ?? '';
    $prescription = $_POST['Prescription'] ?? '';
    $followupDate = $_POST['FollowupDate'] ?? '';

    // ✅ Proceed only if all fields are filled
    if ($doctorName && $patientID && $diagnosis && $prescription && $followupDate) {
        $stmt = $conn->prepare("INSERT INTO consultation (name, PatientID, Diagnosis, Prescription, FollowupDate) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sisss", $doctorName, $patientID, $diagnosis, $prescription, $followupDate);


        if ($stmt->execute()) {
            $success = "Consultation added successfully.";
        } else {
            $error = "Error adding consultation.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

// ✅ Fetch previous consultations
$consultations = [];
$sql = $conn->prepare("SELECT * FROM consultation WHERE name = ?");
$sql->bind_param("s", $doctorName);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    $consultations[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consultation - HMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            background-color: #fff;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h2 {
            text-align: center;
            color: var(--primary-color);
        }

        form {
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #ccc;
        }

        input[readonly] {
            background-color: #f0f0f0;
        }

        button {
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1rem;
            color: green;
        }

        .error {
            color: red;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        th, td {
            padding: 1rem;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--light-color);
        }

        footer {
            text-align: center;
            margin-top: 2rem;
            padding: 1rem;
            background-color: var(--dark-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Consultation Entry</h2>

        <?php if ($success): ?>
            <div class="message"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="consultation.php">
            <div class="form-group">
                <label for="name">Doctor Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($doctorName) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="PatientID">Patient ID</label>
                <input type="text" name="PatientID" required>
            </div>
            <div class="form-group">
                <label for="Diagnosis">Diagnosis</label>
                <textarea name="Diagnosis" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="Prescription">Prescription</label>
                <textarea name="Prescription" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="FollowupDate">Follow-up Date</label>
                <input type="date" name="FollowupDate" required>
            </div>
            <button type="submit">Submit Consultation</button>
        </form>

        <?php if (count($consultations) > 0): ?>
            <h3>Previous Consultations</h3>
            <table>
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Diagnosis</th>
                        <th>Prescription</th>
                        <th>Follow-up</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultations as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['PatientID']) ?></td>
                            <td><?= htmlspecialchars($c['Diagnosis']) ?></td>
                            <td><?= htmlspecialchars($c['Prescription']) ?></td>
                            <td><?= htmlspecialchars($c['FollowupDate']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No consultations yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        &copy; 2025 Hospital Management System. All rights reserved.
    </footer>
</body>
</html>
