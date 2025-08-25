<?php
include "config.php";
session_start();

if (isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //
    if ( !isset($_POST['username'], $_POST['password'], $_POST['email']) ) {
        exit('Please fill both the username and password fields!');
    }
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
        exit('Please complete the registration form');
    }
    if ($stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ?')) {
        $stmt->bindParam(1, $_POST['username']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($user) {
            echo 'Username exists, please choose another!';
        } else {
            if ($stmt = $conn->prepare('INSERT INTO users (username, password, email) VALUES (?, ?, ?)')) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bindParam(1, $_POST['username']);
                $stmt->bindParam(2, $password);
                $stmt->bindParam(3, $_POST['email']);
                $stmt->execute();
                echo 'You have successfully registered, you can now login!';
                header('Location: login.php');
            } else {
                echo 'Could not prepare statement!';
            }
        }
    } else {
        echo 'Could not prepare statement!';
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Register</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	</head>
	<body>
		<div class="container">
			<div class="row mt-5">
				<div class="col-md-6 offset-md-3">
					<h1 class="text-center">Register</h1>
					<form action="register.php" method="post" autocomplete="off">
						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" placeholder="Username" id="username" required class="form-control">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" placeholder="Password" id="password" required class="form-control">
						</div>
						<div class="form-group">
							<label for="email">Email</label>
							<input type="email" name="email" placeholder="Email" id="email" required class="form-control">
						</div>
						<input type="submit" value="Register" class="btn btn-primary">
					</form>
				</div>
			</div>
		</div>
	</body>
</html>