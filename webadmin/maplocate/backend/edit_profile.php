<?php
session_start();
require_once("functions.php");
$role = get_user_role($_SESSION['id_user']);
$id_user_edit = $_SESSION['id_user'];
$user_info = get_user_info($id_user_edit);
$settings = get_settings();
?>

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
                            <input type="text" class="form-control" id="username" value="<?php echo $user_info['username']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="language"><?php echo _("Language"); ?></label>
                            <select class="form-control" id="language">
                                <option <?php echo ($user_info['language']=='') ? 'selected':''; ?> id=""><?php echo _("Default Language"); ?></option>
                                <?php if (check_language_enabled('ar_SA',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='ar_SA') ? 'selected':''; ?> id="ar_SA">Arabic (ar_SA)</option><?php endif; ?>
                                <?php if (check_language_enabled('zh_CN',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='zh_CN') ? 'selected':''; ?> id="zh_CN">Chinese simplified (zh_CN)</option><?php endif; ?>
                                <?php if (check_language_enabled('zh_HK',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='zh_HK') ? 'selected':''; ?> id="zh_HK">Chinese traditional (zh_HK)</option><?php endif; ?>
                                <?php if (check_language_enabled('zh_TW',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='zh_TW') ? 'selected':''; ?> id="zh_TW">Chinese traditional (zh_TW)</option><?php endif; ?>
                                <?php if (check_language_enabled('cs_CZ',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='cs_CZ') ? 'selected':''; ?> id="cs_CZ">Czech (cs_CZ)</option><?php endif; ?>
                                <?php if (check_language_enabled('nl_NL',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='nl_NL') ? 'selected':''; ?> id="nl_NL">Dutch (nl_NL)</option><?php endif; ?>
                                <?php if (check_language_enabled('en_US',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='en_US') ? 'selected':''; ?> id="en_US">English (en_US)</option><?php endif; ?>
                                <?php if (check_language_enabled('en_GB',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='en_GB') ? 'selected':''; ?> id="en_GB">English (en_GB)</option><?php endif; ?>
                                <?php if (check_language_enabled('fil_PH',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='fil_PH') ? 'selected':''; ?> id="fil_PH">Filipino (fil_PH)</option><?php endif; ?>
                                <?php if (check_language_enabled('fr_FR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='fr_FR') ? 'selected':''; ?> id="fr_FR">French (fr_FR)</option><?php endif; ?>
                                <?php if (check_language_enabled('de_DE',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='de_DE') ? 'selected':''; ?> id="de_DE">German (de_DE)</option><?php endif; ?>
                                <?php if (check_language_enabled('hi_IN',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='hi_IN') ? 'selected':''; ?> id="hi_IN">Hindi (hi_IN)</option><?php endif; ?>
                                <?php if (check_language_enabled('hu_HU',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='hu_HU') ? 'selected':''; ?> id="hu_HU">Hungarian (hu_HU)</option><?php endif; ?>
                                <?php if (check_language_enabled('kw_KW',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='kw_KW') ? 'selected':''; ?> id="kw_KW">Kinyarwanda (kw_KW)</option><?php endif; ?>
                                <?php if (check_language_enabled('ko_KR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='ko_KR') ? 'selected':''; ?> id="ko_KR">Korean (ko_KR)</option><?php endif; ?>
                                <?php if (check_language_enabled('id_ID',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='id_ID') ? 'selected':''; ?> id="id_ID">Indonesian (id_ID)</option><?php endif; ?>
                                <?php if (check_language_enabled('it_IT',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='it_IT') ? 'selected':''; ?> id="it_IT">Italian (it_IT)</option><?php endif; ?>
                                <?php if (check_language_enabled('ja_JP',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='ja_JP') ? 'selected':''; ?> id="ja_JP">Japanese (ja_JP)</option><?php endif; ?>
                                <?php if (check_language_enabled('fa_IR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='fa_IR') ? 'selected':''; ?> id="fa_IR">Persian (fa_IR)</option><?php endif; ?>
                                <?php if (check_language_enabled('fi_FI',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='fi_FI') ? 'selected':''; ?> id="fi_FI">Finnish (fi_FI)</option><?php endif; ?>
                                <?php if (check_language_enabled('pl_PL',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='pl_PL') ? 'selected':''; ?> id="pl_PL">Polish (pl_PL)</option><?php endif; ?>
                                <?php if (check_language_enabled('pt_BR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='pt_BR') ? 'selected':''; ?> id="pt_BR">Portuguese Brazilian (pt_BR)</option><?php endif; ?>
                                <?php if (check_language_enabled('pt_PT',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='pt_PT') ? 'selected':''; ?> id="pt_PT">Portuguese European (pt_PT)</option><?php endif; ?>
                                <?php if (check_language_enabled('es_ES',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='es_ES') ? 'selected':''; ?> id="es_ES">Spanish (es_ES)</option><?php endif; ?>
                                <?php if (check_language_enabled('ro_RO',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='ro_RO') ? 'selected':''; ?> id="ro_RO">Romanian (ro_RO)</option><?php endif; ?>
                                <?php if (check_language_enabled('ru_RU',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='ru_RU') ? 'selected':''; ?> id="ru_RU">Russian (ru_RU)</option><?php endif; ?>
                                <?php if (check_language_enabled('sv_SE',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='sv_SE') ? 'selected':''; ?> id="sv_SE">Swedish (sv_SE)</option><?php endif; ?>
                                <?php if (check_language_enabled('tg_TJ',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='tg_TJ') ? 'selected':''; ?> id="tg_TJ">Tajik (tg_TJ)</option><?php endif; ?>
                                <?php if (check_language_enabled('th_TH',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='th_TH') ? 'selected':''; ?> id="tg_TJ">Thai (th_TH)</option><?php endif; ?>
                                <?php if (check_language_enabled('tr_TR',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='tr_TR') ? 'selected':''; ?> id="tr_TR">Turkish (tr_TR)</option><?php endif; ?>
                                <?php if (check_language_enabled('vi_VN',$settings['languages_enabled'])) : ?><option <?php echo ($user_info['language']=='vi_VN') ? 'selected':''; ?> id="vi_VN">Vietnamese (vi_VN)</option><?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _("Password"); ?></label>
                            <button data-toggle="modal" data-target="#modal_change_password" class="btn btn-block btn-primary"><?php echo _("CHANGE"); ?></button>
                        </div>
                    </div>
                </div>
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
                            <input autocomplete="new-password" type="password" minlength="6" required class="form-control" id="password" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repeat_password"><?php echo _("Repeat Password"); ?></label>
                            <input autocomplete="new-password" type="password" minlength="6" required class="form-control" id="repeat_password" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> onclick="change_password();" type="button" class="btn btn-success"><i class="fas fa-key"></i> <?php echo _("Change"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict"; // Start of use strict
        window.user_need_save = false;
        window.id_user_edit = '<?php echo $id_user_edit; ?>';
        $("input[type='text']").change(function(){
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