<?php
session_start();
include '../../database.php';

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
		echo '{}';
		return;
	}

    $identifier = explode(',', str_replace(['"', '[', ']'], '', $data['identifier']));

    $conditions = [];
    $params = [];

    foreach ($identifier as $value) {
        $value = trim($value);
        $conditions[] = "(license = ? OR steam = ? OR xbl = ? OR live = ? OR discord = ? OR playerip = ? OR hwid = ?)";
        for ($i = 0; $i < 7; $i++) {
            $params[] = $value;
        }
    }

    $where = implode(' OR ', $conditions);

    $sql = "SELECT * FROM `$license` WHERE $where";

    try {
        $stmt = $bansdb->prepare($sql);
        $stmt->execute($params);

        $response = ['status' => 200];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response['arg'] = $row['reason'];
            $response['id'] = $row['id'];
            $response['screen'] = $row['screen'];
        }

        echo json_encode($response);

    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
}