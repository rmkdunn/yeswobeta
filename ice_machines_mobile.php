<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: auth/login.php');
    exit;
}

// Clear force_desktop when explicitly accessing mobile
unset($_SESSION['force_desktop']);

include 'config/config.php';

// Handle form submissions for adding/updating ice machines
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                addIceMachine($conn);
                break;
            case 'update_status':
                updateIceMachineStatus($conn);
                break;
            case 'update':
                updateIceMachine($conn);
                break;
            case 'delete':
                deleteIceMachine($conn);
                break;
        }
    }
}

// Functions for CRUD operations (simplified for mobile)
function addIceMachine($conn) {
    $location = $_POST['location'];
    $brand = $_POST['brand'] ?? null;
    $model = $_POST['model'] ?? null;
    $status = $_POST['status'];
    $ice_production_capacity = $_POST['ice_production_capacity'] ?? null;
    $notes = $_POST['notes'] ?? null;
    $created_by = $_SESSION['name'] ?? $_SESSION['username'];
    $photo_path = null;

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $unique_filename = time() . '.' . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $target_dir . $unique_filename;

        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false && in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_path = "uploads/" . $unique_filename;
            }
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO ice_machines (location, brand, model, status, ice_production_capacity, notes, photo, created_by) VALUES (:location, :brand, :model, :status, :ice_production_capacity, :notes, :photo, :created_by)");
        $stmt->execute([
            ':location' => $location,
            ':brand' => $brand,
            ':model' => $model,
            ':status' => $status,
            ':ice_production_capacity' => $ice_production_capacity,
            ':notes' => $notes,
            ':photo' => $photo_path,
            ':created_by' => $created_by
        ]);
        $_SESSION['message'] = "Ice machine added successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

function updateIceMachineStatus($conn) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    try {
        $stmt = $conn->prepare("UPDATE ice_machines SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
        $_SESSION['message'] = "Status updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

function updateIceMachine($conn) {
    $id = $_POST['id'];
    $location = $_POST['location'];
    $brand = $_POST['brand'] ?? null;
    $model = $_POST['model'] ?? null;
    $status = $_POST['status'];
    $ice_production_capacity = $_POST['ice_production_capacity'] ?? null;
    $notes = $_POST['notes'] ?? null;

    try {
        $stmt = $conn->prepare("UPDATE ice_machines SET location = :location, brand = :brand, model = :model, status = :status, ice_production_capacity = :ice_production_capacity, notes = :notes WHERE id = :id");
        $stmt->execute([
            ':location' => $location,
            ':brand' => $brand,
            ':model' => $model,
            ':status' => $status,
            ':ice_production_capacity' => $ice_production_capacity,
            ':notes' => $notes,
            ':id' => $id
        ]);
        $_SESSION['message'] = "Ice machine updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

function deleteIceMachine($conn) {
    $id = $_POST['id'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM ice_machines WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['message'] = "Ice machine deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

// Fetch all ice machines
try {
    $stmt = $conn->prepare("SELECT * FROM ice_machines ORDER BY location ASC");
    $stmt->execute();
    $ice_machines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['message'] = "Error fetching ice machines: " . $e->getMessage();
    $ice_machines = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Ice Machines - Mobile</title>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Mobile-first responsive design */
        body {
            font-size: 16px;
            background-color: #f8f9fa;
            padding-bottom: 80px; /* Space for bottom nav */
        }

        /* Header styling */
        .mobile-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .mobile-header h1 {
            font-size: 1.5rem;
            margin: 0;
            text-align: center;
        }

        .welcome-user {
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Card-based layout for ice machines */
        .ice-machine-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .ice-machine-card:active {
            transform: scale(0.98);
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }

        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0 !important;
            padding: 12px 15px;
        }

        .machine-location {
            font-weight: bold;
            color: #007bff;
            font-size: 1.1rem;
        }

        .machine-brand {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 2px;
        }

        .card-body {
            padding: 15px;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            margin-bottom: 10px;
            display: inline-block;
        }

        .status-operational { background: #d4edda; color: #155724; }
        .status-needs_service { background: #fff3cd; color: #856404; }
        .status-out_of_order { background: #f8d7da; color: #721c24; }
        .status-scheduled_maintenance { background: #d1ecf1; color: #0c5460; }

        /* Photo thumbnail styling */
        .photo-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .photo-thumbnail {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-mobile {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            transition: all 0.2s;
        }

        .btn-mobile:active {
            transform: scale(0.95);
        }

        .btn-status {
            background: #ffc107;
            color: #212529;
        }

        .btn-edit {
            background: #007bff;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        /* Bottom navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e9ecef;
            padding: 10px 0;
            z-index: 1000;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .nav-item {
            text-align: center;
            color: #6c757d;
            text-decoration: none;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
            margin: 0 5px;
        }

        .nav-item:hover, .nav-item.active {
            background: #007bff;
            color: white;
            text-decoration: none;
        }

        .nav-item i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 2px;
        }

        .nav-item span {
            font-size: 0.7rem;
            display: block;
        }

        /* Add button */
        .add-button {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 999;
            transition: all 0.2s;
        }

        .add-button:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        .add-button:active {
            transform: scale(0.9);
        }

        /* Modal adjustments for mobile */
        .modal-content {
            margin: 20px;
            border-radius: 12px;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
        }

        /* Quick status update */
        .status-selector {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }

        .status-btn {
            flex: 1;
            padding: 8px 4px;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .status-btn.active {
            border-color: #007bff;
            background: #007bff;
            color: white;
        }

        /* Service date warnings */
        .service-warning {
            background: #fff3cd;
            color: #856404;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-top: 10px;
        }

        .service-overdue {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <h1><i class="fas fa-snowflake"></i> Ice Machines</h1>
        <div class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>!</div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-3 mt-3">
        <?php
        // Display messages if any
        if(isset($_SESSION['message'])) {
            echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8');
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button></div>';
            unset($_SESSION['message']);
        }
        ?>

        <!-- Ice Machines Cards -->
        <?php if (empty($ice_machines)): ?>
            <div class="text-center mt-5">
                <i class="fas fa-snowflake text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">No ice machines found</h5>
                <p class="text-muted">Tap the + button to add your first ice machine</p>
            </div>
        <?php else: ?>
            <?php foreach ($ice_machines as $machine): ?>
                <div class="ice-machine-card">
                    <div class="card-header">
                        <div class="machine-location"><?php echo htmlspecialchars($machine['location'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="machine-brand">
                            <?php 
                            $brand_model = trim($machine['brand'] . ' ' . $machine['model']);
                            echo htmlspecialchars($brand_model ?: 'Unknown Brand/Model', ENT_QUOTES, 'UTF-8'); 
                            ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <span class="status-badge status-<?php echo $machine['status']; ?>">
                            <?php echo ucwords(str_replace('_', ' ', $machine['status'])); ?>
                        </span>

                        <?php if ($machine['photo']): ?>
                            <div class="photo-container">
                                <img src="<?php echo htmlspecialchars($machine['photo'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="photo-thumbnail" 
                                     onclick="showPhotoModal('<?php echo htmlspecialchars($machine['photo'], ENT_QUOTES, 'UTF-8'); ?>')"
                                     alt="Ice Machine Photo">
                            </div>
                        <?php endif; ?>

                        <?php if ($machine['ice_production_capacity']): ?>
                            <div class="mb-2">
                                <strong>Capacity:</strong> <?php echo htmlspecialchars($machine['ice_production_capacity'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($machine['next_service_due']): ?>
                            <?php 
                            $due_date = strtotime($machine['next_service_due']);
                            $days_until = ceil(($due_date - time()) / (60*60*24));
                            $warning_class = $days_until < 0 ? 'service-overdue' : ($days_until <= 7 ? 'service-warning' : '');
                            if ($warning_class):
                            ?>
                                <div class="service-warning <?php echo $warning_class; ?>">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php 
                                    if ($days_until < 0) {
                                        echo "Service overdue by " . abs($days_until) . " days";
                                    } else if ($days_until <= 7) {
                                        echo "Service due in $days_until days";
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($machine['notes']): ?>
                            <div class="mt-2">
                                <strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($machine['notes'], ENT_QUOTES, 'UTF-8')); ?>
                            </div>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <button type="button" class="btn-mobile btn-status" onclick="showStatusModal(<?php echo $machine['id']; ?>, '<?php echo $machine['status']; ?>')">
                                <i class="fas fa-toggle-on"></i> Status
                            </button>
                            <button type="button" class="btn-mobile btn-edit" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($machine), ENT_QUOTES, 'UTF-8'); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn-mobile btn-delete" onclick="deleteMachine(<?php echo $machine['id']; ?>, '<?php echo addslashes($machine['location']); ?>')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Add Button -->
    <button class="add-button" onclick="$('#addMachineModal').modal('show')">
        <i class="fas fa-plus"></i>
    </button>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="d-flex">
            <a href="mobile.php" class="nav-item flex-fill">
                <i class="fas fa-tasks"></i>
                <span>Tasks</span>
            </a>
            <a href="ice_machines_mobile.php" class="nav-item flex-fill active">
                <i class="fas fa-snowflake"></i>
                <span>Ice Machines</span>
            </a>
            <a href="pages/add_mobile.php" class="nav-item flex-fill">
                <i class="fas fa-plus-circle"></i>
                <span>Add Task</span>
            </a>
            <a href="index.php?desktop=1" class="nav-item flex-fill">
                <i class="fas fa-desktop"></i>
                <span>Desktop</span>
            </a>
        </div>
    </div>

    <!-- Add Machine Modal -->
    <div class="modal fade" id="addMachineModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Ice Machine</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" class="form-control" name="location" required placeholder="e.g., Kitchen - Main Floor">
                        </div>
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" name="brand" placeholder="e.g., Hoshizaki">
                        </div>
                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" name="model" placeholder="e.g., KM-515MAH">
                        </div>
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select class="form-control" name="status" required>
                                <option value="operational">Operational</option>
                                <option value="needs_service">Needs Service</option>
                                <option value="out_of_order">Out of Order</option>
                                <option value="scheduled_maintenance">Scheduled Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ice_production_capacity">Capacity</label>
                            <input type="text" class="form-control" name="ice_production_capacity" placeholder="e.g., 515 lbs/day">
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <input type="file" class="form-control-file" name="photo" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Machine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" id="statusForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" id="status_machine_id">
                        <div class="status-selector">
                            <label class="status-btn" onclick="selectStatus('operational')">
                                <input type="radio" name="status" value="operational" style="display:none">
                                <div style="font-size: 0.7rem;">Operational</div>
                            </label>
                            <label class="status-btn" onclick="selectStatus('needs_service')">
                                <input type="radio" name="status" value="needs_service" style="display:none">
                                <div style="font-size: 0.7rem;">Needs Service</div>
                            </label>
                            <label class="status-btn" onclick="selectStatus('out_of_order')">
                                <input type="radio" name="status" value="out_of_order" style="display:none">
                                <div style="font-size: 0.7rem;">Out of Order</div>
                            </label>
                            <label class="status-btn" onclick="selectStatus('scheduled_maintenance')">
                                <input type="radio" name="status" value="scheduled_maintenance" style="display:none">
                                <div style="font-size: 0.7rem;">Maintenance</div>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Machine Modal -->
    <div class="modal fade" id="editMachineModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Ice Machine</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" id="editMachineForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_machine_id">
                        <div class="form-group">
                            <label for="edit_location">Location *</label>
                            <input type="text" class="form-control" name="location" id="edit_location" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_brand">Brand</label>
                            <input type="text" class="form-control" name="brand" id="edit_brand">
                        </div>
                        <div class="form-group">
                            <label for="edit_model">Model</label>
                            <input type="text" class="form-control" name="model" id="edit_model">
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status *</label>
                            <select class="form-control" name="status" id="edit_status" required>
                                <option value="operational">Operational</option>
                                <option value="needs_service">Needs Service</option>
                                <option value="out_of_order">Out of Order</option>
                                <option value="scheduled_maintenance">Scheduled Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_capacity">Capacity</label>
                            <input type="text" class="form-control" name="ice_production_capacity" id="edit_capacity">
                        </div>
                        <div class="form-group">
                            <label for="edit_notes">Notes</label>
                            <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Machine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ice Machine Photo</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" class="img-fluid" alt="Ice Machine Photo">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        function showStatusModal(id, currentStatus) {
            document.getElementById('status_machine_id').value = id;
            
            // Reset all status buttons
            const buttons = document.querySelectorAll('.status-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Mark current status as active
            selectStatus(currentStatus);
            
            $('#statusModal').modal('show');
        }

        function selectStatus(status) {
            // Reset all buttons
            const buttons = document.querySelectorAll('.status-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Set the radio button value
            const radio = document.querySelector(`input[value="${status}"]`);
            if (radio) {
                radio.checked = true;
                radio.parentElement.classList.add('active');
            }
        }

        function showEditModal(machine) {
            document.getElementById('edit_machine_id').value = machine.id;
            document.getElementById('edit_location').value = machine.location || '';
            document.getElementById('edit_brand').value = machine.brand || '';
            document.getElementById('edit_model').value = machine.model || '';
            document.getElementById('edit_status').value = machine.status;
            document.getElementById('edit_capacity').value = machine.ice_production_capacity || '';
            document.getElementById('edit_notes').value = machine.notes || '';
            
            $('#editMachineModal').modal('show');
        }

        function deleteMachine(id, location) {
            if (confirm('Are you sure you want to delete the ice machine at "' + location + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showPhotoModal(photoSrc) {
            document.getElementById('modalPhoto').src = photoSrc;
            $('#photoModal').modal('show');
        }
    </script>
</body>
</html>