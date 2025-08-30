<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
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
                        <h2 class="text-center">Add New Work Order</h2>
                    </div>
                    <div class="card-body">
                        <form action="addrun.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="room">Room Number</label>
                                <input type="text" name="room" id="room" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="work_to_be_done">Work To Be Done</label>
                                <textarea name="work_to_be_done" id="work_to_be_done" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="photo">Add Photo (Optional)</label>
                                <input type="file" name="photo" id="photo" class="form-control-file">
                            </div>
                            <input type="hidden" name="submitted_by" value="<?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-primary btn-block">Submit Work Order</button>
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