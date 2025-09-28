<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: auth/login.php');
    exit;
}

// Mobile detection function
function isMobile() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $user_agent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4));
}

// Handle desktop version request
if (isset($_GET['desktop'])) {
    $_SESSION['force_desktop'] = true;
}

// Auto-redirect mobile users to mobile version (unless they chose desktop)
if (isMobile() && !isset($_GET['desktop']) && !isset($_SESSION['force_desktop'])) {
    // Redirect to mobile version
    header('Location: ice_machines_mobile.php');
    exit;
}

include 'includes/header.php';
include 'config/config.php';

// Handle form submissions for adding/updating ice machines
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                addIceMachine($conn);
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

// Functions for CRUD operations
function addIceMachine($conn) {
    $location = $_POST['location'];
    $brand = $_POST['brand'] ?? null;
    $model = $_POST['model'] ?? null;
    $serial_number = $_POST['serial_number'] ?? null;
    $installation_date = $_POST['installation_date'] ? $_POST['installation_date'] : null;
    $last_service_date = $_POST['last_service_date'] ? $_POST['last_service_date'] : null;
    $next_service_due = $_POST['next_service_due'] ? $_POST['next_service_due'] : null;
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
        $stmt = $conn->prepare("INSERT INTO ice_machines (location, brand, model, serial_number, installation_date, last_service_date, next_service_due, status, ice_production_capacity, notes, photo, created_by) VALUES (:location, :brand, :model, :serial_number, :installation_date, :last_service_date, :next_service_due, :status, :ice_production_capacity, :notes, :photo, :created_by)");
        $stmt->execute([
            ':location' => $location,
            ':brand' => $brand,
            ':model' => $model,
            ':serial_number' => $serial_number,
            ':installation_date' => $installation_date,
            ':last_service_date' => $last_service_date,
            ':next_service_due' => $next_service_due,
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

function updateIceMachine($conn) {
    $id = $_POST['id'];
    $location = $_POST['location'];
    $brand = $_POST['brand'] ?? null;
    $model = $_POST['model'] ?? null;
    $serial_number = $_POST['serial_number'] ?? null;
    $installation_date = $_POST['installation_date'] ? $_POST['installation_date'] : null;
    $last_service_date = $_POST['last_service_date'] ? $_POST['last_service_date'] : null;
    $next_service_due = $_POST['next_service_due'] ? $_POST['next_service_due'] : null;
    $status = $_POST['status'];
    $ice_production_capacity = $_POST['ice_production_capacity'] ?? null;
    $notes = $_POST['notes'] ?? null;

    try {
        $stmt = $conn->prepare("UPDATE ice_machines SET location = :location, brand = :brand, model = :model, serial_number = :serial_number, installation_date = :installation_date, last_service_date = :last_service_date, next_service_due = :next_service_due, status = :status, ice_production_capacity = :ice_production_capacity, notes = :notes WHERE id = :id");
        $stmt->execute([
            ':location' => $location,
            ':brand' => $brand,
            ':model' => $model,
            ':serial_number' => $serial_number,
            ':installation_date' => $installation_date,
            ':last_service_date' => $last_service_date,
            ':next_service_due' => $next_service_due,
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

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            <i class="fas fa-snowflake text-info"></i> Ice Machine Management
                        </h2>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addIceMachineModal">
                            <i class="fas fa-plus"></i> Add Ice Machine
                        </button>
                    </div>
                    
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

                    <!-- Ice Machines Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Location</th>
                                    <th>Brand/Model</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                    <th>Capacity</th>
                                    <th>Last Service</th>
                                    <th>Next Service Due</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ice_machines)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No ice machines found. Add one to get started!</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ice_machines as $machine): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($machine['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <?php 
                                                $brand_model = trim($machine['brand'] . ' ' . $machine['model']);
                                                echo htmlspecialchars($brand_model ?: 'N/A', ENT_QUOTES, 'UTF-8'); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($machine['serial_number'] ?: 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <span class="badge badge-<?php
                                                    switch($machine['status']) {
                                                        case 'operational': echo 'success'; break;
                                                        case 'needs_service': echo 'warning'; break;
                                                        case 'out_of_order': echo 'danger'; break;
                                                        case 'scheduled_maintenance': echo 'info'; break;
                                                        default: echo 'secondary';
                                                    }
                                                ?>">
                                                    <?php echo ucwords(str_replace('_', ' ', $machine['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($machine['ice_production_capacity'] ?: 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo $machine['last_service_date'] ? date('M j, Y', strtotime($machine['last_service_date'])) : 'N/A'; ?></td>
                                            <td>
                                                <?php 
                                                if ($machine['next_service_due']) {
                                                    $due_date = date('M j, Y', strtotime($machine['next_service_due']));
                                                    $days_until = ceil((strtotime($machine['next_service_due']) - time()) / (60*60*24));
                                                    $class = $days_until <= 7 ? 'text-danger' : ($days_until <= 30 ? 'text-warning' : 'text-success');
                                                    echo "<span class='$class'>$due_date</span>";
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" onclick="viewIceMachine(<?php echo $machine['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editIceMachine(<?php echo htmlspecialchars(json_encode($machine), ENT_QUOTES, 'UTF-8'); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteIceMachine(<?php echo $machine['id']; ?>, '<?php echo addslashes($machine['location']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Ice Machine Modal -->
<div class="modal fade" id="addIceMachineModal" tabindex="-1" role="dialog" aria-labelledby="addIceMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIceMachineModalLabel">Add New Ice Machine</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Location *</label>
                                <input type="text" class="form-control" name="location" required placeholder="e.g., Kitchen - Main Floor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <select class="form-control" name="status" required>
                                    <option value="operational">Operational</option>
                                    <option value="needs_service">Needs Service</option>
                                    <option value="out_of_order">Out of Order</option>
                                    <option value="scheduled_maintenance">Scheduled Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <input type="text" class="form-control" name="brand" placeholder="e.g., Hoshizaki">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" class="form-control" name="model" placeholder="e.g., KM-515MAH">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="serial_number">Serial Number</label>
                                <input type="text" class="form-control" name="serial_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ice_production_capacity">Ice Production Capacity</label>
                                <input type="text" class="form-control" name="ice_production_capacity" placeholder="e.g., 515 lbs/day">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="installation_date">Installation Date</label>
                                <input type="date" class="form-control" name="installation_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="last_service_date">Last Service Date</label>
                                <input type="date" class="form-control" name="last_service_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="next_service_due">Next Service Due</label>
                                <input type="date" class="form-control" name="next_service_due">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="photo">Upload Photo</label>
                        <input type="file" class="form-control-file" name="photo" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Any additional notes or maintenance history"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Ice Machine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Ice Machine Modal -->
<div class="modal fade" id="editIceMachineModal" tabindex="-1" role="dialog" aria-labelledby="editIceMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIceMachineModalLabel">Edit Ice Machine</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="editIceMachineForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_location">Location *</label>
                                <input type="text" class="form-control" name="location" id="edit_location" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status">Status *</label>
                                <select class="form-control" name="status" id="edit_status" required>
                                    <option value="operational">Operational</option>
                                    <option value="needs_service">Needs Service</option>
                                    <option value="out_of_order">Out of Order</option>
                                    <option value="scheduled_maintenance">Scheduled Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_brand">Brand</label>
                                <input type="text" class="form-control" name="brand" id="edit_brand">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_model">Model</label>
                                <input type="text" class="form-control" name="model" id="edit_model">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_serial_number">Serial Number</label>
                                <input type="text" class="form-control" name="serial_number" id="edit_serial_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ice_production_capacity">Ice Production Capacity</label>
                                <input type="text" class="form-control" name="ice_production_capacity" id="edit_ice_production_capacity">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_installation_date">Installation Date</label>
                                <input type="date" class="form-control" name="installation_date" id="edit_installation_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_last_service_date">Last Service Date</label>
                                <input type="date" class="form-control" name="last_service_date" id="edit_last_service_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_next_service_due">Next Service Due</label>
                                <input type="date" class="form-control" name="next_service_due" id="edit_next_service_due">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_notes">Notes</label>
                        <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Ice Machine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Ice Machine Modal -->
<div class="modal fade" id="viewIceMachineModal" tabindex="-1" role="dialog" aria-labelledby="viewIceMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewIceMachineModalLabel">Ice Machine Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewIceMachineContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript functions for ice machine management
function editIceMachine(machine) {
    document.getElementById('edit_id').value = machine.id;
    document.getElementById('edit_location').value = machine.location || '';
    document.getElementById('edit_brand').value = machine.brand || '';
    document.getElementById('edit_model').value = machine.model || '';
    document.getElementById('edit_serial_number').value = machine.serial_number || '';
    document.getElementById('edit_status').value = machine.status;
    document.getElementById('edit_ice_production_capacity').value = machine.ice_production_capacity || '';
    document.getElementById('edit_installation_date').value = machine.installation_date || '';
    document.getElementById('edit_last_service_date').value = machine.last_service_date || '';
    document.getElementById('edit_next_service_due').value = machine.next_service_due || '';
    document.getElementById('edit_notes').value = machine.notes || '';
    
    $('#editIceMachineModal').modal('show');
}

function deleteIceMachine(id, location) {
    if (confirm('Are you sure you want to delete the ice machine at "' + location + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }
}

function viewIceMachine(id) {
    // Find the machine data from the PHP array
    const machines = <?php echo json_encode($ice_machines); ?>;
    const machine = machines.find(m => m.id == id);
    
    if (machine) {
        let content = `
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr><th>Location:</th><td>${machine.location}</td></tr>
                        <tr><th>Brand:</th><td>${machine.brand || 'N/A'}</td></tr>
                        <tr><th>Model:</th><td>${machine.model || 'N/A'}</td></tr>
                        <tr><th>Serial Number:</th><td>${machine.serial_number || 'N/A'}</td></tr>
                        <tr><th>Status:</th><td><span class="badge badge-${getStatusClass(machine.status)}">${machine.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span></td></tr>
                        <tr><th>Ice Production:</th><td>${machine.ice_production_capacity || 'N/A'}</td></tr>
                        <tr><th>Installation Date:</th><td>${machine.installation_date ? new Date(machine.installation_date).toLocaleDateString() : 'N/A'}</td></tr>
                        <tr><th>Last Service:</th><td>${machine.last_service_date ? new Date(machine.last_service_date).toLocaleDateString() : 'N/A'}</td></tr>
                        <tr><th>Next Service Due:</th><td>${machine.next_service_due ? new Date(machine.next_service_due).toLocaleDateString() : 'N/A'}</td></tr>
                        <tr><th>Created By:</th><td>${machine.created_by}</td></tr>
                        <tr><th>Created At:</th><td>${new Date(machine.created_at).toLocaleString()}</td></tr>
                    </table>
                </div>
                ${machine.photo ? `<div class="col-md-4"><img src="${machine.photo}" class="img-fluid" alt="Ice Machine Photo"></div>` : ''}
            </div>
            ${machine.notes ? `<div class="mt-3"><strong>Notes:</strong><br><p class="text-muted">${machine.notes}</p></div>` : ''}
        `;
        
        document.getElementById('viewIceMachineContent').innerHTML = content;
        $('#viewIceMachineModal').modal('show');
    }
}

function getStatusClass(status) {
    switch(status) {
        case 'operational': return 'success';
        case 'needs_service': return 'warning';
        case 'out_of_order': return 'danger';
        case 'scheduled_maintenance': return 'info';
        default: return 'secondary';
    }
}
</script>

<?php include 'includes/footer.php'; ?>