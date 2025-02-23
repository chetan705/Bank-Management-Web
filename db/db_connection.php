<?php
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password
$database = "bank_management_new";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
