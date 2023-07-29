<?php
include "config.php";
if (isset($_POST['room']) && isset($_POST['work_to_be_done'])) {
	//variables
	$room = $_POST['room'];
	$work_to_be_done = $_POST['work_to_be_done'];
	if (!$conn->connect_error) {
		$query = "INSERT INTO `orders`(`room`, `work_to_be_done`) VALUES ('" . $room . "', '" . $work_to_be_done . "')";
		$conn->exec($query);
		session_start();
		$_SESSION['message'] = "<b>Success!</b> Note Created Successfully!</p> ";
		return header('location: index.php');
	}
}

// mark as completed
if (isset($_POST['completed'])) {
	date_default_timezone_set('America/Chicago'); // Set the timezone to Central Standard Time (CST)
	$id = $_POST['completed'];
	$query = $conn->prepare("SELECT * FROM `orders` WHERE id =" . $id);
	$query->execute();
	$query->setFetchMode(PDO::FETCH_ASSOC);
	$data = $query->fetchAll();
	session_start();
	if ($data[0]["completed"] == true) {
		$query = "UPDATE `orders` SET completed='" . false . "', time_completed='" . "" . "' WHERE id =" . $id;
		$conn->exec($query) or die("Something went wrong!");
		$_SESSION['completed'] = "<b>Success!</b> Note Marked as Uncompleted Successfully.</p> ";
	} else {
		$query = "UPDATE `orders` SET completed='" . true . "', time_completed='" . date('Y-m-d H:i:s') . "' WHERE id =" . $id;
		$conn->exec($query) or die("Something went wrong!");
		$_SESSION['completed'] = "<b>Success!</b> Note Marked as Completed Successfully.</p> ";
	}
	return header('location: index.php');
}
?>
