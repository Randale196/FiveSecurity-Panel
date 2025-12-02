<?php
include '../../database.php';

$data = json_decode(file_get_contents("php://input"), true);

if($data) {
  $reason = $data['reason'];
  $licensekey = $data['license'];
  $date = $data['date'];

  $stmt = $paneldb->prepare('SELECT redem_license.expires
                          FROM panel.redem_license
                          JOIN panel.system
                          WHERE redem_license.license = :licensekey
                          LIMIT 1');
  $stmt->execute([':licensekey' => $licensekey]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result) {
    $stmt = $logsdb->prepare("INSERT INTO `logs` (`reason`, `date`, `license`) VALUES (?, ?, ?)");
    $result = $stmt->execute([$reason, $date, $licensekey]);
  }
}

?>