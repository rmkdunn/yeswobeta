
<?php
include 'config.php';

?>

<!doctype html>
<html>
<head>        <meta charset="UTF-8" />
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
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Notes For Next Shift</title>
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
    <div class="row">
        <div class="col-12">
            <form action="/nfnsrun.php" method="post">
                <div class="form-group">
                    <label for="notesfornextshift">Notes for Next Shift:</label>
                    <textarea class="form-control" id="notesfornextshift"  name="notesfornextshift"></textarea>
                </div>

                <div class="form-group">
                    <label for="engineer">Engineer:</label>
                    <select class="form-control" id="engineer" name="engineer">
                        <option></option>
                        <option value="rogelio">Rogelio</option>
                        <option value="me">Randy</option>
                        <option value="kevin">Kevin</option>
                        <option value="sergio">Sergio</option>
                        <!-- add more options as needed -->
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>


		<table class="table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Timestamp</th>
					<th>Notes for Next Shift</th>
					<th>Engineer</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
$stmt = $conn->query('SELECT * FROM notes_for_next_shift');
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($data as $row): ?>
				<tr>
					<td><?php echo htmlspecialchars($row['id']); ?></td>
					<td><?php echo htmlspecialchars($row['timestamp']); ?></td>
					<td><?php echo htmlspecialchars($row['notesfornextshift']); ?></td>
					<td><?php echo htmlspecialchars($row['Engineer']); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>



</body>
</html>
