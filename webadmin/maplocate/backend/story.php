<?php
session_start();
$id_user = $_SESSION['id_user'];
$id_map_sel = $_SESSION['id_map_sel'];
$story = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['story']==0) {
        $story=false;
    }
}
?>

<?php if(!$story): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like you found a glitch in the matrix..."); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-12">
        <a href="#" data-toggle="modal" data-target="#modal_add_markers_story" class="btn btn-block btn-success mb-1"><i class="fa fa-plus"></i> <?php echo _("ADD MARKER TO STORY"); ?></a>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered table-hover" id="markers_story_table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php echo _("Marker"); ?></th>
                        <th><?php echo _("Zoom"); ?></th>
                        <th><?php echo _("Order"); ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_add_markers_story" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Add Marker to Story"); ?></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marker_story_add"><?php echo _("Marker"); ?></label>
                            <select id="marker_story_add" data-live-search="true" data-actions-box="true" class="form-control selectpicker"></select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="zoom_story_add"><?php echo _("Zoom"); ?></label>
                            <select id="zoom_story_add" class="form-control">
                                <option id="1">1</option>
                                <option id="2">2</option>
                                <option id="3">3</option>
                                <option id="4">4</option>
                                <option id="5">5</option>
                                <option id="6">6</option>
                                <option id="7">7</option>
                                <option id="8">8</option>
                                <option id="9">9</option>
                                <option selected id="10">10</option>
                                <option id="11">11</option>
                                <option id="12">12</option>
                                <option id="13">13</option>
                                <option id="14">14</option>
                                <option id="15">15</option>
                                <option id="16">16</option>
                                <option id="17">17</option>
                                <option id="18">18</option>
                                <option id="19">19</option>
                                <option id="20">20</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description_story"><?php echo _("Description"); ?></label>
                            <div id="description_story"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_add_markers_story" onclick="add_markers_story();" type="button" class="btn btn-success"><?php echo _("Yes, Add"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_edit_markers_story" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Edit Marker"); ?></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="zoom_story_edit"><?php echo _("Zoom"); ?></label>
                            <select id="zoom_story_edit" class="form-control">
                                <option id="1">1</option>
                                <option id="2">2</option>
                                <option id="3">3</option>
                                <option id="4">4</option>
                                <option id="5">5</option>
                                <option id="6">6</option>
                                <option id="7">7</option>
                                <option id="8">8</option>
                                <option id="9">9</option>
                                <option id="10">10</option>
                                <option id="11">11</option>
                                <option id="12">12</option>
                                <option id="13">13</option>
                                <option id="14">14</option>
                                <option id="15">15</option>
                                <option id="16">16</option>
                                <option id="17">17</option>
                                <option id="18">18</option>
                                <option id="19">19</option>
                                <option id="20">20</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description_story_edit"><?php echo _("Description"); ?></label>
                            <div id="description_story_edit"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_edit_markers_story" onclick="" type="button" class="btn btn-success"><?php echo _("Yes, Save"); ?></button>
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_delete_markers_story" onclick="" type="button" class="btn btn-danger"><?php echo _("Delete"); ?></button>
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
        window.editor_html_description = null;
        window.editor_html_description_edit = null;
        $(document).ready(function () {
            window.editor_html_description = ace.edit('description_story');
            window.editor_html_description.session.setMode("ace/mode/html");
            window.editor_html_description.session.setUseWrapMode(true);
            window.editor_html_description.session.setOption('indentedSoftWrap', false);
            window.editor_html_description.setOption('enableLiveAutocompletion',true);
            if($('body').hasClass('dark_mode')) {
                window.editor_html_description.setTheme("ace/theme/one_dark");
            }
            window.editor_html_description_edit = ace.edit('description_story_edit');
            window.editor_html_description_edit.session.setMode("ace/mode/html");
            window.editor_html_description_edit.session.setUseWrapMode(true);
            window.editor_html_description_edit.session.setOption('indentedSoftWrap', false);
            window.editor_html_description_edit.setOption('enableLiveAutocompletion',true);
            if($('body').hasClass('dark_mode')) {
                window.editor_html_description_edit.setTheme("ace/theme/one_dark");
            }
            get_markers_story_add();
            $('#markers_story_table').DataTable({
                "order": [[ 2, "desc" ]],
                "pageLength": 100,
                "responsive": true,
                "scrollX": true,
                "processing": true,
                "serverSide": true,
                "ajax": "ajax/get_markers_story.php?id_map="+window.id_map,
                "createdRow": function (row, data, dataIndex) {
                    $(row).attr('data-id', data.DT_RowId);
                    $(row).attr('data-description', data.DT_RowDescription);
                    $(row).attr('data-zoom', data.DT_RowZoom);
                }
            });
            $('#markers_story_table tbody').on('click', 'td', function () {
                if($(this)[0]._DT_CellIndex.column!=2) {
                    var id_marker = $(this).parent().attr("data-id");
                    var zoom = $(this).parent().attr("data-zoom");
                    var description = $(this).parent().attr("data-description");
                    $('#zoom_story_edit option').prop('selected', false);
                    $('#zoom_story_edit option[id=' + zoom + ']').prop('selected', true);
                    window.editor_html_description_edit.setValue(description, -1);
                    $('#btn_edit_markers_story').attr('onclick', 'save_markers_story(' + id_marker + ')');
                    $('#btn_delete_markers_story').attr('onclick', 'delete_markers_story(' + id_marker + ')');
                    $('#modal_edit_markers_story').modal('show');
                }
            });
        });
        $('#modal_add_markers_story').on('shown.bs.modal', function (e) {
            window.editor_html_description.resize();
        });
    })(jQuery);
</script>