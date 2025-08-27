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
    <style>
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .form-inline-centered {
            display: flex;
            justify-content: center;
        }
    </style>
    <title>Rooms On Call</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
        <a class="navbar-brand" href="#">DFWAM</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Orders
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
            <div class="col">
                <h2 class="text-center">Current Work Orders</h2>
                <h3 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>!</h3>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <?php
                if(isset($_SESSION['message'])) {
                    echo '<p class="alert alert-info">'.htmlspecialchars($_SESSION['message']).'</p>';
                    unset($_SESSION['message']);
                }
                if(isset($_SESSION['completed'])) {
                    echo '<p class="alert alert-success">'.htmlspecialchars($_SESSION['completed']).'</p>';
                    unset($_SESSION['completed']);
                }
                ?>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <div class="table-responsive shadow rounded">
                    <table class="table table-light table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Room</th>
                                <th>Work To Be Done</th>
                                <th>Submitted By</th>
                                <th>Time</th>
                                <th>Completed Time</th>
                                <th>Completed By</th>
                                <th class="text-center">Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "config.php";
                            date_default_timezone_set('America/Chicago');
                            $today = (date('H') >= 23) ? date('Y-m-d', strtotime('tomorrow')) : date('Y-m-d');

                            $query = $conn->prepare("SELECT * FROM `orders` WHERE DATE(time) = :today ORDER BY `id` DESC");
                            $query->bindParam(':today', $today);
                            $query->execute();
                            $data = $query->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($data as $row) {
                                $checkbox = $row['completed'] ? "checked" : "";
                                echo '<tr>
                                        <td>' . htmlspecialchars($row['id'] ?? '') . '</td>
                                        <td>' . htmlspecialchars($row['room'] ?? '') . '</td>
                                        <td>' . htmlspecialchars($row['work_to_be_done'] ?? '') . '</td>
                                        <td>' . htmlspecialchars($row['submitted_by'] ?? '') . '</td>
                                        <td>' . htmlspecialchars($row['time'] ?? '') . '</td>
                                        <td>' . htmlspecialchars($row['time_completed'] ?? '') . '</td>
                                        <td>' . htmlspecialchars($row['completed_by'] ?? '') . '</td>
                                        <td class="text-center">
                                            <form action="operate.php" method="post" id="form-' . $row['id'] . '" class="form-inline-centered">
                                                <input type="hidden" name="completed" value="' . $row['id'] . '">
                                                <input onclick="document.getElementById(\'form-' . $row['id'] . '\').submit()" type="checkbox" class="form-check-input" ' . $checkbox . '>
                                            </form>
                                        </td>
                                    </tr>';
                            }
                            ?>
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