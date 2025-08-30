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
        .work-order-photo {
            max-width: 150px;
            max-height: 150px;
            cursor: pointer;
        }
    </style>
    <title>Engineering Task</title>
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
                        <a class="dropdown-item" href="/add.php">Add Task</a>
                        <a class="dropdown-item" href="/search.php">Search Orders</a>
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
                <h2 class="text-center">Current Task</h2>
                <h3 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>!</h3>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <?php
                if(isset($_SESSION['message'])) {
                    echo '<p class="alert alert-info">'.htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8').'</p>';
                    unset($_SESSION['message']);
                }
                if(isset($_SESSION['completed'])) {
                    echo '<p class="alert alert-success">'.htmlspecialchars($_SESSION['completed'], ENT_QUOTES, 'UTF-8').'</p>';
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
                                <th>Location</th>
                                <th>Task</th>
                                <th>Photo</th>
                                <th>Submitted By</th>
                                <th>Time</th>
                                <th class="text-center">Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "config.php";
                            
                            $query = $conn->prepare("SELECT * FROM `orders` WHERE completed = 0 ORDER BY `id` DESC");
                            $query->execute();
                            $data = $query->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($data as $row) {
                                $checkbox = $row['completed'] ? "checked" : "";
                                $id = htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8');
                                $room = htmlspecialchars($row['room'] ?? '', ENT_QUOTES, 'UTF-8');
                                $work_to_be_done = htmlspecialchars($row['work_to_be_done'] ?? '', ENT_QUOTES, 'UTF-8');
                                $photo = htmlspecialchars($row['photo'] ?? '', ENT_QUOTES, 'UTF-8');
                                $submitted_by = htmlspecialchars($row['submitted_by'] ?? '', ENT_QUOTES, 'UTF-8');
                                $time = htmlspecialchars($row['time'] ?? '', ENT_QUOTES, 'UTF-8');

                                echo "
                                <tr>
                                    <td>{$id}</td>
                                    <td>{$room}</td>
                                    <td>{$work_to_be_done}</td>
                                    <td>";
                                if ($photo) {
                                    echo "<img src='{$photo}' alt='Work Order Photo' class='work-order-photo' data-toggle='modal' data-target='#photoModal' data-src='{$photo}'>";
                                }
                                echo "</td>
                                    <td>{$submitted_by}</td>
                                    <td>{$time}</td>
                                    <td class='text-center'>
                                        <form action='operate.php' method='post' class='d-flex justify-content-center'>
                                            <input type='hidden' name='completed' value='{$id}'>
                                            <div class='form-check'>
                                                <input onclick='this.form.submit()' type='checkbox' class='form-check-input' {$checkbox}>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                ";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="" id="modalImage" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $('#photoModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var src = button.data('src');
            var modal = $(this);
            modal.find('#modalImage').attr('src', src);
        });
    </script>
</body>
</html>