<?php
session_start();
$id_user = $_SESSION['id_user'];
$id_map_sel = $_SESSION['id_map_sel'];
$categories = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['categories']==0) {
        $categories=false;
    }
}
?>

<?php if(!$categories): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like you found a glitch in the matrix..."); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-12">
        <a href="index.php?p=add_category" class="btn btn-block btn-success mb-1"><i class="fa fa-plus"></i> <?php echo _("ADD CATEGORY"); ?></a>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered table-hover" id="categories_table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php echo _("Name"); ?></th>
                        <th><?php echo _("Default selected"); ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        $(document).ready(function () {
            $('#categories_table').DataTable({
                "pageLength": 100,
                "responsive": true,
                "scrollX": true,
                "processing": true,
                "serverSide": true,
                "ajax": "ajax/get_categories.php?id_map="+window.id_map
            });
            $('#categories_table tbody').on('click', 'td', function () {
                var category_id = $(this).parent().attr("id");
                location.href = 'index.php?p=edit_category&id='+category_id;
            });
        });
    })(jQuery);
</script>