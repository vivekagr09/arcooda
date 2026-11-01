<?php
session_start();
require_once("functions.php");
$id_map = $_GET['id'];
$map = get_map($id_map,$_SESSION['id_user']);
if($map!==false) {
    $count_markers = get_marker_count_map($id_map);
    $users = get_users($map['id_user']);
    $user_role = get_user_role($_SESSION['id_user']);
    $settings = get_settings();
    if($user_info['role']=='editor') {
        $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map);
        if($editor_permissions['edit_map']==0) {
            $map=false;
        }
    }
    $_SESSION['id_map_sel'] = $id_map;
    $_SESSION['name_map_sel'] = $map['name'];
    if(empty($map['markers_color_hex'])) $map['markers_color_hex']=$map['main_color_hex'];
}
$url_background_image = "";
$url_background_image_mobile = "";
$url_intro_image = "";
if(!empty($map['background_image'])) {
    $url_background_image = "../viewer/content/".$map['background_image'];
}
if(!empty($map['background_image_mobile'])) {
    $url_background_image_mobile = "../viewer/content/".$map['background_image_mobile'];
}
if(!empty($map['intro_image'])) {
    $url_intro_image = "../viewer/content/".$map['intro_image'];
}
if(!empty($map['default_extra_fields'])) {
    $default_extra_fields = json_decode($map['default_extra_fields'],true);
} else {
    $default_extra_fields = array();
}
if(!empty($map['default_am_fields'])) {
    $default_am_fields = json_decode($map['default_am_fields'],true);
} else {
    $default_am_fields = array();
}
if(!empty($map['default_am_placeholders'])) {
    $default_am_placeholders = json_decode($map['default_am_placeholders'],true);
} else {
    $default_am_placeholders = array();
}
$map_style_sel = $map['map_street_provider'];
$map_style_satellite_sel = $map['map_satellite_provider'];
if(!empty($map['intro_params'])) {
    $intro_params = json_decode($map['intro_params'],true);
} else {
    $intro_params = [
            "background"=>"rgba(255,255,255,0.7)",
            "title"=>"Exploration Unleashed: Navigate the Unknown",
            "title_size"=>42,
            "title_color"=>"#000000",
            "description"=>"With the digital map illuminated on their device, the explorer's pulse quickened with anticipation. Each contour hinted at adventure: unexplored forests, meandering rivers, and towering peaks awaited discovery. Armed with determination and a thirst for exploration, they set out to uncover the hidden treasures marked on the map's virtual interface, prepared to leave their mark on the uncharted territories of the digital landscape.",
            "description_size"=>16,
            "description_color"=>"#000000",
            "image_height"=>80,
            "button"=>"START EXPLORE NOW &nbsp;<i class='fa-solid fa-arrow-right-long'></i>",
            "button_size"=>18,
            "button_background"=>"#ffffff",
            "button_color"=>"#000000",
    ];
}
?>

<?php if(!$map): ?>
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

<ul class="nav bg-white nav-pills nav-fill mb-2">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="pill" href="#settings_tab"><i class="fas fa-cogs"></i> <?php echo strtoupper(_("SETTINGS")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#search_tab"><i class="fas fa-search"></i> <?php echo strtoupper(_("SEARCH")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#style_tab"><i class="fas fa-palette"></i> <?php echo strtoupper(_("STYLE")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#loading_tab"><i class="fas fa-spinner"></i> <?php echo strtoupper(_("LOADING")); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="pill" href="#defaults_tab"><i class="fas fa-list-ol"></i> <?php echo strtoupper(_("DEFAULTS")); ?></a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="settings_tab">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"><?php echo _("Name"); ?> *</label>
                                    <input type="text" class="form-control" id="name" value="<?php echo $map['name']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user"><?php echo _("User"); ?> <i title="<?php echo _("owner of the map"); ?>" class="help_t fas fa-question-circle"></i></label>
                                    <select id="user" class="form-control" <?php echo ($user_role=='administrator' && $users['count']>1) ? '' : 'disabled' ?> >
                                        <?php echo $users['options']; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="language"><?php echo _("Language"); ?></label>
                                    <select class="form-control" id="language">
                                        <option <?php echo ($map['language']=='') ? 'selected':''; ?> id=""><?php echo _("Default Language"); ?></option>
                                        <?php if (check_language_enabled('ar_SA',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ar_SA') ? 'selected':''; ?> id="ar_SA">Arabic (ar_SA)</option><?php endif; ?>
                                        <?php if (check_language_enabled('zh_CN',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='zh_CN') ? 'selected':''; ?> id="zh_CN">Chinese simplified (zh_CN)</option><?php endif; ?>
                                        <?php if (check_language_enabled('zh_HK',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='zh_HK') ? 'selected':''; ?> id="zh_HK">Chinese traditional (zh_HK)</option><?php endif; ?>
                                        <?php if (check_language_enabled('zh_TW',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='zh_TW') ? 'selected':''; ?> id="zh_TW">Chinese traditional (zh_TW)</option><?php endif; ?>
                                        <?php if (check_language_enabled('cs_CZ',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='cs_CZ') ? 'selected':''; ?> id="cs_CZ">Czech (cs_CZ)</option><?php endif; ?>
                                        <?php if (check_language_enabled('nl_NL',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='nl_NL') ? 'selected':''; ?> id="nl_NL">Dutch (nl_NL)</option><?php endif; ?>
                                        <?php if (check_language_enabled('en_US',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='en_US') ? 'selected':''; ?> id="en_US">English (en_US)</option><?php endif; ?>
                                        <?php if (check_language_enabled('en_GB',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='en_GB') ? 'selected':''; ?> id="en_GB">English (en_GB)</option><?php endif; ?>
                                        <?php if (check_language_enabled('fil_PH',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='fil_PH') ? 'selected':''; ?> id="fil_PH">Filipino (fil_PH)</option><?php endif; ?>
                                        <?php if (check_language_enabled('fr_FR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='fr_FR') ? 'selected':''; ?> id="fr_FR">French (fr_FR)</option><?php endif; ?>
                                        <?php if (check_language_enabled('de_DE',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='de_DE') ? 'selected':''; ?> id="de_DE">German (de_DE)</option><?php endif; ?>
                                        <?php if (check_language_enabled('hi_IN',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='hi_IN') ? 'selected':''; ?> id="hi_IN">Hindi (hi_IN)</option><?php endif; ?>
                                        <?php if (check_language_enabled('kw_KW',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='kw_KW') ? 'selected':''; ?> id="kw_KW">Kinyarwanda (kw_KW)</option><?php endif; ?>
                                        <?php if (check_language_enabled('ko_KR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ko_KR') ? 'selected':''; ?> id="ko_KR">Korean (ko_KR)</option><?php endif; ?>
                                        <?php if (check_language_enabled('id_ID',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='id_ID') ? 'selected':''; ?> id="id_ID">Indonesian (id_ID)</option><?php endif; ?>
                                        <?php if (check_language_enabled('hu_HU',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='hu_HU') ? 'selected':''; ?> id="hu_HU">Hungarian (hu_HU)</option><?php endif; ?>
                                        <?php if (check_language_enabled('it_IT',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='it_IT') ? 'selected':''; ?> id="it_IT">Italian (it_IT)</option><?php endif; ?>
                                        <?php if (check_language_enabled('ja_JP',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ja_JP') ? 'selected':''; ?> id="ja_JP">Japanese (ja_JP)</option><?php endif; ?>
                                        <?php if (check_language_enabled('fa_IR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='fa_IR') ? 'selected':''; ?> id="fa_IR">Persian (fa_IR)</option><?php endif; ?>
                                        <?php if (check_language_enabled('fi_FI',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='fi_FI') ? 'selected':''; ?> id="fi_FI">Finnish (fi_FI)</option><?php endif; ?>
                                        <?php if (check_language_enabled('pl_PL',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='pl_PL') ? 'selected':''; ?> id="pl_PL">Polish (pl_PL)</option><?php endif; ?>
                                        <?php if (check_language_enabled('pt_BR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='pt_BR') ? 'selected':''; ?> id="pt_BR">Portuguese Brazilian (pt_BR)</option><?php endif; ?>
                                        <?php if (check_language_enabled('pt_PT',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='pt_PT') ? 'selected':''; ?> id="pt_PT">Portuguese European (pt_PT)</option><?php endif; ?>
                                        <?php if (check_language_enabled('es_ES',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='es_ES') ? 'selected':''; ?> id="es_ES">Spanish (es_ES)</option><?php endif; ?>
                                        <?php if (check_language_enabled('ro_RO',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ro_RO') ? 'selected':''; ?> id="ro_RO">Romanian (ro_RO)</option><?php endif; ?>
                                        <?php if (check_language_enabled('ru_RU',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='ru_RU') ? 'selected':''; ?> id="ru_RU">Russian (ru_RU)</option><?php endif; ?>
                                        <?php if (check_language_enabled('sv_SE',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='sv_SE') ? 'selected':''; ?> id="sv_SE">Swedish (sv_SE)</option><?php endif; ?>
                                        <?php if (check_language_enabled('tg_TJ',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='tg_TJ') ? 'selected':''; ?> id="tg_TJ">Tajik (tg_TJ)</option><?php endif; ?>
                                        <?php if (check_language_enabled('th_TH',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='th_TH') ? 'selected':''; ?> id="th_TH">Thai (th_TH)</option><?php endif; ?>
                                        <?php if (check_language_enabled('tr_TR',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='tr_TR') ? 'selected':''; ?> id="tr_TR">Turkish (tr_TR)</option><?php endif; ?>
                                        <?php if (check_language_enabled('vi_VN',$settings['languages_enabled'])) : ?><option <?php echo ($map['language']=='vi_VN') ? 'selected':''; ?> id="vi_VN">Vietnamese (vi_VN)</option><?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_list"><?php echo _("List"); ?> <i title="<?php echo _("enables the list of markers in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_list" <?php echo ($map['enable_list']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_categories"><?php echo _("Categories"); ?> <i title="<?php echo _("enable the category filter in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_categories" <?php echo ($map['enable_categories']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="show_list"><?php echo _("Auto show List"); ?> <i title="<?php echo _("automatically open the marker's list at first map loading"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="show_list" <?php echo ($map['show_list']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cat_filter"><?php echo _("Auto show Categories"); ?> <i title="<?php echo _("automatically open the categories filter"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="cat_filter" <?php echo ($map['cat_filter']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="default_my_location"><?php echo _("Auto show My Location"); ?> <i title="<?php echo _("automatically geolocate and show the location"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="default_my_location" <?php echo ($map['default_my_location']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nav_markers"><?php echo _("Control Arrows Markers"); ?> <i title="<?php echo _("show controls arrows to loop through the markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="nav_markers" <?php echo ($map['nav_markers']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_story"><?php echo _("Story"); ?> <i title="<?php echo _("enables the story feature in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_story" <?php echo ($map['enable_story']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_popup"><?php echo _("Popup"); ?> <i title="<?php echo _("display popup on hover over markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_popup" <?php echo ($map['enable_popup']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sheet_detail"><?php echo _("Details Window"); ?> <i title="<?php echo _("opens the details window by clicking on markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="sheet_detail" <?php echo ($map['sheet_detail']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_directions"><?php echo _("Directions"); ?> <i title="<?php echo _("enable the button 'directions' for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_directions" <?php echo ($map['enable_directions']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_streetview"><?php echo _("Street View"); ?> <i title="<?php echo _("enable the button 'street view' for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_streetview" <?php echo ($map['enable_streetview']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_reviews"><?php echo _("Reviews"); ?> <i title="<?php echo _("enable the reviews for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_reviews" <?php echo ($map['enable_reviews']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_share_map"><?php echo _("Share - Map"); ?> <i title="<?php echo _("enable share buttons for map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_share_map" <?php echo ($map['enable_share_map']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_share"><?php echo _("Share - Markers"); ?> <i title="<?php echo _("enable share buttons for markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_share" <?php echo ($map['enable_share']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <?php
                            $array_share_providers = explode(",",$map['share_providers']);
                            $array_share_providers_settings = explode(",",$settings['share_providers']);
                            ?>
                            <div class="col-md-3">
                                <label for="share_providers"><?php echo _("Providers"); ?></label><br>
                                <select id="share_providers" data-iconBase="fa" data-tickIcon="fa-check" data-actions-box="true" data-selected-text-format="count > 8" data-count-selected-text="{0} <?php echo _("items selected"); ?>" data-deselect-all-text="<?php echo _("Deselect All"); ?>" data-select-all-text="<?php echo _("Select All"); ?>" data-none-selected-text="<?php echo _("Nothing selected"); ?>" data-none-results-text="<?php echo _("No results matched"); ?> {0}" class="form-control selectpicker" multiple>
                                    <?php if(in_array('copy_link',$array_share_providers_settings)) : ?><option data-icon="fas fa-link" id="provider_copy_link" <?php echo (in_array('copy_link',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Copy Link"); ?></option><?php endif; ?>
                                    <?php if(in_array('email',$array_share_providers_settings)) : ?><option data-icon="fas fa-envelope" id="provider_email" <?php echo (in_array('email',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Email"); ?></option><?php endif; ?>
                                    <?php if(in_array('whatsapp',$array_share_providers_settings)) : ?><option data-icon="fab fa-whatsapp" id="provider_whatsapp" <?php echo (in_array('whatsapp',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Whatsapp"); ?></option><?php endif; ?>
                                    <?php if(in_array('facebook',$array_share_providers_settings)) : ?><option data-icon="fab fa-facebook" id="provider_facebook" <?php echo (in_array('facebook',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Facebook"); ?></option><?php endif; ?>
                                    <?php if(in_array('threads',$array_share_providers_settings)) : ?><option data-icon="fab fa-threads" id="provider_threads" <?php echo (in_array('threads',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Threads"); ?></option><?php endif; ?>
                                    <?php if(in_array('twitter',$array_share_providers_settings)) : ?><option data-icon="fab fa-x-twitter" id="provider_twitter" <?php echo (in_array('twitter',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Twitter"); ?></option><?php endif; ?>
                                    <?php if(in_array('linkedin',$array_share_providers_settings)) : ?><option data-icon="fab fa-linkedin" id="provider_linkedin" <?php echo (in_array('linkedin',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Linkedin"); ?></option><?php endif; ?>
                                    <?php if(in_array('telegram',$array_share_providers_settings)) : ?><option data-icon="fab fa-telegram" id="provider_telegram" <?php echo (in_array('telegram',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Telegram"); ?></option><?php endif; ?>
                                    <?php if(in_array('facebook_messenger',$array_share_providers_settings)) : ?><option data-icon="fab fa-facebook-messenger" id="provider_facebook_messenger" <?php echo (in_array('facebook_messenger',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Facebook Messenger"); ?></option><?php endif; ?>
                                    <?php if(in_array('pinterest',$array_share_providers_settings)) : ?><option data-icon="fab fa-pinterest" id="provider_pinterest" <?php echo (in_array('pinterest',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Pinterest"); ?></option><?php endif; ?>
                                    <?php if(in_array('reddit',$array_share_providers_settings)) : ?><option data-icon="fab fa-reddit" id="provider_reddit" <?php echo (in_array('reddit',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Reddit"); ?></option><?php endif; ?>
                                    <?php if(in_array('line',$array_share_providers_settings)) : ?><option data-icon="fab fa-line" id="provider_line" <?php echo (in_array('line',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Line"); ?></option><?php endif; ?>
                                    <?php if(in_array('viber',$array_share_providers_settings)) : ?><option data-icon="fab fa-viber" id="provider_viber" <?php echo (in_array('viber',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Viber"); ?></option><?php endif; ?>
                                    <?php if(in_array('vk',$array_share_providers_settings)) : ?><option data-icon="fab fa-vk" id="provider_vk" <?php echo (in_array('vk',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("VK"); ?></option><?php endif; ?>
                                    <?php if(in_array('qzone',$array_share_providers_settings)) : ?><option data-icon="fab fa-qq" id="provider_qzone" <?php echo (in_array('qzone',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Qzone"); ?></option><?php endif; ?>
                                    <?php if(in_array('wechat',$array_share_providers_settings)) : ?><option data-icon="fab fa-weixin" id="provider_wechat" <?php echo (in_array('wechat',$array_share_providers) ? 'selected' : ''); ?>><?php echo _("Wechat"); ?></option><?php endif; ?>
                                </select>
                            </div>
                            <script type="text/javascript">$('#share_providers').selectpicker('render');</script>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="direction_map"><?php echo _("Directions Map Provider"); ?> <i title="<?php echo _("when you click 'get directions' on the marker you will use this provider to get directions"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <select id="direction_map" class="form-control">
                                        <option <?php echo (empty($settings['openrouteservice_api_key'])) ? 'disabled' : ''; ?> <?php echo ($map['direction_map']=='openrouteservice') ? 'selected' : ''; ?> id="openrouteservice">Openroute Service (<?php echo _("built-in"); ?>)</option>
                                        <option <?php echo ($map['direction_map']=='google') ? 'selected' : ''; ?> id="google">Google</option>
                                        <option <?php echo ($map['direction_map']=='bing') ? 'selected' : ''; ?> id="bing">Bing</option>
                                        <option <?php echo ($map['direction_map']=='baidu') ? 'selected' : ''; ?> id="baidu">Baidu</option>
                                        <option <?php echo ($map['direction_map']=='yandex') ? 'selected' : ''; ?> id="yandex">Yandex</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="streetview_map"><?php echo _("Streetview Map Provider"); ?> <i title="<?php echo _("when you click 'street view' on the marker you will use this provider to get the street view"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <select id="streetview_map" class="form-control">
                                        <option <?php echo ($map['streetview_map']=='google') ? 'selected' : ''; ?> id="google">Google</option>
                                        <option <?php echo ($map['streetview_map']=='bing') ? 'selected' : ''; ?> id="bing">Bing</option>
                                        <option <?php echo ($map['streetview_map']=='yandex') ? 'selected' : ''; ?> id="yandex">Yandex</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sheet_mobile"><?php echo _("Mobile Marker Sheets"); ?> <i title="<?php echo _("displays marker's detail window reduced or opened on mobile"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <select id="sheet_mobile" class="form-control">
                                        <option <?php echo ($map['sheet_mobile']=='reduced') ? 'selected' : ''; ?> id="reduced"><?php echo _("Collapsed"); ?></option>
                                        <option <?php echo ($map['sheet_mobile']=='opened') ? 'selected' : ''; ?> id="opened"><?php echo _("Opened"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_add_marker"><?php echo _("Add Markers from Map"); ?> <i title="<?php echo _("enables adding markers directly from the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <select id="enable_add_marker" class="form-control">
                                        <option <?php echo ($map['enable_add_marker']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Disabled"); ?></option>
                                        <option <?php echo ($map['enable_add_marker']==1) ? 'selected' : ''; ?> id="1"><?php echo _("Enabled"); ?></option>
                                        <option <?php echo ($map['enable_add_marker']==2) ? 'selected' : ''; ?> id="2"><?php echo _("Enabled (Automatic Approval)"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="density_color"><?php echo _("Density color"); ?> <i title="<?php echo _("displays different colors based on the density of marker grouping"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="density_color" <?php echo ($map['density_color']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="density_color_tolerance"><?php echo _("Density Color Tolerance"); ?> <i title="<?php echo _("tolerance to color the grouped markers"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input min="0" step="1" type="number" class="form-control" id="density_color_tolerance" value="<?php echo $map['density_color_tolerance']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cluster_distance"><?php echo _("Density Distance"); ?> <i title="<?php echo _("distance in pixels within which features will be clustered together. 0 to disable clustering"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input min="0" step="1" type="number" class="form-control" id="cluster_distance" value="<?php echo $map['cluster_distance']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="default_zoom"><?php echo _("Initial Zoom"); ?> <i title="<?php echo _("initial zoom when the map is loaded (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                    <select id="default_zoom" class="form-control">
                                        <option <?php echo ($map['default_zoom']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Fit Markers"); ?></option>
                                        <?php
                                        for($i=1;$i<=20;$i++) {
                                            if($map['default_zoom']==$i) {
                                                echo "<option selected id='$i'>$i</option>";
                                            } else {
                                                echo "<option id='$i'>$i</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="selected_zoom"><?php echo _("Marker Zoom"); ?> <i title="<?php echo _("zoom level when the marker is clicked (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                    <select id="selected_zoom" class="form-control">
                                        <option <?php echo ($map['selected_zoom']==0) ? 'selected' : ''; ?> id="0"><?php echo _("No change"); ?></option>
                                        <?php
                                        for($i=1;$i<=20;$i++) {
                                            if($map['selected_zoom']==$i) {
                                                echo "<option selected id='$i'>$i</option>";
                                            } else {
                                                echo "<option id='$i'>$i</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="my_location_zoom"><?php echo _("My Location Zoom"); ?> <i title="<?php echo _("zoom level when enabling geolocation (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                    <select id="my_location_zoom" class="form-control">
                                        <option <?php echo ($map['my_location_zoom']==0) ? 'selected' : ''; ?> id="0"><?php echo _("No change"); ?></option>
                                        <?php
                                        for($i=1;$i<=20;$i++) {
                                            if($map['my_location_zoom']==$i) {
                                                echo "<option selected id='$i'>$i</option>";
                                            } else {
                                                echo "<option id='$i'>$i</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cat_filter_type"><?php echo _("Categories Filter Type"); ?></label><br>
                                    <select id="cat_filter_type" class="form-control">
                                        <option <?php echo ($map['cat_filter_type']=='or') ? 'selected' : ''; ?> id="or"><?php echo _("OR"); ?></option>
                                        <option <?php echo ($map['cat_filter_type']=='and') ? 'selected' : ''; ?> id="and"><?php echo _("AND"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="quality"><?php echo _("Quality"); ?> <i title="<?php echo _("map quality (high can slow movement)"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <select id="quality" class="form-control">
                                        <option <?php echo ($map['quality']=='low') ? 'selected' : ''; ?> id="low"><?php echo _("Low"); ?></option>
                                        <option <?php echo ($map['quality']=='medium') ? 'selected' : ''; ?> id="medium"><?php echo _("Medium"); ?></option>
                                        <option <?php echo ($map['quality']=='high') ? 'selected' : ''; ?> id="high"><?php echo _("High"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="weather"><?php echo _("Weather"); ?></label><br>
                                    <select id="weather" class="form-control">
                                        <option <?php echo ($map['weather']=='none') ? 'selected' : ''; ?> id="none"><?php echo _("None"); ?></option>
                                        <option <?php echo ($map['weather']=='celsius') ? 'selected' : ''; ?> id="celsius"><?php echo _("Celsius"); ?></option>
                                        <option <?php echo ($map['weather']=='fahrenheit') ? 'selected' : ''; ?> id="fahrenheit"><?php echo _("Fahrenheit"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ga_tracking_id"><?php echo _("Google Analytics"); ?> <i title="<?php echo _("Google Analytics Tracking ID. Note: Use the Friendly URL in Google Analytics's property url setting."); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="text" class="form-control" id="ga_tracking_id" value="<?php echo $map['ga_tracking_id']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cookie_consent"><?php echo _("Enable Cookie Consent"); ?></label><br>
                                    <input type="checkbox" id="cookie_consent" <?php echo ($map['cookie_consent'])?'checked':''; ?> />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="search_tab">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_search"><?php echo _("Enable Search"); ?> <i title="<?php echo _("enable the search box in the map"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_search" <?php echo ($map['enable_search']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enable_search_location"><?php echo _("Search Locations"); ?> <i title="<?php echo _("enables the search for a location / city"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="enable_search_location" <?php echo ($map['enable_search_location']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="order_by"><?php echo _("Markers Order by"); ?></label><br>
                                    <select id="order_by" class="form-control">
                                        <option <?php echo ($map['order_by']=='priority') ? 'selected' : ''; ?> id="priority"><?php echo _("Priority"); ?></option>
                                        <option <?php echo ($map['order_by']=='name') ? 'selected' : ''; ?> id="name"><?php echo _("Name"); ?></option>
                                        <option <?php echo ($map['order_by']=='city') ? 'selected' : ''; ?> id="city"><?php echo _("City"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search_highlight"><?php echo _("Highlight Markers Position"); ?> <i title="<?php echo _("highlights the position of the marker when you hover over the search results (1 far - 20 near)"); ?>" class="help_t fas fa-question-circle"></i></label>
                                    <select id="search_highlight" class="form-control">
                                        <option <?php echo ($map['search_highlight']==-1) ? 'selected' : ''; ?> id="-1"><?php echo _("Disabled"); ?></option>
                                        <option <?php echo ($map['search_highlight']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Keep Zoom Level"); ?></option>
                                        <?php
                                        for($i=1;$i<=20;$i++) {
                                            if($map['search_highlight']==$i) {
                                                echo "<option selected id='$i'>$i</option>";
                                            } else {
                                                echo "<option id='$i'>$i</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-circle-dot"></i> <?php echo _("Search Radius"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search_radius_enabled"><?php echo _("Enable"); ?> <i title="<?php echo _("enables the ability to change the search radius of your location"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="search_radius_enabled" <?php echo ($map['search_radius_enabled']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="search_radius_m"><?php echo _("Default Radius Size"); ?> (<span id="search_radius_m_value"><?php echo ($map['search_radius_m']==-1) ? '<i class="fa-solid fa-infinity"></i>' : ($map['search_radius_m']/1000)." km"; ?></span>)</label><br>
                                    <input oninput="change_search_radius_m();" onchange="change_search_radius_m();" type="range" step="1000" min="0" max="100000" class="form-control-range" id="search_radius_m" value="<?php echo $map['search_radius_m']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt"></i> <?php echo _("Highlight Markers Search"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pin_marker_enable"><?php echo _("Enable"); ?> <i title="<?php echo _("enables placement of a marker when hovering over search results"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <input type="checkbox" id="pin_marker_enable" <?php echo ($map['pin_marker_enable']) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _("Icon Type"); ?></label>
                                    <select id="pin_icon_type" onchange="change_pin_icon_type();" class="form-control">
                                        <option <?php echo ($map['pin_marker_icon']=='' && $map['pin_marker_icon_image']=='') ? 'selected' : '' ; ?> id="0"><?php echo _("Circle"); ?></option>
                                        <option <?php echo ($map['pin_marker_icon']!='' && $map['pin_marker_icon_image']=='') ? 'selected' : '' ; ?> id="1"><?php echo _("Icon"); ?></option>
                                        <option <?php echo ($map['pin_marker_icon_image']!='') ? 'selected' : '' ; ?> id="2"><?php echo _("Icon Library"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div id="pin_icon_type_1" class="col-md-4 <?php echo ($map['pin_marker_icon']!='' && $map['pin_marker_icon_image']=='') ? '' : 'd-none' ; ?>">
                                <div class="form-group">
                                    <label><?php echo _("Icon"); ?></label><br>
                                    <button class="btn btn-sm btn-primary" type="button" id="GetIconPicker_pin" data-iconpicker-input="input#marker_icon_input_pin" data-iconpicker-preview="i#marker_icon_pin"><?php echo _("Select Icon"); ?></button>
                                    <input readonly type="text" id="marker_icon_input_pin" name="Icon" value="<?php echo $map['pin_marker_icon']; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                                    <div class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                                        <i id="marker_icon_pin" class="<?php echo $map['pin_marker_icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="pin_icon_type_2" class="col-md-4 <?php echo ($map['pin_marker_icon_image']!='') ? '' : 'd-none' ; ?>">
                                <div class="form-group">
                                    <label for="marker_library_icon_pin"><?php echo _("Icon"); ?></label><br>
                                    <button data-toggle="modal" data-target="#modal_library_icons_pin" class="btn btn-sm btn-primary" type="button" id="btn_library_icon_pin"><?php echo _("Select Library Icon"); ?></button>
                                    <input type="hidden" id="marker_library_icon_pin" value="<?php echo $map['pin_marker_id_icon_library']; ?>">
                                    <img id="marker_library_icon_preview_pin" style="height: 30px;display: <?php echo ($map['pin_marker_icon_image']=='') ? 'none':'inline-block'; ?>" src="../viewer/icons/<?php echo $map['pin_marker_icon_image']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pin_marker_size"><?php echo _("Size"); ?> (<span id="pin_marker_size_value"><?php echo $map['pin_marker_size']; ?></span>)</label><br>
                                    <input oninput="change_pin_marker_size();" onchange="change_pin_marker_size();" type="range" step="0.1" min="0.5" max="2.0" class="form-control-range" id="pin_marker_size" value="<?php echo $map['pin_marker_size']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _("Alignment"); ?></label>
                                    <select id="pin_marker_align" class="form-control">
                                        <option <?php echo ($map['pin_marker_align']=='bottom') ? 'selected' : '' ; ?> id="bottom"><?php echo _("Bottom"); ?></option>
                                        <option <?php echo ($map['pin_marker_align']=='middle') ? 'selected' : '' ; ?> id="middle"><?php echo _("Middle"); ?></option>
                                        <option <?php echo ($map['pin_marker_align']=='top') ? 'selected' : '' ; ?> id="top"><?php echo _("Top"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group <?php echo ($map['pin_marker_icon_image']!='') ? 'disabled':''; ?>">
                                    <label><?php echo _("Background"); ?></label>
                                    <input type="text" class="form-control" id="pin_color_hex" value="<?php echo $map['pin_marker_color_hex']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group <?php echo ($map['pin_marker_icon_image']!='' || ($map['pin_marker_icon']=='' && $map['pin_marker_icon_image']=='')) ? 'disabled':''; ?>">
                                    <label><?php echo _("Color"); ?></label>
                                    <input type="text" class="form-control" id="pin_icon_color_hex" value="<?php echo $map['pin_marker_icon_color_hex']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="defaults_tab">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-wpforms"></i> <?php echo _("Default Extra Fields Icons"); ?></h6>
                    </div>
                    <div class="card-body">
                        <?php
                        for($i=1;$i<=20;$i++) { ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button style="min-width: 150px;" class="btn btn-sm btn-primary field_icon_picker" type="button" id="GetIconPicker_e<?php echo $i; ?>" data-iconpicker-input="input#marker_icon_input_e<?php echo $i; ?>" data-iconpicker-preview="i#marker_icon_e<?php echo $i; ?>"><?php echo _("Field Icon"); ?> <?php echo $i; ?></button>
                                        <input style="display: none" readonly type="text" id="marker_icon_input_e<?php echo $i; ?>" name="Icon" value="<?php echo (array_key_exists($i,$default_extra_fields)) ? $default_extra_fields[$i] : 'far fa-circle'; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                                        <div style="width: 30px;" class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                                            <i id="marker_icon_e<?php echo $i; ?>" class="<?php echo (array_key_exists($i,$default_extra_fields)) ? $default_extra_fields[$i] : 'far fa-circle'; ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button id="btn_assign_fields" onclick="assign_fields_to_markers(<?php echo $id_map; ?>)" class="btn btn-block btn-primary <?php echo ($demo) ? 'disabled':''; ?> <?php echo ($count_markers==0) ? 'disabled' : ''; ?>"><?php echo _("ASSIGN FIELD ICONS TO")." ".$count_markers." "._("MARKERS"); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle"></i> <?php echo _("Add Markers From Map - Fields"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_categories" <?php echo (array_key_exists('categories',$default_am_fields)) ? (($default_am_fields['categories']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("Categories"); ?></label>
                                </div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_website" <?php echo (array_key_exists('website',$default_am_fields)) ? (($default_am_fields['website']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("Website"); ?></label>
                                </div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_email" <?php echo (array_key_exists('email',$default_am_fields)) ? (($default_am_fields['email']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("E-Mail"); ?></label>
                                </div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_phone" <?php echo (array_key_exists('phone',$default_am_fields)) ? (($default_am_fields['phone']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("Phone"); ?></label>
                                </div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_whatsapp" <?php echo (array_key_exists('whatsapp',$default_am_fields)) ? (($default_am_fields['whatsapp']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("Whatsapp"); ?></label>
                                </div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_description" <?php echo (array_key_exists('description',$default_am_fields)) ? (($default_am_fields['description']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("Description"); ?></label>
                                </div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_hours" <?php echo (array_key_exists('hours',$default_am_fields)) ? (($default_am_fields['hours']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("Hours"); ?></label>
                                </div>
                                <div class="form-group">
                                    <label><input type="checkbox" id="am_images" <?php echo (array_key_exists('images',$default_am_fields)) ? (($default_am_fields['images']==1) ? 'checked' : '') : 'checked'; ?> /> <?php echo _("Images"); ?></label>
                                </div>
                                <div class="row">
                                <?php for($i=1;$i<=20;$i++) { ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><input type="checkbox" id="am_field_<?php echo $i; ?>" <?php echo (array_key_exists('field_'.$i,$default_am_fields)) ? (($default_am_fields['field_'.$i]==1) ? 'checked' : '') : ''; ?> /> <?php echo _("Field")." $i"; ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="am_placeholder_<?php echo $i; ?>" class="form-control form-control-sm" placeholder="<?php echo _("placeholder")." $i"; ?>" value="<?php echo (array_key_exists($i,$default_am_placeholders)) ? (!empty($default_am_placeholders[$i]) ? $default_am_placeholders[$i] : '') : ''; ?>" />
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt"></i> <?php echo _("Default Markers Icon"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo _("Icon Type"); ?></label>
                                    <select id="icon_type" onchange="change_icon_type();" class="form-control">
                                        <option <?php echo ($map['markers_icon']=='' && $map['markers_icon_image']=='') ? 'selected' : '' ; ?> id="0"><?php echo _("Circle"); ?></option>
                                        <option <?php echo ($map['markers_icon']!='' && $map['markers_icon_image']=='') ? 'selected' : '' ; ?> id="1"><?php echo _("Icon"); ?></option>
                                        <option <?php echo ($map['markers_icon_image']!='') ? 'selected' : '' ; ?> id="2"><?php echo _("Icon Library"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div id="icon_type_1" class="col-md-12 <?php echo ($map['markers_icon']!='' && $map['markers_icon_image']=='') ? '' : 'd-none' ; ?>">
                                <div class="form-group">
                                    <label><?php echo _("Icon"); ?></label><br>
                                    <button class="btn btn-sm btn-primary" type="button" id="GetIconPicker" data-iconpicker-input="input#marker_icon_input" data-iconpicker-preview="i#marker_icon"><?php echo _("Select Icon"); ?></button>
                                    <input readonly type="text" id="marker_icon_input" name="Icon" value="<?php echo $map['markers_icon']; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                                    <div class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                                        <i id="marker_icon" class="<?php echo $map['markers_icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="icon_type_2" class="col-md-12 <?php echo ($map['markers_icon_image']!='') ? '' : 'd-none' ; ?>">
                                <div class="form-group">
                                    <label for="marker_library_icon"><?php echo _("Icon"); ?></label><br>
                                    <button data-toggle="modal" data-target="#modal_library_icons" class="btn btn-sm btn-primary" type="button" id="btn_library_icon"><?php echo _("Select Library Icon"); ?></button>
                                    <input type="hidden" id="marker_library_icon" value="<?php echo $map['markers_id_icon_library']; ?>">
                                    <img id="marker_library_icon_preview" style="height: 30px;display: <?php echo ($map['markers_icon_image']=='') ? 'none':'inline-block'; ?>" src="../viewer/icons/<?php echo $map['markers_icon_image']; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="markers_size"><?php echo _("Size"); ?> (<span id="markers_size_value"><?php echo $map['markers_size']; ?></span>)</label><br>
                                    <input oninput="change_markers_size();" onchange="change_markers_size();" type="range" step="0.1" min="0.5" max="2.0" class="form-control-range" id="markers_size" value="<?php echo $map['markers_size']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group <?php echo ($map['markers_icon_image']!='') ? 'disabled':''; ?>">
                                    <label><?php echo _("Background"); ?></label>
                                    <input type="text" class="form-control" id="color_hex" value="<?php echo $map['markers_color_hex']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group <?php echo ($map['markers_icon_image']!='' || ($map['markers_icon']=='' && $map['markers_icon_image']=='')) ? 'disabled':''; ?>">
                                    <label><?php echo _("Color"); ?></label>
                                    <input type="text" class="form-control" id="icon_color_hex" value="<?php echo $map['markers_icon_color_hex']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group <?php echo ($map['markers_icon_image']!='') ? 'disabled':''; ?>">
                                    <label><?php echo _("Background (Hover)"); ?></label>
                                    <input type="text" class="form-control" id="color_hex_hover" value="<?php echo $map['markers_color_hex_hover']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group <?php echo ($map['markers_icon_image']!='' || ($map['markers_icon']=='' && $map['markers_icon_image']=='')) ? 'disabled':''; ?>">
                                    <label><?php echo _("Color (Hover)"); ?></label>
                                    <input type="text" class="form-control" id="icon_color_hex_hover" value="<?php echo $map['markers_icon_color_hex_hover']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button id="btn_assign_icons" onclick="assign_icon_to_markers('map',<?php echo $id_map; ?>)" class="btn btn-block btn-primary <?php echo ($demo) ? 'disabled':''; ?> <?php echo ($count_markers==0) ? 'disabled' : ''; ?>"><?php echo _("ASSIGN ICON TO")." ".$count_markers." "._("MARKERS"); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="style_tab">
        <div class="row">
            <div class="col-md-8 p-0">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-image"></i> <?php echo _("Logo"); ?> (PNG)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label><?php echo _("Logo (Desktop)"); ?></label>
                                        </div>
                                        <div style="display: none;" id="div_image_logo" class="col-md-12 mb-2">
                                            <img style="width: 100%;height: 90px;object-fit: contain;background: lightgray" src="../viewer/logos/<?php echo $map['logo']; ?>" />
                                        </div>
                                        <div style="display: none" id="div_delete_logo" class="col-md-12">
                                            <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_logo();" class="btn btn-block btn-danger"><?php echo _("DELETE LOGO"); ?></button>
                                        </div>
                                        <div style="display: none" class="col-md-12" id="div_upload_logo">
                                            <form id="frm" action="ajax/upload_logo_image.php" method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="txtFile" name="txtFile" />
                                                                <label class="custom-file-label" for="txtFile_im"><?php echo _("Choose file"); ?></label>
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
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label><?php echo _("Logo (Mobile)"); ?></label>
                                        </div>
                                        <div style="display: none;" id="div_image_logo_mobile" class="col-md-12 mb-2">
                                            <img style="width: 100%;height: 90px;object-fit: contain;background: lightgray" src="../viewer/logos/<?php echo $map['logo_mobile']; ?>" />
                                        </div>
                                        <div style="display: none" id="div_delete_logo_mobile" class="col-md-12">
                                            <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_logo_mobile();" class="btn btn-block btn-danger"><?php echo _("DELETE LOGO"); ?></button>
                                        </div>
                                        <div style="display: none" class="col-md-12" id="div_upload_logo_mobile">
                                            <form id="frm_mobile" action="ajax/upload_logo_image.php" method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="txtFile_mobile" name="txtFile_mobile" />
                                                                <label class="custom-file-label" for="txtFile_im"><?php echo _("Choose file"); ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_mobile" value="<?php echo _("Upload Logo Image"); ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="preview text-center">
                                                            <div class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                                <div class="progress-bar" id="progressBar_mobile" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                                    0%
                                                                </div>
                                                            </div>
                                                            <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_mobile"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="logo_height"><?php echo _("Height (Desktop)"); ?></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="logo_height" value="<?php echo $map['logo_height']; ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">px</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="logo_height_mobile"><?php echo _("Height (Mobile)"); ?></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="logo_height_mobile" value="<?php echo $map['logo_height_mobile']; ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">px</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="logo_link"><?php echo _("Link"); ?></label>
                                                <input type="tel" class="form-control" id="logo_link" value="<?php echo $map['logo_link']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map"></i> <?php echo _("Map - Street"); ?>&nbsp;&nbsp;&nbsp;<input onchange="change_enable_street();" style="vertical-align:middle" type="checkbox" id="enable_map_street" <?php echo ($map['enable_map_street']) ? 'checked' : ''; ?> /></h6>
                        </div>
                        <div class="card-body street_body <?php echo ($map['enable_map_street']) ? '' : 'disabled'; ?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <label><?php echo _("Style"); ?></label>
                                    <ul class="nav bg-gray-100 nav-pills nav-fill mb-3">
                                        <li class="nav-item">
                                            <a onclick="change_street_style('default')" class="nav-link <?php echo ($map_style_sel=='default') ? 'active' : ''; ?>" data-toggle="pill" href="#map_default_tab"><?php echo strtoupper(_("DEFAULT")); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a onclick="change_street_style('maptiler')" class="nav-link <?php echo ($map_style_sel=='maptiler') ? 'active' : ''; ?>" data-toggle="pill" href="#map_maptiler_tab">MAPTILER</a>
                                        </li>
                                        <li class="nav-item">
                                            <a onclick="change_street_style('here')" class="nav-link <?php echo ($map_style_sel=='here') ? 'active' : ''; ?>" data-toggle="pill" href="#map_here_tab">HERE</a>
                                        </li>
                                        <li class="nav-item">
                                            <a onclick="change_street_style('custom')" class="nav-link <?php echo ($map_style_sel=='custom') ? 'active' : ''; ?>" data-toggle="pill" href="#map_custom_tab"><?php echo strtoupper(_("CUSTOM")); ?></a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane <?php echo ($map_style_sel=='default') ? 'active' : ''; ?>" id="map_default_tab">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="map_style" title="OPEN STREET MAP" data-provider="default" id="osm_free">
                                                        <img src="img/osm_free.png">
                                                    </div>
                                                    <div class="map_style" title="ARCGIS STREETS" data-provider="default" id="arcgis_street">
                                                        <img src="img/arcgis_street.png">
                                                    </div>
                                                    <div class="map_style" title="ARCGIS TOPOGRAPHIC" data-provider="default" id="arcgis_topo">
                                                        <img src="img/arcgis_topo.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane <?php echo ($map_style_sel=='here') ? 'active' : ''; ?>" id="map_here_tab">
                                            <div class="row">
                                                <div class="col-md-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="here_api">Here API KEY (<a target="_blank" href="https://developer.here.com/?create=Freemium-Basic"><?php echo _("link"); ?></a>)</label>
                                                        <input type="text" class="form-control" id="here_api" value="<?php echo $map['here_api']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="map_style" title="BASIC" data-provider="here" id="explore.day">
                                                        <img src="img/explore.day.png">
                                                    </div>
                                                    <div class="map_style" title="DARK" data-provider="here" id="explore.night">
                                                        <img src="img/explore.night.png">
                                                    </div>
                                                    <div class="map_style" title="TOPOGRAPHIC" data-provider="here" id="topo.day">
                                                        <img src="img/topo.day.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane <?php echo ($map_style_sel=='custom') ? 'active' : ''; ?>" id="map_custom_tab">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="street_basemap"><?php echo _("Tile Url"); ?></label>
                                                        <input type="text" class="form-control" id="street_basemap" value="<?php echo $map['street_basemap']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane <?php echo ($map_style_sel=='maptiler') ? 'active' : ''; ?>" id="map_maptiler_tab">
                                            <div class="row">
                                                <div class="col-md-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="maptiler_api">Maptiler API KEY (<a target="_blank" href="https://www.maptiler.com/cloud/plans/"><?php echo _("link"); ?></a>)</label>
                                                        <input type="text" class="form-control" id="maptiler_api" value="<?php echo $map['maptiler_api']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="map_style" title="BASIC" data-provider="maptiler" id="basic-v2">
                                                        <img src="img/basic-v2.png">
                                                    </div>
                                                    <div class="map_style" title="BASIC DARK" data-provider="maptiler" id="basic-v2-dark">
                                                        <img src="img/basic-v2-dark.png">
                                                    </div>
                                                    <div class="map_style" title="BASIC LIGHT" data-provider="maptiler" id="basic-v2-light">
                                                        <img src="img/basic-v2-light.png">
                                                    </div>
                                                    <div class="map_style" title="BACKDROP" data-provider="maptiler" id="backdrop">
                                                        <img src="img/backdrop.png">
                                                    </div>
                                                    <div class="map_style" title="BACKDROP DARK" data-provider="maptiler" id="backdrop-dark">
                                                        <img src="img/backdrop-dark.png">
                                                    </div>
                                                    <div class="map_style" title="BACKDROP LIGHT" data-provider="maptiler" id="backdrop-light">
                                                        <img src="img/backdrop-light.png">
                                                    </div>
                                                    <div class="map_style" title="BRIGHT" data-provider="maptiler" id="bright-v2">
                                                        <img src="img/bright-v2.png">
                                                    </div>
                                                    <div class="map_style" title="BRIGHT DARK" data-provider="maptiler" id="bright-v2-dark">
                                                        <img src="img/bright-v2-dark.png">
                                                    </div>
                                                    <div class="map_style" title="BRIGHT LIGHT" data-provider="maptiler" id="bright-v2-light">
                                                        <img src="img/bright-v2-light.png">
                                                    </div>
                                                    <div class="map_style" title="BRIGHT PASTEL" data-provider="maptiler" id="bright-v2-pastel">
                                                        <img src="img/bright-v2-pastel.png">
                                                    </div>
                                                    <div class="map_style" title="DATAVIZ" data-provider="maptiler" id="dataviz">
                                                        <img src="img/dataviz.png">
                                                    </div>
                                                    <div class="map_style" title="DATAVIZ DARK" data-provider="maptiler" id="dataviz-dark">
                                                        <img src="img/dataviz-dark.png">
                                                    </div>
                                                    <div class="map_style" title="DATAVIZ LIGHT" data-provider="maptiler" id="dataviz-light">
                                                        <img src="img/dataviz-light.png">
                                                    </div>
                                                    <div class="map_style" title="OCEAN" data-provider="maptiler" id="ocean">
                                                        <img src="img/ocean.png">
                                                    </div>
                                                    <div class="map_style" title="OPEN STREET MAP" data-provider="maptiler" id="openstreetmap">
                                                        <img src="img/openstreetmap.png">
                                                    </div>
                                                    <div class="map_style" title="OUTDOOR" data-provider="maptiler" id="outdoor-v2">
                                                        <img src="img/outdoor-v2.png">
                                                    </div>
                                                    <div class="map_style" title="OUTDOOR DARK" data-provider="maptiler" id="outdoor-v2-dark">
                                                        <img src="img/outdoor-v2-dark.png">
                                                    </div>
                                                    <div class="map_style" title="STREETS" data-provider="maptiler" id="streets">
                                                        <img src="img/streets.png">
                                                    </div>
                                                    <div class="map_style" title="STREETS DARK" data-provider="maptiler" id="streets-v2-dark">
                                                        <img src="img/streets-v2-dark.png">
                                                    </div>
                                                    <div class="map_style" title="STREETS LIGHT" data-provider="maptiler" id="streets-v2-light">
                                                        <img src="img/streets-v2-light.png">
                                                    </div>
                                                    <div class="map_style" title="STREETS PASTEL" data-provider="maptiler" id="streets-v2-pastel">
                                                        <img src="img/streets-v2-pastel.png">
                                                    </div>
                                                    <div class="map_style" title="TONER" data-provider="maptiler" id="toner-v2">
                                                        <img src="img/toner-v2.png">
                                                    </div>
                                                    <div class="map_style" title="TONER LITE" data-provider="maptiler" id="toner-v2-lite">
                                                        <img src="img/toner-v2-lite.png">
                                                    </div>
                                                    <div class="map_style" title="TOPO" data-provider="maptiler" id="topo">
                                                        <img src="img/topo.png">
                                                    </div>
                                                    <div class="map_style" title="WINTER" data-provider="maptiler" id="winter-v2">
                                                        <img src="img/winter-v2.png">
                                                    </div>
                                                    <div class="map_style" title="WINTER DARK" data-provider="maptiler" id="winter-v2-dark">
                                                        <img src="img/winter-v2-dark.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="street_maxzoom"><?php echo _("Max Zoom"); ?></label>
                                        <input type="number" min="1" max="20" class="form-control" id="street_maxzoom" value="<?php echo $map['street_maxzoom']; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="street_attributions"><?php echo _("Attributions"); ?> <i title="<?php echo _("uncheck to hide the attributions, leave empty for default attributions"); ?>" class="help_t fas fa-question-circle"></i>&nbsp;&nbsp;<input onclick="change_use_street_attributions();" style="vertical-align:middle;" type="checkbox" id="use_street_attributions" <?php echo ($map['use_street_attributions']) ? 'checked' : ''; ?> /></label>
                                        <input <?php echo ($map['use_street_attributions']) ? '' : 'disabled'; ?> placeholder="<?php echo _("Default Attributions"); ?>" type="text" class="form-control" id="street_attributions" value='' />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="street_attributions_d"><?php echo _("Default Attributions"); ?></label>
                                        <input id="street_attributions_d" type="text" class="form-control" readonly value='' />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map"></i> <?php echo _("Map - Satellite"); ?>&nbsp;&nbsp;&nbsp;<input onchange="change_enable_satellite();" style="vertical-align:middle;" type="checkbox" id="enable_map_satellite" <?php echo ($map['enable_map_satellite']) ? 'checked' : ''; ?> /></h6>
                        </div>
                        <div class="card-body satellite_body <?php echo ($map['enable_map_satellite']) ? '' : 'disabled'; ?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <label><?php echo _("Style"); ?></label>
                                    <ul class="nav bg-gray-100 nav-pills nav-fill mb-3">
                                        <li class="nav-item">
                                            <a onclick="change_satellite_style('default')" class="nav-link <?php echo ($map_style_sel=='default') ? 'active' : ''; ?>" data-toggle="pill" href="#map_default_sat_tab"><?php echo strtoupper(_("DEFAULT")); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a onclick="change_satellite_style('maptiler')" class="nav-link <?php echo ($map_style_sel=='maptiler') ? 'active' : ''; ?>" data-toggle="pill" href="#map_maptiler_sat_tab">MAPTILER</a>
                                        </li>
                                        <li class="nav-item">
                                            <a onclick="change_satellite_style('here')" class="nav-link <?php echo ($map_style_sel=='here') ? 'active' : ''; ?>" data-toggle="pill" href="#map_here_sat_tab">HERE</a>
                                        </li>
                                        <li class="nav-item">
                                            <a onclick="change_satellite_style('custom')" class="nav-link <?php echo ($map_style_sel=='custom') ? 'active' : ''; ?>" data-toggle="pill" href="#map_custom_sat_tab"><?php echo strtoupper(_("CUSTOM")); ?></a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane <?php echo ($map_style_sel=='default') ? 'active' : ''; ?>" id="map_default_sat_tab">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="map_style_satellite" title="ESRI SATELLITE" data-provider="default" id="arcgis">
                                                        <img src="img/arcgis.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane <?php echo ($map_style_sel=='here') ? 'active' : ''; ?>" id="map_here_sat_tab">
                                            <div class="row">
                                                <div class="col-md-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="here_api_satellite">Here API KEY (<a target="_blank" href="https://developer.here.com/?create=Freemium-Basic"><?php echo _("link"); ?></a>)</label>
                                                        <input type="text" class="form-control" id="here_api_satellite" value="<?php echo $map['here_api_satellite']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="map_style_satellite" title="SATELLITE" data-provider="here" id="satellite.day">
                                                        <img src="img/satellite.day.png">
                                                    </div>
                                                    <div class="map_style_satellite" title="HYBRID" data-provider="here" id="explore.satellite.day">
                                                        <img src="img/explore.satellite.day.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane <?php echo ($map_style_sel=='custom') ? 'active' : ''; ?>" id="map_custom_sat_tab">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="satellite_basemap"><?php echo _("Tile Url"); ?></label>
                                                        <input type="text" class="form-control" id="satellite_basemap" value="<?php echo $map['satellite_basemap']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane <?php echo ($map_style_sel=='maptiler') ? 'active' : ''; ?>" id="map_maptiler_sat_tab">
                                            <div class="row">
                                                <div class="col-md-12 mb-1">
                                                    <div class="form-group">
                                                        <label for="maptiler_api_satellite">Maptiler API KEY (<a target="_blank" href="https://www.maptiler.com/cloud/plans/"><?php echo _("link"); ?></a>)</label>
                                                        <input type="text" class="form-control" id="maptiler_api_satellite" value="<?php echo $map['maptiler_api_satellite']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="map_style_satellite" title="SATELLITE" data-provider="maptiler" id="satellite-v2">
                                                        <img src="img/satellite.png">
                                                    </div>
                                                    <div class="map_style_satellite" title="HYBRID" data-provider="maptiler" id="hybrid">
                                                        <img src="img/hybrid.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="satellite_maxzoom"><?php echo _("Max Zoom"); ?></label>
                                        <input type="number" min="1" max="20" class="form-control" id="satellite_maxzoom" value="<?php echo $map['satellite_maxzoom']; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="satellite_attributions"><?php echo _("Attributions"); ?> <i title="<?php echo _("uncheck to hide the attributions, leave empty for default attributions"); ?>" class="help_t fas fa-question-circle"></i>&nbsp;&nbsp;<input onclick="change_use_satellite_attributions();" style="vertical-align:middle;" type="checkbox" id="use_satellite_attributions" <?php echo ($map['use_satellite_attributions']) ? 'checked' : ''; ?> /></label>
                                        <input <?php echo ($map['use_satellite_attributions']) ? '' : 'disabled'; ?> placeholder="<?php echo _("Default Attributions"); ?>" type="text" class="form-control" id="satellite_attributions" value='' />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="satellite_attributions_d"><?php echo _("Default Attributions"); ?></label>
                                        <input id="satellite_attributions_d" type="text" class="form-control" readonly value='' />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-palette"></i> <?php echo _("General"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="main_color_hex"><?php echo _("Main color"); ?></label>
                                    <input type="text" class="form-control" id="main_color_hex" value="<?php echo $map['main_color_hex']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="font_viewer"><?php echo _("Font Viewer"); ?></label><br>
                                    <input type="text" class="form-control <?php echo ($settings['font_provider']=='systems') ? 'd-none' : ''; ?>" id="font_viewer" value="<?php echo $map['font_viewer']; ?>" />
                                    <select class="form-control <?php echo ($settings['font_provider']=='systems') ? '' : 'd-none'; ?>" id="font_viewer_system">
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
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-eye"></i> <?php echo _("View"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="map_type"><?php echo _("Map type"); ?></label><br>
                                    <select id="map_type" class="form-control">
                                        <option <?php echo ($map['map_type']=='default') ? 'selected' : ''; ?> id="default"><?php echo _("Default"); ?></option>
                                        <option <?php echo ($map['map_type']=='large') ? 'selected' : ''; ?> id="large"><?php echo _("High number of markers"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="default_view"><?php echo _("Default view"); ?></label><br>
                                    <select id="default_view" class="form-control">
                                        <option <?php echo ($map['default_view']=='street') ? 'selected' : ''; ?> id="street"><?php echo _("Street"); ?></option>
                                        <option <?php echo ($map['default_view']=='satellite') ? 'selected' : ''; ?> id="satellite"><?php echo _("Satellite"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="enable_globe"><?php echo _("Globe view"); ?></label><br>
                                    <select id="enable_globe" class="form-control">
                                        <option <?php echo ($map['enable_globe']==0) ? 'selected' : ''; ?> id="0"><?php echo _("Disabled"); ?></option>
                                        <option <?php echo ($map['enable_globe']==1) ? 'selected' : ''; ?> id="1"><?php echo _("Enabled"); ?></option>
                                        <option <?php echo ($map['enable_globe']==2) ? 'selected' : ''; ?> id="2"><?php echo _("Enabled (autostart)"); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-atlas"></i> GeoJSON</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label><?php echo _("Link or upload Json"); ?></label>
                                <input type="text" class="form-control" id="geoJSON" value="<?php echo $map['geoJSON']; ?>" />
                                <div class="md-12 mt-3" id="div_upload_geojson">
                                    <form id="frm_geojson" action="ajax/upload_geojson.php" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="txtFile_geojson" name="txtFile_geojson" />
                                                        <label class="custom-file-label" for="txtFile_im"><?php echo _("Choose file"); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_geojson" value="<?php echo _("Upload Json"); ?>" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="preview text-center">
                                                    <div class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                        <div class="progress-bar" id="progressBar_geojson" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                            0%
                                                        </div>
                                                    </div>
                                                    <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_geojson"></div>
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
    </div>
    <div class="tab-pane" id="loading_tab">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-spinner"></i> <?php echo _("Settings"); ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="auto_start"><?php echo _("Auto start"); ?> <i title="<?php echo _("start the map automatically on loading"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                                    <select id="auto_start" class="form-control">
                                        <option <?php echo ($map['auto_start']==0)?'selected':''; ?> id="0"><?php echo _("Disabled"); ?></option>
                                        <option <?php echo ($map['auto_start']==1)?'selected':''; ?> id="1"><?php echo _("Enabled"); ?></option>
                                        <option <?php echo ($map['auto_start']==2)?'selected':''; ?> id="2"><?php echo _("Enabled (Disabled when embedded)"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loading_background_color"><?php echo _("Loading Background Color"); ?></label><br>
                                    <input type="text" class="form-control" id="loading_background_color" value="<?php echo $map['loading_background_color']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loading_text_color"><?php echo _("Loading Text Color"); ?></label><br>
                                    <input type="text" class="form-control" id="loading_text_color" value="<?php echo $map['loading_text_color']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-window-maximize"></i> <?php echo _("Intro"); ?> <i title="<?php echo _("this section will be shown just after the map loads and before you can view it"); ?>" class="help_t fas fa-question-circle"></i>&nbsp;&nbsp;&nbsp;<input onchange="change_enable_intro();" style="vertical-align:middle" type="checkbox" id="enable_intro" <?php echo ($map['enable_intro']) ? 'checked' : ''; ?> /></h6>
                    </div>
                    <div class="card-body intro_body <?php echo ($map['enable_intro']) ? '' : 'disabled'; ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_background"><?php echo _("Background"); ?></label><br>
                                    <input type="text" class="form-control" id="intro_background" value="<?php echo $intro_params['background']; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="intro_title"><?php echo _("Title"); ?></label><br>
                                    <input type="text" class="form-control" id="intro_title" value="<?php echo $intro_params['title']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_title_size"><?php echo _("Title - Size"); ?></label><br>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control" id="intro_title_size" value="<?php echo $intro_params['title_size']; ?>" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_title_color"><?php echo _("Title - Color"); ?></label><br>
                                    <input type="text" class="form-control" id="intro_title_color" value="<?php echo $intro_params['title_color']; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="intro_description"><?php echo _("Description"); ?></label><br>
                                    <textarea rows="2" class="form-control" id="intro_description"><?php echo $intro_params['description']; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_description_size"><?php echo _("Description - Size"); ?></label><br>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control" id="intro_description_size" value="<?php echo $intro_params['description_size']; ?>" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_description_color"><?php echo _("Description - Color"); ?></label><br>
                                    <input type="text" class="form-control" id="intro_description_color" value="<?php echo $intro_params['description_color']; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="intro_image"><?php echo _("Image"); ?></label><br>
                                <div style="display: none" id="div_image_im" class="col-md-12">
                                    <img src="<?php echo $url_intro_image; ?>" />
                                </div>
                                <div style="display: none" id="div_delete_im" class="col-md-12 mt-2 mb-2">
                                    <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_im();" class="btn btn-block btn-danger"><?php echo _("REMOVE IMAGE"); ?></button>
                                </div>
                                <div id="div_upload_im" class="md-12">
                                    <form id="frm_im" action="ajax/upload_intro_image.php" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="txtFile_im" name="txtFile_im" />
                                                        <label class="custom-file-label" for="txtFile_im"><?php echo _("Choose file"); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_im" value="<?php echo _("Upload Image"); ?>" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="preview text-center">
                                                    <div class="progress progress_im mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                        <div class="progress-bar" id="progressBar_im" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                            0%
                                                        </div>
                                                    </div>
                                                    <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_im"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_image_height"><?php echo _("Image - Height"); ?></label><br>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control" id="intro_image_height" value="<?php echo $intro_params['image_height']; ?>" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_button"><?php echo _("Button"); ?></label><br>
                                    <input type="text" class="form-control" id="intro_button" value="<?php echo $intro_params['button']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_button_size"><?php echo _("Button - Size"); ?></label><br>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control" id="intro_button_size" value="<?php echo $intro_params['button_size']; ?>" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_button_background"><?php echo _("Button - Background"); ?></label><br>
                                    <input type="text" class="form-control" id="intro_button_background" value="<?php echo $intro_params['button_background']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="intro_button_color"><?php echo _("Button - Color"); ?></label><br>
                                    <input type="text" class="form-control" id="intro_button_color" value="<?php echo $intro_params['button_color']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="far fa-image"></i> <?php echo _("Background Image (Desktop)"); ?> <i title="<?php echo _("image displayed as background during initial loading and used as preview image for share"); ?>" class="help_t fas fa-question-circle"></i></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div style="display: none" id="div_image_bg" class="col-md-12">
                                <img src="<?php echo $url_background_image; ?>" />
                            </div>
                            <div style="display: none" id="div_delete_bg" class="col-md-12 mt-2">
                                <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_bg();" class="btn btn-block btn-danger"><?php echo _("REMOVE IMAGE"); ?></button>
                            </div>
                            <div style="display: none" class="col-md-12" id="div_upload_bg">
                                <form id="frm_b" action="ajax/upload_background_image.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="txtFile_b" name="txtFile_b" />
                                                    <label class="custom-file-label" for="txtFile_b"><?php echo _("Choose file"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_b" value="<?php echo _("Upload Background Image"); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="preview text-center">
                                                <div class="progress progress_b mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
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
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="far fa-image"></i> <?php echo _("Background Image (Mobile)"); ?> <i title="<?php echo _("image displayed as background during initial loading on a mobile"); ?>" class="help_t fas fa-question-circle"></i></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div style="display: none;" id="div_image_bg_m" class="col-md-12">
                                <img src="<?php echo $url_background_image_mobile; ?>" />
                            </div>
                            <div style="display: none" id="div_delete_bg_m" class="col-md-12 mt-2">
                                <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_bg_m();" class="btn btn-block btn-danger"><?php echo _("REMOVE IMAGE"); ?></button>
                            </div>
                            <div style="display: none" class="col-md-12" id="div_upload_bg_m">
                                <form id="frm_b_m" action="ajax/upload_background_image_m.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="txtFile_b_m" name="txtFile_b_m" />
                                                    <label class="custom-file-label" for="txtFile_b_m"><?php echo _("Choose file"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_b_m" value="<?php echo _("Upload Background Image"); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="preview text-center">
                                                <div class="progress progress_b_m mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                    <div class="progress-bar" id="progressBar_b_m" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                        0%
                                                    </div>
                                                </div>
                                                <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_b_m"></div>
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
</div>

<div id="modal_library_icons" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Library Icons"); ?></h5>
            </div>
            <div class="modal-body">
                <?php echo get_library_icons($id_map); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_library_icons_pin" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Library Icons"); ?></h5>
            </div>
            <div class="modal-body">
                <?php echo get_library_icons_pin($id_map); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_map = <?php echo $id_map; ?>;
        window.map_style = '<?php echo $map['map_style']; ?>';
        window.map_style_satellite = '<?php echo $map['map_style_satellite']; ?>';
        window.map_style_sel = '<?php echo $map_style_sel; ?>';
        window.map_style_satellite_sel = '<?php echo $map_style_satellite_sel; ?>';
        window.logo = '<?php echo $map['logo']; ?>';
        window.logo_mobile = '<?php echo $map['logo_mobile']; ?>';
        window.main_color_hex_spectrum = null;
        window.color_hex_spectrum = null;
        window.icon_color_hex_spectrum = null;
        window.pin_color_hex_spectrum = null;
        window.pin_icon_color_hex_spectrum = null;
        window.color_hex_hover_spectrum = null;
        window.icon_color_hex_hover_spectrum = null;
        window.map_need_save = false;
        window.font_provider = '<?php echo $settings['font_provider']; ?>';
        window.background_image = '<?php echo $map['background_image']; ?>';
        window.background_image_mobile = '<?php echo $map['background_image_mobile']; ?>';
        window.intro_image = '<?php echo $map['intro_image']; ?>';
        window.loading_background_color_spectrum = null;
        window.loading_text_color_spectrum = null;
        window.osm_attributions = '© <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors';
        window.arcgis_attributions = '© <a href="https://www.esri.com/it-it/legal/copyright-trademarks" target="_blank">Esri</a> tiles - © <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors';
        window.maptiler_attributions = '© <a href="https://www.maptiler.com/copyright/" target="_blank">MapTiler</a> tiles - © <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors';
        window.here_attributions = '© <a href="https://legal.here.com/en-gb/terms/here-end-user-terms" target="_blank">HERE</a> Technologies';
        var street_attributions = `<?php echo $map['street_attributions'] ?>`;
        var satellite_attributions = `<?php echo $map['satellite_attributions'] ?>`;
        $(document).ready(function () {
            bsCustomFileInput.init();
            $('.help_t').tooltip();
            $('.map_style').tooltipster({delay:0});
            $('.map_style_satellite').tooltipster({delay:0});
            $('#street_attributions').val(street_attributions);
            $('#satellite_attributions').val(satellite_attributions);
            change_street_style(window.map_style_sel,false);
            change_satellite_style(map_style_satellite_sel,false);
            if(logo=='') {
                $('#div_delete_logo').hide();
                $('#div_image_logo').hide();
                $('#div_upload_logo').show();
            } else {
                $('#div_delete_logo').show();
                $('#div_image_logo').show();
                $('#div_upload_logo').hide();
            }
            if(logo_mobile=='') {
                $('#div_delete_logo_mobile').hide();
                $('#div_image_logo_mobile').hide();
                $('#div_upload_logo_mobile').show();
            } else {
                $('#div_delete_logo_mobile').show();
                $('#div_image_logo_mobile').show();
                $('#div_upload_logo_mobile').hide();
            }
            if(background_image=='') {
                $('#div_delete_bg').hide();
                $('#div_image_bg').hide();
                $('#div_upload_bg').show();
            } else {
                $('#div_delete_bg').show();
                $('#div_image_bg').show();
                $('#div_upload_bg').hide();
            }
            if(background_image_mobile=='') {
                $('#div_delete_bg_m').hide();
                $('#div_image_bg_m').hide();
                $('#div_upload_bg_m').show();
            } else {
                $('#div_delete_bg_m').show();
                $('#div_image_bg_m').show();
                $('#div_upload_bg_m').hide();
            }
            if(intro_image=='') {
                $('#div_delete_im').hide();
                $('#div_image_im').hide();
                $('#div_upload_im').show();
            } else {
                $('#div_delete_im').show();
                $('#div_image_im').show();
                $('#div_upload_im').hide();
            }
            $('#font_viewer').fontpicker({
                variants:false,
                localFonts: {},
                nrRecents: 0
            });
            if(window.font_provider=='systems') {
                $('.font-picker').addClass('d-none');
            }
            window.main_color_hex_spectrum = $('#main_color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: false
            });
            window.loading_background_color_spectrum = $('#loading_background_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: false
            });
            window.loading_text_color_spectrum = $('#loading_text_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: false
            });
            $('#'+map_style+' img').addClass("style-highlight");
            $('#'+map_style_satellite+' img').addClass("style-highlight");
            IconPicker.Init({
                jsonUrl: 'vendor/iconpicker/iconpicker-1.6.0.json',
                searchPlaceholder: '<?php echo _("Search Icon"); ?>',
                showAllButton: '<?php echo _("Show All"); ?>',
                cancelButton: '<?php echo _("Cancel"); ?>',
                noResultsFound: '<?php echo _("No results found."); ?>',
                borderRadius: '20px',
            });
            IconPicker.Run('#GetIconPicker', function(){
                $('#btn_delete_icon').prop('disabled',false);
                window.map_need_save = true;
            });
            IconPicker.Run('#GetIconPicker_pin', function(){
                window.map_need_save = true;
            });
            IconPicker.Run('.field_icon_picker', function(){
                window.marker_need_save = true;
            });
            window.color_hex_spectrum = $('#color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            window.icon_color_hex_spectrum = $('#icon_color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: false
            });
            window.pin_color_hex_spectrum = $('#pin_color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            window.pin_icon_color_hex_spectrum = $('#pin_icon_color_hex').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: false
            });
            window.color_hex_hover_spectrum = $('#color_hex_hover').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            window.icon_color_hex_hover_spectrum = $('#icon_color_hex_hover').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            $('#intro_background').spectrum({
                type: "text",
                preferredFormat: "rgb",
                showAlpha: true,
                showButtons: false,
                allowEmpty: true
            });
            $('#intro_title_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            $('#intro_description_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            $('#intro_button_background').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
            $('#intro_button_color').spectrum({
                type: "text",
                preferredFormat: "hex",
                showAlpha: false,
                showButtons: false,
                allowEmpty: true
            });
        });
        $('.map_style').on('click',function () {
            $('.map_style img').removeClass("style-highlight");
            $(this).find('img').addClass("style-highlight");
            change_street_style(window.map_style_sel,true);
            window.map_need_save = true;
        });
        $('.map_style_satellite').on('click',function () {
            $('.map_style_satellite img').removeClass("style-highlight");
            $(this).find('img').addClass("style-highlight");
            change_satellite_style(map_style_satellite_sel,true);
            window.map_need_save = true;
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
                        window.map_need_save = true;
                        window.logo = evt.target.responseText;
                        $('#div_image_logo img').attr('src','../viewer/logos/'+window.logo);
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

        $('body').on('submit','#frm_mobile',function(e){
            e.preventDefault();
            $('#error_mobile').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_mobile[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_mobile' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar_mobile(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_mobile(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.map_need_save = true;
                        window.logo_mobile = evt.target.responseText;
                        $('#div_image_logo_mobile img').attr('src','../viewer/logos/'+window.logo_mobile);
                        $('#div_delete_logo_mobile').show();
                        $('#div_image_logo_mobile').show();
                        $('#div_upload_logo_mobile').hide();
                    }
                }
                upadte_progressbar_mobile(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_mobile('upload failed');
                upadte_progressbar_mobile(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_mobile('upload aborted');
                upadte_progressbar_mobile(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar_mobile(value){
            $('#progressBar_mobile').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress').hide();
            }else{
                $('.progress').show();
            }
        }

        function show_error_mobile(error){
            $('.progress').hide();
            $('#error_mobile').show();
            $('#error_mobile').html(error);
        }

        $('body').on('submit','#frm_geojson',function(e){
            e.preventDefault();
            $('#error_geojson').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_geojson[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_geojson' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                upadte_progressbar_geojson(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_geojson(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.map_need_save = true;
                        $('#geoJSON').val(evt.target.responseText);
                    }
                }
                upadte_progressbar_geojson(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_geojson('upload failed');
                upadte_progressbar_geojson(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_geojson('upload aborted');
                upadte_progressbar_geojson(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar_geojson(value){
            $('#progressBar_geojson').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress').hide();
            }else{
                $('.progress').show();
            }
        }

        function show_error_geojson(error){
            $('.progress').hide();
            $('#error_geojson').show();
            $('#error_geojson').html(error);
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
                update_progressbar_b(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_b(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.map_need_save = true;
                        window.background_image = evt.target.responseText;
                        $('#div_image_bg img').attr('src','../viewer/content/'+window.background_image);
                        $('#div_delete_bg').show();
                        $('#div_image_bg').show();
                        $('#div_upload_bg').hide();
                    }
                }
                update_progressbar_b(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_b('upload failed');
                update_progressbar_b(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_b('upload aborted');
                update_progressbar_b(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function update_progressbar_b(value){
            $('#progressBar_b').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress_b').hide();
            }else{
                $('.progress_b').show();
            }
        }

        function show_error_b(error){
            $('.progress_b').hide();
            $('#error_b').show();
            $('#error_b').html(error);
        }

        $('body').on('submit','#frm_b_m',function(e){
            e.preventDefault();
            $('#error_b_m').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_b_m[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_b_m' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                update_progressbar_b_m(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_b_m(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.map_need_save = true;
                        window.background_image_mobile = evt.target.responseText;
                        $('#div_image_bg_m img').attr('src','../viewer/content/'+window.background_image_mobile);
                        $('#div_delete_bg_m').show();
                        $('#div_image_bg_m').show();
                        $('#div_upload_bg_m').hide();
                    }
                }
                update_progressbar_b_m(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_b_m('upload failed');
                update_progressbar_b_m(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_b_m('upload aborted');
                update_progressbar_b_m(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function update_progressbar_b_m(value){
            $('#progressBar_b_m').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress_b_m').hide();
            }else{
                $('.progress_b_m').show();
            }
        }

        function show_error_b_m(error){
            $('.progress_b_m').hide();
            $('#error_b_m').show();
            $('#error_b_m').html(error);
        }

        $('body').on('submit','#frm_im',function(e){
            e.preventDefault();
            $('#error_im').hide();
            var url = $(this).attr('action');
            var frm = $(this);
            var data = new FormData();
            if(frm.find('#txtFile_im[type="file"]').length === 1 ){
                data.append('file', frm.find( '#txtFile_im' )[0].files[0]);
            }
            var ajax  = new XMLHttpRequest();
            ajax.upload.addEventListener('progress',function(evt){
                var percentage = (evt.loaded/evt.total)*100;
                update_progressbar_im(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_im(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.map_need_save = true;
                        window.intro_image = evt.target.responseText;
                        $('#div_image_im img').attr('src','../viewer/content/'+window.intro_image);
                        $('#div_delete_im').show();
                        $('#div_image_im').show();
                        $('#div_upload_im').hide();
                    }
                }
                update_progressbar_im(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_im('upload failed');
                update_progressbar_im(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_im('upload aborted');
                update_progressbar_im(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function update_progressbar_im(value){
            $('#progressBar_im').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress_im').hide();
            }else{
                $('.progress_im').show();
            }
        }

        function show_error_im(error){
            $('.progress_im').hide();
            $('#error_im').show();
            $('#error_im').html(error);
        }

        $("input").change(function(){
            window.map_need_save = true;
        });

        $(window).on('beforeunload', function(){
            if(window.map_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });
    })(jQuery);

    const checkbox1 = document.getElementById('enable_map_street');
    const checkbox2 = document.getElementById('enable_map_satellite');
    checkbox1.addEventListener('change', handleCheckboxChange);
    checkbox2.addEventListener('change', handleCheckboxChange);
    function handleCheckboxChange() {
        if (!checkbox1.checked && !checkbox2.checked) {
            this.checked = true;
        }
    }
</script>
