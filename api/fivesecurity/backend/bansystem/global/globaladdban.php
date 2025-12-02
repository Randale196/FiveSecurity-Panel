<?php
session_start();
include '../../database.php';

$data = json_decode(file_get_contents("php://input"), true);

if($data) {
  $steam =$data['steam'];
  $name = $data['name'];
  $hwid = $data['hwid'];
  $license = $data['license'];
  $id = $data['id'];
  $reason = $data['reason'];
  $discord = $data['discord'];
  $live = $data['live'];
  $xbl = $data['xbl'];
  $playerip = $data['playerip'];
  $screen = $data['screen'];
  $fsLicense = $data['fsLicense'];

  $stmt = $paneldb->prepare('SELECT redem_license.expires
                            FROM panel.redem_license
                            JOIN panel.system
                            WHERE redem_license.license = :licensekey
                            LIMIT 1');
  $stmt->execute([':licensekey' => $fsLicense]);

  $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
  if ($result) {
    $sql = "INSERT INTO `globalbanlist` 
    (`id`, `name`, `steam`, `license`, `xbl`, `live`, `discord`, `playerip`, `hwid`, `reason`, `screen`)
    VALUES (:id, :name, :steam, :license, :xbl, :live, :discord, :playerip, :hwid, :reason, :screen)";

    $stmt = $paneldb->prepare($sql);

    $stmt->execute([
        ':id'       => $id,
        ':name'     => $name,
        ':steam'    => $steam,
        ':license'  => $license,
        ':xbl'      => $xbl,
        ':live'     => $live,
        ':discord'  => $discord,
        ':playerip' => $playerip,
        ':hwid'     => $hwid,
        ':reason'   => $reason,
        ':screen'   => $screen
    ]);
  }
}