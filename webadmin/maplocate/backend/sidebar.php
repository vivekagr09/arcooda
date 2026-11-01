<?php
$dashboard_active = "";
$statistics_active = "";
$statistics_all_active = "";
$maps_active = "";
$categories_active = "";
$markers_active = "";
$markers_connections_active = "";
$geometries_active = "";
$story_active = "";
$reviews_active = "";
$icons_active = "";
$preview_active = "";
$publish_active = "";
$users_active = "";
$settings_active = "";
$updater_active = "";
$showcase_active = "";
$collapse_map_show = "";
$collapse_map_active = "";
$collapse_map_expanded = "false";
$collapse_map_c = "collapsed";
$collapse_media_show = "";
$collapse_media_active = "";
$collapse_media_expanded = "false";
$collapse_media_c = "collapsed";
$collapse_publish_show = "";
$collapse_publish_active = "";
$collapse_publish_expanded = "false";
$collapse_publish_c = "collapsed";
$collapse_data_show = "";
$collapse_data_active = "";
$collapse_data_expanded = "false";
$collapse_data_c = "collapsed";
$collapse_stats_show = "";
$collapse_stats_active = "";
$collapse_stats_expanded = "false";
$collapse_stats_c = "collapsed";
switch ($page) {
    case 'dashboard':
        $dashboard_active = "active";
        break;
    case 'statistics':
        $statistics_active = "active";
        $collapse_stats_show = "show";
        $collapse_stats_active = "active";
        $collapse_stats_expanded = "true";
        $collapse_stats_c = "";
        break;
    case 'statistics_all':
        $statistics_all_active = "active";
        $collapse_stats_show = "show";
        $collapse_stats_active = "active";
        $collapse_stats_expanded = "true";
        $collapse_stats_c = "";
        break;
    case 'edit_map':
    case 'maps':
        $maps_active = "active";
        $collapse_map_show = "show";
        $collapse_map_active = "active";
        $collapse_map_expanded = "true";
        $collapse_map_c = "";
        break;
    case 'edit_category':
    case 'add_category':
    case 'categories':
        $categories_active = "active";
        $collapse_map_show = "show";
        $collapse_map_active = "active";
        $collapse_map_expanded = "true";
        $collapse_map_c = "";
        break;
    case 'edit_marker':
    case 'add_marker':
    case 'markers':
    case 'import':
        $markers_active = "active";
        $collapse_map_show = "show";
        $collapse_map_active = "active";
        $collapse_map_expanded = "true";
        $collapse_map_c = "";
        break;
    case 'markers_connections':
        $markers_connections_active = "active";
        $collapse_map_show = "show";
        $collapse_map_active = "active";
        $collapse_map_expanded = "true";
        $collapse_map_c = "";
        break;
    case 'story':
        $story_active = "active";
        $collapse_map_show = "show";
        $collapse_map_active = "active";
        $collapse_map_expanded = "true";
        $collapse_map_c = "";
        break;
    case 'reviews':
        $reviews_active = "active";
        $collapse_data_show = "show";
        $collapse_data_active = "active";
        $collapse_data_expanded = "true";
        $collapse_data_c = "";
        break;
    case 'icons_library':
        $icons_active = "active";
        $collapse_media_show = "show";
        $collapse_media_active = "active";
        $collapse_media_expanded = "true";
        $collapse_media_c = "";
        break;
    case 'preview':
        $preview_active = "active";
        break;
    case 'publish':
        $publish_active = "active";
        $collapse_publish_show = "show";
        $collapse_publish_active = "active";
        $collapse_publish_expanded = "true";
        $collapse_publish_c = "";
        break;
    case 'users':
    case 'edit_user':
        $users_active = "active";
        break;
    case 'settings':
        $settings_active = "active";
        break;
    case 'updater':
        $updater_active = "active";
        break;
    case 'draw':
        $geometries_active = "active";
        $collapse_map_show = "show";
        $collapse_map_active = "active";
        $collapse_map_expanded = "true";
        $collapse_map_c = "";
        break;
    case 'showcases':
    case 'edit_showcase':
        $showcase_active = "active";
        $collapse_publish_show = "show";
        $collapse_publish_active = "active";
        $collapse_publish_expanded = "true";
        $collapse_publish_c = "";
        break;
}
$maps = get_maps($id_user);
$count_maps = count($maps);
if($count_maps==0) {
    $m_disabled = "disabled";
} else {
    $m_disabled = "";
}
$settings = get_settings();
$to_validate = get_markers_to_validate($id_user);
?>

<ul class="navbar-nav <?php echo ($settings['sidebar']=='flat') ? 'bg-flat-primary' : 'bg-gradient-primary'; ?> sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?p=dashboard">
        <div class="sidebar-brand-icon">
            <?php if($settings['logo']!='') { ?>
                <img src="assets/<?php echo $settings['logo']; ?>" />
            <?php } else { ?>
                <?php echo strtoupper($settings['name']); ?>
            <?php } ?>
        </div>
    </a>
    <hr class="sidebar-divider my-0">
    <?php if($demo) : ?>
        <li class="nav-item">
            <div style="cursor:default;border-radius:0;width:100%;" class="card bg-danger text-white p-1 text-center border-0 noselect d-inline-block">
                <?php echo _("DEMO MODE"); ?> <i title="<?php echo _("ADD / SAVE features are disabled."); ?>" class="help_d fas fa-question-circle"></i>
            </div>
        </li>
        <script>
            $('.help_d').tooltipster({
                delay: 0,
                position: 'right'
            });
        </script>
    <?php endif; ?>
    <li class="nav-item <?php echo $dashboard_active; ?>">
        <a class="nav-link" href="index.php?p=dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span><?php echo _("Dashboard"); ?></span></a>
    </li>
    <li class="nav-item <?php echo $collapse_stats_active; ?>">
        <a class="nav-link <?php echo $collapse_stats_c; ?>" href="#" data-toggle="collapse" data-target="#collapse_stats" aria-expanded="<?php echo $collapse_stats_expanded; ?>" aria-controls="collapse_stats">
            <i class="fas fa-fw fa-chart-area"></i>
            <span><?php echo _("Statistics"); ?></span>
        </a>
        <div id="collapse_stats" class="collapse <?php echo $collapse_stats_show; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="glass_effect_sidebar py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $m_disabled; ?> <?php echo $statistics_active; ?>" href="index.php?p=statistics"><i class="fas fa-fw fa-map"></i> <span><?php echo _("Map"); ?></span></a>
                <a class="collapse-item <?php echo $m_disabled; ?> <?php echo $statistics_all_active; ?>" href="index.php?p=statistics_all"><i class="fas fa-fw fa-globe"></i> <span><?php echo _("Overall"); ?></span></a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item <?php echo $collapse_map_active; ?>">
        <a class="nav-link <?php echo $collapse_map_c; ?>" href="#" data-toggle="collapse" data-target="#collapse_map" aria-expanded="<?php echo $collapse_map_expanded; ?>" aria-controls="collapse_map">
            <i class="fas fa-fw fa-map"></i>
            <span><?php echo _("Maps"); ?></span>
        </a>
        <div id="collapse_map" class="collapse <?php echo $collapse_map_show; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="glass_effect_sidebar py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $maps_active; ?>" href="index.php?p=maps"><i class="fas fa-fw fa-list"></i> <span><?php echo _("List Maps"); ?></span></a>
                <a class="collapse-item <?php echo $markers_active; ?> <?php $m_disabled; ?>" href="index.php?p=markers"><i class="fas fa-fw fa-map-marker-alt"></i> <span><?php echo _("Markers"); ?> <?php echo ($to_validate>0) ? '<i style="color:white;font-size:11px;" class="fas fa-exclamation-circle"></i>' : ''; ?></span></a>
                <a class="collapse-item <?php echo $markers_connections_active; ?> <?php $m_disabled; ?>" href="index.php?p=markers_connections"><i class="fas fa-fw fa-exchange-alt"></i> <span><?php echo _("Connections"); ?></span></a>
                <a class="collapse-item <?php echo $geometries_active; ?> <?php $m_disabled; ?>" href="index.php?p=draw"><i class="fas fa-fw fa-draw-polygon"></i> <span><?php echo _("Geometries"); ?></span></a>
                <a class="collapse-item <?php echo $story_active; ?> <?php $m_disabled; ?>" href="index.php?p=story"><i class="fas fa-fw fa-list-ol"></i> <span><?php echo _("Story"); ?></span></a>
                <a class="collapse-item <?php echo $categories_active; ?> <?php $m_disabled; ?>" href="index.php?p=categories"><i class="fas fa-fw fa-stream"></i> <span><?php echo _("Categories"); ?></span></a>
            </div>
        </div>
    </li>
    <li class="nav-item <?php echo $collapse_media_active; ?>">
        <a class="nav-link <?php echo $collapse_media_c; ?>" href="#" data-toggle="collapse" data-target="#collapse_media" aria-expanded="<?php echo $collapse_media_expanded; ?>" aria-controls="collapse_media">
            <i class="fas fa-fw fa-desktop"></i>
            <span><?php echo _("Media"); ?></span>
        </a>
        <div id="collapse_media" class="collapse <?php echo $collapse_media_show; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="glass_effect_sidebar py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $icons_active; ?> <?php echo $m_disabled; ?>" href="index.php?p=icons_library"><i class="fas fa-fw fa-icons"></i> <span><?php echo _("Icons Library"); ?></span></a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">
    <li class="nav-item <?php echo $collapse_publish_active; ?>">
        <a class="nav-link <?php echo $collapse_publish_c; ?>" href="#" data-toggle="collapse" data-target="#collapse_publish" aria-expanded="<?php echo $collapse_publish_expanded; ?>" aria-controls="collapse_publish">
            <i class="fas fa-fw fa-paper-plane"></i>
            <span><?php echo _("Publish"); ?></span>
        </a>
        <div id="collapse_publish" class="collapse <?php echo $collapse_publish_show; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="glass_effect_sidebar py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $m_disabled; ?> <?php echo $publish_active; ?>" href="index.php?p=publish"><i class="fas fa-fw fa-map"></i> <span><?php echo _("Map"); ?></span></a>
                <a class="collapse-item <?php echo $m_disabled; ?> <?php echo $showcase_active; ?>" href="index.php?p=showcases"><i class="fas fa-fw fa-object-group"></i> <span><?php echo _("Showcases"); ?></span></a>
            </div>
        </div>
    </li>
    <li class="nav-item <?php echo $collapse_data_active; ?>">
        <a class="nav-link <?php echo $collapse_data_c; ?>" href="#" data-toggle="collapse" data-target="#collapse_data" aria-expanded="<?php echo $collapse_data_expanded; ?>" aria-controls="collapse_data">
            <i class="fas fa-fw fa-server"></i>
            <span><?php echo _("Collected Data"); ?></span>
        </a>
        <div id="collapse_data" class="collapse <?php echo $collapse_data_show; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar" style="">
            <div class="glass_effect_sidebar py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $m_disabled; ?> <?php echo $reviews_active; ?>" href="index.php?p=reviews"><i class="fas fa-feather-alt"></i> <span><?php echo _("Reviews"); ?></span></a>
            </div>
        </div>
    </li>
    <li class="nav-item <?php echo $preview_active; ?>">
        <a class="nav-link <?php echo $m_disabled; ?>" href="index.php?p=preview">
            <i class="fas fa-fw fa-eye"></i>
            <span><?php echo _("Preview"); ?></span></a>
    </li>
    <?php if($user_info['role']=='administrator') : ?>
        <hr class="sidebar-divider d-none d-md-block">
        <li class="nav-item <?php echo $settings_active; ?>">
            <a class="nav-link" href="index.php?p=settings">
                <i class="fas fa-fw fa-cog"></i>
                <span><?php echo _("Settings"); ?></span></a>
        </li>
        <li class="nav-item <?php echo $updater_active; ?>">
            <a class="nav-link" href="index.php?p=updater">
                <i class="fas fa-fw fa-download"></i>
                <span><?php echo _("Upgrade"); ?> <?php echo (version_compare($version,$latest_version)==-1) ? '<i style="color:white;font-size:11px;" class="fas fa-exclamation-circle"></i>' : ''; ?></span></a>
        </li>
        <li class="nav-item <?php echo $users_active; ?>">
            <a class="nav-link" href="index.php?p=users">
                <i class="fas fa-fw fa-user"></i>
                <span><?php echo _("Users"); ?></span></a>
        </li>
    <?php endif; ?>
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<script>
    if ($(window).width() < 768) {
        $(".nav-item.active .collapse").removeClass('show');
        if(window.wizard_step==-1) {
            $('.page-top').addClass('sidebar-toggled');
            $('#accordionSidebar').addClass('toggled');
        } else {
            sessionStorage.setItem("sidebar_accord", 1);
        }
        if($('#sidebar_logo_small').length) {
            $('#sidebar_logo').hide();
            $('#sidebar_logo_small').show();
        }
    }
    $(window).resize(function() {
        if ($(window).width() < 768) {
            if($('#sidebar_logo_small').length) {
                $('#sidebar_logo').hide();
                $('#sidebar_logo_small').show();
            }
        } else {
            if($('#accordionSidebar').hasClass('toggled')) {
                if($('#sidebar_logo_small').length) {
                    $('#sidebar_logo').hide();
                    $('#sidebar_logo_small').show();
                }
            } else {
                if($('#sidebar_logo_small').length) {
                    $('#sidebar_logo').show();
                    $('#sidebar_logo_small').hide();
                }
            }
        }
    });
    $("#sidebarToggle").click(function(){
        if($('#accordionSidebar').hasClass('toggled')) {
            sessionStorage.setItem("sidebar_accord", 1);
            $(".nav-item.active .collapse").addClass('show');
            if($('#sidebar_logo_small').length) {
                $('#sidebar_logo').show();
                $('#sidebar_logo_small').hide();
            }
        } else {
            sessionStorage.setItem("sidebar_accord", 0);
            $(".collapse").removeClass('show');
            if($('#sidebar_logo_small').length) {
                $('#sidebar_logo').hide();
                $('#sidebar_logo_small').show();
            }
        }
    });
    if ("sidebar_accord" in sessionStorage) {
        if(sessionStorage.getItem('sidebar_accord') == 0) {
            $('#accordionSidebar').addClass('toggled');
            $(".collapse").removeClass('show');
            if($('#sidebar_logo_small').length) {
                $('#sidebar_logo').hide();
                $('#sidebar_logo_small').show();
            }
        } else {
            if ($(window).width() >= 768) {
                $(".nav-item.active .collapse").addClass('show');
                if($('#sidebar_logo_small').length) {
                    $('#sidebar_logo').show();
                    $('#sidebar_logo_small').hide();
                }
            } else {
                if($('#sidebar_logo_small').length) {
                    $('#sidebar_logo').hide();
                    $('#sidebar_logo_small').show();
                }
            }
        }
    }
</script>