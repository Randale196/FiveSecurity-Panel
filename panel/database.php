<?php
require_once 'config.php';

$host = $database_config['host'];
$username = $database_config['username'];
$password = $database_config['password'];

$link = mysqli_connect($host, $username, $password, $database_config['databases']['panel']);
if ($link === false) {
    die("ERROR: Could not connect to 'panel' database. " . mysqli_connect_error());
}

$conn = mysqli_connect($host, $username, $password, "panel");
if ($conn === false) {
    die("ERROR: Could not connect to 'panel' database (conn). " . mysqli_connect_error());
}

$stats = mysqli_connect($host, $username, $password, "counter");
if ($stats === false) {
    die("ERROR: Could not connect to 'counter' database. " . mysqli_connect_error());
}

$stats2 = mysqli_connect($host, $username, $password, "panel");
if ($stats2 === false) {
    die("ERROR: Could not connect to 'panel' database (stats2). " . mysqli_connect_error());
}

$logs = mysqli_connect($host, $username, $password, "logs");
if ($logs === false) {
    die("ERROR: Could not connect to 'logs' database. " . mysqli_connect_error());
}

$svbans = mysqli_connect($host, $username, $password, "serverbans");
if ($svbans === false) {
    die("ERROR: Could not connect to 'serverbans' database. " . mysqli_connect_error());
}

$authy = mysqli_connect($host, $username, $password, "auth");
if ($authy === false) {
    die("ERROR: Could not connect to 'auth' database. " . mysqli_connect_error());
}

?>