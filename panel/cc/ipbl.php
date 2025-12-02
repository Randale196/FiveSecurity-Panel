<?php
include('../config.php'); // Dont remove it!
include('../license.php'); // Dont remove it!

$servername = $database_config['host'];
$username = $database_config['username'];
$password = $database_config['password'];
$dbname = "panel";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("ERROR: Could not connect to 'panel' database " . $conn->connect_error);
}

$uIP = $_SERVER['HTTP_CF_CONNECTING_IP'];

if (filter_var($uIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
    echo "ipv6";
} else {
    $stmt = $conn->prepare('SELECT 1 FROM `server` WHERE serverip = ? AND is_blacklisted = 1');
    $stmt->bind_param('s', $uIP);

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "B";
    } else {
        echo "W";
    }

    $stmt->close();
}

$conn->close();
?>