<?php
// Simple debug page to test photo modal without authentication
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Modal Debug</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .work-order-photo {
            max-width: 150px;
            max-height: 150px;
            cursor: pointer;
            border: 2px solid #007bff;
            border-radius: 5px;
        }
        .debug-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Photo Modal Debug Page</h1>
        
        <div class="debug-info">
            <h3>Available Photos</h3>
            <?php
            $photos = [
                'uploads/1758995159.3362.jpg',
                'uploads/test-photo.jpg'
            ];
            
            foreach ($photos as $photo) {
                $exists = file_exists($photo) ? '✅ EXISTS' : '❌ NOT FOUND';
                $size = file_exists($photo) ? ' (' . round(filesize($photo)/1024, 1) . ' KB)' : '';
                echo "<p><strong>{$photo}</strong> - {$exists}{$size}</p>";
            }
            ?>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Direct Image Display</h3>
                <div class="mb-3">
                    <p><strong>uploads/1758995159.3362.jpg</strong></p>
                    <img src="uploads/1758995159.3362.jpg" alt="Direct Image 1" style="max-width: 200px; border: 1px solid #ccc;">
                </div>
                
                <div class="mb-3">
                    <p><strong>uploads/test-photo.jpg</strong></p>
                    <img src="uploads/test-photo.jpg" alt="Direct Image 2" style="max-width: 200px; border: 1px solid #ccc;">
                </div>
            </div>
            
            <div class="col-md-6">
                <h3>Clickable Thumbnails (Like in App)</h3>
                <div class="mb-3">
                    <p><strong>Photo 1:</strong></p>
                    <img src="uploads/1758995159.3362.jpg" 
                         alt="Work Order Photo" 
                         class="work-order-photo" 
                         data-toggle="modal" 
                         data-target="#photoModal" 
                         data-src="uploads/1758995159.3362.jpg">
                </div>
                
                <div class="mb-3">
                    <p><strong>Photo 2:</strong></p>
                    <img src="uploads/test-photo.jpg" 
                         alt="Work Order Photo" 
                         class="work-order-photo" 
                         data-toggle="modal" 
                         data-target="#photoModal" 
                         data-src="uploads/test-photo.jpg">
                </div>
            </div>
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

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <script>
    console.log('Debug page loaded');
    
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
    
    // Test image loading on page load
    $(document).ready(function() {
        console.log('Document ready');
        
        // Test if images are accessible
        var testImages = ['uploads/1758995159.3362.jpg', 'uploads/test-photo.jpg'];
        testImages.forEach(function(imgPath) {
            var testImg = new Image();
            testImg.onload = function() {
                console.log('✅ Image accessible:', imgPath);
            };
            testImg.onerror = function() {
                console.error('❌ Image NOT accessible:', imgPath);
            };
            testImg.src = imgPath;
        });
    });
    </script>
</body>
</html>