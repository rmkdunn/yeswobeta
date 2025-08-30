<?php
include 'header.php'; // Includes the new header with navbar and theme styles

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-center mb-4">Add a New Task</h2>
                    
                    <?php
                    // Display messages if any
                    if(isset($_SESSION['message'])) {
                        echo '<p class="alert alert-info">'.htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8').'</p>';
                        unset($_SESSION['message']);
                    }
                    ?>

                    <form action="addrun.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="room">Location/Room #</label>
                            <input type="number" class="form-control" name="room" id="room" required>
                        </div>
                        <div class="form-group">
                            <label for="work_to_be_done">Task</label>
                            <textarea class="form-control" name="work_to_be_done" id="work_to_be_done" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="photo">Upload Photo (Optional)</label>
                            <input type="file" class="form-control-file" name="photo" id="photo">
                        </div>
                        <div class="form-group">
                            <label for="submitted_by">Submitted By</label>
                            <input type="text" class="form-control" name="submitted_by" id="submitted_by" value="<?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Task</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; // Includes the new footer with global JavaScript ?>