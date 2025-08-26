<?php
session_start();
include "config.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $message = 'Please fill both the username and password fields!';
    } else {
        $stmt = $conn->prepare('SELECT id, password FROM accounts WHERE username = ?');
        $stmt->execute([$_POST['username']]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($account && password_verify($_POST['password'], $account['password'])) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $account['id'];
            header('Location: index.php');
            exit;
        } else {
            $message = 'Incorrect username and/or password!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h1 class="text-center mb-4">DFWAM Login</h1>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>