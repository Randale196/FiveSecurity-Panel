<?php
include('../func.php');
include('../config.php');


$discordClientId = $website_config['discord_client_id'];
$discordClientSecret = $website_config['discord_client_secret'];
$discordRedirectUri = 'https://' . $website_config['site_domain'] . '/discord/unlink.php';
$discordScope = 'identify';

if (!isset($_GET['code'])) {
    $authUrl = 'https://discord.com/api/oauth2/authorize?client_id=' . $discordClientId . '&redirect_uri=' . urlencode($discordRedirectUri) . '&response_type=code&scope=' . urlencode($discordScope);
    header('Location: ' . $authUrl);
    exit();
}

$tokenUrl = 'https://discord.com/api/oauth2/token';
$fields = [
    'client_id' => $discordClientId,
    'client_secret' => $discordClientSecret,
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'redirect_uri' => $discordRedirectUri,
    'scope' => $discordScope,
];

$curl = curl_init($tokenUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);

if (curl_errno($curl)) {
    die('CURL Error ' . curl_error($curl));
}

curl_close($curl);

$responseData = json_decode($response, true);
if (!isset($responseData['access_token'])) {
    die('Error fetching access token');
}

$accessToken = $responseData['access_token'];

$userUrl = 'https://discord.com/api/users/@me';
$curl = curl_init($userUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$userResponse = curl_exec($curl);

if (curl_errno($curl)) {
    die('CURL Error ' . curl_error($curl));
}

curl_close($curl);

$userData = json_decode($userResponse, true);
if (!isset($userData['id'])) {
    die('Error fetching user data');
}

$discordUserId = $userData['id'];

$useridpanel = $_SESSION['id'] ?? null;

if (!$useridpanel) {
    die("User ID from session is not set");
}

$sql = "UPDATE users SET discord = ? WHERE userid = ?";
$stmt = mysqli_prepare($link, $sql);

if ($stmt === false) {
    die("Could not prepare statement " . mysqli_error($link));
}

$discordStatus = "not set";
mysqli_stmt_bind_param($stmt, "ss", $discordStatus, $useridpanel);

$executeResult = mysqli_stmt_execute($stmt);
if ($executeResult === false) {
    die("Could not execute statement " . mysqli_stmt_error($stmt));
}

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Discord ID successfully unlinked";
} else {
    echo "No rows were updated. Check if the user ID exists...";
}

mysqli_stmt_close($stmt);

header('Location: https://' . $website_config['site_domain'] . '/account/');
exit();

?>