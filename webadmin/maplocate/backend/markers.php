<?php
session_start();
$id_map_sel = $_SESSION['id_map_sel'];
$can_create = true;
$can_edit = 1;
$can_delete = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['create_markers']==0) {
        $can_create=false;
    }
    if($editor_permissions['edit_markers']==0) {
        $can_edit=0;
    }
    if($editor_permissions['delete_markers']==0) {
        $can_delete=false;
    }
}
$markers_missing_coordinates = get_missing_coordinates_markers($id_map_sel);
$count_missing_coordinates = count($markers_missing_coordinates);
$count_markers = count_markers($id_map_sel);
?>

<div class="row marker_add_bar">
    <div class="col-md-3">
        <?php if($can_create) : ?>
            <a id="btn_add_marker" href="index.php?p=add_marker" class="btn btn-block btn-success mb-1"><i class="fa fa-plus"></i> <?php echo _("ADD MARKER"); ?></a>
        <?php endif; ?>
    </div>
    <div class="col-md-3">
        <?php if($can_create) : ?>
            <a id="btn_import_marker" href="index.php?p=import" class="btn btn-block btn-primary mb-1"><i class="far fa-file-excel"></i> <?php echo _("IMPORT EXCEL"); ?></a>
        <?php endif; ?>
    </div>
    <div class="col-md-3">
        <button data-toggle="modal" data-target="#modal_batch_geocode" class="btn btn-block btn-primary <?php echo ($count_missing_coordinates==0) ? 'disabled' : ''; ?> mb-1"><i class="fas fa-map-pin"></i> <?php echo _("BATCH GEOCODE"); ?></button>
    </div>
    <div class="col-md-3">
        <button onclick="bulk_select();" class="btn btn-block btn-secondary mb-1 <?php echo ($demo) ? 'disabled':''; ?>"><i class="fas fa-tasks"></i> <?php echo _("BULK SELECT"); ?></button>
    </div>
</div>
<div style="display: none;" class="row marker_bulk_select_bar">
    <div class="col-md-9">
        <div style="pointer-events:none;" class="btn btn-block text-left mb-1"><?php echo _("SELECTED MARKERS"); ?> <b id="markers_selected">0</b>&nbsp;&nbsp;&nbsp;<span onclick="select_all_markers();" style="cursor:pointer;pointer-events:initial;" class="badge badge-secondary"><?php echo _("select all"); ?></span>&nbsp;<span onclick="deselect_all_markers();" style="cursor:pointer;pointer-events:initial;" class="badge badge-secondary"><?php echo _("deselect all"); ?></span></div>
    </div>
    <div class="col-md-3">
        <button onclick="exit_bulk_select();" class="btn btn-block btn-dark mb-1"><i class="far fa-times"></i> <?php echo _("CANCEL"); ?></button>
    </div>
    <div class="col-md-2 mt-3">
        <?php if($can_delete) : ?>
        <button id="btn_exec_delete_markers" onclick="exec_bulk_delete();" class="btn btn_bulk_action btn-block btn-danger mb-1 disabled"><i class="far fa-trash"></i> <?php echo _("DELETE"); ?></button>
        <?php endif; ?>
    </div>
    <div class="col-md-2 mt-3">
        <button id="btn_exec_activate_markers" onclick="exec_bulk_activate();" class="btn btn_bulk_action btn-block btn-primary mb-1 disabled"><i class="far fa-check"></i> <?php echo _("ACTIVATE"); ?></button>
    </div>
    <div class="col-md-2 mt-3">
        <button id="btn_exec_disable_markers" onclick="exec_bulk_disable();" class="btn btn_bulk_action btn-block btn-primary mb-1 disabled"><i class="far fa-times"></i> <?php echo _("DISABLE"); ?></button>
    </div>
    <div class="col-md-2 mt-3">
        <button id="btn_exec_validate_markers" onclick="exec_bulk_validate();" class="btn btn_bulk_action btn-block btn-primary mb-1 disabled"><i class="far fa-check"></i> <?php echo _("VALIDATE"); ?></button>
    </div>
    <div class="col-md-2 mt-3">
        <button id="btn_exec_featured_markers" onclick="exec_bulk_add_featured();" class="btn btn_bulk_action btn-block btn-primary mb-1 disabled"><i class="far fa-plus"></i> <?php echo _("FEATURED"); ?></button>
    </div>
    <div class="col-md-2 mt-3">
        <button id="btn_exec_unfeatured_markers" onclick="exec_bulk_remove_featured();" class="btn btn_bulk_action btn-block btn-primary mb-1 disabled"><i class="far fa-minus"></i> <?php echo _("FEATURED"); ?></button>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered table-hover" id="markers_table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php echo _("Name"); ?></th>
                        <?php if($count_markers<=1000) : ?>
                        <th><?php echo _("Category"); ?></th>
                        <?php endif; ?>
                        <th><?php echo _("Address"); ?></th>
                        <th><?php echo _("Active"); ?></th>
                        <th><?php echo _("Validated"); ?></th>
                        <th><?php echo _("Centered"); ?></th>
                        <th><?php echo _("Featured"); ?></th>
                        <th><?php echo _("Order"); ?></th>
                       <th style="min-width:50px"></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-center">
        <a class="badge badge-primary" target="_blank" href="ajax/export_markers.php?id_map=<?php echo $id_map_sel; ?>"><?php echo _("export markers"); ?></a>
    </div>
</div>

<div id="modal_delete_marker" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Delete Marker"); ?></h5>
            </div>
            <div class="modal-body">
                <p><?php echo _("Are you sure you want to delete the marker?"); ?></p>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_delete_marker" onclick="" type="button" class="btn btn-danger"><?php echo _("Yes, Delete"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_clone_marker" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Duplicate Marker"); ?></h5>
            </div>
            <div class="modal-body">
                <p><?php echo _("Are you sure you want to duplicate the marker?"); ?></p>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="duplicate_target_map"><?php echo _("Map"); ?></label><br>
                            <select class="form-control" id="duplicate_target_map">
                                <option id="0"><?php echo _("On the same map"); ?></option>
                                <?php
                                $maps_d = get_maps($_SESSION['id_user']);
                                foreach ($maps_d as $map_d) {
                                    $id_map_d = $map_d['id'];
                                    if($id_map_d!=$id_map_sel) {
                                        $name_map_d = $map_d['name'];
                                        echo "<option id='$id_map_d'>$name_map_d</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_clone_marker" onclick="" type="button" class="btn btn-success"><?php echo _("Yes, Duplicate"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_batch_geocode" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Batch Geocode"); ?></h5>
            </div>
            <div class="modal-body">
                <p><?php echo _("This procedure will try to associate the coordinates to all those markers that are missing, converting them from the addresses."); ?></p>
                <div id="batch_geocode_progress" class="progress" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0 / <?php echo $count_missing_coordinates; ?></div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_batch_geocode" onclick="start_batch_geocode();" type="button" class="btn btn-success"><?php echo _("Start"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<style>
    <?php if(!$can_delete) : ?>
    .btn-danger {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    <?php endif; ?>
    <?php if($can_edit==0) : ?>
    #markers_table tbody tr {
        pointer-events: none !important;
    }
    <?php endif; ?>
</style>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        window.can_edit = <?php echo $can_edit; ?>;
        window.bulk_select_mode = false;
        window.missing_markers_coord_json = `<?php echo json_encode($markers_missing_coordinates); ?>`;
        $(document).ready(function () {
            $('#markers_table').DataTable({
                "order": [[ 7, "desc" ]],
                "responsive": true,
                "scrollX": true,
                "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "ajax": "ajax/get_markers.php?id_map="+window.id_map,
                "language": {
                    "decimal":        "",
                    "emptyTable":     "<?php echo _("No data available in table"); ?>",
                    "info":           "<?php echo sprintf(_("Showing %s to %s of %s entries"),'_START_','_END_','_TOTAL_'); ?>",
                    "infoEmpty":      "<?php echo _("Showing 0 to 0 of 0 entries"); ?>",
                    "infoFiltered":   "<?php echo sprintf(_("(filtered from %s total entries)"),'_MAX_'); ?>",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "<?php echo sprintf(_("Show %s entries"),'_MENU_'); ?>",
                    "loadingRecords": "<?php echo _("Loading"); ?>...",
                    "processing":     "<?php echo _("Processing"); ?>...",
                    "search":         "<?php echo _("Search"); ?>:",
                    "zeroRecords":    "<?php echo _("No matching records found"); ?>",
                    "paginate": {
                        "first":      "<?php echo _("First"); ?>",
                        "last":       "<?php echo _("Last"); ?>",
                        "next":       "<?php echo _("Next"); ?>",
                        "previous":   "<?php echo _("Previous"); ?>"
                    },
                    "aria": {
                        "sortAscending":  ": <?php echo _("activate to sort column ascending"); ?>",
                        "sortDescending": ": <?php echo _("activate to sort column descending"); ?>"
                    }
                },
                "drawCallback": function( settings ) {
                    if(window.bulk_select_mode) {
                        $('.btn_delete_marker').hide();
                        $('.btn_duplicate_marker').hide();
                        $('.check_bulk_marker').show();
                        $('.order_icon').addClass('disabled');
                        $('#markers_selected').html(0);
                        $('.btn_bulk_action').addClass('disabled');
                        $('.check_bulk_marker').change(function(){
                            var num = 0;
                            $('.check_bulk_marker').each(function() {
                                if ($(this).is(':checked')) {
                                    num++;
                                }
                            });
                            $('#markers_selected').html(num);
                            if(num!=0) {
                                $('.btn_bulk_action').removeClass('disabled');
                            } else {
                                $('.btn_bulk_action').addClass('disabled');
                            }
                        });
                    }
                }
            });
            if(window.can_edit==1) {
                $('#markers_table tbody').on('click', 'td', function () {
                    if(!window.bulk_select_mode) {
                        if($(this)[0]._DT_CellIndex.column!=7 && $(this)[0]._DT_CellIndex.column!=8) {
                            var marker_id = $(this).parent().attr("id");
                            location.href = 'index.php?p=edit_marker&id='+marker_id;
                        }
                    } else {
                        if($(this)[0]._DT_CellIndex.column!=7 && $(this)[0]._DT_CellIndex.column!=8) {
                            var marker_id = $(this).parent().attr("id");
                            $('#marker_'+marker_id).trigger('click');
                        }
                    }
                });
            }
        });
    })(jQuery);
</script>