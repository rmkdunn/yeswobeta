<?php
session_start();
include "config.php";

// Function to send a detailed error response and stop the script
function send_error($code, $message) {
    http_response_code($code);
    die($message); // Use die() to stop execution and send the message back to the JavaScript
}

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    send_error(403, "Access Forbidden. You are not logged in.");
}

// Check if the required POST data and file are present
if (!isset($_POST['task_id']) || !isset($_FILES['photo'])) {
    send_error(400, "Invalid request. Task ID or file is missing.");
}

$task_id = $_POST['task_id'];
$photo_file = $_FILES['photo'];

// Check for built-in PHP upload errors
if ($photo_file['error'] !== UPLOAD_ERR_OK) {
    $error_messages = [
        UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the server's maximum file size limit (upload_max_filesize).",
        UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the form's maximum file size limit.",
        UPLOAD_ERR_PARTIAL    => "The file was only partially uploaded.",
        UPLOAD_ERR_NO_FILE    => "No file was selected for upload.",
        UPLOAD_ERR_NO_TMP_DIR => "Server configuration error: Missing a temporary folder.",
        UPLOAD_ERR_CANT_WRITE => "Server error: Failed to write file to disk.",
        UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload.",
    ];
    $error_code = $photo_file['error'];
    $message = $error_messages[$error_code] ?? "An unknown file upload error occurred.";
    send_error(500, $message);
}

$target_dir = "uploads/";

// Check if the uploads directory exists and is writable
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0755, true)) {
        send_error(500, "Server error: Failed to create the 'uploads' directory. Please check parent directory permissions.");
    }
}
if (!is_writable($target_dir)) {
    send_error(500, "Server error: The 'uploads' directory is not writable. Please check its permissions.");
}

// Create a unique file name to prevent overwriting existing files
$file_name = uniqid() . '-' . basename($photo_file["name"]);
$target_file = $target_dir . $file_name;

// Try to move the uploaded file to the target directory
if (move_uploaded_file($photo_file["tmp_name"], $target_file)) {
    // File moved successfully, now update the database
    try {
        $query = $conn->prepare("UPDATE orders SET photo = :photo WHERE id = :id");
        $query->bindParam(':photo', $target_file);
        $query->bindParam(':id', $task_id);
        
        if ($query->execute()) {
            http_response_code(200);
            echo "Photo uploaded successfully.";
        } else {
            $error_info = $query->errorInfo();
            send_error(500, "Database update failed: " . ($error_info[2] ?? 'Unknown database error.'));
        }
    } catch (PDOException $e) {
        send_error(500, "Database connection error: " . $e->getMessage());
    }
} else {
    send_error(500, "Server error: Failed to move the uploaded file.");
}

?>