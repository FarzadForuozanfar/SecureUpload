<?php
require_once __DIR__ . '/../vendor/autoload.php';

use SecureUpload\FileTypes\ImageTypes;
use SecureUpload\Interfaces\FileSize;
use SecureUpload\Uploader\SecureUploader;

$results = [];

/** @var SecureUploader */
$uploader;

if (!empty($_FILES['uploaded_file'])) {
    $allowedExtensions = ImageTypes::getAllExtensions();
    $maxFileNameLength = 50;
    $maxFileSize = FileSize::TEN_MG;

    $uploader = new SecureUploader($allowedExtensions, $maxFileNameLength, $maxFileSize);

    $files = [];
    foreach ($_FILES['uploaded_file'] as $key => $items) {
        foreach ($items as $index => $item) {
            $files[$index][$key] = $item;
        }
    }
    foreach ($files as $file) {
        $result = $uploader->validate($file['tmp_name'], $file['name']);
        $results[] = [
            'name' => $file['name'],
            'status' => isset($result['error']) ? 'error' : 'success',
            'message' => $result['error'] ?? ('✅ File uploaded successfully in ' . round($result['time'], 2) . ' ms.')
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Upload</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #74ABE2, #5563DE) no-repeat fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 4% auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #5563DE;
            margin-bottom: 30px;
        }

        input[type="file"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background: #5563DE;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #3f50b5;
        }

        .result {
            margin-top: 20px;
        }

        .result-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            background: #f7f7f7;
            border-left: 5px solid;
        }

        .result-item.success {
            border-color: #28a745;
            background-color: #e6ffed;
        }

        .result-item.error {
            border-color: #dc3545;
            background-color: #ffe6e6;
        }

        .result-item strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Secure Upload</h1>
        <form enctype="multipart/form-data" method="post">
            <input multiple type="file" name="uploaded_file[]" id="file">
            <input type="submit" value="Upload">
        </form>

        <?php if (!empty($results)): ?>
            <div class="result">
                <?php foreach ($results as $res): ?>
                    <div class="result-item <?= $res['status'] ?>">
                        <strong><?= htmlspecialchars($res['name']) ?></strong>
                        <p dir="<?= $uploader->getLang() === 'fa' ? 'rtl' : 'ltr' ?>" ><?= htmlspecialchars($res['message']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
