<?php
session_start();
include("../../func.php");
include("../../config.php");

function send_error($code, $message) {
    http_response_code($code);
    die(json_encode(['error' => $message]));
}

if (!isset($_SESSION['id']) || !isset($_SESSION['group'])) {
    send_error(401, 'Unauthorized');
}

if (is_banned($_SESSION["id"])) {
    send_error(403, 'Account is banned');
}

$stmt = $link->prepare("SELECT license FROM redem_license WHERE userid = ?");
if (!$stmt) {
    send_error(500, 'Database error');
}

$stmt->bind_param("i", $_SESSION["id"]);
if (!$stmt->execute()) {
    send_error(500, 'Failed to execute query');
}

$result = $stmt->get_result();
if (!$result || !($row = $result->fetch_assoc())) {
    send_error(404, 'License not found');
}

$license = $row['license'];
if (empty($license)) {
    send_error(400, 'Invalid license');
}

global $download_config;
if (!isset($download_config) || !is_array($download_config)) {
    send_error(500, 'Invalid configuration');
}

$base_path = $download_config['base_path'];
$config_path = $download_config['config_path'];

if (!file_exists($base_path)) {
    send_error(500, 'Anticheat files not found');
}

if (!file_exists($config_path)) {
    send_error(500, 'Configuration file not found');
}

$existing_data = file_get_contents($config_path);
if ($existing_data === false) {
    send_error(500, 'Failed to read configuration file');
}

$license_key_pos = strpos($existing_data, $download_config['license_key_name']);
$new_data = $download_config['license_key_name'] . " = \"$license\"";
$data = $existing_data;

if ($license_key_pos !== false) {
    $next_line_pos = strpos($existing_data, "\n", $license_key_pos);
    if ($next_line_pos !== false) {
        $old_license_key = substr($existing_data, $license_key_pos, $next_line_pos - $license_key_pos);
        $data = str_replace($old_license_key, $new_data, $existing_data);
    }
} else {
    $data = $new_data . "\n" . $existing_data;
}

$temp_file = tmpfile();
if ($temp_file === false || !fwrite($temp_file, $data)) {
    send_error(500, 'Failed to create temporary file');
}

if (!class_exists('ZipArchive')) {
    send_error(500, 'ZipArchive class not found. PHP Zip extension required.');
}

$zip = new ZipArchive();
$zip_file = tempnam(sys_get_temp_dir(), $download_config['temp_prefix']) . '.zip';
if ($zip_file === false) {
    fclose($temp_file);
    send_error(500, 'Failed to create temporary zip file');
}

$folder_to_zip = $base_path;
if ($zip->open($zip_file, ZipArchive::CREATE) !== TRUE) {
    fclose($temp_file);
    unlink($zip_file);
    send_error(500, 'Could not create archive');
}

try {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder_to_zip, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $key => $value) {
        $real_path = realpath($key);
        if ($real_path === false) continue;
        
        if (!is_dir($key)) {
            if (!$zip->addFile($real_path, str_replace($folder_to_zip . '/', '', $key))) {
                throw new Exception("Could not add file: $key");
            }
        }
    }

    if (!$zip->addFile(stream_get_meta_data($temp_file)['uri'], $download_config['config_file_path'])) {
        throw new Exception('Could not add config file to archive');
    }
} catch (Exception $e) {
    $zip->close();
    fclose($temp_file);
    unlink($zip_file);
    send_error(500, $e->getMessage());
}

if (!$zip->close()) {
    fclose($temp_file);
    unlink($zip_file);
    send_error(500, 'Error closing archive');
}

header('Content-Type: ' . $download_config['headers']['content_type']);
header('Content-Disposition: attachment; filename="' . $download_config['zip_filename'] . '"');
header('Content-Length: ' . filesize($zip_file));
header('Cache-Control: ' . $download_config['headers']['cache_control']);
header('Pragma: ' . $download_config['headers']['pragma']);
header('Expires: ' . $download_config['headers']['expires']);

if (ob_get_level()) ob_clean();
flush();

if (!readfile($zip_file)) {
    unlink($zip_file);
    fclose($temp_file);
    send_error(500, 'Failed to send file');
}

unlink($zip_file);
fclose($temp_file);
exit;
?>