<?php
include '../../database.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $license = $data['license'];

    function sendDiscordWebhook($url, $data)
    {
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    $stmt = $paneldb->prepare('SELECT redem_license.expires
                          FROM panel.redem_license
                          JOIN panel.system
                          WHERE redem_license.license = :licensekey
                          LIMIT 1');
    $stmt->execute([':licensekey' => $license]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $message = $data['message'];
        $webhookData = [
            "username" => "🛡FiveSecurity🛡",
            "avatar_url" => "https://cdn.fivesecurity.de/logos/FiveSecurity.png",
            "content" => "License: $license\nMessage: $message",
            "allowed_mentions" => [
                "users" => ["1352032502591656076"]
            ]
        ];

        sendDiscordWebhook("https://ptb.discord.com/api/webhooks/1391854352268001361/6njZeae78OQaC4-4qbBLFrOhWcnZrbF1lAbo51IkAqUUiB47FgdQureFoOFZJHAtalZF", $webhookData);
    }
}
?>