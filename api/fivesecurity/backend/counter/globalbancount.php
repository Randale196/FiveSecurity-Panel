<?php
include '../database.php';

$stmt = $paneldb->prepare("SELECT COUNT(1) FROM `globalbanlist`");
$stmt->execute();
$globalban = $stmt->fetchColumn();

$globalban = (int) $globalban;

echo $globalban;
?>