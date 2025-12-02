<?php
include '../../database.php';

$ip = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER['REMOTE_ADDR'] ?? null;

$token = "MTM4MjQ2NTUxNTI1MDc4MjIwOA.GfVCBG.5lQCGTLJHa-NJqDK_QGDyu3yNnN5oHlZCVbCY4"; // Discord Customer Bot Token - Only works when the Bot is on the Server from the Customer / RP Server

if (!$ip) {
    die(json_encode(["error" => "IP address not found"]));
}

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $paneldb->prepare("SELECT role_id, server_id FROM server WHERE serverip = :ip");
$stmt->execute([':ip' => $ip]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $roleID = $row['role_id'];
    $serverID = $row['server_id'];

    function getDiscordIDs($serverID, $roleID, $token)
    {
        $url = "https://discord.com/api/v9/guilds/$serverID/members?limit=1000";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bot $token",
            'Content-Type: application/json'
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            $discordIDs = [];

            foreach ($data as $member) {
                if (isset($member['roles']) && in_array($roleID, $member['roles'])) {
                    $discordIDs[] = $member['user']['id'];
                }
            }

            return $discordIDs;
        }

        return false;
    }

    $discordIDs = getDiscordIDs($serverID, $roleID, $token);

    if ($discordIDs) {
        echo json_encode($discordIDs);
    } else {
        echo json_encode(["error" => "No Discord IDs found."]);
    }
} else {
    echo json_encode(["error" => "No server data found for the specified IP"]);
}
?>