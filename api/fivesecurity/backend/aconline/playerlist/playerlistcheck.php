<?php
session_start();
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];  
include '../../database.php';

$response = array();
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $licensekey = $data['license'];
    
    $stmt = $paneldb->prepare('SELECT redem_license.expires
                            FROM panel.redem_license
                            JOIN panel.system
                            WHERE redem_license.license = :licensekey
                            LIMIT 1');
    $stmt->execute([':licensekey' => $licensekey]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $stmt = $paneldb->prepare("SELECT * FROM `playerlist` WHERE ip = ?");
        $stmt->execute([$ip]);
        $sv = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sv) {
            if (isset($sv['reason'])) {
                $response['reason'] = $sv['reason'];
            } else {
                $response['reason'] = "error";
            }

            if (isset($sv['id'])) {
                $response['id'] = $sv['id'];
            } else {
                $response['id'] = "error";
            }

            $id = $sv['id'];

            $ip1 = $ip;

            $stmt = $paneldb->prepare("DELETE FROM `playerlist` WHERE id = ? AND ip = ?");
            $stmt->execute([$id, $ip1]);
            
            $response['success'] = true;
            $response['message'] = "Successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "Action for the $ip was not found.";
        }   
    } else {
        $response['success'] = false;
        $response['message'] = "License not found.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Update required";
}

print(json_encode($response));