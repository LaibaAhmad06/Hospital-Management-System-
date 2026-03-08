<?php
session_start();
require 'db.php';

// Redirect if not logged in or not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success = $error = "";

// Add new item
if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $code = $_POST['item_code'];
    $price = $_POST['price'];
    $orderLevel = $_POST['order_level'];

    $stmt = $conn->prepare("INSERT INTO inventory (ItemName, Category, Quantity, ItemCode, Price, OrderLevel) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissd", $name, $category, $quantity, $code, $price, $orderLevel);

    if ($stmt->execute()) $success = "Item added successfully.";
    else $error = "Failed to add item.";
}

// Edit item
if (isset($_POST['edit_item'])) {
    $id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $code = $_POST['item_code'];
    $price = $_POST['price'];
    $orderLevel = $_POST['order_level'];

    $stmt = $conn->prepare("UPDATE inventory SET ItemName=?, Category=?, Quantity=?, ItemCode=?, Price=?, OrderLevel=? WHERE ItemID=?");
    $stmt->bind_param("ssissdi", $name, $category, $quantity, $code, $price, $orderLevel, $id);

    if ($stmt->execute()) $success = "Item updated successfully.";
    else $error = "Failed to update item.";
}

// Restock item
if (isset($_POST['restock_item'])) {
    $id = $_POST['restock_id'];
    $added_qty = $_POST['restock_qty'];

    $stmt = $conn->prepare("UPDATE inventory SET Quantity = Quantity + ? WHERE ItemID = ?");
    $stmt->bind_param("ii", $added_qty, $id);

    if ($stmt->execute()) $success = "Item restocked successfully.";
    else $error = "Restocking failed.";
}

// Fetch all inventory
$items = [];
$result = $conn->query("SELECT * FROM inventory");
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management - HMS</title>
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
            max-width: 960px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h1, h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .inventory-actions {
            margin-bottom: 1.5rem;
            text-align: right;
        }

        .inventory-actions button {
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .inventory-actions button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        thead th {
            background-color: var(--light-color);
            color: var(--dark-color);
            padding: 1rem;
            text-align: left;
            font-weight: bold;
        }

        tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--light-color);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody td a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        tbody td a:hover {
            color: #2980b9;
        }

        .add-item-form {
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: 2rem;
            display: none; /* Initially hidden */
        }

        .add-item-form h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-align: left;
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

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
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

        .add-item-form button[type="submit"],
        .add-item-form button[type="button"].button-secondary {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-item-form button[type="submit"] {
            background-color: var(--success-color);
            color: white;
            margin-right: 1rem;
        }

        .add-item-form button[type="submit"]:hover {
            background-color: #1e8449;
        }

        .add-item-form button[type="button"].button-secondary {
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        .add-item-form button[type="button"].button-secondary:hover {
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
            .container {
                padding: 1.5rem;
            }

            table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<h2>Inventory Management</h2>

<?php if ($success): ?>
    <p class="success"><?= $success ?></p>
<?php elseif ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Item Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Reorder Level</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['ItemCode']) ?></td>
            <td><?= htmlspecialchars($item['ItemName']) ?></td>
            <td><?= htmlspecialchars($item['Category']) ?></td>
            <td><?= $item['Quantity'] ?></td>
            <td><?= $item['Price'] ?></td>
            <td><?= $item['OrderLevel'] ?></td>
            <td>
                <!-- Edit Form -->
                <form style="display:inline-block;" method="POST">
                    <input type="hidden" name="item_id" value="<?= $item['ItemID'] ?>">
                    <input type="hidden" name="item_name" value="<?= $item['ItemName'] ?>">
                    <input type="hidden" name="category" value="<?= $item['Category'] ?>">
                    <input type="hidden" name="quantity" value="<?= $item['Quantity'] ?>">
                    <input type="hidden" name="item_code" value="<?= $item['ItemCode'] ?>">
                    <input type="hidden" name="price" value="<?= $item['Price'] ?>">
                    <input type="hidden" name="order_level" value="<?= $item['OrderLevel'] ?>">
                    <button name="edit_item_form">Edit</button>
                </form>

                <!-- Restock Form -->
                <form style="display:inline-block;" method="POST">
                    <input type="hidden" name="restock_id" value="<?= $item['ItemID'] ?>">
                    <input type="number" name="restock_qty" placeholder="Qty" min="1" required>
                    <button name="restock_item">Restock</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Add New Item -->
<form method="POST">
    <h3>Add New Inventory Item</h3>
    <input type="text" name="item_name" placeholder="Item Name" required>
    <select name="category" required>
        <option value="">Select Category</option>
        <option value="medications">Medications</option>
        <option value="supplies">Supplies</option>
        <option value="equipment">Equipment</option>
    </select>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <input type="text" name="item_code" placeholder="Item Code" required>
    <input type="number" step="0.01" name="price" placeholder="Unit Price" required>
    <input type="number" name="order_level" placeholder="Reorder Level">
    <button name="add_item">Add Item</button>
</form>

<!-- Edit Existing Item -->
<?php if (isset($_POST['edit_item_form'])): ?>
<form method="POST">
    <h3>Edit Inventory Item</h3>
    <input type="hidden" name="item_id" value="<?= $_POST['item_id'] ?>">
    <input type="text" name="item_name" value="<?= $_POST['item_name'] ?>" required>
    <input type="text" name="category" value="<?= $_POST['category'] ?>" required>
    <input type="number" name="quantity" value="<?= $_POST['quantity'] ?>" required>
    <input type="text" name="item_code" value="<?= $_POST['item_code'] ?>" required>
    <input type="number" step="0.01" name="price" value="<?= $_POST['price'] ?>" required>
    <input type="number" name="order_level" value="<?= $_POST['order_level'] ?>">
    <button name="edit_item">Update Item</button>
</form>
<?php endif; ?>

</body>
</html>
