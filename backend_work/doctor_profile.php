<?php
session_start();
require 'db.php';

// Redirect if not logged in as doctor
if (!isset($_SESSION['DoctorID']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$doctorID = $_SESSION['DoctorID'];
$success = $error = "";

// Fetch doctor info
$stmt = $conn->prepare("SELECT * FROM doctors WHERE DoctorID = ?");
$stmt->bind_param("i", $doctorID);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate password match
    if (!empty($newPassword) && $newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } else {
        // Build update query
        if (!empty($newPassword)) {
            $stmt = $conn->prepare("UPDATE doctors SET Email = ?, PhoneNumber = ?, Password = ? WHERE DoctorID = ?");
            $stmt->bind_param("sssi", $email, $phone, $newPassword, $doctorID);
        } else {
            $stmt = $conn->prepare("UPDATE doctors SET Email = ?, PhoneNumber = ? WHERE DoctorID = ?");
            $stmt->bind_param("ssi", $email, $phone, $doctorID);
        }

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Refresh doctor data
            $stmt = $conn->prepare("SELECT * FROM doctors WHERE DoctorID = ?");
            $stmt->bind_param("i", $doctorID);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();
        } else {
            $error = "Failed to update profile.";
        }
    }
}
?>

<!-- HTML Starts Below -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile - HMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include 'style.css'; ?> /* or paste your inline CSS if not using external */
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1><i class="fas fa-hospital-alt"></i> HMS - Doctor Portal</h1>
        <nav>
            <ul>
                <li><a href="dashboard_doctor.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="doctor_profile.php" class="active"><i class="fas fa-user-md"></i> My Profile</a></li>
                <li><a href="consultation.php"><i class="fas fa-comments"></i> Consultations</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h2>Doctor Profile</h2>

    <?php if ($success): ?>
        <script>alert("✅ <?= $success ?>");</script>
    <?php elseif ($error): ?>
        <script>alert("❌ <?= $error ?>");</script>
    <?php endif; ?>

    <form action="doctor_profile.php" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" readonly value="<?= htmlspecialchars($doctor['Name']) ?>">
            </div>
            <div class="form-group">
                <label for="specialization">Specialization</label>
                <input type="text" readonly value="<?= htmlspecialchars($doctor['Specialization']) ?>">
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <input type="text" readonly value="<?= htmlspecialchars($doctor['Department']) ?>">
            </div>
            <div class="form-group">
                <label for="license">License Number</label>
                <input type="text" readonly value="<?= htmlspecialchars($doctor['LicenceNumber']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($doctor['Email']) ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" required value="<?= htmlspecialchars($doctor['PhoneNumber']) ?>">
            </div>
        </div>

        <h3>Update Password</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Repeat new password">
            </div>
        </div>

        <div class="button-group">
            <button type="submit">Update Profile</button>
        </div>
    </form>
</div>

<footer>
    <p>&copy; 2025 Hospital Management System. Lahore, Punjab, Pakistan.</p>
</footer>

</body>
</html>
