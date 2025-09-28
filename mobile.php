<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: auth/login.php');
    exit;
}

// Clear force_desktop when explicitly accessing mobile
unset($_SESSION['force_desktop']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Work Orders - Mobile</title>
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

        /* Modal enhancements for mobile */
        .modal-dialog {
            margin: 10px;
            max-width: calc(100vw - 20px);
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
        }

        /* Success/Error messages */
        .alert {
            border-radius: 10px;
            margin: 15px;
            border: none;
        }

        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            border: 0;
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
                color: #e0e0e0;
            }
            
            .work-order-card {
                background: #1e1e1e;
                color: #e0e0e0;
            }
            
            .bottom-nav {
                background: #1e1e1e;
                border-top-color: #333;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .container-fluid {
                padding: 10px;
            }
            
            .mobile-header h1 {
                font-size: 1.3rem;
            }
            
            .order-meta {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-tasks"></i> Work Orders</h1>
                    <div class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>!</div>
                </div>
                <div>
                    <a href="index.php?desktop=1" class="text-white" style="font-size: 0.8rem; opacity: 0.8; text-decoration: none;">
                        <i class="fas fa-desktop"></i><br>
                        Desktop
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
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
        if(isset($_SESSION['completed'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['completed'], ENT_QUOTES, 'UTF-8');
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button></div>';
            unset($_SESSION['completed']);
        }
        ?>

        <!-- Work Orders -->
        <div class="work-orders-container">
            <?php
            include "config/config.php";

            $query = $conn->prepare("SELECT * FROM `orders` WHERE completed = 0 ORDER BY `id` DESC");
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);

            if (empty($data)) {
                echo '<div class="empty-state">
                        <i class="fas fa-clipboard-check"></i>
                        <h3>All caught up!</h3>
                        <p>No pending work orders at the moment.</p>
                      </div>';
            } else {
                foreach ($data as $row) {
                    $checkbox = $row['completed'] ? "checked" : "";
                    $id = htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8');
                    $room = htmlspecialchars($row['room'] ?? '', ENT_QUOTES, 'UTF-8');
                    $work_to_be_done = htmlspecialchars($row['work_to_be_done'] ?? '', ENT_QUOTES, 'UTF-8');
                    $photo = htmlspecialchars($row['photo'] ?? '', ENT_QUOTES, 'UTF-8');
                    $submitted_by = htmlspecialchars($row['submitted_by'] ?? '', ENT_QUOTES, 'UTF-8');
                    $time = htmlspecialchars($row['time'] ?? '', ENT_QUOTES, 'UTF-8');

                    echo '<div class="card work-order-card">
                            <div class="card-header">
                                <div class="order-id">Order #'.$id.'</div>
                                <div class="order-location"><i class="fas fa-map-marker-alt"></i> '.$room.'</div>
                            </div>
                            <div class="card-body">
                                <div class="task-description">'.$work_to_be_done.'</div>';
                    
                    // Photo section
                    if ($photo) {
                        $photo_display_path = $photo;
                        $photo_file_path = strpos($photo, 'uploads/') === 0 ? $photo : 'uploads/' . $photo;
                        
                        echo '<div class="photo-container">';
                        if (file_exists($photo_file_path)) {
                            echo '<img src="'.$photo_display_path.'" alt="Work Order Photo" class="mobile-photo" data-toggle="modal" data-target="#photoModal" data-src="'.$photo_display_path.'" loading="lazy">';
                        } else {
                            echo '<div class="text-muted"><i class="fas fa-image"></i> Photo not available</div>';
                        }
                        echo '</div>';
                    }

                    echo '      <div class="order-meta">
                                    <span class="submitted-by"><i class="fas fa-user"></i> '.$submitted_by.'</span>
                                    <span class="time-stamp"><i class="far fa-clock"></i> '.$time.'</span>
                                </div>
                                
                                <div class="action-buttons">
                                    <form action="pages/operate.php" method="post" class="completion-form" style="display: inline-block; width: 100%;">
                                        <input type="hidden" name="completed" value="'.$id.'">
                                        <button type="button" class="btn-mobile btn-complete completion-checkbox">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-image"></i> Work Order Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" style="max-height: 80vh; border-radius: 8px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Upload Modal -->
    <div class="modal fade" id="addPhotoModal" tabindex="-1" role="dialog" aria-labelledby="addPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPhotoModalLabel"><i class="fas fa-camera"></i> Add Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Task marked as complete. Would you like to add a photo?</p>
                    <form id="photoUploadForm" enctype="multipart/form-data">
                        <input type="hidden" name="task_id" id="modal_task_id">
                        <div class="form-group">
                            <label for="photo"><i class="fas fa-upload"></i> Select photo:</label>
                            <input type="file" name="photo" id="photo" class="form-control-file" accept="image/*" capture="environment" required>
                            <small class="form-text text-muted">You can take a photo or select from gallery</small>
                        </div>
                    </form>
                    <div id="uploadStatus"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">
                        <i class="fas fa-times"></i> Skip
                    </button>
                    <button type="button" class="btn btn-primary" id="uploadPhotoButton">
                        <i class="fas fa-upload"></i> Upload Photo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <a href="index.php" class="nav-item active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="col">
                    <a href="pages/add.php" class="nav-item">
                        <i class="fas fa-plus"></i>
                        <span>Add Task</span>
                    </a>
                </div>
                <div class="col">
                    <a href="ice_machines_mobile.php" class="nav-item">
                        <i class="fas fa-snowflake"></i>
                        <span>Ice Machines</span>
                    </a>
                </div>
                <div class="col">
                    <a href="pages/search.php" class="nav-item">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </a>
                </div>
                <div class="col">
                    <a href="auth/logout.php" class="nav-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <script>
    $(document).ready(function() {
        console.log("Mobile page JavaScript loaded");
        
        // Script to view photo in modal
        $("#photoModal").on("show.bs.modal", function (event) {
            console.log("Photo modal opening...");
            var button = $(event.relatedTarget);
            var src = button.data("src");
            var modal = $(this);
            
            console.log("Image source:", src);
            
            // Clear previous image and show loading
            modal.find(".modal-body").html('<div class="text-center"><p><i class="fas fa-spinner fa-spin"></i> Loading image...</p><p><small>' + src + '</small></p></div>');
            
            // Create new image element to test if image loads
            var img = new Image();
            img.onload = function() {
                console.log("Image loaded successfully:", src);
                modal.find(".modal-body").html('<img src="' + src + '" id="modalImage" class="img-fluid" style="max-height: 80vh; border-radius: 8px;">');
            };
            img.onerror = function() {
                console.error("Image failed to load:", src);
                modal.find(".modal-body").html('<div class="text-center text-muted"><p><i class="fas fa-exclamation-triangle"></i><br>Photo could not be loaded<br><small>' + src + '</small></p></div>');
            };
            
            img.src = src;
        });
        
        // Task completion handling
        $(".completion-checkbox").on("click", function(e) {
            e.preventDefault();

            var form = $(this).closest("form");
            var taskId = form.find("input[name='completed']").val();
            var button = $(this);

            // Show loading state
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            button.prop('disabled', true);

            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),
                success: function(response) {
                    $("#modal_task_id").val(taskId);
                    $("#addPhotoModal").modal("show");
                    button.html('<i class="fas fa-check"></i> Mark Complete');
                    button.prop('disabled', false);
                },
                error: function() {
                    alert("Error completing the task. Please try again.");
                    button.html('<i class="fas fa-check"></i> Mark Complete');
                    button.prop('disabled', false);
                }
            });
        });

        // Photo upload handling
        $("#uploadPhotoButton").on("click", function() {
            var form = $("#photoUploadForm")[0];
            var formData = new FormData(form);
            var button = $(this);

            if ($("#photo").get(0).files.length === 0) {
                $("#uploadStatus").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Please select a file to upload.</div>');
                return;
            }

            // Show loading state
            button.html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
            button.prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "upload_photo.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $("#uploadStatus").html('<div class="alert alert-success"><i class="fas fa-check"></i> Photo uploaded successfully!</div>');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var errorMessage = jqXHR.responseText || "An unknown error occurred during upload.";
                    $("#uploadStatus").html('<div class="alert alert-danger"><i class="fas fa-times"></i> <strong>Error:</strong> ' + errorMessage + '</div>');
                    button.html('<i class="fas fa-upload"></i> Upload Photo');
                    button.prop('disabled', false);
                }
            });
        });

        // Touch feedback for cards
        $(".work-order-card").on("touchstart", function() {
            $(this).addClass("shadow-lg");
        }).on("touchend", function() {
            var self = $(this);
            setTimeout(function() {
                self.removeClass("shadow-lg");
            }, 150);
        });

        // Auto-dismiss alerts
        setTimeout(function() {
            $(".alert").fadeOut();
        }, 5000);
    });
    </script>
</body>
</html>