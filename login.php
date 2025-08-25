<?php
include "config.php";
session_start();

if (isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ( !isset($_POST['username'], $_POST['password']) ) {
        exit('Please fill both the username and password fields!');
    }

    if ($stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ?')) {
        $stmt->bindParam(1, $_POST['username']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $user['id'];
            header('Location: index.php');
        } else {
            echo 'Incorrect username and/or password!';
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
		<title>Login</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
	</head>
	<body>
		<div class="container">
			<div class="row mt-5">
				<div class="col-md-6 offset-md-3">
					<h1 class="text-center">Login</h1>
					<form action="login.php" method="post">
						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" placeholder="Username" id="username" required class="form-control">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" placeholder="Password" id="password" required class="form-control">
						</div>
						<input type="submit" value="Login" class="btn btn-primary">
                        <a href="register.php" class="btn btn-secondary">Register</a>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>