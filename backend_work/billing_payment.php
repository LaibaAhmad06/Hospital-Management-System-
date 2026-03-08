<?php
session_start();
require 'db.php';

// Redirect if not logged in as patient
if (!isset($_SESSION['PatientID']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$patientId = $_SESSION['PatientID'];
$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $billId = !empty($_POST['bill_id']) ? intval($_POST['bill_id']) : null;
    $amount = floatval($_POST['payment_amount']);
    $method = $_POST['payment_method'];
    $date = date('Y-m-d');

    if ($billId) {
        // Update existing bill status
        $stmt = $conn->prepare("UPDATE billing SET Status = 'Paid', PaymentMethod = ?, Date = ? WHERE BillID = ? AND PatientID = ?");
        $stmt->bind_param("ssii", $method, $date, $billId, $patientId);
        if ($stmt->execute()) {
            $success = "Bill marked as paid.";
        } else {
            $error = "Failed to update bill.";
        }
    } else {
        // Insert new billing record
        $desc = "General Payment by patient";
        $status = "Paid";
        $stmt = $conn->prepare("INSERT INTO billing (PatientID, Amount, Date, Description, Status, PaymentMethod) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idssss", $patientId, $amount, $date, $desc, $status, $method);
        if ($stmt->execute()) {
            $success = "New payment recorded.";
        } else {
            $error = "Failed to record new payment.";
        }
    }
}

// Fetch updated bills
$bills = [];
$result = $conn->query("SELECT * FROM billing WHERE PatientID = $patientId ORDER BY Date DESC");
while ($row = $result->fetch_assoc()) {
    $bills[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing & Payment - HMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* You can extract this into a style.css */
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav ul {
            display: flex;
            list-style: none;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }
        nav a:hover, nav a.active {
            background-color: #3498db;
            border-radius: 8px;
        }
        .container {
            max-width: 960px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        table {
            width: 100%;
            margin: 1rem 0 2rem;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
        }
        table th {
            background-color: #ecf0f1;
        }
        .payment-section {
            background: #ecf0f1;
            padding: 1rem;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-group label {
            font-weight: 600;
        }
        button[type="submit"] {
            padding: 0.75rem 1.5rem;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <h1><i class="fas fa-receipt"></i> HMS - Patient Services</h1>
        <nav>
            <ul>
                <li><a href="dashboard_patient.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="appointment_booking.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="consultation.php"><i class="fas fa-video"></i> Consultations</a></li>
                <li><a href="billing_payment.php" class="active"><i class="fas fa-money-bill-wave"></i> Billing</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h2>Billing & Payment History</h2>

    <?php if ($success): ?>
        <script>alert("✅ <?= $success ?>");</script>
    <?php elseif ($error): ?>
        <script>alert("❌ <?= $error ?>");</script>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>Bill ID</th>
            <th>Date</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Method</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($bills)): ?>
            <?php foreach ($bills as $bill): ?>
                <tr>
                    <td><?= htmlspecialchars($bill['BillID']) ?></td>
                    <td><?= htmlspecialchars($bill['Date']) ?></td>
                    <td><?= htmlspecialchars($bill['Description']) ?></td>
                    <td><?= htmlspecialchars($bill['Amount']) ?> PKR</td>
                    <td><?= htmlspecialchars($bill['Status']) ?></td>
                    <td><?= htmlspecialchars($bill['PaymentMethod']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No billing records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="payment-section">
        <h3>Make a New Payment</h3>
        <form action="billing_payment.php" method="POST">
            <div class="form-group">
                <label for="bill-id">Bill ID (optional)</label>
                <input type="text" name="bill_id" id="bill-id" placeholder="Leave blank for new entry">
            </div>
            <div class="form-group">
                <label for="payment-amount">Amount (PKR)</label>
                <input type="number" name="payment_amount" id="payment-amount" required>
            </div>
            <div class="form-group">
                <label for="payment-method">Payment Method</label>
                <select name="payment_method" id="payment-method" required>
                    <option value="">Select Method</option>
                    <option value="credit-card">Credit Card</option>
                    <option value="debit-card">Debit Card</option>
                    <option value="bank-transfer">Bank Transfer</option>
                </select>
            </div>
            <button type="submit">Proceed to Payment</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2025 Hospital Management System. Lahore, Pakistan.</p>
</footer>

</body>
</html>
