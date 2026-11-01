<?php
$settings = get_settings();
if(empty($_SESSION['lang'])) {
    $lang = $settings['language'];
} else {
    $lang = $_SESSION['lang'];
}
$sub_header = '';
switch ($page) {
    case 'dashboard':
        $menu_title = '<i class="fas fa-fw fa-tachometer-alt text-gray-700"></i> <span class="mb-0 text-gray-800">' . _("Dashboard") . '</span>';
        break;
    case 'statistics':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-map text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Statistics")." - "._("Map").'</span>';
        break;
    case 'statistics_all':
        $menu_title = '<i class="fas fa-fw fa-chart-area text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Statistics").' - '._("Overall").'</span>';
        break;
    case 'maps':
        $menu_title = '<i class="fas fa-fw fa-list text-gray-700"></i> <span class="mb-0 text-gray-800">' . _("List Maps") . '</span>';
        break;
    case 'edit_map':
        $sub_header = '<div class="map_select_header"><a id="save_btn" href="#" onclick="save_map();return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a></div>';
        $menu_title = '<i class="fas fa-fw fa-map text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Edit Map").'</span>';
        break;
    case 'markers':
    case 'import':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-map-marker-alt text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Markers").'</span>';
        break;
    case 'add_marker':
        $sub_header = '<div class="map_select_header"><a id="save_btn" href="#" onclick="save_marker();return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a></div>';
        $menu_title = '<i class="fas fa-fw fa-map-marker-alt text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Add Marker").'</span>';
        break;
    case 'edit_marker':
        $id_marker = $_GET['id'];
        $marker = get_marker($id_marker,$_SESSION['id_user']);
        if($marker!==false) {
            $id_map = $marker['id_map'];
            $next_prev_marker = get_next_prev_marker_id($id_marker,$id_map);
            $id_next_marker = $next_prev_marker[0];
            $id_prev_marker = $next_prev_marker[1];
            $btn_nav_rooms = '<a title="'._("EDIT PREVIOUS MARKER").'" href="index.php?p=edit_marker&id='.$id_prev_marker.'" class="btn btn-sm tooltip_arrows btn-primary btn-icon-split">
        <span class="icon text-white-50">
          <i class="fas fa-angle-left"></i>
        </span>
        </a>
        <a title="'._("EDIT NEXT MARKER").'" href="index.php?p=edit_marker&id='.$id_next_marker.'" class="btn btn-sm tooltip_arrows btn-primary btn-icon-split">
        <span class="icon text-white-50">
          <i class="fas fa-angle-right"></i>
        </span>
        </a>';
            $sub_header = '<div class="map_select_header"><div class="justify-content-end">'.$btn_nav_rooms.'<a style="margin-left: 5px" id="save_btn" href="#" onclick="save_marker();return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a>&nbsp;&nbsp;&nbsp;<button data-toggle="modal" data-target="#modal_delete_room" type="button" class="btn btn-sm btn-danger '.(($delete_permission) ? '' : 'd-none').'">'._("DELETE").'</button></div></div>';
        }
        $menu_title = '<i class="fas fa-fw fa-map-marker-alt text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Edit Marker").'</span>';
        break;
    case 'markers_connections':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-exchange-alt text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Connections").'</span>';
        break;
    case 'story':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-list-ul text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Story").'</span>';
        break;
    case 'categories':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-stream text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Categories").'</span>';
        break;
    case 'add_category':
        $sub_header = '<div class="map_select_header"><div><a id="save_btn" href="#" onclick="save_category();return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a></div></div>';
        $menu_title = '<i class="fas fa-fw fa-stream text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Add Category").'</span>';
        break;
    case 'edit_category':
        $id_category = $_GET['id'];
        $sub_header = '<div class="map_select_header"><div><a id="save_btn" href="#" onclick="save_category();return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a><button '.(($demo) ? 'disabled':'').' onclick="modal_delete_category('.$id_category.');" class="btn btn-sm btn-danger ml-2">'._("DELETE").'</button></div></div>';
        $menu_title = '<i class="fas fa-fw fa-stream text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Edit Category").'</span>';
        break;
    case 'icons_library':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-icons text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Icons Library").'</span>';
        break;
    case 'reviews':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-feather-alt text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Reviews").'</span>';
        break;
    case 'preview':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-eye text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Preview").'</span>';
        break;
    case 'publish':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-map text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Publish")." - "._("Map").'</span>';
        break;
    case 'settings':
        $sub_header = '<div class="map_select_header"><a id="save_btn" href="#" onclick="save_settings(false);return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a></div>';
        $menu_title = '<i class="fas fa-fw fa-cogs text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Settings").'</span>';
        break;
    case 'updater':
        $menu_title = '<i class="fas fa-fw fa-download text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Upgrade").'</span>';
        break;
    case 'users':
        $menu_title = '<i class="fas fa-fw fa-users text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Users").'</span>';
        break;
    case "edit_user":
        $id_user_edit = $_GET['id'];
        $btn_del_user = '';
        if($_SESSION['id_user']!=$id_user_edit) {
            $btn_del_user = '<button '.(($demo) ? 'disabled':'').' onclick="modal_delete_user(\''.$id_user_edit.'\');" class="btn btn-sm btn-danger ml-2">'._("DELETE").'</button>';
        }
        $sub_header = '<div class="map_select_header"><a id="save_btn" href="#" onclick="save_user('.$id_user_edit.');return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a>'.$btn_del_user.'</div>';
        $menu_title = '<i class="fas fa-fw fa-users text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Edit User").'</span>';
        break;
    case 'edit_profile':
        $id_user_edit = $_SESSION['id_user'];
        $sub_header = '<div class="map_select_header"><a id="save_btn" href="#" onclick="save_profile(false);return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a></div>';
        $menu_title = '<i class="fas fa-fw fa-user text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Edit Profile").'</span>';
        break;
    case 'draw':
        $sub_header = print_map_selector();
        $menu_title = '<i class="fas fa-fw fa-draw-polygon text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Geometries").'</span>';
        break;
    case 'showcases':
        $menu_title = '<i class="fas fa-fw fa-object-group text-gray-700"></i> <span class="mb-0 text-gray-800">'._("Publish")." - "._("Showcases").'</span>';
        break;
    case 'edit_showcase':
        $id_showcase = $_GET['id'];
        $sub_header = '<div class="map_select_header"><div><a id="save_btn" href="#" onclick="save_showcase('.$id_showcase.');return false;" class="btn btn-sm btn-success btn-icon-split '.(($demo) ? 'disabled':'').'"><span class="icon text-white-50"><i class="far fa-circle"></i></span><span class="text">'._("SAVE").'</span></a><button '.(($demo) ? 'disabled':'').' onclick="modal_delete_showcase('.$id_showcase.');" class="btn btn-sm btn-danger ml-2">'._("DELETE").'</button></div></div>';
        $menu_title = '<i class="fas fa-fw fa-object-group text-gray-700"></i> <span class="mb-0 text-gray-800">'. _("Edit Showcase").'</span>';
        break;
}
?>
<nav class="navbar navbar-expand navbar-light bg-white topbar static-top <?php echo (empty($sub_header) ? 'shadow' : '') ?>">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <div class="header_menu_title noselect ml-1"><?php echo $menu_title; ?></div>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow lang_switcher">
            <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php echo ($settings['languages_count']==1) ? 'style="cursor:default;pointer-events:none;"' : ''; ?> >
                <img style="height: 14px;" src="img/flags_lang/<?php echo $lang; ?>.png" />
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="langDropdown">
                <?php
                if(check_language_enabled('en_US',$settings['languages_enabled']) && check_language_enabled('en_GB',$settings['languages_enabled'])) {
                    $en_gb = "English (British)";
                    $en_us = "English (American)";
                } else if(!check_language_enabled('en_US',$settings['languages_enabled']) && check_language_enabled('en_GB',$settings['languages_enabled'])) {
                    $en_gb = "English";
                    $en_us = "English";
                } else if(check_language_enabled('en_US',$settings['languages_enabled']) && !check_language_enabled('en_GB',$settings['languages_enabled'])) {
                    $en_gb = "English";
                    $en_us = "English";
                }
                ?>
                <?php if(check_language_enabled('ar_SA',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ar_SA');" class="<?php echo ($lang=='ar_SA') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ar_SA.png?v=2" /> <span class="ml-2">العربية</span></span> <?php endif; ?>
                <?php if(check_language_enabled('zh_CN',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('zh_CN');" class="<?php echo ($lang=='zh_CN') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/zh_CN.png?v=2" /> <span class="ml-2">简体中文</span></span> <?php endif; ?>
                <?php if(check_language_enabled('zh_HK',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('zh_HK');" class="<?php echo ($lang=='zh_HK') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/zh_HK.png?v=2" /> <span class="ml-2">繁體中文（香港）</span></span> <?php endif; ?>
                <?php if(check_language_enabled('zh_TW',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('zh_TW');" class="<?php echo ($lang=='zh_TW') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/zh_TW.png?v=2" /> <span class="ml-2">繁體中文（台灣）</span></span> <?php endif; ?>
                <?php if(check_language_enabled('cs_CZ',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('cs_CZ');" class="<?php echo ($lang=='cs_CZ') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/cs_CZ.png?v=2" /> <span class="ml-2">Čeština</span></span> <?php endif; ?>
                <?php if(check_language_enabled('nl_NL',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('nl_NL');" class="<?php echo ($lang=='nl_NL') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/nl_NL.png?v=2" /> <span class="ml-2">Nederlands</span></span> <?php endif; ?>
                <?php if(check_language_enabled('en_US',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('en_US');" class="<?php echo ($lang=='en_US') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/en_US.png?v=2" /> <span class="ml-2"><?php echo $en_us; ?></span></span> <?php endif; ?>
                <?php if(check_language_enabled('en_GB',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('en_GB');" class="<?php echo ($lang=='en_GB') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/en_GB.png?v=2" /> <span class="ml-2"><?php echo $en_gb; ?></span></span> <?php endif; ?>
                <?php if(check_language_enabled('fil_PH',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fil_PH');" class="<?php echo ($lang=='fil_PH') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fil_PH.png?v=2" /> <span class="ml-2">Filipino</span></span> <?php endif; ?>
                <?php if(check_language_enabled('fr_FR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fr_FR');" class="<?php echo ($lang=='fr_FR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fr_FR.png?v=2" /> <span class="ml-2">Français</span></span> <?php endif; ?>
                <?php if(check_language_enabled('de_DE',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('de_DE');" class="<?php echo ($lang=='de_DE') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/de_DE.png?v=2" /> <span class="ml-2">Deutsch</span></span> <?php endif; ?>
                <?php if(check_language_enabled('hi_IN',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('hi_IN');" class="<?php echo ($lang=='hi_IN') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/hi_IN.png?v=2" /> <span class="ml-2">हिंदी</span></span> <?php endif; ?>
                <?php if(check_language_enabled('hu_HU',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('hu_HU');" class="<?php echo ($lang=='hu_HU') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/hu_HU.png?v=2" /> <span class="ml-2">Magyar</span></span> <?php endif; ?>
                <?php if(check_language_enabled('rw_RW',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('rw_RW');" class="<?php echo ($lang=='rw_RW') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/rw_RW.png?v=2" /> <span class="ml-2">Kinyarwanda</span></span> <?php endif; ?>
                <?php if(check_language_enabled('ko_KR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ko_KR');" class="<?php echo ($lang=='ko_KR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ko_KR.png?v=2" /> <span class="ml-2">한국어</span></span> <?php endif; ?>
                <?php if(check_language_enabled('id_ID',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('id_ID');" class="<?php echo ($lang=='id_ID') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/id_ID.png?v=2" /> <span class="ml-2">Bahasa Indonesia</span></span> <?php endif; ?>
                <?php if(check_language_enabled('it_IT',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('it_IT');" class="<?php echo ($lang=='it_IT') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/it_IT.png?v=2" /> <span class="ml-2">Italiano</span></span> <?php endif; ?>
                <?php if(check_language_enabled('ja_JP',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ja_JP');" class="<?php echo ($lang=='ja_JP') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ja_JP.png?v=2" /> <span class="ml-2">日本語</span></span> <?php endif; ?>
                <?php if(check_language_enabled('fa_IR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fa_IR');" class="<?php echo ($lang=='fa_IR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fa_IR.png?v=2" /> <span class="ml-2">فارسی</span></span> <?php endif; ?>
                <?php if(check_language_enabled('fi_FI',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('fi_FI');" class="<?php echo ($lang=='fi_FI') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/fi_FI.png?v=2" /> <span class="ml-2">Suomen Kieli</span></span> <?php endif; ?>
                <?php if(check_language_enabled('pl_PL',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('pl_PL');" class="<?php echo ($lang=='pl_PL') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/pl_PL.png?v=2" /> <span class="ml-2">Polski</span></span> <?php endif; ?>
                <?php if(check_language_enabled('pt_BR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('pt_BR');" class="<?php echo ($lang=='pt_BR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/pt_BR.png?v=2" /> <span class="ml-2">Português Brasileiro</span></span> <?php endif; ?>
                <?php if(check_language_enabled('pt_PT',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('pt_PT');" class="<?php echo ($lang=='pt_PT') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/pt_PT.png?v=2" /> <span class="ml-2">Português Europeu</span></span> <?php endif; ?>
                <?php if(check_language_enabled('es_ES',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('es_ES');" class="<?php echo ($lang=='es_ES') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/es_ES.png?v=2" /> <span class="ml-2">Español</span></span> <?php endif; ?>
                <?php if(check_language_enabled('ro_RO',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ro_RO');" class="<?php echo ($lang=='ro_RO') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ro_RO.png?v=2" /> <span class="ml-2">Română</span></span> <?php endif; ?>
                <?php if(check_language_enabled('ru_RU',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('ru_RU');" class="<?php echo ($lang=='ru_RU') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/ru_RU.png?v=2" /> <span class="ml-2">Русский</span></span> <?php endif; ?>
                <?php if(check_language_enabled('sv_SE',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('sv_SE');" class="<?php echo ($lang=='sv_SE') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/sv_SE.png?v=2" /> <span class="ml-2">Svenska</span></span> <?php endif; ?>
                <?php if(check_language_enabled('tg_TJ',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('tg_TJ');" class="<?php echo ($lang=='tg_TJ') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/tg_TJ.png?v=2" /> <span class="ml-2">Тоҷикӣ</span></span> <?php endif; ?>
                <?php if(check_language_enabled('th_TH',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('th_TH');" class="<?php echo ($lang=='th_TH') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/th_TH.png?v=2" /> <span class="ml-2">ไทย</span></span> <?php endif; ?>
                <?php if(check_language_enabled('tr_TR',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('tr_TR');" class="<?php echo ($lang=='tr_TR') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/tr_TR.png?v=2" /> <span class="ml-2">Türkçe</span></span> <?php endif; ?>
                <?php if(check_language_enabled('vi_VN',$settings['languages_enabled'])) : ?> <span style="cursor: pointer;" onclick="switch_language('vi_VN');" class="<?php echo ($lang=='vi_VN') ? 'lang_active' : ''; ?> noselect dropdown-item align-middle"><img class="mb-1" src="img/flags_lang/vi_VN.png?v=2" /> <span class="ml-2">Tiếng Việt</span></span> <?php endif; ?>
            </div>
        </li>
        <?php if($settings['dark_mode']==1) : ?>
            <li class="nav-item dropdown no-arrow dark_mode_switcher ml-2">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i onclick="switch_dark_mode();" id='btn_light_mode' style="opacity:0;" class='btn_mode_switcher fas fa-sun'></i>
                    <script>
                        if (localStorage.getItem("dark_mode") === null) {
                            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                                $('.btn_mode_switcher').attr('id','btn_dark_mode');
                                $('.btn_mode_switcher').removeClass('fa-sun').addClass('fa-moon');
                            }
                        } else {
                            if(localStorage.getItem('dark_mode')==1) {
                                $('.btn_mode_switcher').attr('id','btn_dark_mode');
                                $('.btn_mode_switcher').removeClass('fa-sun').addClass('fa-moon');
                            }
                        }
                        $('.btn_mode_switcher').css('opacity',1);
                    </script>
                </a>
            </li>
        <?php endif; ?>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span style="padding-top:3px;" class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $user_info['username']; ?></span>&nbsp;
                <img class="img-profile rounded-circle" src="img/avatar1.png">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="index.php?p=edit_profile">
                    <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?php echo _("Edit profile"); ?>
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    <?php echo _("Logout"); ?>
                </a>
            </div>
        </li>
    </ul>
</nav>
<?php echo $sub_header; ?>
<div class="mb-3"></div>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo _("Ready to Leave?"); ?></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body"><?php echo _("Select Logout below if you are ready to end your current session."); ?></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo _("Cancel"); ?></button>
                <button class="btn btn-primary" onclick="logout();"><?php echo _("Logout"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#map_selector').on('shown.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var w_b = $('.map_selector_btn').outerWidth();
        var w_d = $('.bootstrap-select.show .dropdown-menu').outerWidth();
        var left = (((w_d - w_b)/2)-20)*-1;
        $('.bootstrap-select .dropdown-menu').css('opacity',0);
        setTimeout(function () {
            $('.bootstrap-select .dropdown-menu').css('left',left+'px');
            $('.bootstrap-select .dropdown-menu').css('opacity',1);
        },100);
    });
    $('#map_selector').on('rendered.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        $('#loading_header').hide();
        setTimeout(function() {
            $('#map_selector').css('opacity',1);
        },50);
        setTimeout(function() {
            $('.map_select_header .quick_action').css('opacity',1);
        },100);
    });
    $(window).resize(function () {
        if($('.bootstrap-select .dropdown-menu').is(':visible')) {
            $('.bootstrap-select .dropdown-menu').css('opacity',0);
            setTimeout(function() {
                var w_b = $('.map_selector_btn').outerWidth();
                var w_d = $('.bootstrap-select.show .dropdown-menu').outerWidth();
                var left = (((w_d - w_b)/2)-20)*-1;
                $('.bootstrap-select .dropdown-menu').css('left',left+'px');
                $('.bootstrap-select .dropdown-menu').css('opacity',1);
            },10);
        }
    });
</script>