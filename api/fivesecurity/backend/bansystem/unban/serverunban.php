<?php
session_start();
include '../../database.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $license = $data['license'];
    $id = $data['id'];
    $stmt = $paneldb->prepare('SELECT redem_license.expires
								FROM panel.redem_license
								JOIN panel.system
								WHERE redem_license.license = :licensekey
								LIMIT 1');
	$stmt->execute([':licensekey' => $license]);

	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$result) {
		echo "An error occurred. Please try again later.";
		return;
	}

    try {
        $stmt = $bansdb->prepare("SELECT id FROM `$license` WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        if ($stmt->fetch()) {
            $delStmt = $bansdb->prepare("DELETE FROM `$license` WHERE id = :id");
            $success = $delStmt->execute([':id' => $id]);

            if ($success) {
                echo "Ban ID: $id was successfully unbanned";
            } else {
                echo "Error code #6";
            }
        } else {
            echo "Ban ID: $id not found";
        }
    } catch (PDOException $e) {
        // Fehlerbehandlung
        error_log("Database error: " . $e->getMessage());
        echo "An error occurred. Please try again later.";
    }
} else {
    echo "";
}

?>