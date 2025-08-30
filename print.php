<?php
include 'header.php'; // Includes the new header with navbar and theme styles

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include "config.php";

// Fetch today's completed orders
$today = date("Y-m-d");
$query = $conn->prepare("SELECT * FROM `orders` WHERE completed = 1 AND DATE(time_completed) = :today ORDER BY `time_completed` DESC");
$query->execute([':today' => $today]);
$completed_orders = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="text-center mb-4">
        <h2>Daily Engineering Report</h2>
        <h4><?php echo date("l, F j, Y"); ?></h4>
        <button onclick="window.print()" class="btn btn-primary d-print-none">Print Report</button>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Completed Work Orders</h3>
        </div>
        <div class="card-body">
            <?php if (count($completed_orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Location</th>
                                <th>Task</th>
                                <th>Completed By</th>
                                <th>Time Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completed_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['room']); ?></td>
                                    <td><?php echo htmlspecialchars($order['work_to_be_done']); ?></td>
                                    <td><?php echo htmlspecialchars($order['completed_by']); ?></td>
                                    <td><?php echo htmlspecialchars(date('h:i A', strtotime($order['time_completed']))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No work orders were completed today.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; // Includes the new footer with global JavaScript ?>