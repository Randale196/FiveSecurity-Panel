<?php
include('../config.php'); // Dont remove it!
$site_name = $website_config["site_name"]; // Dont remove it!

session_start();
function generateRandomString($length)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';

	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}

	return $randomString;
}

include 'conn.php';
require 'Base2n.php';

$authorized = false;
$base32 = new Base2n(5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', false, true, true);
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
$firstinstall = false;
$status = "";


function isCloudflareIP($ip)
{
	$cfIPRanges = array(
		'103.21.244.0/22',
		'103.22.200.0/22',
		'103.31.4.0/22',
		'104.16.0.0/12',
		'108.162.192.0/18',
		'131.0.72.0/22',
		'141.101.64.0/18',
		'162.158.0.0/15',
		'172.64.0.0/13',
		'173.245.48.0/20',
		'188.114.96.0/20',
		'190.93.240.0/20',
		'197.234.240.0/22',
		'198.41.128.0/17',
		'199.27.128.0/21',
		'2400:cb00::/32',
		'2606:4700::/32',
		'2803:f800::/32',
		'2405:b500::/32',
		'2405:8100::/32'
	);

	foreach ($cfIPRanges as $range) {
		if (ipInRange($ip, $range)) {
			return true;
		}
	}

	return false;
}

function ipInRange($ip, $range)
{
	if (strpos($range, ':') === false) {
		list($subnet, $mask) = explode('/', $range);
		$subnet = ip2long($subnet);
		$ip = ip2long($ip);
		$mask = ~((1 << (32 - $mask)) - 1);

		return ($ip & $mask) === ($subnet & $mask);
	} else {
		list($subnet, $mask) = explode('/', $range);
		$subnet = inet_pton($subnet);
		$ip = inet_pton($ip);
		$mask = str_repeat("\xFF", $mask / 8) . str_repeat("\x00", 16 - $mask / 8);

		return substr($ip, 0, strlen($mask)) === $mask && substr($ip, 0, strlen($subnet)) === $subnet;
	}
}

function isReservedIP($ip)
{
	$reservedIPRanges = array(
		'0.0.0.0/8',
		'10.0.0.0/8',
		'100.64.0.0/10',
		'127.0.0.0/8',
		'169.254.0.0/16',
		'172.16.0.0/12',
		'192.0.0.0/24',
		'192.0.2.0/24',
		'192.168.0.0/16',
		'198.18.0.0/15',
		'198.51.100.0/24',
		'203.0.113.0/24',
		'224.0.0.0/4',
		'240.0.0.0/4',
		'::/128',
		'fc00::/7',
		'fe80::/10'
	);

	foreach ($reservedIPRanges as $range) {
		if (ipInRange($ip, $range)) {
			return true;
		}
	}

	return false;
}

function ipInRangeReserved($ip, $range)
{
	if (strpos($range, ':') === false) {
		// IPv4
		list($subnet, $mask) = explode('/', $range);
		$subnet = ip2long($subnet);
		$ip = ip2long($ip);
		$mask = ~((1 << (32 - $mask)) - 1);

		return ($ip & $mask) === ($subnet & $mask);
	} else {
		// IPv6
		list($subnet, $mask) = explode('/', $range);
		$subnet = inet_pton($subnet);
		$ip = inet_pton($ip);
		$mask = str_repeat("\xFF", $mask / 8) . str_repeat("\x00", 16 - $mask / 8);

		return substr($ip, 0, strlen($mask)) === $mask && substr($ip, 0, strlen($subnet)) === $subnet;
	}
}

$stop = false;
$data = @$_REQUEST['data'];

if (!empty($data)) {

	$sec1 = substr($data, 1);
	$sec2 = $base32->decode($sec1);

	$sec3 = str_rot13($sec2);
	$sec4 = base64_decode($sec3);

	$jsonarray = json_decode($sec4, true);
	if ($jsonarray !== null) {

		$key = $jsonarray['authKey'];
		$servername = $jsonarray['serverName'];
		$licensekey = $jsonarray['licenseKey'];
		$port = $jsonarray['port'];
		$resname = $jsonarray['resName'];
		$randomHash = $jsonarray['_'];
		$r1 = $jsonarray['r1'];
		$r2 = $jsonarray['r2'];

		$scriptid = 1;

		$authDataArray = json_encode($jsonarray);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {

			$status = "ipv6";
			$authorized = false;
			$arrayOutput = array("status" => $status, "firstinstall" => $firstinstall);
			die(json_encode($arrayOutput));
		} elseif (isCloudflareIP($ip) || isReservedIP($ip)) {
			$status = "ipERROR";
			$authorized = false;
			$arrayOutput = array("status" => $status, "firstinstall" => $firstinstall);
			die(json_encode($arrayOutput));
		}
		
		$query = $db3->query('SELECT redem_license.expires FROM panel.redem_license JOIN panel.system WHERE redem_license.license= "' . $licensekey . '" LIMIT 1', PDO::FETCH_ASSOC);

		$query2 = $db3->query('SELECT server.serverip FROM panel.redem_license JOIN panel.server ON server.serverid = redem_license.serverid WHERE server.serverip = "' . $ip . '" OR redem_license.license = "' . $licensekey . '"', PDO::FETCH_ASSOC);
		$query4 = $db3->query('SELECT server.serverip FROM panel.redem_license JOIN panel.server ON server.serverid = redem_license.serverid WHERE redem_license.license = "' . $licensekey . '" LIMIT 1', PDO::FETCH_ASSOC);
		$licensePhoto = $website_config['site_logo'];

		$authQuery = $db->query("SELECT * FROM panel.system WHERE auth_maintenance = 1", PDO::FETCH_ASSOC);
		if ($authQuery->rowCount() > 0) {
			$authorized = false;
			$status = "Maintenance";
			$arrayOutput = array("status" => $status, "firstinstall" => $firstinstall);
			die(json_encode($arrayOutput));
		}

		if ($query->rowCount() > 0 && !$stop) {


			if ($query2->rowCount() == 0) {
				$firstinstall = true;
				$date = date('d.m.y | H:i');

				$idquery23 = $db->query("SELECT redem_license.licenseid FROM panel.redem_license WHERE redem_license.license = '" . $licensekey . "'");
				$users2 = $idquery23->fetch(PDO::FETCH_ASSOC);

				$idquery4 = $db->query("SELECT * FROM panel.users JOIN panel.redem_license ON redem_license.userid = users.userid WHERE redem_license.license = '" . $licensekey . "'");
				$userid = $idquery4->fetch(PDO::FETCH_ASSOC);
				$sql44 = "INSERT INTO notifications (`text`, `date`, `userid`) VALUES ('Your server IP has been successfully registered and is now ready to use FiveSecurity. If you encounter any issues or have any questions, please reach out to our support team via Discord for assistance.', '" . $date . "', '" . $userid['userid'] . "')";
				$db3->query($sql44);

				$sql = "INSERT INTO panel.server(serverip, name, port, status,licenseid, latestres_name) VALUES ('" . $ip . "','" . $servername . "','" . $port . "', '0','" . $users2['licenseid'] . "', '" . $resname . "')";
				$db->query($sql);
				$idquery = $db->query("SELECT server.serverid FROM panel.server WHERE server.serverip = '" . $ip . "'");
				$user = $idquery->fetch(PDO::FETCH_ASSOC);

				$lsid = $users2['licenseid'];
				$svid = $user['serverid'];
				$sql3 = "UPDATE panel.redem_license SET redem_license.serverid = ? WHERE redem_license.licenseid = ?";
				$db->prepare($sql3)->execute([$svid, $lsid]);

				$sql4 = "INSERT INTO panel.users_server(userid,serverid,is_owner) VALUES ('" . $userid['userid'] . "','" . $svid . "', '1')";
				$db->query($sql4);

				$execsql = "CREATE TABLE IF NOT EXISTS `$licensekey` (
					`id` int(11) NOT NULL PRIMARY KEY,
					`name` varchar(50) DEFAULT NULL,
					`steam` varchar(50) DEFAULT NULL,
					`license` varchar(50) NOT NULL,
					`xbl` varchar(50) DEFAULT NULL,
					`live` varchar(50) DEFAULT NULL,
					`discord` varchar(50) DEFAULT NULL,
					`playerip` varchar(50) DEFAULT NULL,
					`hwid` varchar(950) DEFAULT NULL,
					`reason` varchar(255) NOT NULL DEFAULT 'Banned,
					`screen` text NOT NULL DEFAULT 'https://cdn.discordapp.com/attachments/876505680255778857/1068989846271578142/Unbenannt-1-Wiederhergestellt.png'
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				   ";
				$db2->query($execsql);
			} else {
				$result = $query2->fetch();
				if ($ip != $result['serverip']) {
					$authorized = false;
					$status = "falseip";
					$arrayOutput = array("status" => $status, "firstinstall" => $firstinstall);
					die(json_encode($arrayOutput));
				}
				$now = date_create();
				foreach ($query as $row) {
					$deadline = date_create_from_format('d.m.y', $row['expires']);
					if (!$deadline) {
						$deadline = date_create($row['expires']);
					}
					if ($now < $deadline) {
						$authorized = true;
					} else {
						$status = "expired";
						$authorized = false;
					}
				}
			}
		} else {
			$status = "invalid";
			$authorized = false;
		}
	}
} else {
	$status = "error";

}
if ($authorized == false) {
	$arrayOutput = array("status" => $status, "firstinstall" => $firstinstall);
	echo json_encode($arrayOutput);
}

if ($authorized) {
	try {
		$db2->query("SELECT 1 FROM $licensekey LIMIT 1");
		$exists = true;
	} catch (PDOException $e) {
		$exists = false;
	}

	$sqlss = "UPDATE server
		JOIN redem_license ON redem_license.licenseid = panel.server.licenseid
		SET latestres_name = '" . $resname . "' WHERE redem_license.license = '" . $licensekey . "' AND server.serverip = '" . $ip . "' ";
	$db3->query($sqlss);

	$sqlsss = "UPDATE server
		JOIN redem_license ON redem_license.licenseid = panel.server.licenseid
		SET port = '" . $port . "' WHERE redem_license.license = '" . $licensekey . "' AND server.serverip = '" . $ip . "' ";
	$db3->query($sqlsss);

	$sqlssss = "UPDATE server
		JOIN redem_license ON redem_license.licenseid = panel.server.licenseid
		SET name = '" . $servername . "' WHERE redem_license.license = '" . $licensekey . "' AND server.serverip = '" . $ip . "' ";
	$db3->query($sqlssss);


	$sqlssssdddd = "INSERT INTO totalauths (license) VALUES ('" . $licensekey . "')";
	$db4->query($sqlssssdddd);



	if (!$exists) {
		$execsqls = "CREATE TABLE IF NOT EXISTS `$licensekey` (
				`id` int(11) NOT NULL PRIMARY KEY,
				`name` varchar(50) DEFAULT NULL,
				`steam` varchar(50) DEFAULT NULL,
				`license` varchar(50) NOT NULL,
				`xbl` varchar(50) DEFAULT NULL,
				`live` varchar(50) DEFAULT NULL,
				`discord` varchar(50) DEFAULT NULL,
				`playerip` varchar(50) DEFAULT NULL,
				`hwid` varchar(950) DEFAULT NULL,
				`reason` varchar(255) NOT NULL DEFAULT 'no reason given',
				`screen` text NOT NULL
			  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
			   ";
		$db2->query($execsqls);
	}

	$array = [];
	$array[0] = $key;
	$query = $db->query('SELECT * FROM scripts WHERE id=\'' . $scriptid . '\'', PDO::FETCH_ASSOC);

	foreach ($query as $row) {
		$randomnumber = rand(40, 90);
		$array[1] = generateRandomString($randomnumber);
		$array[2] = $randomnumber;
		$array[3] = str_rot13(base64_encode(str_rot13($randomHash)));
		$array[4] = $r1 * 666;
		$array[5] = $r2 * 6;
		$status = "valid";
		$arrayOutput = array("status" => $status, "firstinstall" => $firstinstall, "script" => $array);
		echo json_encode($arrayOutput);

		$logowner = $row['owner'];
		$datetime = date('d/m/Y h:i');

		$query = $db->prepare('INSERT INTO logs SET title = ?, text = ?, isread = ?, icon = ?, color = ?, type = ?, owner = ?, data = ?, date = ?');
		$insert = $query->execute(['' . $site_name . ' Started | Authorized!', $ip . 'have been started successfully with valid license key', 'false', 'mdi-settings', 'text-danger', 'license', $logowner, $authDataArray, $datetime]);


		$url = $website_config['discord_webhook_url'];
		$hookObject = json_encode([

			'tts' => false,
			'embeds' => [
				[
					'title' => ':no_entry: ' . $site_name . ' Started | Authorized!',
					'type' => 'rich',
					'description' => '',
					'color' => 14680064,
					'footer' => ['text' => $language['SystemName'] . ' • ' . $site_name . ' Started | Authorized!'],
					'image' => ['url' => $licensePhoto],
					'fields' => [
						['name' => 'Server Name', 'value' => '```' . $servername . '```', 'inline' => false],
						['name' => 'IP Address', 'value' => '`' . $ip . '`', 'inline' => true],
						['name' => 'Server Key', 'value' => '`' . $licensekey . '`', 'inline' => true]
					]
				]
			]
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $hookObject,
			CURLOPT_HTTPHEADER => ['Content-Type: application/json']
		]);
		$response = curl_exec($ch);
		curl_close($ch);

	}
} else {
	$query = $db->query('SELECT * FROM scripts WHERE id=1', PDO::FETCH_ASSOC);

	if ($query->rowCount()) {
		foreach ($query as $row) {
			$logowner = $row['owner'];
			$datetime = date('d/m/Y h:i');
			$query = $db->prepare('INSERT INTO logs SET title = ?, text = ?, isread = ?, icon = ?, color = ?, type = ?, owner = ?, data = ?, date = ?');
			$insert = $query->execute(['Unauthorized use', $ip . ' Used without permission.', 'false', 'mdi-settings', 'text-danger', 'license', $logowner, $authDataArray, $datetime]);
		}
	}

	$url = $website_config['discord_webhook_url'];
	$hookObject = json_encode([

		'tts' => false,
		'embeds' => [
			[
				'title' => ':no_entry: ' . $site_name . ' has been used on an wrong IP!',
				'type' => 'rich',
				'description' => '',
				'color' => 14680064,
				'footer' => ['text' => '• Licence Not Approved'],
				'image' => ['url' => $licensePhoto],
				'fields' => [
					['name' => 'Server Name', 'value' => '```' . $servername . '```', 'inline' => false],
					['name' => 'IP Address', 'value' => '`' . $ip . '`', 'inline' => true],
					['name' => 'Server Key', 'value' => '`' . $licensekey . '`', 'inline' => true]
				]
			]
		]
	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	$ch = curl_init();
	curl_setopt_array($ch, [
		CURLOPT_URL => $url,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $hookObject,
		CURLOPT_HTTPHEADER => ['Content-Type: application/json']
	]);
	$response = curl_exec($ch);
	curl_close($ch);
}
?>