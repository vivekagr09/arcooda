<div class="row">

    <div class="col-sm-12">

        <div role="tabpanel">

            <ul class="nav nav-tabs nav-pills" role="tablist">

                <?php $i=1; foreach ($setting_tabs as $key => $value) { ?>

                <li role="presentation">

                    <a href="#<?php echo $key ?>" class="nav-link <?php echo ($i == 1) ? 'active' : ''; ?>" aria-controls="<?php echo $key ?>" role="tab" data-bs-toggle="tab"><?php echo $value ?></a>

                </li>

                <?php $i++; } ?>

            </ul>

            <br>

            <form method="post" action="" enctype="multipart/form-data">

                <div class="tab-content">

                    <?php $i=1; foreach ($setting_tabs as $key => $value) { ?>

                    <div role="tabpanel" class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="<?php echo $key ?>">

                        <div class="card" style="min-height: 550px;">

                            <div class="card-body">

                                <div class="row front-design-form vc">

                                    <div class="col-sm-2">

                                        <div>

                                            <ul class="card1-list">

                                                <li data-show='<?php echo $key ?>-header' class="active"><?= __('admin.header') ?></li>

                                                <li data-show='<?php echo $key ?>-topleft'><?= __('admin.top_left') ?></li>

                                                <li data-show='<?php echo $key ?>-sidebar'><?= __('admin.sidebar') ?></li>

                                            </ul>

                                        </div>

                                    </div>

                                    <div class="col-sm-10">

                                        <div>

                                            <div class="card1 active" data-toggle='<?php echo $key ?>-header'>

                                                <div class="card1-header"><?= __('admin.header_setting') ?></div>

                                                <div class="card1-body">

                                                    <table class="table-striped table">

                                                        <tr>

                                                            <td><?= __('admin.header') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_background_color]" value="<?= $theme_setting[$key]['header_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_text_color]" value="<?= $theme_setting[$key]['header_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td><?= __('admin.language') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_language_text_color]" value="<?= $theme_setting[$key]['header_language_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td><?= __('admin.currency') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_currency_text_color]" value="<?= $theme_setting[$key]['header_currency_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td><?= __('admin.alert') ?></td>

                                                            <td>

                                                                <table>
                                                                    <tr>

                                                                        <td><?= __('admin.background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_alert_background_color]" value="<?= $theme_setting[$key]['header_alert_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_alert_text_color]" value="<?= $theme_setting[$key]['header_alert_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td><?= __('admin.menu') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.dropdown_background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_menu_dropdown_background_color]" value="<?= $theme_setting[$key]['header_menu_dropdown_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_menu_background_color]" value="<?= $theme_setting[$key]['header_menu_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[header_menu_text_color]" value="<?= $theme_setting[$key]['header_menu_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </div>

                                            </div>

                                            <div class="card1" data-toggle='<?php echo $key ?>-topleft'>

                                                <div class="card1-header"><?= __('admin.top_left_settings') ?></div>

                                                <div class="card1-body">

                                                    <table class="table-striped table">

                                                        <tr>

                                                            <td><?= __('admin.top_left') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.text') ?></td>

                                                                        <td><input  name="<?php echo $key ?>[top_left_text]" value="<?= $theme_setting[$key]['top_left_text']; ?>" class="form-control"  type="text"></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[top_left_background_color]" value="<?= $theme_setting[$key]['top_left_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[top_left_text_color]" value="<?= $theme_setting[$key]['top_left_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </div>

                                            </div>

                                            <div class="card1" data-toggle='<?php echo $key ?>-sidebar'>

                                                <div class="card1-header"><?= __('admin.side_bar_settings') ?></div>

                                                <div class="card1-body">

                                                    <table class="table-striped table">

                                                        <tr>

                                                            <td><?= __('admin.side_bar') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[sidebar_background_color]" value="<?= $theme_setting[$key]['sidebar_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td><?= __('admin.menu') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[sidebar_menu_background_color]" value="<?= $theme_setting[$key]['sidebar_menu_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[sidebar_menu_text_color]" value="<?= $theme_setting[$key]['sidebar_menu_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td><?= __('admin.menu_bottom_links') ?></td>

                                                            <td>

                                                                <table>

                                                                    <tr>

                                                                        <td><?= __('admin.background_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[sidebar_menu_bottom_links_background_color]" value="<?= $theme_setting[$key]['sidebar_menu_bottom_links_background_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                    <tr>

                                                                        <td><?= __('admin.text_color') ?></td>

                                                                        <td><input type="text" name="<?php echo $key ?>[sidebar_menu_bottom_links_text_color]" value="<?= $theme_setting[$key]['sidebar_menu_bottom_links_text_color'] ?>" class='form-control color-input'></td>

                                                                    </tr>

                                                                </table>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <br>

                                <div class="form-group text-right">

                                    <button class="btn btn-sm btn-primary"><?= __('admin.save_settings') ?></button>

                                </div>

                            </div>

                        </div>

                    </div>

                    <?php $i++; } ?>

                </div>

            </form>

        </div>

    </div>

</div>

<script type="text/javascript" src="<?= base_url('assets/plugins/html2canvas/ace.js') ?>"></script>

<script>

var editor = null;



apply_color($(".color-input"))

$(".card1-list li").on('click',function(){

    $this = $(this).parents('.tab-pane');

    $this.find(".card1-list li").removeClass("active");

    $(this).addClass("active");



    $this.find(".card1[data-toggle]").hide();

    $this.find(".card1[data-toggle='"+ $(this).attr("data-show") +"']").show();



    setCookie('design_last_tab', $(this).attr("data-show"));

})



$(document).ready(function(){

if(getCookie('design_last_tab') != ''){

    $(".card1-list li[data-show='"+ getCookie('design_last_tab') +"']").trigger('click');

}

})



function initAceEditor(ext,path) {

if(editor){ editor.destroy(); }

editor = ace.edit("editor");

editor.setTheme("ace/theme/monokai");

editor.getSession().setMode("ace/mode/" + ext);

editor.getSession().setUseWorker(false);

editor.setOption("showPrintMargin", false)



editor.file_path = path;

}



$(".full-screen-btn").on('click',function(){

$(".file-editor").toggleClass("fixed");

editor.resize();

});

$(".editor-file-list p").on('click',function(){

$(this).toggleClass("hide-files");

})

$(".editor-file-list li").on('click',function(){

$this = $(this);



$(".editor-file-list li").removeClass('selected');

$this.addClass("selected");

$.ajax({

    url:'<?= base_url("admincontrol/editor_get_file") ?>',

    type:'POST',

    dataType:'json',

    data:{path: $this.attr("data-path")},

    beforeSend:function(){$this.btn("loading");},

    complete:function(){$this.btn("reset");},

    success:function(json){

        

        $("#editor").text(json['contents']);

        initAceEditor(json['ext'], $this.attr("data-path"))

    

    },

})

})



$("#btn-save").on('click',function(){

if(editor.file_path){

    var text = editor.getValue();



    $this = $(this);

    $.ajax({

        url:'<?= base_url("admincontrol/editor_save_file") ?>',

        type:'POST',

        dataType:'json',

        data:{path: editor.file_path, text:text},

        beforeSend:function(){$this.btn("loading");},

        complete:function(){$this.btn("reset");},

        success:function(json){

            $("#editor-success").html('');

            if(json['success']){

                $("#editor-success").html(json['success']);

            }

        },

    })

}

});



$(".editor-file-list li:first").trigger("click");

</script>



<script type="text/javascript"><!--

$(document).ready(function(){

$('#image_manager .model_image').load("<?= $image_manager_url ?>");

$(".summernote").summernote()

})

//--></script>



