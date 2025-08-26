<?php
session_start();
include "config.php";

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['completed'])) {
    $order_id = $_POST['completed'];

    // First, get the current status of the order
    $query = $conn->prepare("SELECT completed FROM `orders` WHERE `id` = :order_id");
    $query->bindParam(':order_id', $order_id);
    $query->execute();
    $order = $query->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $new_status = $order['completed'] ? 0 : 1;
        $completed_by = $new_status ? $_SESSION['name'] : null;
        $time_completed = $new_status ? date('Y-m-d H:i:s') : null;

        $update = $conn->prepare("UPDATE `orders` SET `completed` = :new_status, `time_completed` = :time_completed, `completed_by` = :completed_by WHERE `id` = :order_id");
        $update->bindParam(':new_status', $new_status);
        $update->bindParam(':time_completed', $time_completed);
        $update->bindParam(':completed_by', $completed_by);
        $update->bindParam(':order_id', $order_id);
        
        if ($update->execute()) {
            $_SESSION['completed'] = "Order #$order_id status has been updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update order status.";
        }
    } else {
        $_SESSION['message'] = "Order not found.";
    }
    header('Location: index.php');
    exit;
}

// Redirect back if the POST variable is not set
header('Location: index.php');
exit;