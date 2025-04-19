<?php
session_start();
require './connect.php';

$maxFileSize = 1000 * 1024; // 100 KB
$uploadDir = "uploads/"; // Make sure this directory exists and is writable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phno = $_POST["phno"];
    $ophno = $_POST["ophno"];
    $password = $_POST["password"];
    $id = uniqid();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        // Validate file size
        if ($_FILES["image"]["size"] > $maxFileSize) {
            $_SESSION['status'] = "Image exceeds maximum file size (100KB).";
            $_SESSION['status_code'] = "error";
            header('Location: join.php');
            exit();
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $detectedType = mime_content_type($_FILES["image"]["tmp_name"]);
        if (!in_array($detectedType, $allowedTypes)) {
            $_SESSION['status'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            $_SESSION['status_code'] = "error";
            header('Location: join.php');
            exit();
        }

        // Generate unique filename
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $fileName = $id . '.' . $fileExtension;
        $targetPath = $uploadDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            // Insert data with file path
            try {
                $query = "INSERT INTO usertable (id, name, email, phno, ophno, password, image) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $query);
                $imagePath = $targetPath; // Store full path or just filename depending on your needs

                mysqli_stmt_bind_param($stmt, "sssssss", $id, $name, $email, $phno, $ophno, $hashedPassword, $fileName);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new mysqli_sql_exception(mysqli_stmt_error($stmt));
                }

                $_SESSION['status'] = "Registered successfully!";
                $_SESSION['status_code'] = "success";
                header('Location: join.php');
                exit();

            } catch (mysqli_sql_exception $e) {
                // Handle database errors
                $_SESSION['status'] = "Database error: " . $e->getMessage();
                $_SESSION['status_code'] = "error";
                header('Location: join.php');
                exit();
            }
        } else {
            $_SESSION['status'] = "Failed to upload image.";
            $_SESSION['status_code'] = "error";
            header('Location: join.php');
            exit();
        }
    } else {
        $_SESSION['status'] = "Please select a valid image file.";
        $_SESSION['status_code'] = "error";
        header('Location: join.php');
        exit();
    }
}

// Keep your existing reconnectDatabase() function if needed

?>