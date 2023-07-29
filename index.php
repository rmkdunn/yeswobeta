<?php
include "config.php";
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <link rel="icon" href="/favicon.ico" type="image/x-icon" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
        <!-- JavaScript -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
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
            <a class="nav-link" href="/index.php">Home<span class="sr-only">(current)</span></a>
         </li>
 		<li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           Room Status
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
               <a class="dropdown-item" href="/VMList.php">VM</a>
               <a class="dropdown-item" href="#">VR</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="/VMadd.php">Update Status</a>
            </div>
         </li>
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Orders
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
               <a class="dropdown-item" href="/index.php">Current Work Orders</a>
               <a class="dropdown-item" href="/add.php">Add Work Order</a>
               <div class="dropdown-divider">
            </div>
				<a class="dropdown-item" href="/nfns.php">Notes for Techs</a></div>
         </li>
		           <li class="nav-item active">
					   <a class="nav-link" href="/print.php">Print Daily Report</a>
            
         </li>
      </ul>
      <form class="form-inline my-2 my-lg-0">
         <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
         <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
      </form>
   </div>
</nav>

        <div class="container">
            <div class="row mt-2">
                <div class="col">
                    <h2 class="py-2 text-center ">Current work orders</h2>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php
                    session_start();
                    if(isset($_SESSION['message'])) {
                        $message = $_SESSION['message'];
                        unset($_SESSION['message']);
                        echo '<p class="alert alert-info">'.$message.'</p>';
                    }
                    if(isset($_SESSION['completed'])) {
                        $completed = $_SESSION['completed'];
                        unset($_SESSION['completed']);
                        echo '<p class="alert alert-success">'.$completed.'</p>';
                    }
                    ?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-light table-striped table-hover shadow rounded">
                            <thead class="thead-dark" style="width: 100% !important">
                                <tr>
                                    <th>#</th>
                                    <th>Room No</th>
                                    <th>Work to be done</th>
                                    <th>Time</th>
                                    <th>Completed Time</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            <?php
                            date_default_timezone_set('America/Chicago'); // set timezone to Eastern Time
                            include "config.php";
    $today = date('Y-m-d');
    $query = $conn->prepare("SELECT * FROM `orders` WHERE DATE(time) = :today ORDER BY `id` desc");
    $query->bindParam(':today', $today);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $data = $query->fetchAll();

    foreach ($data as $row) {
        $checkbox = $row['completed'] ? "checked" : "";
        echo '<tr>
                <td class="text-center">' . $row['id'] . '</td>
                <td class="text-left">' . $row['room'] . '</td>
                <td class="text-left">' . $row['work_to_be_done'] . '</td>
                <td class="text-left">' . $row['time'] . '</td>
                <td class="text-left">' . $row['time_completed'] . '</td>
                <td class="text-center form-inline">
                    <form action="operate.php" method="post" id="' . $row['id'] . '" class="">
                        <input type="hidden" name="completed"  value="' . $row['id'] . '">
                        <input onclick="document.getElementById(' . $row['id'] . ').submit()" type="checkbox" class="" ' . $checkbox . '>
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
    </body>

    </html>