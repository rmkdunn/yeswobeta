<?php
include 'header.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

include "config.php";

$data = [];
$search_performed = !empty($_GET);

if ($search_performed) {
    // Base query
    $query_sql = "SELECT * FROM `orders` WHERE 1=1";
    $params = [];

    // Add conditions based on search parameters
    if (!empty($_GET['search'])) { // General search from navbar
        $searchTerm = '%' . $_GET['search'] . '%';
        $query_sql .= " AND (room LIKE :search OR work_to_be_done LIKE :search OR submitted_by LIKE :search OR completed_by LIKE :search)";
        $params[':search'] = $searchTerm;
    } else { // Specific search from form
        if (!empty($_GET['room'])) {
            $query_sql .= " AND room = :room";
            $params[':room'] = $_GET['room'];
        }
        if (!empty($_GET['work_to_be_done'])) {
            $query_sql .= " AND work_to_be_done LIKE :work_to_be_done";
            $params[':work_to_be_done'] = '%' . $_GET['work_to_be_done'] . '%';
        }
        if (!empty($_GET['completed_by'])) {
            $query_sql .= " AND completed_by LIKE :completed_by";
            $params[':completed_by'] = '%' . $_GET['completed_by'] . '%';
        }
        if (!empty($_GET['time'])) {
            $query_sql .= " AND DATE(time) = :time";
            $params[':time'] = $_GET['time'];
        }
    }


    $query_sql .= " ORDER BY `id` DESC";

    $query = $conn->prepare($query_sql);
    $query->execute($params);
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col">
            <h2 class="text-center">Search Work Orders</h2>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Advanced Search</h5>
                    <form method="GET" action="search.php">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="room">Room #</label>
                                <input type="text" class="form-control" id="room" name="room" value="<?php echo htmlspecialchars($_GET['room'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="work_to_be_done">Work To Be Done</label>
                                <input type="text" class="form-control" id="work_to_be_done" name="work_to_be_done" value="<?php echo htmlspecialchars($_GET['work_to_be_done'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="completed_by">Completed By</label>
                                <input type="text" class="form-control" id="completed_by" name="completed_by" value="<?php echo htmlspecialchars($_GET['completed_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="time">Date Submitted</label>
                                <input type="date" class="form-control" id="time" name="time" value="<?php echo htmlspecialchars($_GET['time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="/search.php" class="btn btn-secondary">Clear</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($search_performed): ?>
    <div class="row mt-4">
        <div class="col">
            <h3 class="text-center">Search Results</h3>
            <?php if (count($data) > 0): ?>
            <div class="table-responsive shadow rounded">
                <table class="table table-light table-striped table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Location</th>
                            <th>Task</th>
                            <th>Photo</th>
                            <th>Submitted By</th>
                            <th>Time</th>
                            <th>Completed</th>
                            <th>Completed Time</th>
                            <th>Completed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <?php
                            $id = htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8');
                            $room = htmlspecialchars($row['room'] ?? '', ENT_QUOTES, 'UTF-8');
                            $work_to_be_done = htmlspecialchars($row['work_to_be_done'] ?? '', ENT_QUOTES, 'UTF-8');
                            $photo = htmlspecialchars($row['photo'] ?? '', ENT_QUOTES, 'UTF-8');
                            $submitted_by = htmlspecialchars($row['submitted_by'] ?? '', ENT_QUOTES, 'UTF-8');
                            $time = htmlspecialchars($row['time'] ?? '', ENT_QUOTES, 'UTF-8');
                            $completed = $row['completed'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                            $time_completed = htmlspecialchars($row['time_completed'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                            $completed_by = htmlspecialchars($row['completed_by'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $room; ?></td>
                                <td><?php echo $work_to_be_done; ?></td>
                                <td>
                                    <?php if ($photo): ?>
                                        <img src='<?php echo $photo; ?>' alt='Work Order Photo' class='work-order-photo' data-toggle='modal' data-target='#photoModal' data-src='<?php echo $photo; ?>'>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $submitted_by; ?></td>
                                <td><?php echo $time; ?></td>
                                <td class="text-center"><?php echo $completed; ?></td>
                                <td><?php echo $time_completed; ?></td>
                                <td><?php echo $completed_by; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="alert alert-warning text-center mt-3">No work orders found matching your criteria.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" id="modalImage" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
    $('#photoModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var src = button.data('src');
        var modal = $(this);
        modal.find('#modalImage').attr('src', src);
    });
</script>

<?php include 'footer.php'; ?>