<?php
session_start();

// Simple test - bypass authentication for debugging
$_SESSION['loggedin'] = true;
$_SESSION['name'] = 'Debug User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Test - Main App Style</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .work-order-photo {
            max-width: 150px;
            max-height: 150px;
            cursor: pointer;
            border: 2px solid #007bff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Main App Modal Test</h1>
        
        <div class="alert alert-info">
            <strong>Testing:</strong> This page mimics the main app structure to test the photo modal.
        </div>
        
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Simulate the exact database results from the main app
                    include "config/config.php";
                    
                    $query = $conn->prepare("SELECT * FROM `orders` WHERE photo IS NOT NULL AND photo != '' ORDER BY `id` DESC LIMIT 5");
                    $query->execute();
                    $data = $query->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($data)) {
                        echo "<tr><td colspan='6' class='text-center'>No photos found in database</td></tr>";
                    }
                    
                    foreach ($data as $row) {
                        $id = htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8');
                        $room = htmlspecialchars($row['room'] ?? '', ENT_QUOTES, 'UTF-8');
                        $work_to_be_done = htmlspecialchars($row['work_to_be_done'] ?? '', ENT_QUOTES, 'UTF-8');
                        $photo = htmlspecialchars($row['photo'] ?? '', ENT_QUOTES, 'UTF-8');
                        $submitted_by = htmlspecialchars($row['submitted_by'] ?? '', ENT_QUOTES, 'UTF-8');
                        $time = htmlspecialchars($row['time'] ?? '', ENT_QUOTES, 'UTF-8');
                        
                        echo "<tr>";
                        echo "<td>{$id}</td>";
                        echo "<td>{$room}</td>";
                        echo "<td>{$work_to_be_done}</td>";
                        echo "<td>";
                        
                        if ($photo) {
                            // Ensure photo path is relative to web root
                            $photo_display_path = $photo;
                            // If path doesn't start with uploads/, prepend it for file_exists check
                            $photo_file_path = strpos($photo, 'uploads/') === 0 ? $photo : 'uploads/' . $photo;
                            
                            echo "<div>Path in DB: <code>{$photo}</code></div>";
                            echo "<div>Display path: <code>{$photo_display_path}</code></div>";
                            echo "<div>File check path: <code>{$photo_file_path}</code></div>";
                            
                            if (file_exists($photo_file_path)) {
                                echo "<div class='text-success'>✅ File exists</div>";
                                echo "<img src='{$photo_display_path}' alt='Work Order Photo' class='work-order-photo' data-toggle='modal' data-target='#photoModal' data-src='{$photo_display_path}' loading='lazy'>";
                            } else {
                                echo "<div class='text-danger'>❌ File not found: {$photo_file_path}</div>";
                                echo "<span class='text-muted'><i>Photo not found</i></span>";
                            }
                        } else {
                            echo "<span class='text-muted'>No photo</span>";
                        }
                        
                        echo "</td>";
                        echo "<td>{$submitted_by}</td>";
                        echo "<td>{$time}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Photo Modal (Exact copy from main app) -->
    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Work Order Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" style="max-height: 70vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Use same JavaScript libraries as updated footer -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <script>
    console.log('Main app modal test page loaded');
    
    // Exact copy of modal script from main app
    $('#photoModal').on('show.bs.modal', function (event) {
        console.log('Photo modal opening...');
        var button = $(event.relatedTarget);
        var src = button.data('src');
        var modal = $(this);
        var modalImage = modal.find('#modalImage');
        
        console.log('Image source:', src);
        console.log('Button element:', button);
        
        // Clear previous image and show loading
        modalImage.attr('src', '');
        modal.find('.modal-body').html('<div class="text-center"><p>Loading image...</p><p><small>Path: ' + src + '</small></p></div>');
        
        // Create new image element to test if image loads
        var img = new Image();
        img.onload = function() {
            console.log('Image loaded successfully:', src);
            // Image loaded successfully
            modal.find('.modal-body').html('<img src="' + src + '" id="modalImage" class="img-fluid" style="max-height: 70vh;">');
        };
        img.onerror = function() {
            console.error('Image failed to load:', src);
            // Image failed to load
            modal.find('.modal-body').html('<div class="text-center text-muted"><p><i class="fas fa-exclamation-triangle"></i><br>Photo could not be loaded<br><small>' + src + '</small></p></div>');
        };
        
        console.log('Setting image src to:', src);
        img.src = src;
    });
    
    $(document).ready(function() {
        console.log('Document ready');
    });
    </script>
</body>
</html>