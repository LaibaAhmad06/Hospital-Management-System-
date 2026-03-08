<?php
session_start();
require 'db.php';
$success = "";
$error = "";

// Check if patient is logged in
if (!isset($_SESSION['PatientID'])) {
    die("❌ Unauthorized access. Please log in as a patient.");
}


// Form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = $_SESSION['PatientID'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];
    $added_by = 1;

    $stmt = $conn->prepare("INSERT INTO appointments (PatientID, DoctorID, AppointmentDate, Time, Added_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $patient_id, $doctor_id, $appointment_date, $time, $added_by);
    if ($stmt->execute()) {
        $success = "✅ Appointment booked successfully.";
    } else {
        $error = "❌ Failed to book appointment.";
    }
}

// Load data
$patients = $conn->query("SELECT PatientID, Name FROM patients")->fetch_all(MYSQLI_ASSOC);
$doctors = $conn->query("SELECT DoctorID, Name, Department FROM doctors")->fetch_all(MYSQLI_ASSOC);

// Reorganize doctors by department
$doctors_by_dept = [];
foreach ($doctors as $doc) {
    $doctors_by_dept[$doc['Department']][] = $doc;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment - HMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* same CSS as your current HTML */
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h2 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 600;
        }

        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--light-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            color: #555;
        }

        .form-group select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%23555" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>');
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
        }

        .form-group textarea {
            resize: vertical;
        }

        button[type="submit"],
        button[type="reset"] {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"] {
            background-color: var(--secondary-color);
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        button[type="reset"] {
            background-color: var(--light-color);
            color: var(--dark-color);
            margin-left: 1rem;
        }

        button[type="reset"]:hover {
            background-color: #ddd;
        }

        .button-secondary {
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        .button-secondary:hover {
            background-color: #ddd;
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

            .container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1><i class="fas fa-calendar-plus"></i> HMS - Patient Services</h1>
        <nav>
            <ul>
                <li><a href="dashboard_patient.html"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="appointment_booking.php" class="active"><i class="fas fa-calendar-check"></i> Book Appointment</a></li>
                <li><a href="#"><i class="fas fa-list"></i> My Appointments</a></li>
                <li><a href="consultation.html"><i class="fas fa-video"></i> My Consultations</a></li>
                <li><a href="login.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h2>Book a New Appointment</h2>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST" action="appointment_booking.php">

        <!-- Department -->
        <div class="form-group">
            <label for="department">Department</label>
            <select id="department" name="department" required>
                <option value="">Select Department</option>
                <?php foreach (array_keys($doctors_by_dept) as $dept): ?>
                    <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Doctor -->
        <div class="form-group">
            <label for="doctor_id">Doctor</label>
            <select id="doctor_id" name="doctor_id" disabled required>
                <option value="">Select Doctor</option>
            </select>
        </div>

        <!-- Date & Time -->
        <div class="form-group">
            <label for="date">Preferred Date</label>
            <input type="date" id="date" name="date" required>
        </div>

        <div class="form-group">
            <label for="time">Preferred Time</label>
            <select id="time" name="time" required>
                <option value="">Select Time</option>
                <option value="09:00:00">9:00 AM</option>
                <option value="09:30:00">9:30 AM</option>
                <option value="10:00:00">10:00 AM</option>
            </select>
        </div>

        <!-- Reason -->
        <div class="form-group">
            <label for="reason">Reason for Appointment (Optional)</label>
            <textarea id="reason" name="reason" rows="3"></textarea>
        </div>

        <button type="submit">Book Appointment</button>
        <button type="reset" class="button-secondary">Clear Form</button>
    </form>
</div>

<script>
// Dynamic doctors by department
const doctors = <?= json_encode($doctors_by_dept) ?>;
const departmentSelect = document.getElementById('department');
const doctorSelect = document.getElementById('doctor_id');

departmentSelect.addEventListener('change', function () {
    const selectedDept = this.value;
    doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
    doctorSelect.disabled = true;

    if (doctors[selectedDept]) {
        doctors[selectedDept].forEach(doc => {
            const option = document.createElement('option');
            option.value = doc.DoctorID;
            option.textContent = doc.Name;
            doctorSelect.appendChild(option);
        });
        doctorSelect.disabled = false;
    }
});
</script>

</body>
</html>
