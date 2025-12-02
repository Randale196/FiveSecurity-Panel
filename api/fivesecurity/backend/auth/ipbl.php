<?php
include '../database.php';

$visitorIP = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
$modifiedString = str_replace('.', '', $visitorIP);

$sql = "SELECT serverip FROM server WHERE serverip = :visitorIP AND is_blacklisted = 1";
$stmt = $paneldb->prepare($sql);
$stmt->execute([':visitorIP' => $visitorIP]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo base64_decode($modifiedString . "B");
} else {
    echo base64_decode($modifiedString . "W");
}
?>