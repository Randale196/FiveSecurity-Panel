<?php
  session_start();
  include '../../database.php';

  $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
  $stmt = $counterdb->prepare("INSERT INTO totalauths (license) VALUES (?)");
  $stmt->execute([$ip]);
?>