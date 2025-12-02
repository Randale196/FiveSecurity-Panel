<?php
include('database.php'); // Dont remove it!

session_start();

function isValidTableName($tableName, $stats) {
    $allowedTables = ['totalauths', 'totaljoins', 'totalbans', 'totalscreenshots'];
    
    if (!in_array($tableName, $allowedTables)) {
        return false;
    }
    
    $stmt = $stats->prepare("SHOW TABLES LIKE '" . $tableName . "'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

function getNumRows($tableName) {
    global $stats;
    
    if (!isValidTableName($tableName, $stats)) {
        return 'invalid';
    }
    
    $sql = "SELECT COUNT(*) as count FROM " . $tableName;
    $result = $stats->query($sql);
    
    if (!$result) {
        return 'db error';
    }
    
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

$tableName = isset($_GET['tableName']) ? trim($_GET['tableName']) : '';

if (empty($tableName)) {
    echo '...';
} else {
    echo getNumRows($tableName);
}
?>