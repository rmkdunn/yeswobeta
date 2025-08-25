<?php
session_start();

// If the user is not logged in, redirect to the login page.
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Notes For Next Shift</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
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
        <div class="col-12">
            <h2>Notes for Next Shift</h2>
            <form action="nfnsrun.php" method="post">
                <div class="form-group">
                    <label for="notesfornextshift">Enter your notes below:</label>
                    <textarea class="form-control" id="notesfornextshift"  name="notesfornextshift" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Note</button>
            </form>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-12">
            <h3>Previous Notes</h3>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>Notes</th>
                        <th>Engineer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all notes from the database and display them
                    $stmt = $conn->query('SELECT * FROM notes_for_next_shift ORDER BY id DESC');
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($data as $row):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                        <td><?php echo htmlspecialchars($row['notesfornextshift']); ?></td>
                        <td><?php echo htmlspecialchars($row['engineer']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>