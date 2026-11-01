<?php
session_start();
$id_map_sel = $_SESSION['id_map_sel'];
$icons_library = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['icons_library']==0) {
        $icons_library=false;
    }
}
?>

<?php if(!$icons_library): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like you found a glitch in the matrix..."); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="ajax/upload_icon_image.php" class="dropzone" id="gallery-dropzone"></form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-grip-horizontal"></i> <?php echo _("Icons List"); ?></h6>
            </div>
            <div class="card-body">
                <div id="list_images">
                    <p><?php echo _("Loading icons"); ?> ...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict"; // Start of use strict
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        Dropzone.autoDiscover = false;
        $(document).ready(function () {
            get_icon_images(id_map);
            var gallery_dropzone = new Dropzone("#gallery-dropzone", {
                    url: "ajax/upload_icon_image.php",
                    parallelUploads: 1,
                    maxFilesize: 20,
                    timeout: 120000,
                    acceptedFiles: 'image/*'
                });
            gallery_dropzone.on("addedfile", function(file) {
                $('#list_images').addClass('disabled');
            });
            gallery_dropzone.on("success", function(file,rsp) {
                add_image_to_icon(id_map,rsp);
            });
            gallery_dropzone.on("queuecomplete", function() {
                $('#list_images').removeClass('disabled');
                gallery_dropzone.removeAllFiles();
            });
        });
    })(jQuery); // End of use strict
</script>