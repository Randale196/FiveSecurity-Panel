<?php
header('Content-Type: application/json');

function http_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $output = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($output === false) {
        return null;
    }
    return $output;
}

$ip = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (isset($data['ip'])) {
        $ip = $data['ip'];
    }
} else {
    $ip = isset($_GET['ip']) ? $_GET['ip'] : null;
}
if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing IP parameter.']);
    exit;
}

$apis = [
    [
        'name' => 'ip-api',
        'url' => "http://ip-api.com/json/$ip?fields=proxy,hosting,query",
        'parse' => function($data) {
            $json = json_decode($data, true);
            return isset($json['proxy']) && $json['proxy'] === true;
        }
    ],
    [
        'name' => 'ipinfo',
        'url' => "https://ipinfo.io/$ip/json",
        'parse' => function($data) {
            $json = json_decode($data, true);
            return isset($json['privacy']['vpn']) && $json['privacy']['vpn'] === true;
        }
    ],
    [
        'name' => 'ipqualityscore',
        'url' => "https://ipqualityscore.com/api/json/ip/demo/$ip",
        'parse' => function($data) {
            $json = json_decode($data, true);
            return isset($json['vpn']) && $json['vpn'] === true;
        }
    ],
    [
        'name' => 'vpnapi',
        'url' => "https://vpnapi.io/api/$ip?key=demo",
        'parse' => function($data) {
            $json = json_decode($data, true);
            return isset($json['security']['vpn']) && $json['security']['vpn'] === true;
        }
    ]
];

$results = [];
$vpn_count = 0;
foreach ($apis as $api) {
    $response = http_get($api['url']);
    if ($response !== null) {
        $is_vpn = false;
        try {
            $is_vpn = $api['parse']($response);
        } catch (Exception $e) {
            $is_vpn = false;
        }
        $results[$api['name']] = $is_vpn;
        if ($is_vpn) $vpn_count++;
    } else {
        $results[$api['name']] = 'error';
    }
}

$final = $vpn_count >= 1;

$response = [
    'ip' => $ip,
    'is_vpn' => $final,
    'details' => $results
];

echo $final ? 'y' : 'n'; 