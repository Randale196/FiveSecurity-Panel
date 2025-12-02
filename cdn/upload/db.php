<?php
error_reporting(0);

$conn = mysqli_connect("localhost", "andarale", "IimeiphiH8uphookiec3zohph8zo2zee", "counter");

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS totalscreenshots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license VARCHAR(45) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    upload_time DATETIME NOT NULL,
    expiry_time DATETIME NOT NULL
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}
?>