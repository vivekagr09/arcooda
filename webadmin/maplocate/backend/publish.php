<?php
session_start();
$id_map_sel = $_SESSION['id_map_sel'];
$map = get_map($id_map_sel,$_SESSION['id_user']);
if (is_ssl()) { $protocol = 'https'; } else { $protocol = 'http'; }
$base_url = $protocol ."://". $_SERVER['SERVER_NAME'] . str_replace("backend/index.php","",$_SERVER['SCRIPT_NAME']);
$link = $protocol ."://". $_SERVER['SERVER_NAME'] . str_replace("backend/index.php","viewer/index.php?code=",$_SERVER['SCRIPT_NAME']);
$link_f = $protocol ."://". $_SERVER['SERVER_NAME'] . str_replace("backend/index.php","viewer/",$_SERVER['SCRIPT_NAME']);
$publish = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['publish']==0) {
        $publish=false;
    }
}
if(!empty($map['password'])) {
    $map['password']="keep_password";
}
$settings = get_settings();
?>

<?php if(!$publish): ?>
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
            <a href="#collapsePI" class="d-block card-header py-3 collapsed" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapsePI">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-lock"></i> <?php echo _("Protection"); ?></h6>
            </a>
            <div class="collapse" id="collapsePI">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="vt_password"><?php echo _("Password"); ?></label>
                            <input autocomplete="new-password" type="password" class="form-control" id="map_password" value="<?php echo $map['password']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button id="btn_protect" onclick="set_password_map();" class="btn btn-sm btn-success btn-block <?php echo ($demo) ? 'disabled_d':''; ?>"><?php echo _("SAVE"); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <a href="#collapsePI2" class="d-block card-header py-3 collapsed" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapsePI">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-hashtag"></i> <?php echo _("Meta Tag"); ?></h6>
            </a>
            <div class="collapse" id="collapsePI2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_title"><?php echo _("Title"); ?></label>
                                <input oninput="change_meta_title();" onchange="change_meta_title();" type="text" class="form-control" id="meta_title" value="<?php echo $map['meta_title']; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="meta_description"><?php echo _("Description"); ?></label>
                                <textarea oninput="change_meta_description();" onchange="change_meta_description();" rows="3" class="form-control" id="meta_description"><?php echo $map['meta_description']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label><?php echo _("Image"); ?></label>
                                <div style="display: none" id="div_delete_image_meta" class="form-group mt-2">
                                    <button <?php echo ($demo) ? 'disabled':''; ?> onclick="delete_image_meta('map',<?php echo $id_map_sel; ?>);" class="btn btn-block btn-danger"><?php echo _("DELETE IMAGE"); ?></button>
                                </div>
                                <div style="display: none" id="div_upload_image_meta">
                                    <form id="frm_im" action="ajax/upload_meta_image.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <input type="file" class="form-control" id="txtFile_im" name="txtFile_im" />
                                        </div>
                                        <div class="form-group">
                                            <input <?php echo ($demo) ? 'disabled':''; ?> type="submit" class="btn btn-block btn-success" id="btnUpload_im" value="<?php echo _("Upload Image"); ?>" />
                                        </div>
                                        <div class="preview text-center">
                                            <div class="progress mb-3 mb-sm-3 mb-lg-0 mb-xl-0" style="height: 2.35rem;display: none">
                                                <div class="progress-bar" id="progressBar_im" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                                    0%
                                                </div>
                                            </div>
                                            <div style="display: none;padding: .38rem;" class="alert alert-danger" id="error_im"></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label><?php echo _("Preview"); ?></label><br>
                            <div class="facebook-preview preview">
                                <div class="facebook-preview__link">
                                    <?php if(empty($map['meta_image'])) {
                                        if(empty($map['background_image'])) {
                                            $meta_image = '';
                                            $meta_path = '';
                                        } else {
                                            $meta_image = $map['background_image'];
                                            $meta_path = "../viewer/content/$meta_image";
                                        }
                                    } else {
                                        $meta_image = $map['meta_image'];
                                        $meta_path = "../viewer/content/$meta_image";
                                    } ?>
                                    <img class="facebook-preview__image <?php echo (empty($meta_image)) ? 'd-none' : ''; ?>" src="../viewer/content/<?php echo $meta_image; ?>" alt="">
                                    <div class="facebook-preview__content">
                                        <div class="facebook-preview__url">
                                            <?php echo $_SERVER['SERVER_NAME']; ?>
                                        </div>
                                        <h2 class="facebook-preview__title">
                                            <?php if(empty($map['meta_title'])) {
                                                echo $map['name'];
                                            } else {
                                                echo $map['meta_title'];
                                            } ?>
                                        </h2>
                                        <div class="facebook-preview__description">
                                            <?php echo $map['meta_description']; ?>
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
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-share-alt"></i> <?php echo _("Share & Embed"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mr-4 d-inline-block">
                            <label for="status"><?php echo _("Status"); ?></label><br>
                            <input <?php echo ($demo) ? 'disabled' : ''; ?> id="status" <?php echo ($map['active']) ? 'checked' : ''; ?> type="checkbox" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-size="normal" data-on="<?php echo _("Activated"); ?>" data-off="<?php echo _("Deactivated"); ?>">
                        </div>
                        <div class="form-group <?php echo ($user_info['role']!='administrator') ? 'd-none' : 'd-inline-block' ?>">
                            <label for="show_in_first_page"><?php echo _("Show as first page"); ?> (<?php echo $base_url; ?>) <i title="<?php echo _("only visible to administrators"); ?>" class="help_t fas fa-question-circle"></i></label><br>
                            <input <?php echo ($demo) ? 'disabled' : ''; ?> id="show_in_first_page" <?php echo ($map['show_in_first_page']) ? 'checked' : ''; ?> type="checkbox" data-toggle="toggle" data-onstyle="success" data-offstyle="light" data-size="normal" data-on="<?php echo _("Yes"); ?>" data-off="<?php echo _("No"); ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <label for="link"><i class="fas fa-link"></i> <?php echo _("Link"); ?></label><br>
                            <i><?php echo _("Url variables"); ?>: &coord=xxxxx,yyyyy&zoom=z&addm=m (xxxxx = lat, yyyyy = lon, z = zoom, m = 0/1 add marker open automatically)</i><br>
                            <div class="input-group mt-2 mb-0">
                                <input readonly type="text" class="form-control bg-white mb-0 pb-0" id="link" value="<?php echo $link . $map['code']; ?>" />
                                <div class="input-group-append">
                                    <a title="<?php echo _("OPEN LINK"); ?>" class="btn btn-success" href="<?php echo $link . $map['code']; ?>" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button title="<?php echo _("COPY TO CLIPBOARD"); ?>" class="btn btn-primary cpy_btn" data-clipboard-target="#link">
                                        <i class="far fa-clipboard"></i>
                                    </button>
                                    <button title="<?php echo _("QR CODE"); ?>" onclick="open_qr_code_modal('<?php echo $link . $map['code']; ?>');" class="btn btn-secondary">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <?php $array_share_providers = explode(",",$settings['share_providers']); ?>
                        <div style="margin-top: 10px" class="a2a_kit a2a_kit_size_32 a2a_default_style" data-a2a-url="<?php echo $link . $map['code']; ?>">
                            <a class="a2a_button_email <?php echo (in_array('email',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_whatsapp <?php echo (in_array('whatsapp',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_facebook <?php echo (in_array('facebook',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_threads <?php echo (in_array('threads',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_x <?php echo (in_array('twitter',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_linkedin <?php echo (in_array('linkedin',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_telegram <?php echo (in_array('telegram',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_facebook_messenger <?php echo (in_array('facebook_messenger',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_pinterest <?php echo (in_array('pinterest',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_reddit <?php echo (in_array('reddit',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_line <?php echo (in_array('line',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_viber <?php echo (in_array('viber',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_vk <?php echo (in_array('vk',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_qzone <?php echo (in_array('qzone',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                            <a class="a2a_button_wechat <?php echo (in_array('wechat',$array_share_providers) ? '' : 'hidden'); ?>"></a>
                        </div>
                        <?php if($settings['cookie_consent']) { ?>
                            <script type="text/plain" data-category="functionality" data-service="Social Share (AddToAny)" async src="https://static.addtoany.com/menu/page.js"></script>
                            <div style="display:none" id="cookie_denied_msg"><?php echo _("To use sharing via social networks, enable \"Social Share\" cookies in the <a data-cc='show-consentModal' href='#'>cookie preferences</a>."); ?></div>
                        <?php } else { ?>
                            <script async src="https://static.addtoany.com/menu/page.js"></script>
                        <?php } ?>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="link_f"><i class="fas fa-link"></i> <?php echo _("Friendly Link"); ?></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text noselect" id="basic-addon3"><?php echo $link_f; ?></span>
                                </div>
                                <input <?php echo ($demo) ? 'disabled' : ''; ?> type="text" class="form-control bg-white" id="link_f" value="<?php echo $map['friendly_url']; ?>" />
                                <div class="input-group-append <?php echo (empty($map['friendly_url'])) ? 'disabled' : '' ; ?>">
                                    <a id="link_open" title="<?php echo _("OPEN LINK"); ?>" class="btn btn-success" href="<?php echo $link_f . $map['friendly_url']; ?>" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button id="link_copy" title="<?php echo _("COPY TO CLIPBOARD"); ?>" class="btn btn-primary cpy_btn" data-clipboard-text="<?php echo $link_f . $map['friendly_url'];; ?>">
                                        <i class="far fa-clipboard"></i>
                                    </button>
                                    <button id="link_qr" title="<?php echo _("QR CODE"); ?>" onclick="open_qr_code_modal('<?php echo $link_f . $map['friendly_url']; ?>');" class="btn btn-secondary">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="code"><i class="fas fa-code"></i> <?php echo _("Embed Code"); ?></label>
                            <div class="input-group">
                                <textarea id="code" class="form-control" rows="2"><iframe id="sml_iframe_<?php echo $map['code']; ?>" allow="accelerometer; camera; display-capture; fullscreen; geolocation; gyroscope; magnetometer; microphone; midi; xr-spatial-tracking;" width="100%" height="600px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $link . $map['code']; ?>"></iframe></textarea>
                                <div class="input-group-append">
                                    <button title="<?php echo _("COPY TO CLIPBOARD"); ?>" class="btn btn-primary cpy_btn" data-clipboard-target="#code">
                                        <i class="far fa-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_qrcode" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("QR Code"); ?></h5>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-spin fa-spinner"></i>
                <img style="width: 100%;" src="" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        window.link_f = '<?php echo $link_f; ?>';
        window.image_meta = '<?php echo $map['meta_image']; ?>';
        window.image_meta_default = '';
        window.title_meta_default = `<?php echo $map['name']; ?>`;
        window.description_meta_default = ``;
        $(document).ready(function () {
            $('.help_t').tooltip();
            $('.cpy_btn').tooltip({
                trigger: 'click',
                placement: 'bottom'
            });
            if(window.image_meta=='') {
                $('#div_delete_image_meta').hide();
                $('#div_upload_image_meta').show();
            } else {
                $('#div_delete_image_meta').show();
                $('#div_upload_image_meta').hide();
            }
            var clipboard = new ClipboardJS('.cpy_btn');
            clipboard.on('success', function(e) {
                setTooltip(e.trigger, window.backend_labels.copied+"!");
                hideTooltip(e.trigger);
            });
        });

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
                upadte_progressbar_im(Math.round(percentage));
            },false);
            ajax.addEventListener('load',function(evt){
                if(evt.target.responseText.toLowerCase().indexOf('error')>=0){
                    show_error_im(evt.target.responseText);
                } else {
                    if(evt.target.responseText!='') {
                        window.image_meta = evt.target.responseText;
                        $('.facebook-preview__image').attr('src','../viewer/content/'+window.image_meta);
                        $('.facebook-preview__image').removeClass('d-none');
                        $('#div_delete_image_meta').show();
                        $('#div_upload_image_meta').hide();
                        save_metadata('map',window.id_map);
                    }
                }
                upadte_progressbar_im(0);
                frm[0].reset();
            },false);
            ajax.addEventListener('error',function(evt){
                show_error_im('upload failed');
                upadte_progressbar_im(0);
            },false);
            ajax.addEventListener('abort',function(evt){
                show_error_im('upload aborted');
                upadte_progressbar_im(0);
            },false);
            ajax.open('POST',url);
            ajax.send(data);
            return false;
        });

        function upadte_progressbar_im(value){
            $('#progressBar_im').css('width',value+'%').html(value+'%');
            if(value==0){
                $('.progress').hide();
            }else{
                $('.progress').show();
            }
        }

        function show_error_im(error){
            $('.progress').hide();
            $('#error_im').show();
            $('#error_im').html(error);
        }

        var timer_furl;
        $('#link_f').on('input',function(){
            if(timer_furl) {
                clearTimeout(timer_furl);
            }
            timer_furl = setTimeout(function() {
                change_friendly_url('map','link_f',window.id_map);
            },400);
        });
        $('#status').change(function() {
            if($(this).prop('checked')) {
                var status = 1;
            } else {
                var status = 0;
            }
            set_status_map(status);
        });
        $('#show_in_first_page').change(function() {
            if($(this).prop('checked')) {
                var show_in_first_page = 1;
            } else {
                var show_in_first_page = 0;
            }
            set_show_in_first_page(show_in_first_page,'map');
        });

        var timer_meta;
        $('#meta_title,#meta_description').on('input',function(){
            if(timer_meta) {
                clearTimeout(timer_meta);
            }
            timer_meta = setTimeout(function() {
                save_metadata('map',window.id_map);
            },400);
        });
    })(jQuery);

    function setTooltip(btn, message) {
        $(btn).tooltip('hide')
            .attr('data-original-title', message)
            .tooltip('show');
    }
    function hideTooltip(btn) {
        setTimeout(function() {
            $(btn).tooltip('hide');
        }, 1000);
    }
</script>