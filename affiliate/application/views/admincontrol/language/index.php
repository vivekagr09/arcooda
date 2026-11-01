<div class="row">
    <div class="col-12">
        <div class="card m-b-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><?= __("admin.language") ?></h4>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('admincontrol/update_user_langauges/all') ?>" class="btn btn-warning text-dark"><?= __("admin.update_languages") ?></a>
                    <a href="<?= base_url('admincontrol/translation_edit/'.$lang['id']) ?>" class="btn btn-primary add-new" id="<?= $lang['id'] ?>"><?= __("admin.add_new") ?></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-rep-plugin">
                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table class="table">
                            <thead>
                            <tr>
                                <th width="50px"><?= __("admin.flag") ?></th>
                                <th><?= __("admin.name") ?></th>
                                <th width="100px"><?= __("admin.translation_missing_all") ?></th>
                                <th width="50px"><?= __("admin.is_default") ?></th>
                                <th width="50px"><?= __("admin.status") ?></th>
                                <th width="180px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($language as $lang){ ?>
                                <tr>
                                    <td>
                                        <img src="<?= base_url($lang['flag']) ?>" style="height: 20px">
                                    </td>
                                    <td>
                                        <?= $lang['name'] ?>
                                        <?php if($lang['is_default']){ echo "<small class='default-badge'> ( ".__('admin.default')." ) </small>"; } ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $lang['count']['missing'] ?> /
                                        <?= $language_count['all'] ?>
                                    </td>

                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input btn_default_lang btn_lang_toggle" type="checkbox" id="switchDefaultLang<?= $lang['id'] ?>" <?= ($lang['is_default'] == 1) ? "checked" : ""?> data-lang_id="<?= $lang['id'] ?>" data-column="is_default" style="cursor: pointer;">
                                            <label class="form-check-label" for="switchDefaultLang<?= $lang['id'] ?>"></label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input btn_lang_toggle" type="checkbox" id="switchStatusLang<?= $lang['id'] ?>" <?= ($lang['status'] == 1) ? "checked" : ""?> data-lang_id="<?= $lang['id'] ?>" data-column="status" style="cursor: pointer;">
                                            <label class="form-check-label" for="switchStatusLang<?= $lang['id'] ?>"></label>
                                        </div>
                                    </td>

                                    <td>
                                        <button class="btn btn-primary open-details"><?= __('admin.import_export') ?></button>
                                        <a href="<?= base_url('admincontrol/translation_edit/'.$lang['id']) ?>" class="btn btn-primary edit-button" id="<?= $lang['id'] ?>"><?= __("admin.edit") ?></a>
                                        <?php if($lang['id'] != 1){ ?>
                                            <a class="btn btn-primary edit-button" href="<?= base_url('admincontrol/translation/'.$lang['id']) ?>"> <?= __("admin.translation") ?> </a>
                                        <?php } ?>
                                        <?php if (ENVIRONMENT !== 'demo'): ?>
                                            <?php if($lang['is_default'] == '0' && $lang['id'] != 1){ ?>
                                                <button class="btn btn-danger detele-button" id="<?= $lang['id'] ?>"><?= __("admin.delete") ?></button>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <tr style="display: none" class="details-tr">
                                    <td colspan="100%" class="p-0">
                                        <div class="px-4 py-3 bg-light rounded-0">
                                            <div class="mb-3">
                                                <h4 class="mb-2"><?= __('admin.language_uploader') ?></h4>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <span class="me-2"><?= __('admin.want_to_export_language_file') ?></span> 
                                                    <a href="<?= base_url("admincontrol/language_export/".$lang['id']) ?>" target="_blank" class="btn btn-info"><?= __('admin.export_language') ?></a>
                                                </div>
                                            </div>
                                            <?php if($lang['id'] != 1){ ?>
                                                <form class="form-language">
                                                    <div class="lang-message text-center mb-3"></div>
                                                    <input class="d-none" data-lang_file="<?= $lang['id'] ?>" type="file" name="file">
                                                    <input type="hidden" name="id" value="<?= $lang['id'] ?>">
                                                </form>
                                                <div class="d-grid gap-2 mt-3">
                                                    <button type="button" data-lang_id="<?= $lang['id'] ?>" id="language_xls_upload_btn" class="btn btn-primary"><?= __('admin.import_excel_file') ?></button>
                                                    <button type="button" id="language_zip_upload_btn" class="btn btn-secondary"><?= __('admin.import_language_package') ?></button>
                                                </div>
                                            <?php } else { ?>
                                                <div class="alert alert-danger mt-3"><?= __('admin.you_can_not_import_main_language') ?></div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <div class="d-none">
                                <form id="language_zip_upload_form" action="<?= base_url("admincontrol/language_zip_upload") ?>" method="post" enctype="multipart/form-data">
                                    <input type="file" name="file" id="language_zip_upload_input">
                                </form>
                            </div>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    $(document).on('click', '#language_xls_upload_btn', function(){
        $('input[data-lang_file="'+$(this).data('lang_id')+'"]').click();
    });

    $(document).on('change', 'input[type="file"][data-lang_file]', function(){
        $(this).closest('form').submit();
    });

    $(document).on('click', '#language_zip_upload_btn', function(){
        $('#language_zip_upload_input').click();
    });

    $(document).on('change', '#language_zip_upload_input', function(){
        $('#language_zip_upload_form').submit();
    });

    $(document).on('change', ".btn_lang_toggle", function(){
        let skip_change = false;
        let id = $(this).data('lang_id');
        let column = $(this).data('column');
        let status = this.checked ? 1 : 0;

        if (column == 'is_default' && !status) {
            Swal.fire('Warning', '<?= __('admin.please_select_another_language_as_default') ?>', 'warning');
            this.checked = !this.checked; // Flip the switch back
            skip_change = true;
        } else if (column == 'is_default') {
            if(!$('.btn_lang_toggle[data-lang_id="'+id+'"][data-column="status"]').prop('checked')) {
                Swal.fire('Warning', '<?= __('admin.inactive_language_can_not_be_set_as_default') ?>', 'warning');
                this.checked = !this.checked; // Flip the switch back
                skip_change = true;
            } else {
                $('.btn_default_lang').prop('checked', false);
            }
        } else {
            if($('.btn_lang_toggle[data-lang_id="'+id+'"][data-column="is_default"]').prop('checked')) {
                Swal.fire('Warning', '<?= __('admin.default_language_can_not_be_set_as_inactive') ?>', 'warning');
                this.checked = !this.checked; // Flip the switch back
                skip_change = true;
            }
        }

        if(!skip_change) {
            if (status && column == 'is_default') { 
                $('.default-badge').remove();
                $(this).closest('tr').find('td:nth-child(2)').append("<small class='default-badge'> ( "+'<?= __('admin.default') ?>'+" ) </small>");
            }

            $.ajax({
                url: "<?= base_url('admincontrol/lang_status_toggle')?>",
                type: "POST",
                dataType: "json",
                data: {
                    id:id,
                    status:status,
                    column:column
                },
                success: function (response) {    
                    if(response.reload) {
                        window.location.reload();
                    }
                    if(response.status) {
                        $('.notification-list.language').html(response.languages);
                    }
                }
            });
        }
    });

    $(".open-details").on('click', function(){
        $tr = $(this).parents("tr").next(".details-tr");

        if($tr.css("display") == 'none'){
            $tr.show();
        } else {
            $tr.hide();
        }
    });

    $(".detele-button").on('click', function(){
        if(!confirm('<?= __('admin.are_you_sure') ?>')) return false;

        $this = $(this);
        $.ajax({
            url: '<?= base_url("admincontrol/delete_update_language") ?>',
            type: 'POST',
            dataType: 'json',
            data: {id: $this.attr("id")},
            beforeSend: function(){
                $this.prop("disabled", true);
            },
            complete: function(){
                $this.prop("disabled", false);
            },
            success: function(json){
                window.location.reload();
            },
        });
    });

    $(".form-language").submit(function(evt){
        evt.preventDefault();
        var formData = new FormData($(this)[0]);
        formData = formDataFilter(formData);
        $this = $(this);

        $this.find('.btn-submit').btn("loading");
        $.ajax({
            url: '<?= base_url('admincontrol/language_import') ?>',
            type: 'POST',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            error: function(){
                $this.find('.btn-submit').btn("reset");
            },
            success: function(json){
                $this.find('.btn-submit').btn("reset");
                $this.find(".lang-message").html('');

                if(json['success']){
                    $this.find(".lang-message").html('<div class="d-inline-block text-success">'+ json['success'] +'</div>');
                    $this[0].reset();
                }
                if(json['warning']){
                    $this.find(".lang-message").html('<div class="d-inline-block text-danger">'+ json['warning'] +'</div>');
                }
            },
        });
        return false;
    });
</script>
