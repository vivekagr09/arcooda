<?php
session_start();
require_once("functions.php");
$role = get_user_role($_SESSION['id_user']);
$id_user_edit = $_GET['id'];
$user_info_edit = get_user_info($id_user_edit);
$user_info = get_user_info($_SESSION['id_user']);
if(!isset($_SESSION['lang'])) {
    if(!empty($user_info['language'])) {
        $language = $user_info['language'];
    } else {
        $language = $settings['language'];
    }
} else {
    $language = $_SESSION['lang'];
}
?>

<?php if(($role!='administrator') || (count($user_info_edit)==0)): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like you found a glitch in the matrix..."); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
    <script>
        $('.map_select_header').remove();
    </script>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-cog"></i> <?php echo _("Account"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="username"><?php echo _("Username"); ?></label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user_info_edit['username']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="language"><?php echo _("Language"); ?></label>
                            <select class="form-control" id="language">
                                <option <?php echo ($user_info_edit['language']=='') ? 'selected':''; ?> id=""><?php echo _("Default Language"); ?></option>
                                <?php if (check_language_enabled('ar_SA',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='ar_SA') ? 'selected':''; ?> id="ar_SA">Arabic (ar_SA)</option><?php endif; ?>
                                <?php if (check_language_enabled('zh_CN',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='zh_CN') ? 'selected':''; ?> id="zh_CN">Chinese simplified (zh_CN)</option><?php endif; ?>
                                <?php if (check_language_enabled('zh_HK',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='zh_HK') ? 'selected':''; ?> id="zh_HK">Chinese traditional (zh_HK)</option><?php endif; ?>
                                <?php if (check_language_enabled('zh_TW',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='zh_TW') ? 'selected':''; ?> id="zh_TW">Chinese traditional (zh_TW)</option><?php endif; ?>
                                <?php if (check_language_enabled('cs_CZ',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='cs_CZ') ? 'selected':''; ?> id="cs_CZ">Czech (cs_CZ)</option><?php endif; ?>
                                <?php if (check_language_enabled('nl_NL',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='nl_NL') ? 'selected':''; ?> id="nl_NL">Dutch (nl_NL)</option><?php endif; ?>
                                <?php if (check_language_enabled('en_US',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='en_US') ? 'selected':''; ?> id="en_US">English (en_US)</option><?php endif; ?>
                                <?php if (check_language_enabled('en_GB',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='en_GB') ? 'selected':''; ?> id="en_GB">English (en_GB)</option><?php endif; ?>
                                <?php if (check_language_enabled('fil_PH',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='fil_PH') ? 'selected':''; ?> id="fil_PH">Filipino (fil_PH)</option><?php endif; ?>
                                <?php if (check_language_enabled('fr_FR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='fr_FR') ? 'selected':''; ?> id="fr_FR">French (fr_FR)</option><?php endif; ?>
                                <?php if (check_language_enabled('de_DE',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='de_DE') ? 'selected':''; ?> id="de_DE">German (de_DE)</option><?php endif; ?>
                                <?php if (check_language_enabled('hi_IN',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='hi_IN') ? 'selected':''; ?> id="hi_IN">Hindi (hi_IN)</option><?php endif; ?>
                                <?php if (check_language_enabled('kw_KW',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='kw_KW') ? 'selected':''; ?> id="kw_KW">Kinyarwanda (kw_KW)</option><?php endif; ?>
                                <?php if (check_language_enabled('ko_KR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='ko_KR') ? 'selected':''; ?> id="ko_KR">Korean (ko_KR)</option><?php endif; ?>
                                <?php if (check_language_enabled('id_ID',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='id_ID') ? 'selected':''; ?> id="id_ID">Indonesian (id_ID)</option><?php endif; ?>
                                <?php if (check_language_enabled('hu_HU',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='hu_HU') ? 'selected':''; ?> id="hu_HU">Hungarian (hu_HU)</option><?php endif; ?>
                                <?php if (check_language_enabled('it_IT',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='it_IT') ? 'selected':''; ?> id="it_IT">Italian (it_IT)</option><?php endif; ?>
                                <?php if (check_language_enabled('ja_JP',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='ja_JP') ? 'selected':''; ?> id="ja_JP">Japanese (ja_JP)</option><?php endif; ?>
                                <?php if (check_language_enabled('fa_IR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='fa_IR') ? 'selected':''; ?> id="fa_IR">Persian (fa_IR)</option><?php endif; ?>
                                <?php if (check_language_enabled('fi_FI',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='fi_FI') ? 'selected':''; ?> id="fi_FI">Finnish (fi_FI)</option><?php endif; ?>
                                <?php if (check_language_enabled('pl_PL',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='pl_PL') ? 'selected':''; ?> id="pl_PL">Polish (pl_PL)</option><?php endif; ?>
                                <?php if (check_language_enabled('pt_BR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='pt_BR') ? 'selected':''; ?> id="pt_BR">Portuguese Brazilian (pt_BR)</option><?php endif; ?>
                                <?php if (check_language_enabled('pt_PT',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='pt_PT') ? 'selected':''; ?> id="pt_PT">Portuguese European (pt_PT)</option><?php endif; ?>
                                <?php if (check_language_enabled('es_ES',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='es_ES') ? 'selected':''; ?> id="es_ES">Spanish (es_ES)</option><?php endif; ?>
                                <?php if (check_language_enabled('ro_RO',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='ro_RO') ? 'selected':''; ?> id="ro_RO">Romanian (ro_RO)</option><?php endif; ?>
                                <?php if (check_language_enabled('ru_RU',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='ru_RU') ? 'selected':''; ?> id="ru_RU">Russian (ru_RU)</option><?php endif; ?>
                                <?php if (check_language_enabled('sv_SE',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='sv_SE') ? 'selected':''; ?> id="sv_SE">Swedish (sv_SE)</option><?php endif; ?>
                                <?php if (check_language_enabled('tg_TJ',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='tg_TJ') ? 'selected':''; ?> id="tg_TJ">Tajik (tg_TJ)</option><?php endif; ?>
                                <?php if (check_language_enabled('th_TH',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='th_TH') ? 'selected':''; ?> id="th_TH">Thai (th_TH)</option><?php endif; ?>
                                <?php if (check_language_enabled('tr_TR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='tr_TR') ? 'selected':''; ?> id="tr_TR">Turkish (tr_TR)</option><?php endif; ?>
                                <?php if (check_language_enabled('vi_VN',$settings['languages_enabled'])) : ?><option <?php echo ($user_info_edit['language']=='vi_VN') ? 'selected':''; ?> id="vi_VN">Vietnamese (vi_VN)</option><?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="role"><?php echo _("Role"); ?></label>
                            <select class="form-control" id="role">
                                <option <?php echo ($user_info_edit['role']=='customer') ? 'selected' : '' ; ?> id="customer"><?php echo _("Customer"); ?></option>
                                <option <?php echo ($user_info_edit['role']=='administrator') ? 'selected' : '' ; ?> id="administrator"><?php echo _("Administrator"); ?></option>
                                <option <?php echo ($user_info_edit['role']=='editor') ? 'selected' : '' ; ?> id="editor"><?php echo _("Editor"); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <br>
                            <button data-toggle="modal" data-target="#modal_change_password" class="btn btn-block btn-primary"><?php echo _("CHANGE PASSWORD"); ?></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="active"><?php echo _("Active"); ?></label><br>
                            <input <?php echo ($user_info_edit['active']) ? 'checked' : '' ; ?> type="checkbox" id="active" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="<?php echo ($user_info_edit['role']=='editor') ? 'display:none' : ''; ?>">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?php echo _("Maps"); ?></div>
                        <div id="num_maps" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><?php echo _("Markers"); ?></div>
                        <div id="num_markers" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row assign_map_div" style="display: <?php echo ($user_info_edit['role']=='editor') ? 'block' : 'none'; ?>">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo _("Assigned Maps"); ?></h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="assign_map_table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php echo _("Assign"); ?></th>
                        <th style="min-width: 350px"><?php echo _("Map"); ?></th>
                        <th><?php echo _("Edit Map"); ?></th>
                        <th><?php echo _("Create Markers"); ?></th>
                        <th><?php echo _("Edit Markers"); ?></th>
                        <th><?php echo _("Delete Markers"); ?></th>
                        <th><?php echo _("Connections"); ?></th>
                        <th><?php echo _("Geometries"); ?></th>
                        <th><?php echo _("Story"); ?></th>
                        <th><?php echo _("Categories"); ?></th>
                        <th><?php echo _("Icons Library"); ?></th>
                        <th><?php echo _("Reviews"); ?></th>
                        <th><?php echo _("Publish"); ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_change_password" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Change Password"); ?></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password"><?php echo _("Password"); ?></label>
                            <input type="password" class="form-control" id="password" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repeat_password"><?php echo _("Repeat password"); ?></label>
                            <input type="password" class="form-control" id="repeat_password" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> onclick="change_password();" type="button" class="btn btn-success"><?php echo _("Change"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_delete_user" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Delete User"); ?></h5>
            </div>
            <div class="modal-body">
                <p><?php echo _("Are you sure you want to delete the user?"); ?><br>
                <?php echo _("Attention: all the maps of that user will be deleted!"); ?></p>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_delete_user" onclick="" type="button" class="btn btn-danger"><?php echo _("Yes, Delete"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict"; // Start of use strict
        window.user_need_save = false;
        window.id_user_edit = '<?php echo $id_user_edit; ?>';
        $(document).ready(function () {
            get_user_stats(window.id_user_edit);
            $('#assign_map_table').DataTable({
                "order": [[ 1, "asc" ]],
                "responsive": true,
                "scrollX": true,
                "processing": true,
                "searching": true,
                "serverSide": true,
                "ajax": "ajax/get_assigned_map.php?id_user_edit="+window.id_user_edit,
                "drawCallback": function() {
                    $('.assigned_map').change(function() {
                        var checked = this.checked;
                        if(checked) checked=1; else checked=0;
                        var id_map = $(this).attr('id');
                        assign_map_editor(id_map,checked);
                        $('.assigned_map').each(function () {
                            var checked = this.checked;
                            var id_map = $(this).attr('id');
                            if(checked) {
                                $('.editor_permissions[id='+id_map+']').prop('disabled',false);
                            } else {
                                $('.editor_permissions[id='+id_map+']').prop('disabled',true);
                            }
                        });
                    });
                    $('.editor_permissions').change(function() {
                        var checked = this.checked;
                        if(checked) checked=1; else checked=0;
                        var id_map = $(this).attr('id');
                        var field = $(this).attr('class');
                        field = field.replace('editor_permissions ','');
                        set_permission_map_editor(id_map,field,checked);
                    });
                    $('#assign_map_table tr').on('click',function () {
                        $('#assign_map_table tr').removeClass('highlight');
                        $(this).addClass('highlight');
                    });
                    $('.assigned_map').each(function () {
                        var checked = this.checked;
                        var id_map = $(this).attr('id');
                        if(checked) {
                            $('.editor_permissions[id='+id_map+']').prop('disabled',false);
                        } else {
                            $('.editor_permissions[id='+id_map+']').prop('disabled',true);
                        }
                    });
                },
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
                }
            });
        });
        $("input[type='text']").change(function(){
            window.user_need_save = true;
        });
        $("input[type='checkbox']").change(function(){
            window.user_need_save = true;
        });
        $("select").change(function(){
            window.user_need_save = true;
        });
        $(window).on('beforeunload', function(){
            if(window.user_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });
    })(jQuery); // End of use strict
</script>