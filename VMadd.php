<!DOCTYPE html>
<html>
<head>
    <title>Edit Room Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <h1>Edit Room Status</h1>

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
  
        // If form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Fetch form data
            $room_number = $_POST["room_number"];
            $status_vdvrvm = $_POST["status_vdvrvm"];
            $reason_vm = $_POST["reason_vm"];

            // Prepare an update statement
            $stmt = $conn->prepare("UPDATE Rooms SET status_vdvrvm = ?, reason_vm = ?, recent_update = CURRENT_TIMESTAMP WHERE room_number = ?");

            // Bind parameters
            $stmt->bind_param("ssi", $status_vdvrvm, $reason_vm, $room_number);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $stmt->error;
            }
        }

        // Fetch room numbers for select field
        $rooms = $conn->query("SELECT room_number FROM Rooms");
    ?>
   <div class="col bg-light mx-auto shadow rounded">
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
        <label for="room_number">Room Number:</label>
        <select id="room_number" name="room_number">
            <?php
                // Output data of each row
                if ($rooms->num_rows > 0) {
                    while($row = $rooms->fetch_assoc()) {
                        echo "<option value='" . $row["room_number"]. "'>" . $row["room_number"]. "</option>";
                    }
                } else {
                    echo "<option>No rooms found</option>";
                }
            ?>
        </select>

        <label for="status_vdvrvm">Status:</label>
        <select id="status_vdvrvm" name="status_vdvrvm">
            <option value="VR">VR</option>
            <option value="VC">VC</option>
            <option value="VM">VM</option>
        </select>

        <label for="reason_vm">Reason:</label>
        <input type="text" id="reason_vm" name="reason_vm">

        <input type="submit" value="Submit">
    </form>
	</div>
    <?php
        // Close the connection
        $conn->close();
    ?>

</body>
</html>
