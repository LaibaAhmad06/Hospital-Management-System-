<?php
// Connect to MySQL using mysqli
$conn = mysqli_connect("localhost", "root", "", "project.db");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
