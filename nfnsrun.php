<?php

include 'config.php';

// Get the posted data
$notesfornextshift = $_POST['notesfornextshift'];
$engineer = $_POST['engineer'];

// Insert the data into the database
$sql = "INSERT INTO notes_for_next_shift (notesfornextshift, Engineer) VALUES (?, ?)";
$stmt= $conn->prepare($sql);
$stmt->execute([$notesfornextshift, $engineer]);

// Redirect back to the form page
header("Location: /nfns.php");
exit;
?>

