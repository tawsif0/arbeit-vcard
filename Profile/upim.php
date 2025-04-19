<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Redirecting</title>
    <link rel="shortcut icon" href="../assests/image/arbeit technology logo.png" type="image/x-icon" />
    <style>
    body {
        background: #212529;
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>
    <?php
  session_start();
  require_once "../connect.php";

  // Configuration
  $uploadDir = "../uploads/";
  $maxFileSize = 1000 * 1024; // 100 KB
  $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    try {
      // Create upload directory if not exists
      if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      $userId = $_SESSION['id'];
      $file = $_FILES['image'];

      // Validate file
      if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error");
      }

      if ($file['size'] > $maxFileSize) {
        throw new Exception("File size exceeds 100KB limit");
      }

      $fileInfo = new finfo(FILEINFO_MIME_TYPE);
      $mimeType = $fileInfo->file($file['tmp_name']);

      if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception("Invalid file type");
      }

      // Get current image filename
      $stmt = mysqli_prepare($con, "SELECT image FROM usertable WHERE id = ?");
      mysqli_stmt_bind_param($stmt, "s", $userId);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $oldImage = mysqli_fetch_assoc($result)['image'];

      // Delete old image if exists
      if ($oldImage && file_exists($uploadDir . $oldImage)) {
        unlink($uploadDir . $oldImage);
      }

      // Generate new filename
      $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
      $newFilename = $userId . '_' . time() . '.' . $extension;
      $targetPath = $uploadDir . $newFilename;

      // Process image
      $originalImage = imagecreatefromstring(file_get_contents($file['tmp_name']));
      $width = 200;
      $height = 200;
      $resizedImage = imagecreatetruecolor($width, $height);
      imagecopyresampled(
        $resizedImage,
        $originalImage,
        0,
        0,
        0,
        0,
        $width,
        $height,
        imagesx($originalImage),
        imagesy($originalImage)
      );

      // Save resized image
      imagejpeg($resizedImage, $targetPath, 85);

      // Update database
      $stmt = mysqli_prepare($con, "UPDATE usertable SET image = ? WHERE id = ?");
      mysqli_stmt_bind_param($stmt, "ss", $newFilename, $userId);
      mysqli_stmt_execute($stmt);

      // Cleanup
      imagedestroy($originalImage);
      imagedestroy($resizedImage);

      echo "<script>
                Swal.fire('Updated', 'Image successfully updated!', 'success')
                .then(() => window.location = 'index.php');
            </script>";

    } catch (Exception $e) {
      error_log($e->getMessage());
      echo "<script>
                Swal.fire('Error', '" . addslashes($e->getMessage()) . "', 'error')
                .then(() => window.location = 'index.php');
            </script>";
    }
  } else {
    echo "<script>
            Swal.fire('Error', 'No image uploaded', 'error')
            .then(() => window.location = 'index.php');
        </script>";
  }
  ?>
</body>

</html>