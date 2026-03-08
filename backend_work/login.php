<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!$username || !$password || !$role) {
        $error = "❌ All fields are required.";
    } else {
        if ($role === 'patient') {
            $stmt = $conn->prepare("SELECT PatientID, Name, Password FROM patients WHERE Email = ?");
        } elseif ($role === 'doctor') {
            $stmt = $conn->prepare("SELECT DoctorID, Name, Password FROM doctors WHERE Email = ?");
        } elseif ($role === 'admin') {
            $stmt = $conn->prepare("SELECT AdminID, Username, Password FROM admin WHERE Username = ?");
        } else {
            $error = "❌ Invalid role selected.";
            goto skipAuth;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // In production, use password_verify() with hashes
            if ($row['Password'] === $password) {
                $_SESSION['role'] = $role;

                if ($role === 'patient') {
                    $_SESSION['PatientID'] = $row['PatientID'];
                    $_SESSION['PatientName'] = $row['Name'];
                    header("Location: dashboard_patient.php");
                } elseif ($role === 'doctor') {
                    $_SESSION['DoctorID'] = $row['DoctorID'];
                    $_SESSION['DoctorName'] = $row['Name'];
                    header("Location: dashboard_doctor.php");
                } elseif ($role === 'admin') {
                    $_SESSION['AdminID'] = $row['AdminID'];
                    $_SESSION['AdminUsername'] = $row['Username'];
                    header("Location: dashboard_admin.php");
                }
                exit;
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ User not found.";
        }
    }
}

skipAuth:
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - HMS</title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.6;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 2rem;
        }

        .login-box {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo h2 {
            color: var(--primary-color);
            font-size: 1.75rem;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 600;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--light-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            color: #555;
        }

        select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg fill="%23555" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>');
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 1rem;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .links a {
            color: var(--secondary-color);
            text-decoration: none;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .links a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .error-message {
            color: var(--accent-color);
            font-size: 0.9rem;
            margin-top: 0.25rem;
            display: none;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        footer p {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) 
        {
            .login-box {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Login - HMS</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Email or Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Login As:</label>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="patient">Patient</option>
            <option value="doctor">Doctor</option>
            <option value="admin">Administrator</option>
        </select>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
