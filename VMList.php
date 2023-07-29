<!DOCTYPE html>
<html>
<head>
    <title>View Room Status</title>
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
</head>
<body>
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
    <h1>View Room Status</h1>

    <?php
        // Database credentials
        $servername = "localhost";
        $username = "root";
        $password = "1134206";
        $dbname = "dfwam_vm";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        date_default_timezone_set('America/Chicago'); // set timezone to Eastern Time
        // Fetch all data from Rooms table
        $result = $conn->query("SELECT * FROM Rooms WHERE status_vdvrvm = 'VM'");


        // Check if there are any results
        if ($result->num_rows > 0) {
            // Start a table
            echo "<table>";
            echo "<tr><th>Room Number</th><th>Status</th><th>Reason</th><th>Recent Update</th></tr>";

            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["room_number"] . "</td><td>" . $row["status_vdvrvm"] . "</td><td>" . $row["reason_vm"] . "</td><td>" . $row["recent_update"] . "</td></tr>";
            }

            // End the table
            echo "</table>";
        } else {
            echo "No results found";
        }

        // Close the connection
        $conn->close();
    ?>

</body>
</html>
