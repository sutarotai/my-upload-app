<?php
require_once __DIR__ . '/vendor/autoload.php';

if (!isset($_FILES['file'])) {
    http_response_code(400);
    exit('Không có file');
}

$client = new Google_Client();
$client->setAuthConfig('service-account.json');
$client->addScope(Google_Service_Drive::DRIVE);
$service = new Google_Service_Drive($client);

$file = $_FILES['file'];
$fileName = basename($file['name']);
$filePath = $file['tmp_name'];

$folderId = '1l01tNgJzzCBIDGUaH2CONuNPJDOUVCZG'; // ← Thay bằng ID thư mục Drive

$driveFile = new Google_Service_Drive_DriveFile([
    'name' => $fileName,
    'parents' => [$folderId]
]);

try {
    $content = file_get_contents($filePath);

    $createdFile = $service->files->create($driveFile, [
        'data' => $content,
        'uploadType' => 'multipart',
        'fields' => 'id, name, webViewLink, webContentLink'
    ]);

    echo "<a href='{$createdFile->webViewLink}' target='_blank'>{$createdFile->name}</a> ( <a href='{$createdFile->webContentLink}'>Tải xuống</a> )";
} catch (Exception $e) {
    http_response_code(500);
    echo 'Lỗi: ' . $e->getMessage();
}
