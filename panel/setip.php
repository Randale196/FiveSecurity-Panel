<?php
session_start();

include('func.php');
include('config.php');

if (!(isset($_POST['ip']))) {
  session_destroy();
  header("Location: https://" . $website_config['site_domain']);
}

if (isset($_POST['ip'])) {
  $newip = decrypt_string($_POST['ip']);
  $_SESSION["ip"] = $newip;
}