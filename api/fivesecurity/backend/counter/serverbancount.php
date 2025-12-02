<?php
session_start();
include '../database.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $license = $data['license'];
    $stmt = $paneldb->prepare('SELECT redem_license.expires
                        FROM panel.redem_license
                        JOIN panel.system
                        WHERE redem_license.license = :licensekey
                        LIMIT 1');
    $stmt->execute([':licensekey' => $license]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if (!$result) {
        echo "0";
        return;
    }

    $stmt = $bansdb->prepare("SELECT COUNT(*) FROM `$license`");
    $stmt->execute();
    $count = $stmt->fetchColumn();

    echo $count;
}
