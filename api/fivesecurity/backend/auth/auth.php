<?php

// Useless ATM

include '../database.php';

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];

try {
    $stmt = $paneldb->prepare("SELECT ip FROM licenses WHERE ip = ?");
    $stmt->execute([$ip]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "hoffnichtduopfer";
    } else {
        echo "404";
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
}
?>