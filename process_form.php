<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = "localhost";
    $username = "root";
    $password = "1134206";
    $dbname = "work_orders";
    
    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO pool_data (temperature, chlorine, free_chlorine, total_chlorine, ph, orp, machine_ph, total_alk, bather_load, safety_equipment, other) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $temperature, $chlorine, $free_chlorine, $total_chlorine, $ph, $orp, $machine_ph, $total_alk, $bather_load, $safety_equipment, $other);

    // Set parameters and execute
    $temperature = $_POST['temperature'];
    $chlorine = $_POST['chlorine'];
    $free_chlorine = $_POST['free_chlorine'];
    $total_chlorine = $_POST['total_chlorine'];
    $ph = $_POST['ph'];
    $orp = $_POST['orp'];
    $machine_ph = $_POST['machine_ph'];
    $total_alk = $_POST['total_alk'];
    $bather_load = $_POST['bather_load'];
    $safety_equipment = isset($_POST['safety_equipment']) ? 1 : 0; // if checkbox is checked set 1 otherwise 0
    $other = $_POST['other'];
    $stmt->execute();
    
    $stmt->close();
    $conn->close();

    // Redirect to index.html
    header("Location: /checklist.php");
    exit();
} else {
    echo "Invalid request";
}
?>
