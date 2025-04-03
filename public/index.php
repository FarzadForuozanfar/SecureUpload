<?php
require_once __DIR__ . '/../vendor/autoload.php';

use SecureUpload\FileTypes\ImageTypes;
use SecureUpload\Interfaces\FileSize;
use SecureUpload\Uploader\SecureUploader;


if (!empty($_FILES['uploaded_file'])) {
    $allowedExtensions = ImageTypes::getAllExtensions();
    $maxFileNameLength = 50;
    $maxFileSize = FileSize::TEN_MG;
    $antivirusEnabled = true;
    $enableLogging = true;

    $uploader = new SecureUploader( $allowedExtensions, $maxFileNameLength, $maxFileSize, $antivirusEnabled, $enableLogging);

    $files = [];
    foreach ($_FILES['uploaded_file'] as $key => $items) {
        foreach ($items as $index => $item) {
            $files[$index][$key] = $item;
        }
    }
    foreach ($files as $file) {
        $result = $uploader->validate($file['tmp_name'], $file['name']);
        if (isset($result['error'])) {
            var_dump($result);
            die();
        } else {
            var_dump($result);
        }
    }
} else {
    echo "No file uploaded";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Upload</title>
</head>
<body>
    <form enctype="multipart/form-data" method="post">
        <input multiple type="file" name="uploaded_file[]" id="file">
        <input type="submit" value="Upload">
    </form>
</body>
</html>
