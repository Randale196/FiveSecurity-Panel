<?php
include '../../database.php';

$data2 = json_decode(file_get_contents("php://input"), true);
$js = $data2['jsonData'];
$jsonData2 = $js['discordembed'][0];
echo json_encode($jsonData2);

$username = $jsonData2['username'];
$userurl = $jsonData2['avatar_url'];
$embed = $jsonData2['embeds'];
$title = $embed['title'];
$description = $embed['description'];
$color = $embed['color'];
$footer = $embed['footer'];
$ftText = $footer['text'];
$ftIcon = $footer['icon_url'];
$thumbnail = $embed['thumbnail'];
$thUrl = $thumbnail['url'];
$author = $embed['author'];
$auName = $author['name'];
$auUrl = $author['url'];
$auiUrl = $author['icon_url'];
$imageURL = $embed['image']['url'];
$fields = $embed['fields'];
$licenseKey = $data2['license'];

$fieldData = [];
foreach ($fields as $field) {
    $fieldName = $field['name'];
    $fieldValue = $field['value'];
    $fieldInline = $field['inline'];

    $fieldData[] = [
        "name" => $fieldName,
        "value" => $fieldValue,
        "inline" => $fieldInline
    ];
}

$webhookData = [
    "username" => "🛡FiveSecurity🛡",
    "avatar_url" => "https://cdn.fivesecurity.de/logos/FiveSecurity.png",
    "embeds" => [
      [
        "footer" => [
          "text" => $ftText,
          "icon_url" => $ftIcon
        ],
        "image" => [
             "url" => $imageURL
        ],
        "thumbnail" => [
          "url" => $thUrl
        ],
        "author" => [
          "name" => $auName,
          "url" => $auUrl,
          "icon_url" => $auiUrl
        ],
        "fields" => $fieldData,
        "description" => $description,
        "title" => $title,
        "color" => $color
    ],
    ]
  ];


$category = $js['category'];

if (isset($category)) {
    
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
    $stmt->execute([':licensekey' => $licenseKey]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Anticheat Webhooks Logs
        $banWebhookURL = "https://canary.discord.com/api/webhooks/1391804790639689888/HLoELGK-Hh69rGknc7G6YFhBHeaQX3rQKhAAdOPU4dWtGg2L98rTV9MhYrdW4bp_VcM_"; // Banned Webhook URL
        $backdoorWebhookURL = "https://canary.discord.com/api/webhooks/1391806005180104714/2qc1S0X6m4slbxaukAIyRKnophcS2rAXVskp8nmMlmWdyaFgSV5MNQEnno2xhX69zCXm"; // Backdoor Webhook URL
        $connectWebhookURL = "https://canary.discord.com/api/webhooks/1391805898330083348/FZ1N4pwAUvvB9R75ysMhvFcele8iRSkIDyH7VC_kU0qLHe4j4DWTfMFln9Qj_x3HqRzW"; // Connect Webhook URL
        $globalbanWebhookURL = "https://canary.discord.com/api/webhooks/1391806126420525246/YNlOlZUoZi3Exum1z39IPgnKzR7NV-PGFXoVJFinZu6dIxjqMUHfKc20Qm88Q2ocMQyC"; // Global Banned Webhook URL
        $posiblecheaterWebhookURL = "https://canary.discord.com/api/webhooks/1391806243319844948/-LOIHbNyjmDscv4StGX0VrrZNOmq0ekJLqF0b9UkvkmQrDXRViVA-zInxGuK11Xn7de7"; // Possible Cheater Webhook URL
        $triedtojoinWebhookURL = "https://canary.discord.com/api/webhooks/1391806344364822539/Ls8mJ6t2xB-U2QdFWeZXLhABOuyXjTG2sVwfcg1P42DVS2GrLmJTmzCIG5K4BuruBn1W"; // Tried to Join Webhook URL

        // Auth Logs
        $validlicenseWebhookURL = "https://canary.discord.com/api/webhooks/1391806478830276660/hpNCKa8IdhEbpVWIhW5yv51L3qqm15H8wccWaZ_QKamBZjPsgPos52s175zxkbyrCE9h"; // Valid License Webhook URL
        $wrongWebhookURL = "https://canary.discord.com/api/webhooks/1391806589823881216/fubuFpkHeNV-2KwQrDC03U4z7SydcKKTLUTQhD176eKIl2c-TP7-4wNUxCj5wDZhX3D5"; // Wrong Webhook URL

        // Crack Logs
        $bypassWebhookURL = "https://canary.discord.com/api/webhooks/1391806726264852510/0M04IN8apYNPR6zg5otI1owTdSEVIlqZKOEUn1clSrBwFFQZ-3FV9qMUZ7eaCSsztOeh"; // Bypass Webhook URL
        $crackWebhookURL = "https://ptb.discord.com/api/webhooks/1391854352268001361/6njZeae78OQaC4-4qbBLFrOhWcnZrbF1lAbo51IkAqUUiB47FgdQureFoOFZJHAtalZF";

        if ($category === "ban") { // Ban Logs
            sendDiscordWebhook($banWebhookURL, $webhookData);
        } elseif ($category === "backdoor") { // Backdoor Logs
            sendDiscordWebhook($backdoorWebhookURL, $webhookData);
        } elseif ($category === "connect") { // Connect Logs
            sendDiscordWebhook($connectWebhookURL, $webhookData);
        } elseif ($category === "globalban") { // Global Ban Logs
            sendDiscordWebhook($globalbanWebhookURL, $webhookData);
        } elseif ($category === "posiblecheater") { // Possible Cheater Logs
            sendDiscordWebhook($posiblecheaterWebhookURL, $webhookData);
        } elseif ($category === "triedtojoin") { // Tried to Join Logs
            sendDiscordWebhook($triedtojoinWebhookURL, $webhookData);
        } elseif ($category === "validlicense") { // Valid License Logs
            sendDiscordWebhook($validlicenseWebhookURL, $webhookData);
        } elseif ($category === "wronglicense") { // Invalid License Logs
            sendDiscordWebhook($wrongWebhookURL, $webhookData);
        } elseif ($category === "bypass") { // Invalid License Logs
            sendDiscordWebhook($bypassWebhookURL, $webhookData);
        } elseif ($category === "crack") {
            sendDiscordWebhook($crackWebhookURL, $webhookData);
        } else {
            echo "Invalid category";
        }
    }
} else {
   
}
?>