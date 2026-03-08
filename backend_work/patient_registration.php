<?php
session_start();
require 'db.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['first_name'] . ' ' . $_POST['last_name']);
    $age = date('Y') - date('Y', strtotime($_POST['dob']));
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $cnic = $_POST['cnic'] ?? '';
    $password = password_hash('default123', PASSWORD_DEFAULT); // Default password assigned

    $stmt = $conn->prepare("INSERT INTO patients (Name, Age, Gender, Email, Password, PhoneNumber, Address, CNIC) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssss", $name, $age, $gender, $email, $password, $phone, $address, $cnic);

    if ($stmt->execute()) {
        $success = "✅ Patient registered successfully with default password: 'default123'";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Registration - HMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'style.css'; ?>
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1><i class="fas fa-user-plus"></i> HMS - Patient Services</h1>
        <nav>
            <ul>
                <li><a href="dashboard_admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="patient_registration.php" class="active"><i class="fas fa-user-plus"></i> Register Patient</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h2>New Patient Registration</h2>

    <?php if ($success): ?>
        <p style="color: green; font-weight: bold;"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p style="color: red; font-weight: bold;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <h3>Personal Information</h3>
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" required>
        </div>
        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="dob" required>
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" required>
                <option value="">--Select--</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <h3>Contact Information</h3>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email">
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" required>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label>CNIC</label>
            <input type="text" name="cnic">
        </div>

        <button type="submit">Register Patient</button>
        <button type="reset" class="button-secondary">Clear Form</button>
    </form>
</div>

<footer>
    <p>&copy; 2025 Hospital Management System. Lahore, Punjab, Pakistan.</p>
</footer>

</body>
</html>
