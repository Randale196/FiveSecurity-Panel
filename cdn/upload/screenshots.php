<?php
require_once('curl.php');
include 'db.php';

$path = 'screens/';
$fileName = uniqid('', true) . '.jpg';
$file = $path . $fileName;
$url = "https://cdn.fivesecurity.de/upload/screens/";

if (isset($_FILES['files']['tmp_name'][0]) && is_uploaded_file($_FILES['files']['tmp_name'][0])) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $fileType = mime_content_type($_FILES['files']['tmp_name'][0]);
    if (strpos($fileType, 'image/') !== false) {
        if (move_uploaded_file($_FILES['files']['tmp_name'][0], $file)) {
            $expiration = time() + (10 * 24 * 60 * 60);

            $result = [
                "success" => true,
                "files" => [
                    [
                        "hash" => $fileName,
                        "name" => $fileName,
                        "url" => $url . $fileName,
                        "size" => $_FILES['files']['size'][0],
                        "expiration" => $expiration
                    ]
                ],
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
            echo json_encode(["success" => false, "message" => "Error moving the file."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Uploaded file is not an image."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No file uploaded or file is invalid."]);
}
?>