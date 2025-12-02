<?php
session_start();
include '../../database.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $license = $data['license'];

    $stmt = $counterdb->prepare("INSERT INTO `totalbans` (`license`) VALUES (?)");
    $result = $stmt->execute([$license]);
}