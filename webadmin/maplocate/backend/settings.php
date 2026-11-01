<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once("functions.php");
$role = get_user_role($_SESSION['id_user']);
$settings = get_settings();
if (is_ssl()) { $protocol = 'https'; } else { $protocol = 'http'; }
$callback_url = $protocol ."://". $_SERVER['SERVER_NAME'] . str_replace("backend/index.php","backend/social_auth.php",$_SERVER['SCRIPT_NAME']);
$domain = $_SERVER['SERVER_NAME'];
?>

<?php if($role!='administrator'): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like that you do not have permission to access this page"); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
<?php die(); endif; ?>

<?php if($_SESSION['input_license']==1) : ?>
    <div class="card bg-warning text-white shadow mb-3">
        <div class="card-body">
            <?php echo _("Please enter a valid purchase code to continue using the application."); ?>
        </div>
    </div>
<?php endif; ?>

<ul class="nav bg-white nav-pills nav-fill mb-2 <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
    <?php if(!$demo) : ?>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#license_tab"><i class="fas fa-key"></i> <?php echo strtoupper(_("LICENSE")); ?></a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
        <a class="nav-link active" data-toggle="pill" href="#settings_tab"><i class="fas fa-cogs"></i> <?php echo strtoupper(_("GENERAL")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#style_tab"><i class="fas fa-palette"></i> <?php echo strtoupper(_("STYLE")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#localization_tab"><i class="fas fa-language"></i> <?php echo strtoupper(_("LOCALIZATION")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#tools_tab"><i class="fas fa-tools"></i> <?php echo strtoupper(_("TOOLS")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#custom_tab"><i class="fas fa-file-code"></i> <?php echo strtoupper(_("CUSTOM CSS / JS")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#legal_tab"><i class="fas fa-balance-scale"></i> <?php echo strtoupper(_("LEGAL")); ?></a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane" id="tools_tab">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-directions"></i> Openroute Service</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="openrouteservice_api_key"><?php echo _("API Key"); ?> (<?php echo sprintf(_("Request it from %s"),'<a class="text-primary" target="_blank" href="https://openrouteservice.org">Openroute Service <i class="fas fa-external-link-square-alt"></i></a>'); ?>)</label>
                                    <input autocomplete="new-password" class="form-control" type="password" id="openrouteservice_api_key" value="<?php echo ($settings['openrouteservice_api_key']!='') ? 'keep_openrouteservice_api_key' : ''; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane <?php echo ($_SESSION['input_license']==1) ? 'active' : ''; ?>" id="license_tab">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-key"></i> <?php echo _("License"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_code"><?php echo _("Purchase Code"); ?> <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">(<?php echo _("Where i can find it?"); ?>)</a></label>
                                    <input type="text" class="form-control" id="purchase_code" value="<?php echo $settings['purchase_code']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="color: white">.</label>
                                    <button id="btn_check_license" onclick="check_license()" class="btn btn-primary btn-block"><?php echo _("CHECK"); ?></button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label><?php echo _("Status"); ?></label><br>
                                <div id="license_status" class="mt-2">
                                    <?php
                                    if($settings['purchase_code']=='') {
                                        echo "<i class=\"fas fa-circle\"></i> Unchecked";
                                    } else {
                                        if($settings['license']=='') {
                                            echo "<i style='color: red' class=\"fas fa-circle\"></i> Invalid License";
                                        } else {
                                            $y0='';if(array_key_exists(base64_decode('U0VSVkVSX0FERFI='),$_SERVER)){$y0=$_SERVER[base64_decode('U0VSVkVSX0FERFI=')];if(!filter_var($y0,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)){$y0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}}elseif(array_key_exists(base64_decode('TE9DQUxfQUREUg=='),$_SERVER)){$y0=$_SERVER[base64_decode('TE9DQUxfQUREUg==')];}elseif(array_key_exists(base64_decode('U0VSVkVSX05BTUU='),$_SERVER)){$y0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);}else{if(stristr(PHP_OS,base64_decode('V0lO'))){$y0=gethostbyname(php_uname(base64_decode('bg==')));}else{$q1=shell_exec(base64_decode('L3NiaW4vaWZjb25maWcgZXRoMA=='));preg_match(base64_decode('L2FkZHI6KFtcZFwuXSspLw=='),$q1,$f2);$y0=$f2[1];}}$k4=$settings;$q3=$y0.base64_decode('UlI=').$k4[base64_decode('cHVyY2hhc2VfY29kZQ==')];$w5=password_verify($q3,$k4[base64_decode('bGljZW5zZQ==')]);$q3=$y0.base64_decode('UkU=').$k4[base64_decode('cHVyY2hhc2VfY29kZQ==')];$j6=password_verify($q3,$k4[base64_decode('bGljZW5zZQ==')]);$q3=$y0.base64_decode('RQ==').$k4[base64_decode('cHVyY2hhc2VfY29kZQ==')];$u7=password_verify($q3,$k4[base64_decode('bGljZW5zZQ==')]);if($w5||$j6){echo base64_decode('PGkgc3R5bGU9J2NvbG9yOiBncmVlbicgY2xhc3M9ImZhcyBmYS1jaXJjbGUiPjwvaT4gVmFsaWQsIFJlZ3VsYXIgTGljZW5zZQ==');}else if($u7){echo base64_decode('PGkgc3R5bGU9J2NvbG9yOiBncmVlbicgY2xhc3M9ImZhcyBmYS1jaXJjbGUiPjwvaT4gVmFsaWQsIEV4dGVuZGVkIExpY2Vuc2U=');}else{echo base64_decode('PGkgc3R5bGU9J2NvbG9yOiByZWQnIGNsYXNzPSJmYXMgZmEtY2lyY2xlIj48L2k+IEludmFsaWQgTGljZW5zZQ==');}
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="custom_tab">
        <div class="row mt-2 mb-4">
            <div class="col-md-6">
                <button id="btn_custom_viewer" onclick="switch_custom_mode('viewer');" class="btn btn-block btn-primary"><?php echo _("Viewer"); ?></button>
            </div>
            <div class="col-md-6">
                <button id="btn_custom_backend" onclick="switch_custom_mode('backend');" class="btn btn-block btn-outline-primary"><?php echo _("Backend"); ?></button>
            </div>
        </div>
        <div id="custom_viewer_div">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-css3-alt"></i> <?php echo _("Custom CSS"); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-1">
                                    <select onchange="change_editor_css();" class="form-control" id="css_name">
                                        <option id="css_custom"><?php echo _("General (affects all maps)"); ?></option>
                                        <?php echo get_maps_options_css(); ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div style="position: relative;width: 100%;height: 400px;" class="editors_css" id="custom"><?php echo get_editor_css_content('custom'); ?></div>
                                    <?php echo get_maps_editors_css(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-js-square"></i> <?php echo _("Custom JS"); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-1">
                                    <select onchange="change_editor_js();" class="form-control" id="js_name">
                                        <option id="js_custom"><?php echo _("General (affects all maps)"); ?></option>
                                        <?php echo get_maps_options_js(); ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div style="position: relative;width: 100%;height: 400px;" class="editors_js" id="custom_js"><?php echo htmlspecialchars(get_editor_js_content('custom')); ?></div>
                                    <?php echo get_maps_editors_js(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="far fa-file-code"></i> <?php echo _("Custom Head Elements"); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-1">
                                    <select onchange="change_editor_head();" class="form-control" id="head_name">
                                        <option id="head_custom"><?php echo _("General (affects all maps)"); ?></option>
                                        <?php echo get_maps_options_head(); ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div style="position: relative;width: 100%;height: 400px;" class="editors_head" id="custom_head"><?php echo htmlspecialchars(get_editor_head_content('custom')); ?></div>
                                    <?php echo get_maps_editors_head(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-none" id="custom_backend_div">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-css3-alt"></i> <?php echo _("Custom CSS"); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="position: relative;width: 100%;height: 400px;" class="editors_css" id="custom_b"><?php echo htmlspecialchars(get_editor_css_content('custom_b')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-js-square"></i> <?php echo _("Custom JS"); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="position: relative;width: 100%;height: 400px;" class="editors_js" id="custom_b_js"><?php echo htmlspecialchars(get_editor_js_content('custom_b')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="far fa-file-code"></i> <?php echo _("Custom Head Elements"); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="position: relative;width: 100%;height: 400px;" class="editors_head" id="custom_b_head"><?php echo htmlspecialchars(get_editor_head_content('custom_b')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="legal_tab">
        <ul class="nav bg-white nav-pills nav-fill mb-2">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#terms_and_conditions_tab"><i class="fas fa-file-contract"></i> <?php echo strtoupper(_("Terms and Conditions")); ?> <i title="<?php echo _("visible on backend footer"); ?>" class="help_t fas fa-question-circle"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#privacy_policy_tab"><i class="fas fa-user-secret"></i> <?php echo strtoupper(_("Privacy Policy")); ?> <i title="<?php echo _("visible on backend footer"); ?>" class="help_t fas fa-question-circle"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#cookie_policy_tab"><i class="fas fa-cookie"></i> <?php echo strtoupper(_("Cookie")); ?></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="terms_and_conditions_tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div id="terms_and_conditions"><?php echo $settings['terms_and_conditions']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="privacy_policy_tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div id="privacy_policy"><?php echo $settings['privacy_policy']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="cookie_policy_tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fa-regular fa-window-maximize"></i> <?php echo _("Cookie Consent"); ?></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cookie_consent"><?php echo _("Enable"); ?> <i title="<?php echo _("enable cookie consent modal on the backend"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                            <input type="checkbox" id="cookie_consent" <?php echo ($settings['cookie_consent'])?'checked':''; ?> />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fa-regular fa-file-lines"></i> <?php echo _("Cookie Policy"); ?> <i title="<?php echo _("visible in backend footer and / or into consent modal"); ?>" class="help_t fas fa-question-circle"></i></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div id="cookie_policy"><?php echo $settings['cookie_policy']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="localization_tab">
        <div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
            <div class="col-md-12 mb-4">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-language"></i> <?php echo _("Localization"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="timezone"><?php echo _("Timezone"); ?></label> <i style="font-size:12px;" id="preview_timezone"><?php echo formatTime("dd MMM y - HH:mm",$settings['language'],time()); ?></i>
                                    <select onchange="changeTimezone();" class="form-control" id="timezone">
                                        <?php
                                        $timezones = timezone_identifiers_list();
                                        if(empty($settings['timezone'])) {
                                            $currentTimezone = date_default_timezone_get();
                                        } else {
                                            $currentTimezone = $settings['timezone'];
                                        }
                                        foreach ($timezones as $timezone) {
                                            $selected = ($timezone === $currentTimezone) ? 'selected' : '';
                                            echo "<option value=\"$timezone\" $selected>$timezone</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="language"><?php echo _("Default Language"); ?></label>
                                    <select class="form-control" id="language">
                                        <option <?php echo ($settings['language']=='ar_SA') ? 'selected':''; ?> id="ar_SA">Arabic (ar_SA)</option>
                                        <option <?php echo ($settings['language']=='zh_CN') ? 'selected':''; ?> id="zh_CN">Chinese simplified (zh_CN)</option>
                                        <option <?php echo ($settings['language']=='zh_HK') ? 'selected':''; ?> id="zh_HK">Chinese traditional (zh_HK)</option>
                                        <option <?php echo ($settings['language']=='zh_TW') ? 'selected':''; ?> id="zh_TW">Chinese traditional (zh_TW)</option>
                                        <option <?php echo ($settings['language']=='cs_CZ') ? 'selected':''; ?> id="cs_CZ">Czech (cs_CZ)</option>
                                        <option <?php echo ($settings['language']=='nl_NL') ? 'selected':''; ?> id="nl_NL">Dutch (nl_NL)</option>
                                        <option <?php echo ($settings['language']=='en_US') ? 'selected':''; ?> id="en_US">English (en_US)</option>
                                        <option <?php echo ($settings['language']=='en_GB') ? 'selected':''; ?> id="en_GB">English (en_GB)</option>
                                        <option <?php echo ($settings['language']=='fil_PH') ? 'selected':''; ?> id="fil_PH">Filipino (fil_PH)</option>
                                        <option <?php echo ($settings['language']=='fr_FR') ? 'selected':''; ?> id="fr_FR">French (fr_FR)</option>
                                        <option <?php echo ($settings['language']=='de_DE') ? 'selected':''; ?> id="de_DE">German (de_DE)</option>
                                        <option <?php echo ($settings['language']=='hi_IN') ? 'selected':''; ?> id="hi_IN">Hindi (hi_IN)</option>
                                        <option <?php echo ($settings['language']=='hu_HU') ? 'selected':''; ?> id="hu_HU">Hungarian (hu_HU)</option>
                                        <option <?php echo ($settings['language']=='rw_RW') ? 'selected':''; ?> id="rw_RW">Kinyarwanda (rw_RW)</option>
                                        <option <?php echo ($settings['language']=='ko_KR') ? 'selected':''; ?> id="ko_KR">Korean (ko_KR)</option>
                                        <option <?php echo ($settings['language']=='id_ID') ? 'selected':''; ?> id="id_ID">Indonesian (id_ID)</option>
                                        <option <?php echo ($settings['language']=='it_IT') ? 'selected':''; ?> id="it_IT">Italian (it_IT)</option>
                                        <option <?php echo ($settings['language']=='ja_JP') ? 'selected':''; ?> id="ja_JP">Japanese (ja_JP)</option>
                                        <option <?php echo ($settings['language']=='fa_IR') ? 'selected':''; ?> id="fa_IR">Persian (fa_IR)</option>
                                        <option <?php echo ($settings['language']=='fi_FI') ? 'selected':''; ?> id="fi_FI">Finnish (fi_FI)</option>
                                        <option <?php echo ($settings['language']=='pl_PL') ? 'selected':''; ?> id="pl_PL">Polish (pl_PL)</option>
                                        <option <?php echo ($settings['language']=='pt_BR') ? 'selected':''; ?> id="pt_BR">Portuguese Brazilian (pt_BR)</option>
                                        <option <?php echo ($settings['language']=='pt_PT') ? 'selected':''; ?> id="pt_PT">Portuguese European (pt_PT)</option>
                                        <option <?php echo ($settings['language']=='es_ES') ? 'selected':''; ?> id="es_ES">Spanish (es_ES)</option>
                                        <option <?php echo ($settings['language']=='ro_RO') ? 'selected':''; ?> id="ro_RO">Romanian (ro_RO)</option>
                                        <option <?php echo ($settings['language']=='ru_RU') ? 'selected':''; ?> id="ru_RU">Russian (ru_RU)</option>
                                        <option <?php echo ($settings['language']=='sv_SE') ? 'selected':''; ?> id="sv_SE">Swedish (sv_SE)</option>
                                        <option <?php echo ($settings['language']=='tg_TJ') ? 'selected':''; ?> id="tg_TJ">Tajik (tg_TJ)</option>
                                        <option <?php echo ($settings['language']=='th_TH') ? 'selected':''; ?> id="th_TH">Thai (th_TH)</option>
                                        <option <?php echo ($settings['language']=='tr_TR') ? 'selected':''; ?> id="tr_TR">Turkish (tr_TR)</option>
                                        <option <?php echo ($settings['language']=='vi_VN') ? 'selected':''; ?> id="vi_VN">Vietnamese (vi_VN)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="language_domain"><?php echo _("Translation Type"); ?></label>
                                    <select class="form-control" id="language_domain">
                                        <option <?php echo ($settings['language_domain']=='default') ? 'selected':''; ?> id="default_lang">Default</option>
                                        <option <?php echo ($settings['language_domain']=='custom') ? 'selected':''; ?> id="custom_lang">Custom</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="languages_enabled"><?php echo _("Languages Enabled"); ?></label>
                                    <select style="height: 125px" multiple class="form-control selectpicker" id="languages_enabled" data-actions-box="true" data-selected-text-format="count > 3" data-count-selected-text="{0} <?php echo _("items selected"); ?>" data-deselect-all-text="<?php echo _("Deselect All"); ?>" data-select-all-text="<?php echo _("Select All"); ?>" data-none-selected-text="<?php echo _("Nothing selected"); ?>" data-none-results-text="<?php echo _("No results matched"); ?> {0}">
                                        <option <?php echo (check_language_enabled('ar_SA',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ar_SA">Arabic (ar_SA)</option>
                                        <option <?php echo (check_language_enabled('zh_CN',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_zh_CN">Chinese simplified (zh_CN)</option>
                                        <option <?php echo (check_language_enabled('zh_HK',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_zh_HK">Chinese traditional (zh_HK)</option>
                                        <option <?php echo (check_language_enabled('zh_TW',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_zh_TW">Chinese traditional (zh_TW)</option>
                                        <option <?php echo (check_language_enabled('cs_CZ',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_cs_CZ">Czech (cs_CZ)</option>
                                        <option <?php echo (check_language_enabled('nl_NL',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_nl_NL">Dutch (nl_NL)</option>
                                        <option <?php echo (check_language_enabled('en_US',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_en_US">English (en_US)</option>
                                        <option <?php echo (check_language_enabled('en_GB',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_en_GB">English (en_GB)</option>
                                        <option <?php echo (check_language_enabled('fil_PH',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_fil_PH">Filipino (fil_PH)</option>
                                        <option <?php echo (check_language_enabled('fr_FR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_fr_FR">French (fr_FR)</option>
                                        <option <?php echo (check_language_enabled('de_DE',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_de_DE">German (de_DE)</option>
                                        <option <?php echo (check_language_enabled('hi_IN',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_hi_IN">Hindi (hi_IN)</option>
                                        <option <?php echo (check_language_enabled('hu_HU',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_hu_HU">Hungarian (hu_HU)</option>
                                        <option <?php echo (check_language_enabled('rw_RW',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_rw_RW">Kinyarwanda (rw_RW)</option>
                                        <option <?php echo (check_language_enabled('ko_KR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ko_KR">Korean (ko_KR)</option>
                                        <option <?php echo (check_language_enabled('id_ID',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_id_ID">Indonesian (id_ID)</option>
                                        <option <?php echo (check_language_enabled('it_IT',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_it_IT">Italian (it_IT)</option>
                                        <option <?php echo (check_language_enabled('ja_JP',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ja_JP">Japanese (ja_JP)</option>
                                        <option <?php echo (check_language_enabled('fa_IR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_fa_IR">Persian (fa_IR)</option>
                                        <option <?php echo (check_language_enabled('fi_FI',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_fi_FI">Finnish (fi_FI)</option>
                                        <option <?php echo (check_language_enabled('pl_PL',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_pl_PL">Polish (pl_PL)</option>
                                        <option <?php echo (check_language_enabled('pt_BR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_pt_BR">Portuguese Brazilian (pt_BR)</option>
                                        <option <?php echo (check_language_enabled('pt_PT',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_pt_PT">Portuguese European (pt_PT)</option>
                                        <option <?php echo (check_language_enabled('es_ES',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_es_ES">Spanish (es_ES)</option>
                                        <option <?php echo (check_language_enabled('ro_RO',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ro_RO">Romanian (ro_RO)</option>
                                        <option <?php echo (check_language_enabled('ru_RU',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_ru_RU">Russian (ru_RU)</option>
                                        <option <?php echo (check_language_enabled('sv_SE',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_sv_SE">Swedish (sv_SE)</option>
                                        <option <?php echo (check_language_enabled('tg_TJ',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_tg_TJ">Tajik (tg_TJ)</option>
                                        <option <?php echo (check_language_enabled('th_TH',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_th_TH">Thai (th_TH)</option>
                                        <option <?php echo (check_language_enabled('tr_TR',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_tr_TR">Turkish (tr_TR)</option>
                                        <option <?php echo (check_language_enabled('vi_VN',$settings['languages_enabled'])) ? 'selected':''; ?> id="ls_vi_VN">Vietnamese (vi_VN)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p>
                                    If you want to edit translation file you need to follow this instructions:<br>
                                    1) NEW CUSTOM TRANSLATION: Copy the file <i>locale/lang_code/LC_MESSAGES/<b>default.po</b></i> to your computer and rename it to <b>custom.po</b><br>
                                    or<br>
                                    1) EXISTING CUSTOM TRANSLATION: Execute this command <b>msgmerge --update locale/lang_code/LC_MESSAGES/custom.po locale/sml.pot</b> to merge the new strings with your existing <b>custom.po</b> translation file<br>
                                    2) Edit the file <b>custom.po</b> with a text editor or with a POEditor like <a target="_blank" href="https://poedit.net/">this one</a><br>
                                    3) Compile and generate the file <b>custom.mo</b> with the POEditor or with this command <b>msgfmt custom.po --output-file=custom.mo</b><br>
                                    4) Copy the files <b>custom.po</b> and <b>custom.mo</b> to <i>locale/lang_code/LC_MESSAGES/</i><br>
                                    5) Change Translation Type to <b>Custom</b><br>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane <?php echo ($_SESSION['input_license']==1) ? '' : 'active'; ?>" id="settings_tab">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog"></i> <?php echo _("General"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="font_provider"><?php echo _("Font Provider"); ?></label><br>
                                <select id="font_provider" class="form-control" onchange="change_font_provider();">
                                    <option id="systems" <?php echo ($settings['font_provider']=='systems') ? 'selected' : ''; ?>>System Fonts</option>
                                    <option id="google" <?php echo ($settings['font_provider']=='google') ? 'selected' : ''; ?>>Google Fonts</option>
                                    <option id="collabs" <?php echo ($settings['font_provider']=='collabs') ? 'selected' : ''; ?>>CoolLabs Fonts</option>
                                </select>
                            </div>
                            <?php $array_share_providers = explode(",",$settings['share_providers']); ?>
                            <div class="col-md-4">
                                <label for="share_providers"><?php echo _("Share Providers"); ?></label><br>
                                <select id="share_providers" data-iconBase="fa" data-tickIcon="fa-check" data-actions-box="true" data-selected-text-format="count > 8" data-count-selected-text="{0} <?php echo _("items selected"); ?>" data-deselect-all-text="<?php echo _("Deselect All"); ?>" data-select-all-text="<?php echo _("Select All"); ?>" data-none-selected-text="<?php echo _("Nothing selected"); ?>" data-none-results-text="<?php echo _("No results matched"); ?> {0}" class="form-control selectpicker" multiple>
                                    <option data-icon="fas fa-link" id="provider_copy_link" <?php echo (in_array('copy_link',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Copy Link"); ?></option>
                                    <option data-icon="fas fa-envelope" id="provider_email" <?php echo (in_array('email',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Email"); ?></option>
                                    <option data-icon="fab fa-whatsapp" id="provider_whatsapp" <?php echo (in_array('whatsapp',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Whatsapp"); ?></option>
                                    <option data-icon="fab fa-facebook" id="provider_facebook" <?php echo (in_array('facebook',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Facebook"); ?></option>
                                    <option data-icon="fab fa-threads" id="provider_threads" <?php echo (in_array('threads',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Threads"); ?></option>
                                    <option data-icon="fab fa-x-twitter" id="provider_twitter" <?php echo (in_array('twitter',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Twitter"); ?></option>
                                    <option data-icon="fab fa-linkedin" id="provider_linkedin" <?php echo (in_array('linkedin',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Linkedin"); ?></option>
                                    <option data-icon="fab fa-telegram" id="provider_telegram" <?php echo (in_array('telegram',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Telegram"); ?></option>
                                    <option data-icon="fab fa-facebook-messenger" id="provider_facebook_messenger" <?php echo (in_array('facebook_messenger',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Facebook Messenger"); ?></option>
                                    <option data-icon="fab fa-pinterest" id="provider_pinterest" <?php echo (in_array('pinterest',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Pinterest"); ?></option>
                                    <option data-icon="fab fa-reddit" id="provider_reddit" <?php echo (in_array('reddit',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Reddit"); ?></option>
                                    <option data-icon="fab fa-line" id="provider_line" <?php echo (in_array('line',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Line"); ?></option>
                                    <option data-icon="fab fa-viber" id="provider_viber" <?php echo (in_array('viber',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Viber"); ?></option>
                                    <option data-icon="fab fa-vk" id="provider_vk" <?php echo (in_array('vk',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("VK"); ?></option>
                                    <option data-icon="fab fa-qq" id="provider_qzone" <?php echo (in_array('qzone',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Qzone"); ?></option>
                                    <option data-icon="fab fa-weixin" id="provider_wechat" <?php echo (in_array('wechat',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Wechat"); ?></option>
                                </select>
                                <script type="text/javascript">$('#share_providers').selectpicker('render');</script>
                            </div>
                            <div id="ga_tracking_id_div" class="col-md-4">
                                <div class="form-group">
                                    <label for="ga_tracking_id"><?php echo _("Google Analytics Tracking ID"); ?> <i title="<?php echo _("Google Analytics Tracking ID (G-XXXXXXXXX)."); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="text" class="form-control" id="ga_tracking_id" value="<?php echo $settings['ga_tracking_id']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="furl_blacklist"><?php echo _("Friendly Urls Blacklist"); ?></label>
                                    <input type="text" class="form-control" id="furl_blacklist" placeholder="<?php echo _("Enter friendly urls separated by comma"); ?>" value="<?php echo $settings['furl_blacklist']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="style_tab">
        <div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
            <div class="col-md-12 mb-4">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-sketch"></i> <?php echo _("Branding"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name"><?php echo _("Application Name"); ?></label>
                                    <input type="text" class="form-control" id="name" value="<?php echo $settings['name']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sidebar"><?php echo _("Sidebar"); ?></label>
                                    <select onchange="change_sidebar();" class="form-control" id="sidebar">
                                        <option <?php echo ($settings['sidebar']=='gradient') ? 'selected' : ''; ?> id="gradient"><?php echo _("Gradient"); ?></option>
                                        <option <?php echo ($settings['sidebar']=='flat') ? 'selected' : ''; ?> id="flat"><?php echo _("Flat"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dark_mode"><?php echo _("Dark Mode"); ?></label><br>
                                    <select class="form-control" id="dark_mode">
                                        <option <?php echo ($settings['dark_mode']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Disabled"); ?></option>
                                        <option <?php echo ($settings['dark_mode']==1) ? 'selected' : ''; ?> id="1"><?php echo _("Enabled"); ?></option>
                                        <option <?php echo ($settings['dark_mode']==2) ? 'selected' : ''; ?> id="2"><?php echo _("Forced (only dark mode)"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="font_backend"><?php echo _("Font"); ?></label><br>
                                        <input type="text" class="form-control <?php echo ($settings['font_provider']=='systems') ? 'd-none' : ''; ?>" id="font_backend" value="<?php echo $settings['font_backend']; ?>" />
                                        <select onchange="apply_system_font();" class="form-control <?php echo ($settings['font_provider']=='systems') ? '' : 'd-none'; ?>" id="font_backend_system">
                                            <option id="Arial" <?php echo ($settings['font_backend'] === 'Arial') ? 'selected' : ''; ?>>Arial</option>
                                            <option id="Helvetica" <?php echo ($settings['font_backend'] === 'Helvetica') ? 'selected' : ''; ?>>Helvetica</option>
                                            <option id="Tahoma" <?php echo ($settings['font_backend'] === 'Tahoma') ? 'selected' : ''; ?>>Tahoma</option>
                                            <option id="Verdana" <?php echo ($settings['font_backend'] === 'Verdana') ? 'selected' : ''; ?>>Verdana</option>
                                            <option id="Geneva" <?php echo ($settings['font_backend'] === 'Geneva') ? 'selected' : ''; ?>>Geneva</option>
                                            <option id="Calibri" <?php echo ($settings['font_backend'] === 'Calibri') ? 'selected' : ''; ?>>Calibri</option>
                                            <option id="Trebuchet MS" <?php echo ($settings['font_backend'] === 'Trebuchet MS') ? 'selected' : ''; ?>>Trebuchet MS</option>
                                            <option id="Times New Roman" <?php echo ($settings['font_backend'] === 'Times New Roman') ? 'selected' : ''; ?>>Times New Roman</option>
                                            <option id="Georgia" <?php echo ($settings['font_backend'] === 'Georgia') ? 'selected' : ''; ?>>Georgia</option>
                                            <option id="Garamond" <?php echo ($settings['font_backend'] === 'Garamond') ? 'selected' : ''; ?>>Garamond</option>
                                            <option id="Book Antiqua" <?php echo ($settings['font_backend'] === 'Book Antiqua') ? 'selected' : ''; ?>>Book Antiqua</option>
                                            <option id="Palatino Linotype" <?php echo ($settings['font_backend'] === 'Palatino Linotype') ? 'selected' : ''; ?>>Palatino Linotype</option>
                                            <option id="Courier New" <?php echo ($settings['font_backend'] === 'Courier New') ? 'selected' : ''; ?>>Courier New</option>
                                            <option id="Lucida Console" <?php echo ($settings['font_backend'] === 'Lucida Console') ? 'selected' : ''; ?>>Lucida Console</option>
                                            <option id="Monaco" <?php echo ($settings['font_backend'] === 'Monaco') ? 'selected' : ''; ?>>Monaco</option>
                                            <option id="Comic Sans MS" <?php echo ($settings['font_backend'] === 'Comic Sans MS') ? 'selected' : ''; ?>>Comic Sans MS</option>
                                            <option id="Bradley Hand" <?php echo ($settings['font_backend'] === 'Bradley Hand') ? 'selected' : ''; ?>>Bradley Hand</option>
                                            <option id="Impact" <?php echo ($settings['font_backend'] === 'Impact') ? 'selected' : ''; ?>>Impact</option>
                                            <option id="Chalkduster" <?php echo ($settings['font_backend'] === 'Chalkduster') ? 'selected' : ''; ?>>Chalkduster</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="theme_color"><?php echo _("Theme Color"); ?>&nbsp;&nbsp;<i class="fas fa-sun"></i></label>
                                    <input type="text" class="form-control" id="theme_color" value="<?php echo $settings['theme_color']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sidebar_color_1"><?php echo _("Sidebar Color 1"); ?>&nbsp;&nbsp;<i class="fas fa-sun"></i></label>
                                    <input type="text" class="form-control" id="sidebar_color_1" value="<?php echo $settings['sidebar_color_1']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sidebar_color_2"><?php echo _("Sidebar Color 2"); ?>&nbsp;&nbsp;<i class="fas fa-sun"></i></label>
                                    <input type="text" class="form-control <?php echo ($settings['sidebar']=='flat') ? 'disabled' : ''; ?>" id="sidebar_color_2" value="<?php echo $settings['sidebar_color_2']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="theme_color_dark"><?php echo _("Theme Color"); ?>&nbsp;&nbsp;<i class="fas fa-moon"></i></label>
                                    <input type="text" class="form-control" id="theme_color_dark" value="<?php echo $settings['theme_color_dark']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sidebar_color_1_dark"><?php echo _("Sidebar Color 1"); ?>&nbsp;&nbsp;<i class="fas fa-moon"></i></label>
                                    <input type="text" class="form-control" id="sidebar_color_1_dark" value="<?php echo $settings['sidebar_color_1_dark']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sidebar_color_2_dark"><?php echo _("Sidebar Color 2"); ?>&nbsp;&nbsp;<i class="fas fa-moon"></i></label>
                                    <input type="text" class="form-control <?php echo ($settings['sidebar']=='flat') ? 'disabled' : ''; ?>" id="sidebar_color_2_dark" value="<?php echo $settings['sidebar_color_2_dark']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="welcome_msg"><?php echo _("Welcome Message"); ?> <i title="<?php echo _("leave empty for default welcome message"); ?>" class="help_t fas fa-question-circle"></i></label>
                                    <div id="welcome_msg"><?php echo $settings['welcome_msg']; ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _("Login Theme"); ?></label>
                                    <select class="form-control" id="style_login">
                                        <option <?php echo ($settings['style_login']==1) ? 'selected' : ''; ?> id="1"><?php echo _("Style 1 (image alongside the form)"); ?></option>
                                        <option <?php echo ($settings['style_login']==2) ? 'selected' : ''; ?> id="2"><?php echo _("Style 2 (image as background)"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                            </div>
                            <div class="col-md-6">
                                <label><?php echo _("Logo"); ?></label>
                                <div style="background-color:#4e73df;display: none" id="div_image_logo" class="col-md-12">
                                    <img style="width: 100%" src="assets/<?php echo $settings['logo']; ?>" />
                                </div>
                                <div style="display: none" id="div_delete_logo" class="col-md-12 mt-4">
                                    <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_b_logo();" class="btn btn-block btn-danger"><?php echo _("DELETE IMAGE"); ?></button>
                                </div>
                                <div style="display: none" id="div_upload_logo">
                                    <form id="frm" action="ajax/upload_b_logo_image.php" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="txtFile" name="txtFile" />
                                                        <label class="custom-file-label text-left" for="txtFile"><?php echo _("Choose file"); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload" value="<?php echo _("Upload Logo Image"); ?>" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="preview text-center">
                                                    <div id="progress_l" class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
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
                            <div class="col-md-6">
                                <label><?php echo _("Login image"); ?></label>
                                <div style="display: none" id="div_image_bg" class="col-md-12">
                                    <img style="width: 100%" src="assets/<?php echo $settings['background']; ?>" />
                                </div>
                                <div style="display: none" id="div_delete_bg" class="col-md-12 mt-4">
                                    <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_b_bg();" class="btn btn-block btn-danger"><?php echo _("DELETE IMAGE"); ?></button>
                                </div>
                                <div style="display: none" id="div_upload_bg">
                                    <form id="frm_b" action="ajax/upload_b_background_image.php" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="txtFile_b" name="txtFile_b" />
                                                        <label class="custom-file-label text-left" for="txtFile_b"><?php echo _("Choose file"); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_b" value="<?php echo _("Upload Login Image"); ?>" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="preview text-center">
                                                    <div id="progress_bl" class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                        <div class="progress-bar" id="progressBar_b" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                            0%
                                                        </div>
                                                    </div>
                                                    <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_b"></div>
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
        <div class="row <?php echo ($_SESSION['input_license']==1) ? 'd-none' : ''; ?>">
            <div class="col-md-12 mb-4">
                <div class="card shadow mb-12">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-ellipsis-h"></i> <?php echo _("Footer"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="footer_link_1"><?php echo _("Name Item 1"); ?></label><br>
                                    <input type="text" class="form-control" id="footer_link_1" value="<?php echo $settings['footer_link_1']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="footer_value_1"><?php echo _("Content Item 1"); ?> <i title="<?php echo _("insert a textual content or a link to an external site"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <div id="footer_value_1"><?php echo $settings['footer_value_1']; ?></div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="footer_link_2"><?php echo _("Name Item 2"); ?></label><br>
                                    <input type="text" class="form-control" id="footer_link_2" value="<?php echo $settings['footer_link_2']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="footer_value_2"><?php echo _("Content Item 2"); ?> <i title="<?php echo _("insert a textual content or a link to an external site"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <div id="footer_value_2"><?php echo $settings['footer_value_2']; ?></div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="footer_link_3"><?php echo _("Name Item 3"); ?></label><br>
                                    <input type="text" class="form-control" id="footer_link_3" value="<?php echo $settings['footer_link_3']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="footer_value_3"><?php echo _("Content Item 3"); ?> <i title="<?php echo _("insert a textual content or a link to an external site"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <div id="footer_value_3"><?php echo $settings['footer_value_3']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $z0='';if(array_key_exists(base64_decode('U0VSVkVSX0FERFI='),$_SERVER))$z0=$_SERVER[base64_decode('U0VSVkVSX0FERFI=')];elseif(array_key_exists(base64_decode('TE9DQUxfQUREUg=='),$_SERVER))$z0=$_SERVER[base64_decode('TE9DQUxfQUREUg==')];elseif(array_key_exists(base64_decode('U0VSVkVSX05BTUU='),$_SERVER))$z0=gethostbyname($_SERVER[base64_decode('U0VSVkVSX05BTUU=')]);else{if(stristr(PHP_OS,base64_decode('V0lO'))){$z0=gethostbyname(php_uname(base64_decode('bg==')));}else{$e1=shell_exec(base64_decode('L3NiaW4vaWZjb25maWcgZXRoMA=='));preg_match(base64_decode('L2FkZHI6KFtcZFwuXSspLw=='),$e1,$d2);$z0=$d2[1];}}$j3=$_SERVER[base64_decode('U0VSVkVSX05BTUU=')];echo"<script>window.server_name = '$j3'; window.server_ip = '$z0';</script>";?>

<script>
    (function($) {
        "use strict"; // Start of use strict
        window.settings_need_save = false;
        window.input_license = <?php echo $_SESSION['input_license']; ?>;
        window.b_logo_image = '<?php echo $settings['logo']; ?>';
        window.b_background_image = '<?php echo $settings['background']; ?>';
        window.current_language = '<?php echo $settings['language']; ?>';
        window.welcome_msg_editor = null;
        window.theme_color_spectrum = null;
        window.sidebar_color_1_spectrum = null;
        window.sidebar_color_2_spectrum = null;
        window.theme_color_dark_spectrum = null;
        window.sidebar_color_1_dark_spectrum = null;
        window.sidebar_color_2_dark_spectrum = null;
        window.editors_css = [];
        window.editors_js = [];
        window.editors_head = [];
        window.footer_value_1 = null;
        window.footer_value_2 = null;
        window.footer_value_3 = null;
        window.terms_and_conditions_editor = null;
        window.privacy_policy_editor = null;
        window.cookie_policy_editor = null;
        Quill.register("modules/htmlEditButton", htmlEditButton);
        var DirectionAttribute = Quill.import('attributors/attribute/direction');
        Quill.register(DirectionAttribute,true);
        var AlignClass = Quill.import('attributors/class/align');
        Quill.register(AlignClass,true);
        var BackgroundClass = Quill.import('attributors/class/background');
        Quill.register(BackgroundClass,true);
        var ColorClass = Quill.import('attributors/class/color');
        Quill.register(ColorClass,true);
        var DirectionClass = Quill.import('attributors/class/direction');
        Quill.register(DirectionClass,true);
        var FontClass = Quill.import('attributors/class/font');
        Quill.register(FontClass,true);
        var SizeClass = Quill.import('attributors/class/size');
        Quill.register(SizeClass,true);
        var AlignStyle = Quill.import('attributors/style/align');
        Quill.register(AlignStyle,true);
        var BackgroundStyle = Quill.import('attributors/style/background');
        Quill.register(BackgroundStyle,true);
        var ColorStyle = Quill.import('attributors/style/color');
        Quill.register(ColorStyle,true);
        var DirectionStyle = Quill.import('attributors/style/direction');
        Quill.register(DirectionStyle,true);
        var FontStyle = Quill.import('attributors/style/font');
        Quill.register(FontStyle,true);
        var SizeStyle = Quill.import('attributors/style/size');
        Quill.register(SizeStyle,true);
        var LinkFormats = Quill.import("formats/link");
        Quill.register(LinkFormats,true);

        $(document).ready(function () {
            $('#font_backend').fontpicker({
                variants:false,
                localFonts: {},
                nrRecents: 0,
                onSelect: function (font) {
                    var font_family = font.fontFamily;
                    var font_provider = $('#font_provider option:selected').attr('id');
                    switch (font_provider) {
                        case 'google':
                            $('#font_backend_link').attr('href','https://fonts.googleapis.com/css?family='+font_family);
                            break;
                        case 'collabs':
                            $('#font_backend_link').attr('href','https://api.fonts.coollabs.io/css2?family='+font_family+'&display=swap');
                            break;
                    }
                    $('#style_css').html("*{ font-family:'"+font_family+"',sans-serif; }");
                }
            });
            var font_provider = $('#font_provider option:selected').attr('id');
            if(font_provider=='systems') {
                $('.font-picker').addClass('d-none');
            } else {
                $('.font-picker').css('width','100%');
            }
            bsCustomFileInput.init();
            $('.help_t').tooltip();
            if(window.b_logo_image=='') {
                $('#div_delete_logo').hide();
                $('#div_image_logo').hide();
                $('#div_upload_logo').show();
            } else {
                $('#div_delete_logo').show();
                $('#div_image_logo').show();
                $('#div_upload_logo').hide();
            }
            if(window.b_background_image=='') {
                $('#div_delete_bg').hide();
                $('#div_image_bg').hide();
                $('#div_upload_bg').show();
            } else {
                $('#div_delete_bg').show();
                $('#div_image_bg').show();
                $('#div_upload_bg').hide();
            }
            $(".editors_css").each(function() {
                var id = $(this).attr('id');
                window.editors_css[id] = ace.edit(id);
                window.editors_css[id].session.setUseWorker(false);
                window.editors_css[id].session.setMode("ace/mode/css");
                window.editors_css[id].setOption('enableLiveAutocompletion',true);
                window.editors_css[id].setShowPrintMargin(false);
                if($('body').hasClass('dark_mode')) {
                    window.editors_css[id].setTheme("ace/theme/one_dark");
                }
            });
            $(".editors_js").each(function() {
                var id = $(this).attr('id');
                window.editors_js[id] = ace.edit(id);
                window.editors_js[id].session.setUseWorker(false);
                window.editors_js[id].session.setMode("ace/mode/javascript");
                window.editors_js[id].setOption('enableLiveAutocompletion',true);
                window.editors_js[id].setShowPrintMargin(false);
                if($('body').hasClass('dark_mode')) {
                    window.editors_js[id].setTheme("ace/theme/one_dark");
                }
            });
            $(".editors_head").each(function() {
                var id = $(this).attr('id');
                window.editors_head[id] = ace.edit(id);
                window.editors_head[id].session.setUseWorker(false);
                window.editors_head[id].session.setMode("ace/mode/html");
                window.editors_head[id].setOption('enableLiveAutocompletion',true);
                window.editors_head[id].setShowPrintMargin(false);
                if($('body').hasClass('dark_mode')) {
                    window.editors_head[id].setTheme("ace/theme/one_dark");
                }
            });
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['clean']
            ];
            var toolbarOptions_wm = [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],['link'],['image'],
                ['clean']
            ];
            var toolbarHtml = {
                debug: false,
                msg: `<?php echo _("Edit the content in HTML format"); ?>`,
                okText: `<?php echo _("Ok"); ?>`,
                cancelText: `<?php echo _("Cancel"); ?>`,
                buttonHTML: '<i class="fas fa-code"></i>',
                buttonTitle: `<?php echo _("Show HTML Source"); ?>`,
                syntax: true,
                prependSelector: null,
                editorModules: {}
            };
            window.welcome_msg_editor = new Quill('#welcome_msg', {
                modules: {
                    toolbar: toolbarOptions_wm,
                    htmlEditButton: toolbarHtml
                },
                theme: 'snow'
            });
            window.terms_and_conditions_editor = new Quill('#terms_and_conditions', {
                modules: {
                    toolbar: toolbarOptions_wm,
                    htmlEditButton: toolbarHtml
                },
                theme: 'snow'
            });
            window.privacy_policy_editor = new Quill('#privacy_policy', {
                modules: {
                    toolbar: toolbarOptions_wm,
                    htmlEditButton: toolbarHtml
                },
                theme: 'snow'
            });
            window.cookie_policy_editor = new Quill('#cookie_policy', {
                modules: {
                    toolbar: toolbarOptions_wm,
                    htmlEditButton: toolbarHtml
                },
                theme: 'snow'
            });
            window.theme_color_spectrum = $('#theme_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: true,
                allowEmpty: false,
                cancelText: "<?php echo _("Cancel"); ?>",
                chooseText: "<?php echo _("Choose"); ?>",
                change: function(color) {
                    var hex = color.toHexString();
                    var sidebar_color_1 = $('#sidebar_color_1').val();
                    var sidebar_color_2 = $('#sidebar_color_2').val();
                    var theme_color_dark = $('#theme_color_dark').val();
                    var sidebar_color_1_dark = $('#sidebar_color_1_dark').val();
                    var sidebar_color_2_dark = $('#sidebar_color_2_dark').val();
                    set_session_theme_color(hex,sidebar_color_1,sidebar_color_2,theme_color_dark,sidebar_color_1_dark,sidebar_color_2_dark);
                }
            });
            window.sidebar_color_1_spectrum = $('#sidebar_color_1').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: true,
                allowEmpty: true,
                cancelText: "<?php echo _("Cancel"); ?>",
                chooseText: "<?php echo _("Choose"); ?>",
                change: function(color) {
                    if(color!=null) {
                        var hex = color.toHexString();
                    } else {
                        var hex = '';
                    }
                    var theme_color = $('#theme_color').val();
                    var sidebar_color_2 = $('#sidebar_color_2').val();
                    var theme_color_dark = $('#theme_color_dark').val();
                    var sidebar_color_1_dark = $('#sidebar_color_1_dark').val();
                    var sidebar_color_2_dark = $('#sidebar_color_2_dark').val();
                    set_session_theme_color(theme_color,hex,sidebar_color_2,theme_color_dark,sidebar_color_1_dark,sidebar_color_2_dark);
                }
            });
            window.sidebar_color_2_spectrum = $('#sidebar_color_2').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: true,
                allowEmpty: true,
                cancelText: "<?php echo _("Cancel"); ?>",
                chooseText: "<?php echo _("Choose"); ?>",
                change: function(color) {
                    if(color!=null) {
                        var hex = color.toHexString();
                    } else {
                        var hex = '';
                    }
                    var theme_color = $('#theme_color').val();
                    var sidebar_color_1 = $('#sidebar_color_1').val();
                    var theme_color_dark = $('#theme_color_dark').val();
                    var sidebar_color_1_dark = $('#sidebar_color_1_dark').val();
                    var sidebar_color_2_dark = $('#sidebar_color_2_dark').val();
                    set_session_theme_color(theme_color,sidebar_color_1,hex,theme_color_dark,sidebar_color_1_dark,sidebar_color_2_dark);
                }
            });
            window.theme_color_dark_spectrum = $('#theme_color_dark').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: true,
                allowEmpty: true,
                cancelText: "<?php echo _("Cancel"); ?>",
                chooseText: "<?php echo _("Choose"); ?>",
                change: function(color) {
                    if(color!=null) {
                        var hex = color.toHexString();
                    } else {
                        var hex = '';
                    }
                    var theme_color = $('#theme_color').val();
                    var sidebar_color_1 = $('#sidebar_color_1').val();
                    var sidebar_color_2 = $('#sidebar_color_2').val();
                    var sidebar_color_1_dark = $('#sidebar_color_1_dark').val();
                    var sidebar_color_2_dark = $('#sidebar_color_2_dark').val();
                    set_session_theme_color(theme_color,sidebar_color_1,sidebar_color_2,hex,sidebar_color_1_dark,sidebar_color_2_dark);
                }
            });
            window.sidebar_color_1_dark_spectrum = $('#sidebar_color_1_dark').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: true,
                allowEmpty: true,
                cancelText: "<?php echo _("Cancel"); ?>",
                chooseText: "<?php echo _("Choose"); ?>",
                change: function(color) {
                    if(color!=null) {
                        var hex = color.toHexString();
                    } else {
                        var hex = '';
                    }
                    var theme_color = $('#theme_color').val();
                    var sidebar_color_1 = $('#sidebar_color_1').val();
                    var sidebar_color_2 = $('#sidebar_color_2').val();
                    var theme_color_dark = $('#theme_color_dark').val();
                    var sidebar_color_1_dark = $('#sidebar_color_1_dark').val();
                    var sidebar_color_2_dark = $('#sidebar_color_2_dark').val();
                    set_session_theme_color(theme_color,sidebar_color_1,sidebar_color_2,theme_color_dark,hex,sidebar_color_2_dark);
                }
            });
            window.sidebar_color_2_dark_spectrum = $('#sidebar_color_2_dark').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: true,
                allowEmpty: true,
                cancelText: "<?php echo _("Cancel"); ?>",
                chooseText: "<?php echo _("Choose"); ?>",
                change: function(color) {
                    if(color!=null) {
                        var hex = color.toHexString();
                    } else {
                        var hex = '';
                    }
                    var theme_color = $('#theme_color').val();
                    var sidebar_color_1 = $('#sidebar_color_1').val();
                    var sidebar_color_2 = $('#sidebar_color_2').val();
                    var theme_color_dark = $('#theme_color_dark').val();
                    var sidebar_color_1_dark = $('#sidebar_color_1_dark').val();
                    set_session_theme_color(theme_color,sidebar_color_1,sidebar_color_2,theme_color_dark,sidebar_color_1_dark,hex);
                }
            });
            window.footer_value_1 = new Quill('#footer_value_1', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
            window.footer_value_2 = new Quill('#footer_value_2', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
            window.footer_value_3 = new Quill('#footer_value_3', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
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
                        window.settings_need_save = true;
                        window.b_logo_image = evt.target.responseText;
                        $('#div_image_logo img').attr('src','assets/'+window.b_logo_image);
                        $('#div_delete_logo').show();
                        $('#div_image_logo').show();
                        $('#div_upload_logo').hide();
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
            $('#progressBar').css('width',value+'%').html(value+'%');
            if(value==0){
                $('#progress_l').hide();
            }else{
                $('#progress_l').show();
            }
        }

        function show_error(error){
            $('#progress_l').hide();
            $('#error').show();
            $('#error').html(error);
        }

        $('body').on('submit','#frm_b',function(e){
            e.preventDefault();
            $('#error_b').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_b[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_b' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar_b(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_b(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.settings_need_save = true;
                        window.b_background_image = evt.target.responseText;
                        $('#div_image_bg img').attr('src','assets/'+window.b_background_image);
                        $('#div_delete_bg').show();
                        $('#div_image_bg').show();
                        $('#div_upload_bg').hide();
                    }
                }
                upadte_progressbar_b(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_b('upload failed');
                upadte_progressbar_b(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_b('upload aborted');
                upadte_progressbar_b(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar_b(value){
            $('#progressBar_b').css('width',value+'%').html(value+'%');
            if(value==0){
                $('#progress_bl').hide();
            }else{
                $('#progress_bl').show();
            }
        }

        function show_error_b(error){
            $('#progress_bl').hide();
            $('#error_b').show();
            $('#error_b').html(error);
        }

        $("input").change(function(){
            window.settings_need_save = true;
        });

        $(window).on('beforeunload', function(){
            if(window.settings_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });

    })(jQuery); // End of use strict

    function change_sidebar() {
        var sidebar = $('#sidebar option:selected').attr('id');
        switch (sidebar) {
            case 'flat':
                $('#accordionSidebar').removeClass('bg-gradient-primary').addClass('bg-flat-primary');
                $('#sidebar_color_2').addClass('disabled');
                $('#sidebar_color_2_dark').addClass('disabled');
                break;
            case 'gradient':
                $('#accordionSidebar').removeClass('bg-flat-primary').addClass('bg-gradient-primary');
                $('#sidebar_color_2').removeClass('disabled');
                $('#sidebar_color_2_dark').removeClass('disabled');
                break;
        }
    }
</script>