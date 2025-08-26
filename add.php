<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room = $_POST['room'] ?? '';
    $work_to_be_done = $_POST['work_to_be_done'] ?? '';
    $submitted_by = $_SESSION['name'];

    if (!empty($room) && !empty($work_to_be_done)) {
        $query = $conn->prepare("INSERT INTO `orders` (`room`, `work_to_be_done`, `submitted_by`) VALUES (:room, :work_to_be_done, :submitted_by)");
        $query->bindParam(':room', $room);
        $query->bindParam(':work_to_be_done', $work_to_be_done);
        $query->bindParam(':submitted_by', $submitted_by);

        if ($query->execute()) {
            $_SESSION['message'] = "Work order added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add work order.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <title>Add Work Order</title>
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
                <li class="nav-item dropdown active">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Orders <span class="sr-only">(current)</span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="/index.php">Current Work Orders</a>
                        <a class="dropdown-item" href="/add.php">Add Work Order</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/nfns.php">Notes for Techs</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/print.php">Print Daily Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="text-center">Add a new work order</h2>
                    </div>
                    <div class="card-body">
                        <form action="add.php" method="post">
                            <div class="form-group">
                                <label for="room">Room number</label>
                                <input type="text" name="room" class="form-control" id="room" placeholder="Enter room number" required>
                            </div>
                            <div class="form-group">
                                <label for="work_to_be_done">Work to be done</label>
                                <textarea name="work_to_be_done" class="form-control" id="work_to_be_done" placeholder="Describe the work to be done" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Work Order</button>
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