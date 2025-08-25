<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include "config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['room'], $_POST['work'])) {
        $room = $_POST['room'];
        $work = $_POST['work'];
        $submitted_by = $_SESSION['name']; // Get username from session

        $stmt = $conn->prepare("INSERT INTO orders (room, work_to_be_done, submitted_by) VALUES (?, ?, ?)");
        $stmt->execute([$room, $work, $submitted_by]);

        $_SESSION['message'] = "Work order added successfully!";
        header("location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Work Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-6 offset-md-3">
                <h1 class="text-center">Add Work Order</h1>
                <form action="add.php" method="post">
                    <div class="form-group">
                        <label for="room">Room Number</label>
                        <input type="text" name="room" id="room" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="work">Work to be done</label>
                        <textarea name="work" id="work" class="form-control" required></textarea>
                    </div>
                    <input type="submit" value="Add Work Order" class="btn btn-primary">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>