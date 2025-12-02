<?php
include '../database.php';

function getCountFormatted(PDO $db, string $table) {
    $stmt = $db->prepare("SELECT COUNT(1) FROM `$table`");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return number_format($count);
}

// Version aus system-Tabelle holen
$stmt = $paneldb->query("SELECT version FROM `system` LIMIT 1");
$version = $stmt->fetchColumn();

$globalban = getCountFormatted($paneldb, 'globalbanlist');
$customers = getCountFormatted($paneldb, 'users');
$screens = getCountFormatted($counterdb, 'totalscreenshots');
$auths = getCountFormatted($counterdb, 'totalauths');
$bans = getCountFormatted($counterdb, 'totalbans');
$joins = getCountFormatted($counterdb, 'totaljoins');

die(json_encode([
    "globalbans" => $globalban,
    "screenshots" => $screens,
    "auths" => $auths,
    "totalbans" => $bans,
    "totaljoins" => $joins,
    "version" => $version,
    "customers" => $customers,
]));
?>