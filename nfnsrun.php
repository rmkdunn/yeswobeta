<?php
session_start();

// If the user is not logged in, they should not be able to submit a note.
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';

// Check if the form was submitted and the notes field is not empty.
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['notesfornextshift'])) {

    // Get the note from the form submission.
    $notes = $_POST['notesfornextshift'];

    // Get the engineer's name from the current session.
    $engineer = $_SESSION['name'];

    // Prepare the SQL statement to prevent SQL injection.
    $stmt = $conn->prepare("INSERT INTO notes_for_next_shift (notesfornextshift, engineer) VALUES (?, ?)");

    // Execute the statement with the notes and engineer's name.
    $stmt->execute([$notes, $engineer]);

    // Redirect back to the notes page after submission.
    header("Location: nfns.php");
    exit;

} else {
    // If the form was submitted without notes, or accessed directly, just redirect back.
    header("Location: nfns.php");
    exit;
}
?>