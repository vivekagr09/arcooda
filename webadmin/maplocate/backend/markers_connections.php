<?php
session_start();
$id_user = $_SESSION['id_user'];
$id_map_sel = $_SESSION['id_map_sel'];
$connections = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['connections']==0) {
        $connections=false;
    }
}
?>

<?php if(!$connections): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like you found a glitch in the matrix..."); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-12">
        <a href="#" data-toggle="modal" data-target="#modal_add_markers_connections" class="btn btn-block btn-success mb-1"><i class="fa fa-plus"></i> <?php echo _("ADD CONNECTION"); ?></a>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered table-hover" id="markers_connections_table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php echo _("Marker"); ?> 1</th>
                        <th><?php echo _("Line"); ?></th>
                        <th><?php echo _("Marker"); ?> 2</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_add_markers_connections" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Add Connection"); ?></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marker_source"><?php echo _("Marker"); ?> 1</label>
                            <select id="marker_source" data-live-search="true" data-actions-box="true" class="form-control selectpicker">
                                <?php echo get_markers_options($id_map_sel,0); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marker_dest"><?php echo _("Marker"); ?> 2</label>
                            <select id="marker_dest" data-live-search="true" data-actions-box="true" class="form-control selectpicker">
                                <?php echo get_markers_options($id_map_sel,0); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="color"><?php echo _("Line Color"); ?></label>
                            <input id="color" type="text" class="form-control" value="#ff0000" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="width"><?php echo _("Line Width"); ?></label>
                            <input id="width" type="number" min="1" max="10" class="form-control" value="2" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title_mc"><?php echo _("Popup Title"); ?></label>
                            <input id="title_mc" type="text" class="form-control" value="" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description_mc"><?php echo _("Popup Description"); ?></label>
                            <div id="description_mc"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_add_markers_connections" onclick="add_markers_connections();" type="button" class="btn btn-success"><?php echo _("Yes, Add"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_edit_markers_connections" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Edit Connection"); ?></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marker_source_edit"><?php echo _("Marker"); ?> 1</label>
                            <select id="marker_source_edit" data-live-search="true" data-actions-box="true" class="form-control selectpicker">
                                <?php echo get_markers_options($id_map_sel,0); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marker_dest_edit"><?php echo _("Marker"); ?> 2</label>
                            <select id="marker_dest_edit" data-live-search="true" data-actions-box="true" class="form-control selectpicker">
                                <?php echo get_markers_options($id_map_sel,0); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="color_edit"><?php echo _("Line Color"); ?></label>
                            <input id="color_edit" type="text" class="form-control" value="" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="width_edit"><?php echo _("Line Width"); ?></label>
                            <input id="width_edit" type="number" min="1" max="10" class="form-control" value="" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title_mc_edit"><?php echo _("Popup Title"); ?></label>
                            <input id="title_mc_edit" type="text" class="form-control" value="" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description_mc_edit"><?php echo _("Popup Description"); ?></label>
                            <div id="description_mc_edit"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_edit_markers_connections" onclick="" type="button" class="btn btn-success"><?php echo _("Yes, Save"); ?></button>
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_delete_markers_connections" onclick="" type="button" class="btn btn-danger"><?php echo _("Delete"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        window.color_spectrum = null;
        window.color_edit_spectrum = null;
        window.editor_html_description = null;
        window.editor_html_description_edit = null;
        $(document).ready(function () {
            window.editor_html_description = ace.edit('description_mc');
            window.editor_html_description.session.setMode("ace/mode/html");
            window.editor_html_description.session.setUseWrapMode(true);
            window.editor_html_description.session.setOption('indentedSoftWrap', false);
            window.editor_html_description.setOption('enableLiveAutocompletion',true);
            if($('body').hasClass('dark_mode')) {
                window.editor_html_description.setTheme("ace/theme/one_dark");
            }
            window.editor_html_description_edit = ace.edit('description_mc_edit');
            window.editor_html_description_edit.session.setMode("ace/mode/html");
            window.editor_html_description_edit.session.setUseWrapMode(true);
            window.editor_html_description_edit.session.setOption('indentedSoftWrap', false);
            window.editor_html_description_edit.setOption('enableLiveAutocompletion',true);
            if($('body').hasClass('dark_mode')) {
                window.editor_html_description_edit.setTheme("ace/theme/one_dark");
            }
            $('#markers_connections_table').DataTable({
                "pageLength": 100,
                "responsive": true,
                "scrollX": true,
                "processing": true,
                "serverSide": true,
                "ajax": "ajax/get_markers_connections.php?id_map="+window.id_map,
                "createdRow": function (row, data, dataIndex) {
                    $(row).attr('data-id', data.DT_RowId);
                    $(row).attr('data-marker_source', data.DT_RowIdMarkerSource);
                    $(row).attr('data-marker_dest', data.DT_RowIdMarkerDest);
                    $(row).attr('data-color', data.DT_RowColor);
                    $(row).attr('data-width', data.DT_RowWidth);
                    $(row).attr('data-title', data.DT_RowTitle);
                    $(row).attr('data-description', data.DT_RowDescription);
                }
            });
            $('#markers_connections_table tbody').on('click', 'td', function () {
                var id_markers_connections = $(this).parent().attr("data-id");
                var id_marker_source = $(this).parent().attr("data-marker_source");
                var id_marker_dest = $(this).parent().attr("data-marker_dest");
                var color = $(this).parent().attr("data-color");
                var width = $(this).parent().attr("data-width");
                var title = $(this).parent().attr("data-title");
                var description = $(this).parent().attr("data-description");
                $('#marker_source_edit option').prop('selected',false);
                $('#marker_source_edit option[id='+id_marker_source+']').prop('selected',true);
                $('#marker_source_edit').selectpicker('refresh');
                $('#marker_dest_edit option').prop('selected',false);
                $('#marker_dest_edit option[id='+id_marker_dest+']').prop('selected',true);
                $('#marker_dest_edit').selectpicker('refresh');
                $('#color_edit').val(color);
                $('#width_edit').val(width);
                $('#title_mc_edit').val(title);
                window.editor_html_description_edit.setValue(description,-1);
                window.color_edit_spectrum = $('#color_edit').spectrum({
                    type: "text",
                    preferredFormat: "hex",
                    showAlpha: false,
                    showButtons: false,
                    allowEmpty: false
                });
                $('#btn_edit_markers_connections').attr('onclick','save_markers_connections('+id_markers_connections+')');
                $('#btn_delete_markers_connections').attr('onclick','delete_markers_connections('+id_markers_connections+')');
                $('#modal_edit_markers_connections').modal('show');
            });
            window.color_spectrum = $('#color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: false
            });
        });
        $('#modal_add_markers_connections').on('shown.bs.modal', function (e) {
            window.editor_html_description.resize();
        });
    })(jQuery);
</script>