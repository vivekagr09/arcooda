<?php
session_start();
$maps = get_maps($id_user);
$count_maps = count($maps);
$array_list_m = array();
if ($count_maps==1) {
    $id_map_sel = $maps[0]['id'];
    $name_map_sel = $maps[0]['name'];
    $_SESSION['id_map_sel'] = $id_map_sel;
    $_SESSION['name_map_sel'] = $name_map_sel;
    $array_list_m[] = array("id"=>$id_map_sel,"name"=>$name_map_sel);
} else {
    if(isset($_SESSION['id_map_sel'])) {
        $id_map_sel = $_SESSION['id_map_sel'];
        $name_map_sel = $_SESSION['name_map_sel'];
    } else {
        $id_map_sel = $maps[0]['id'];
        $name_map_sel = $maps[0]['name'];
        $_SESSION['id_map_sel'] = $id_map_sel;
        $_SESSION['name_map_sel'] = $name_map_sel;
    }
    foreach ($maps as $map) {
        $id_map = $map['id'];
        $name_map = $map['name'];
        $array_list_m[] = array("id"=>$id_map,"name"=>$name_map);
    }
}
$can_create = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['create_markers']==0) {
        $can_create=false;
    }
}
?>

<?php if(!$can_create): ?>
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
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Import</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <p><?php echo sprintf(_('Use %s template excel or the exported csv to import multiple markers at once.'),'<a target="_blank" href="import_template.xlsx">'._("this").'</a>'); ?></p>
                            <form id="frm" action="ajax/import_markers.php" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="file" class="form-control" id="txtFile" name="txtFile" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload" value="<?php echo _("Import Markers"); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="preview text-center">
                                            <div class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                <div class="progress-bar" id="progressBar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div>
                                            <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_import_marker" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Import Markers"); ?></h5>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button onclick="exit_import()" type="button" class="btn btn-success"><?php echo _("Ok"); ?></button>
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

        });

        $('body').on('submit','#frm',function(e){
            e.preventDefault();
            $('#error').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        $('#modal_import_marker p').html(evt.target.responseText);
                        $('#modal_import_marker').modal("show");
                    }
                }
                upadte_progressbar(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error('upload failed');
                upadte_progressbar(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error('upload aborted');
                upadte_progressbar(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar(value){
            if(value==100) {
                $('#progressBar').css('width',value+'%').html('<?php echo _("IMPORTING MARKERS ... DO NOT CLOSE THIS WINDOW!!!!"); ?>');
            } else {
                $('#progressBar').css('width',value+'%').html(value+'%');
            }
            if(value==0){
                $('.progress').hide();
            }else{
                $('.progress').show();
            }
        }

        function show_error(error){
            $('.progress').hide();
            $('#error').show();
            $('#error').html(error);
        }

        window.exit_import = function() {
            location.href = "index.php?p=markers";
        }

    })(jQuery);
</script>