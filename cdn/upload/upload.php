<?php
include 'db.php';

$path = 'img/';
$fileName = uniqid('', true) . '.jpg';
$file = $path . $fileName;
$url = "https://cdn.fivesecurity.de/upload/img/";

if (isset($_FILES['files']['tmp_name'][0]) && is_uploaded_file($_FILES['files']['tmp_name'][0])) {
    if (!is_dir($path)) mkdir($path, 0755, true);

    $image = @imagecreatefromstring(file_get_contents($_FILES['files']['tmp_name'][0]));
    if (!$image) {
        echo json_encode(["success" => false, "message" => "Invalid image"]);
        exit;
    }

    imagejpeg($image, $file, 10);
    imagedestroy($image);

    $expiration = time() + (4 * 24 * 60 * 60);
    $result = [
        "success" => true,
        "files" => [[
            "hash" => $fileName,
            "name" => $fileName,
            "url" => $url . $fileName,
            "size" => filesize($file),
            "expiration" => $expiration
        ]]
    ];
    echo json_encode($result);

    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO totalscreenshots (license) VALUES (?)");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();

    $task = "0 0 * * * rm -f $file";
    exec("echo '$task' | crontab -");

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No file uploaded."]);
}
