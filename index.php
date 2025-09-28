<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: auth/login.php');
    exit;
}

// Mobile detection and redirect (optional - user can choose)
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
    header('Location: mobile.php');
    exit;
}

include 'includes/header.php';
?>



<div class="container mt-4">
    <div class="row">
        <div class="col">
            <h2 class="text-center">Current Task</h2>
            <h3 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>!</h3>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <?php
            if(isset($_SESSION['message'])) {
                echo '<p class="alert alert-info">'.htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8').'</p>';
                unset($_SESSION['message']);
            }
            if(isset($_SESSION['completed'])) {
                echo '<p class="alert alert-success">'.htmlspecialchars($_SESSION['completed'], ENT_QUOTES, 'UTF-8').'</p>';
                unset($_SESSION['completed']);
            }
            ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
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
                            <th class="text-center">Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "config/config.php";

                        $query = $conn->prepare("SELECT * FROM `orders` WHERE completed = 0 ORDER BY `id` DESC");
                        $query->execute();
                        $data = $query->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($data as $row) {
                            $checkbox = $row['completed'] ? "checked" : "";
                            $id = htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8');
                            $room = htmlspecialchars($row['room'] ?? '', ENT_QUOTES, 'UTF-8');
                            $work_to_be_done = htmlspecialchars($row['work_to_be_done'] ?? '', ENT_QUOTES, 'UTF-8');
                            $photo = htmlspecialchars($row['photo'] ?? '', ENT_QUOTES, 'UTF-8');
                            $submitted_by = htmlspecialchars($row['submitted_by'] ?? '', ENT_QUOTES, 'UTF-8');
                            $time = htmlspecialchars($row['time'] ?? '', ENT_QUOTES, 'UTF-8');

                            echo "
                            <tr>
                                <td>{$id}</td>
                                <td>{$room}</td>
                                <td>{$work_to_be_done}</td>
                                <td>";
                            if ($photo) {
                                // Ensure photo path is relative to web root
                                $photo_display_path = $photo;
                                // If path doesn't start with uploads/, prepend it for file_exists check
                                $photo_file_path = strpos($photo, 'uploads/') === 0 ? $photo : 'uploads/' . $photo;
                                
                                if (file_exists($photo_file_path)) {
                                    echo "<img src='{$photo_display_path}' alt='Work Order Photo' class='work-order-photo' data-toggle='modal' data-target='#photoModal' data-src='{$photo_display_path}' loading='lazy'>";
                                } else {
                                    echo "<span class='text-muted'><i>Photo not found: {$photo_file_path}</i></span>";
                                }
                            } else {
                                echo "<span class='text-muted'>No photo</span>";
                            }
                            echo "</td>
                                <td>{$submitted_by}</td>
                                <td>{$time}</td>
                                <td class='text-center'>
                                    <form action='pages/operate.php' method='post' class='completion-form'>
                                        <input type='hidden' name='completed' value='{$id}'>
                                        <div class='form-check'>
                                            <input type='checkbox' class='form-check-input completion-checkbox' {$checkbox}>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            ";
                        }

                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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

<div class="modal fade" id="addPhotoModal" tabindex="-1" role="dialog" aria-labelledby="addPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPhotoModalLabel">Add Photo to Completed Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Task marked as complete. Would you like to add a photo?</p>
                <form id="photoUploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="modal_task_id">
                    <div class="form-group">
                        <label for="photo">Select photo:</label>
                        <input type="file" name="photo" id="photo" class="form-control-file" required>
                    </div>
                </form>
                 <div id="uploadStatus"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">Skip</button>
                <button type="button" class="btn btn-primary" id="uploadPhotoButton">Upload Photo</button>
            </div>
        </div>
    </div>
</div>

<?php 
// Store the JavaScript to be included in the footer
$page_scripts = '
<script>
    // Script for task completion and photo upload modal
    $(document).ready(function() {
        console.log("Index page JavaScript loaded");
        
        // Script to view photo in modal
        $("#photoModal").on("show.bs.modal", function (event) {
            console.log("Photo modal opening...");
            var button = $(event.relatedTarget);
            var src = button.data("src");
            var modal = $(this);
            var modalImage = modal.find("#modalImage");
            
            console.log("Image source:", src);
            console.log("Button element:", button);
            
            // Clear previous image and show loading
            modalImage.attr("src", "");
            modal.find(".modal-body").html("<div class=\"text-center\"><p>Loading image...</p><p><small>Path: " + src + "</small></p></div>");
            
            // Create new image element to test if image loads
            var img = new Image();
            img.onload = function() {
                console.log("Image loaded successfully:", src);
                // Image loaded successfully
                modal.find(".modal-body").html("<img src=\"" + src + "\" id=\"modalImage\" class=\"img-fluid\" style=\"max-height: 70vh;\">");
            };
            img.onerror = function() {
                console.error("Image failed to load:", src);
                // Image failed to load
                modal.find(".modal-body").html("<div class=\"text-center text-muted\"><p><i class=\"fas fa-exclamation-triangle\"></i><br>Photo could not be loaded<br><small>" + src + "</small></p></div>");
            };
            
            console.log("Setting image src to:", src);
            img.src = src;
        });
        
        $(".completion-checkbox").on("click", function(e) {
            e.preventDefault();

            var form = $(this).closest("form");
            var taskId = form.find("input[name=\"completed\"]").val();

            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),
                success: function(response) {
                    $("#modal_task_id").val(taskId);
                    $("#addPhotoModal").modal("show");
                },
                error: function() {
                    alert("Error completing the task. Please try again.");
                }
            });
        });

        $("#uploadPhotoButton").on("click", function() {
            var form = $("#photoUploadForm")[0];
            var formData = new FormData(form);

            if ($("#photo").get(0).files.length === 0) {
                $("#uploadStatus").html("<p class=\"text-danger\">Please select a file to upload.</p>");
                return;
            }

            $.ajax({
                type: "POST",
                url: "upload_photo.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $("#uploadStatus").html("<p class=\"text-success\">Photo uploaded successfully!</p>");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var errorMessage = jqXHR.responseText || "An unknown error occurred during upload.";
                    $("#uploadStatus").html("<p class=\"text-danger\"><strong>Error:</strong> " + errorMessage + "</p>");
                }
            });
        });
    });
</script>
';

include 'includes/footer.php'; 
?>