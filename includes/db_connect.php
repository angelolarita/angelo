<?php
require_once __DIR__ . '/config.php'; // Include config file

$conn = mysqli_connect("localhost", DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>