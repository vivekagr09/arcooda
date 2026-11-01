<?php
session_start();
require_once("functions.php");
$id_category = $_GET['id'];
$category = get_category($id_category);
$map = get_map($category['id_map'],$_SESSION['id_user']);
$count_markers = get_marker_count_category($id_category);
if(empty($category['markers_color_hex'])) $category['markers_color_hex']=$map['main_color_hex'];
if (is_ssl()) { $protocol = 'https'; } else { $protocol = 'http'; }
$link_cat = $protocol ."://". $_SERVER['SERVER_NAME'] . str_replace("backend/index.php","viewer/index.php?code=",$_SERVER['SCRIPT_NAME']);
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

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-link"></i> <?php echo _("Direct Link"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input readonly type="text" class="form-control bg-white" id="link" value="<?php echo $link_cat.$map['code']."&cat=".$id_category; ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog"></i> <?php echo _("Details"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name"><?php echo _("Name"); ?> *</label>
                            <input type="text" class="form-control" id="name" value="<?php echo $category['name']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_category_parent"><?php echo _("Parent Category"); ?></label>
                            <select class="form-control" id="id_category_parent">
                                <option id="0">--</option>
                                <?php echo get_parent_categories($category['id_map'],$category['id_category_parent']); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="default_selected"><?php echo _("Default selected"); ?></label><br>
                            <input <?php echo ($category['default_selected']) ? 'checked' : '' ; ?> type="checkbox" id="default_selected" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt"></i> <?php echo _("Assign Markers Icon"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?php echo _("Type"); ?></label>
                            <select id="icon_type" onchange="change_icon_type();" class="form-control">
                                <option <?php echo ($category['markers_icon']=='' && $category['markers_icon_image']=='') ? 'selected' : '' ; ?> id="0"><?php echo _("Circle"); ?></option>
                                <option <?php echo ($category['markers_icon']!='' && $category['markers_icon_image']=='') ? 'selected' : '' ; ?> id="1"><?php echo _("Icon"); ?></option>
                                <option <?php echo ($category['markers_icon_image']!='') ? 'selected' : '' ; ?> id="2"><?php echo _("Icon Library"); ?></option>
                            </select>
                        </div>
                    </div>
                    <div id="icon_type_1" class="col-md-4 <?php echo ($category['markers_icon']!='' && $category['markers_icon_image']=='') ? '' : 'd-none' ; ?>">
                        <div class="form-group">
                            <label><?php echo _("Icon"); ?></label><br>
                            <button class="btn btn-sm btn-primary" type="button" id="GetIconPicker" data-iconpicker-input="input#marker_icon_input" data-iconpicker-preview="i#marker_icon"><?php echo _("Select Icon"); ?></button>
                            <input readonly type="text" id="marker_icon_input" name="Icon" value="<?php echo $category['markers_icon']; ?>" required="" placeholder="" autocomplete="off" spellcheck="false">
                            <div class="icon-preview d-inline-block ml-1" data-toggle="tooltip" title="">
                                <i id="marker_icon" class="<?php echo $category['markers_icon']; ?>"></i>
                            </div>
                        </div>
                    </div>
                    <div id="icon_type_2" class="col-md-4 <?php echo ($category['markers_icon_image']!='') ? '' : 'd-none' ; ?>">
                        <div class="form-group">
                            <label for="marker_library_icon"><?php echo _("Icon"); ?></label><br>
                            <button data-toggle="modal" data-target="#modal_library_icons" class="btn btn-sm btn-primary" type="button" id="btn_library_icon"><?php echo _("Select Library Icon"); ?></button>
                            <input type="hidden" id="marker_library_icon" value="<?php echo $category['markers_id_icon_library']; ?>">
                            <img id="marker_library_icon_preview" style="height: 30px;display: <?php echo ($category['markers_icon_image']=='') ? 'none':'inline-block'; ?>" src="../viewer/icons/<?php echo $category['markers_icon_image']; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="markers_size"><?php echo _("Size"); ?> (<span id="markers_size_value"><?php echo $category['markers_size']; ?></span>)</label><br>
                            <input oninput="change_markers_size();" onchange="change_markers_size();" type="range" step="0.1" min="0.5" max="2.0" class="form-control-range" id="markers_size" value="<?php echo $category['markers_size']; ?>" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group <?php echo ($category['markers_icon_image']!='') ? 'disabled':''; ?>">
                            <label><?php echo _("Background"); ?></label>
                            <input type="text" class="form-control" id="color_hex" value="<?php echo $category['markers_color_hex']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group <?php echo ($category['markers_icon_image']!='' || ($category['markers_icon']=='' && $category['markers_icon_image']=='')) ? 'disabled':''; ?>">
                            <label><?php echo _("Color"); ?></label>
                            <input type="text" class="form-control" id="icon_color_hex" value="<?php echo $category['markers_icon_color_hex']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group <?php echo ($category['markers_icon_image']!='') ? 'disabled':''; ?>">
                            <label><?php echo _("Background (Hover)"); ?></label>
                            <input type="text" class="form-control" id="color_hex_hover" value="<?php echo $category['markers_color_hex_hover']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group <?php echo ($category['markers_icon_image']!='' || ($category['markers_icon']=='' && $category['markers_icon_image']=='')) ? 'disabled':''; ?>">
                            <label><?php echo _("Color (Hover)"); ?></label>
                            <input type="text" class="form-control" id="icon_color_hex_hover" value="<?php echo $category['markers_icon_color_hex_hover']; ?>" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label style="opacity:0">.</label><br>
                            <button id="btn_assign_icons" onclick="assign_icon_to_markers('category',<?php echo $id_category; ?>)" class="btn btn-block btn-primary <?php echo ($demo) ? 'disabled':''; ?> <?php echo ($count_markers==0) ? 'disabled' : ''; ?>"><?php echo _("ASSIGN ICON TO")." ".$count_markers." "._("MARKERS"); ?></button>
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
                <?php echo get_library_icons($map['id']); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_delete_category" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("Delete Category"); ?></h5>
            </div>
            <div class="modal-body">
                <p><?php echo _("Are you sure you want to delete the category?"); ?></p>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> id="btn_delete_category" onclick="" type="button" class="btn btn-danger"><?php echo _("Yes, Delete"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_category = <?php echo $id_category; ?>;
        window.color_hex_spectrum = null;
        window.icon_color_hex_spectrum = null;
        window.color_hex_hover_spectrum = null;
        window.icon_color_hex_hover_spectrum = null;
        window.category_need_save = false;

        $(document).ready(function () {
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
        });

        $("input").change(function(){
            window.category_need_save = true;
        });

        $(window).on('beforeunload', function(){
            if(window.category_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });
    })(jQuery);
</script>
