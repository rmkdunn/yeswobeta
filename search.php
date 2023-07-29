<?php
$host = 'localhost'; // your host name
$db   = 'work_orders'; // your database name
$user = 'root'; // your database user
$pass = '1134206'; // your database password
$charset = 'utf8mb4';

$searchTerm = $_GET['search']; // get the search term from the form, we use $_GET since the form does not specify method, and the default is GET

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

$stmt = $pdo->prepare('SELECT * FROM orders WHERE room = ?'); // replace 'column_name' with the column you're searching
$stmt->execute([$searchTerm]);
echo '<table border="1">';
echo '<tr><th>Room</th><th>Work to be done</th><th>Completed</th><th>Time completed</th><th>Submitted</th></tr>'; // Add the header for your table here
while ($row = $stmt->fetch())
{
  
    echo '<tr>';
    echo '<td>' . $row['room'] . '</td>';
    echo '<td>' . $row['work_to_be_done'] . '</td>';
    echo '<td>' . $row['completed'] . '</td>';
    echo '<td>' . $row['time_completed'] . '</td>';
    echo '<td>' . $row['time'] . '</td>';
    //... and so on for each column
    echo '</tr>';
}
echo '</table>';
?>