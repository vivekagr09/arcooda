<?php
if($user_info['role']=='editor') {
    $can_create = 0;
} else {
    $can_create = 1;
}
?>

<div class="row">
    <div class="col-md-12">
        <?php if($user_info['role']!='editor') : ?>
        <div class="card mb-2 py-3 border-left-success">
            <div class="card-body" style="padding-top: 0;padding-bottom: 0;">
                <div id="modal_new_map" class="row align-items-end">
                    <div class="col-md-6 text-center text-sm-center text-md-left text-lg-left">
                        <div class="form-group mb-0">
                            <label class="mb-0" for="name"><?php echo _("Name"); ?></label>
                            <input type="text" class="form-control" id="name" />
                        </div>
                    </div>
                    <div class="col-md-6 mt-2 text-center text-sm-center text-md-right text-lg-right">
                        <button <?php echo ($demo) ? 'disabled':''; ?> onclick="add_map();" type="button" class="btn btn-success"><i class="fas fa-plus"></i> <?php echo _("Create"); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div id="maps_list">
            <div class="card mb-4 py-2 border-left-primary">
                <div class="card-body" style="padding-top: 0;padding-bottom: 0;">
                    <div class="row">
                        <div class="col-md-8 text-center text-sm-center text-md-left text-lg-left">
                            <?php echo _("LOADING MAPS"); ?> ...
                        </div>
                        <div class="col-md-4 text-center text-sm-center text-md-right text-lg-right">
                            <a href="#" class="btn btn-primary btn-circle">
                                <i class="fas fa-spin fa-spinner"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_delete_map" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Delete Map"); ?></h5>
            </div>
            <div class="modal-body">
                <p><?php echo _("Are you sure you want to delete the entire map, included all the markers?"); ?></p>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_delete_map" onclick="" type="button" class="btn btn-danger"><?php echo _("Yes, Delete"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_duplicate_map" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Duplicate Map"); ?></h5>
            </div>
            <div class="modal-body">
                <p><?php echo _("Are you sure you want to duplicate the entire map, included all the markers?"); ?></p>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_duplicate_map" onclick="" type="button" class="btn btn-success"><?php echo _("Yes, Duplicate"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.can_create = <?php echo $can_create; ?>;
        $(document).ready(function () {
            get_maps();
        });
    })(jQuery);
</script>