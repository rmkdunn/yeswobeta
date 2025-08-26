<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include "config_notes.php";

$today = date('Y-m-d');
$stmt = $conn->prepare('SELECT * FROM notes_for_next_shift WHERE DATE(timestamp) = :today ORDER BY id DESC');
$stmt->bindParam(':today', $today);
$stmt->execute();
$notes_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <style>
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
    <title>Notes for Next Shift</title>
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
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h2 class="text-center">Add Note for Next Shift</h2>
                    </div>
                    <div class="card-body">
                        <form action="nfnsrun.php" method="post">
                            <div class="form-group">
                                <label for="notesfornextshift">Note:</label>
                                <textarea class="form-control" name="notesfornextshift" id="notesfornextshift" rows="4" required></textarea>
                            </div>
                            <input type="hidden" name="engineer" value="<?php echo htmlspecialchars($_SESSION['name']); ?>">
                            <button type="submit" class="btn btn-primary btn-block">Submit Note</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h3 class="text-center">Today's Notes</h3>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Timestamp</th>
                                <th>Notes</th>
                                <th>Engineer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes_data as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['timestamp'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['notesfornextshift'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['engineer'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>