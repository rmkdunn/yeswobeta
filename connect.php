<?php 
 // Connects to Our Database 
$link = new mysqli('localhost', 'root', '1134206', 'work_orders');

/* check connection */
if (mysqli_connect_errno()) {
    echo "Could not connect to database, please check your connection details";
    exit();
}

 ?> 