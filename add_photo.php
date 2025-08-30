<?php
session_start();
include "config.php"; // Your database connection

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$task_id = $_GET['id'] ?? null;

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST['id'] ?? null;
    
    if ($task_id && isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
        $target_dir = "uploads/";
        // Create the uploads directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file_name = uniqid() . '-' . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if($check !== false) {
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                // Try to upload file
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    // File uploaded successfully, now update the database
                    $query = $conn->prepare("UPDATE orders SET photo = :photo WHERE id = :id");
                    $query->bindParam(':photo', $target_file);
                    $query->bindParam(':id', $id);
                    
                    if($query->execute()){
                        $_SESSION['message'] = "The photo has been uploaded and linked to the task.";
                        header("Location: index.php");
                        exit;
                    } else {
                        $message = "Sorry, there was an error updating the database.";
                    }
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $message = "File is not an image.";
        }
    } else {
        $message = "No file was uploaded or task ID is missing.";
    }
}

if (!$id) {
    die("Task ID is required.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <title>Add Photo</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
        <a class="navbar-brand" href="#">DFWAM</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">Home</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h3 class="text-center">Add Photo for Task #<?php echo htmlspecialchars($task_id); ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($message)): ?>
                            <div class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form action="add_photo.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id); ?>">
                            <div class="form-group">
                                <label for="photo">Select photo to upload:</label>
                                <input type="file" name="photo" id="photo" class="form-control-file" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Upload Photo</button>
                            <a href="index.php" class="btn btn-secondary btn-block mt-2">Skip</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>