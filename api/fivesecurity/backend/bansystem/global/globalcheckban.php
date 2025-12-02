<?php

session_start();
include '../../database.php';

$data = json_decode(file_get_contents("php://input"), true);
if ($data) {
	$identifier = explode(',', str_replace(array('"', '[', ']'), '', $data['identifier']));

	$license = $data['license'];
	$enabled = $data['enabled'];
	$stmt = $paneldb->prepare('SELECT redem_license.expires
								FROM panel.redem_license
								JOIN panel.system
								WHERE redem_license.license = :licensekey
								LIMIT 1');
	$stmt->execute([':licensekey' => $license]);

	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if (!$result || !$enabled) {
		echo '{}';
		return;
	}

	$index = 0;
	$search = '1';
	foreach ($identifier as $value) {
		if ($index > 0) {
			$search .= " OR license = '{$value}' OR steam = '{$value}' OR xbl = '{$value}' OR live = '{$value}' OR discord = '{$value}' OR playerip = '{$value}' OR hwid = '{$value}' ";
		} else {
			$search = " '{$value}'";
		}
		$index++;
	}

	try {
		$stmt = $paneldb->prepare("SELECT * FROM `globalbanlist` WHERE license = :license");
		$stmt->execute([':license' => $search]);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$response = ['status' => 200];
		if ($rows) {
			$lastRow = end($rows);
			$response['arg'] = $lastRow['reason'];
			$response['id'] = $lastRow['id'];
			$response['screen'] = $lastRow['screen'];
		}
		
		echo json_encode($response);
	} catch (PDOException $e) {
		die("Error: " . $e->getMessage());
	}
}