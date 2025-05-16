<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/service-account.json');
$client->addScope(Google_Service_Drive::DRIVE);
$service = new Google_Service_Drive($client);

$folderId = '1l01tNgJzzCBIDGUaH2CONuNPJDOUVCZG'; // Thư mục Drive

// Xử lý xóa file
if (isset($_GET['delete'])) {
    $fileId = $_GET['delete'];
    try {
        $service->files->delete($fileId);
        header("Location: list.php");
        exit();
    } catch (Exception $e) {
        echo "Lỗi khi xóa: " . $e->getMessage();
        exit();
    }
}

// Lấy danh sách file trong folder
$optParams = [
    'q' => "'$folderId' in parents and trashed=false",
    'fields' => 'files(id, name, webViewLink, webContentLink)',
    'orderBy' => 'createdTime desc'
];

$results = $service->files->listFiles($optParams);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách file đã upload</title>
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #eee; }
  </style>
</head>
<body>
  <h2>Danh sách file đã upload</h2>
  <table>
    <thead>
      <tr>
        <th>Tên file</th>
        <th>Xem</th>
        <th>Tải xuống</th>
        <th>Xóa</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($results->getFiles()) == 0): ?>
        <tr><td colspan="4">Chưa có file nào</td></tr>
      <?php else: ?>
        <?php foreach ($results->getFiles() as $file): ?>
          <tr>
            <td><?php echo htmlspecialchars($file->name); ?></td>
            <td><a href="<?php echo $file->webViewLink; ?>" target="_blank">Xem</a></td>
            <td><a href="<?php echo $file->webContentLink; ?>" target="_blank">Tải xuống</a></td>
            <td><a href="?delete=<?php echo $file->id; ?>" onclick="return confirm('Bạn có chắc muốn xóa file này?')">Xóa</a></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <p><a href="index.html">← Quay lại trang upload</a></p>
</body>
</html>
