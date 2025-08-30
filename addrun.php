<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room = $_POST['room'];
    $work_to_be_done = $_POST['work_to_be_done'];
    $submitted_by = $_POST['submitted_by'];
    $photo_path = null;

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Allow certain file formats
            if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $photo_path = $target_file;
                }
            }
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO `orders` (room, work_to_be_done, photo, submitted_by) VALUES (:room, :work_to_be_done, :photo, :submitted_by)");
        $stmt->bindParam(':room', $room);
        $stmt->bindParam(':work_to_be_done', $work_to_be_done);
        $stmt->bindParam(':photo', $photo_path);
        $stmt->bindParam(':submitted_by', $submitted_by);
        $stmt->execute();
        $_SESSION['message'] = "Work order added successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

header('Location: index.php');
exit;
?>