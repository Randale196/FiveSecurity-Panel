<?php
$hostname = "localhost";
$username = "andarale";
$password = "IimeiphiH8uphookiec3zohph8zo2zee";

$dbNamePanel = "panel";
$dbNameBans = "serverbans";
$dbNameLogs = "logs";
$dbNameCounter = "counter";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $paneldb = new PDO("mysql:host=$hostname;dbname=$dbNamePanel;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    die("Unexpected error on paneldb connection: " . $e->getMessage());
}

try {
    $bansdb = new PDO("mysql:host=$hostname;dbname=$dbNameBans;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    die("Unexpected error on bansdb connection: " . $e->getMessage());
}

try {
    $logsdb = new PDO("mysql:host=$hostname;dbname=$dbNameLogs;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    die("Unexpected error on logsdb connection: " . $e->getMessage());
}

try {
    $counterdb = new PDO("mysql:host=$hostname;dbname=$dbNameCounter;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    die("Unexpected error on counterdb connection: " . $e->getMessage());
}
?>