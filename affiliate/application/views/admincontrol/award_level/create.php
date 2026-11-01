<?php if($award_level_status){ ?>
    <div class="content-body">
        <div class="card award-level">
            <div class="card-header bg-secondary text-white d-flex justify-content-between">
                <h5><?= __('admin.award_level') ?></h5>
                <a id="toggle-uploader" href="<?= base_url('admincontrol/award_level') ?>" class="btn btn-sm btn-light">
                    <?= __('admin.back') ?>
                </a>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-content">
                        <div class="mb-3">
                            <label class="form-label">
                                <?= __('admin.level_number') ?>
                                <span class="field-description" data-bs-toggle="tooltip" title="<?= __('admin.award_level_level_number_desc') ?>">
                                </span>
                            </label>
                            <input type="text" class="form-control" name="level_number" placeholder="<?= __('admin.level_number') ?>">
                            <p class="error-message"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <?= __('admin.jump_level') ?>
                                <span class="field-description" data-bs-toggle="tooltip" title="<?= __('admin.award_level_jump_level_desc') ?>"></span>
                                <span class="field-description default-level-description d-none" data-bs-toggle="tooltip" title="<?= __('admin.award_level_jump_default_level_desc') ?>"></span>
                            </label>
                            <select class="form-select" name="jump_level">
                                <option value=''><?= __('admin.choose_jump_level') ?></option>
                                <option value="0"><?= __('admin.default') ?></option>
                                <?php foreach($award_levels as $key => $value): ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['level_number'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <p class="error-message"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <?= __('admin.minimum_earning') ?>
                                <span class="field-description" data-bs-toggle="tooltip" title="<?= __('admin.award_level_minimum_earning_desc') ?>"></span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text refer-reg-symball"><?= $CurrencySymbol ?></span>
                                <input type="number" class="form-control" name="minimum_earning" min="0" step="0.01" placeholder="<?= __('admin.minimum_earning') ?>">
                            </div>
                            <p class="error-message"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <?= __('admin.sale_comission_rate') ?>
                                <span class="field-description" data-bs-toggle="tooltip" title="<?= __('admin.award_level_sale_comission_rate_desc') ?>"></span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text refer-reg-symball">%</span>
                                <input type="number" class="form-control" name="sale_comission_rate" min="0" step="0.01" placeholder="<?= __('admin.sale_comission_rate') ?>">
                            </div>
                            <p class="error-message"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <?= __('admin.bonus') ?>
                                <span class="field-description" data-bs-toggle="tooltip" title="<?= __('admin.award_level_bonus_desc') ?>"></span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text refer-reg-symball"><?= $CurrencySymbol ?></span>
                            <input type="number" class="form-control" name="bonus" min="0" step="0.01" placeholder="<?= __('admin.bonus') ?>">
                        </div>
                        <p class="error-message"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label">
                            <?= __('admin.set_default') ?>
                            <span class="field-description" data-bs-toggle="tooltip" title="<?= __('admin.award_level_default_desc') ?>"></span>
                        </label>
                        <input type="checkbox" class="form-check-input" name="default_registration_level" value="1">
                        <p class="error-message"></p>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary me-3"><?= __('admin.save') ?></button>
                    <button type="submit" class="btn btn-primary" data-redirect='true'><?= __('admin.save_and_close') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("button[type='submit']").on('click',function(e){
        e.preventDefault();

        $this = $(this);
        let form = $(this).parents('form');
        let url = form.attr('action');

        $.ajax({
            type:'POST',
            dataType:'json',
            data:form.serialize(),
            success:function(result){
                $("input,select").removeClass('error');
                $(".error-message").text('');

                if(result.validation){
                    $.each(result.validation,function(key,value){
                        $("[name='"+key+"']").addClass('error');
                        $("[name='"+key+"']").siblings('.error-message').text(value);
                        showPrintMessage(value, 'error');
                    }) 
                } else {
                    if (result.status) {
                          showPrintMessage(result.message, 'success');

                          let redirect = $this.data('redirect');
                          if (redirect) {
                            setTimeout(function() {
                            window.location = '<?= base_url("admincontrol/award_level") ?>';
                            }, 1000);
                          }
                    } else {
                        showPrintMessage(result.message, 'error');
                    }
                    
                }
            },
        }); 
    })


    let jumpLevelDesc = '<?= __('admin.award_level_jump_level_desc') ?>';
    let defaultJumpLevelDesc = '<?= __('admin.award_level_jump_default_level_desc') ?>';
    $("select[name='jump_level']").on('change',function(){
        let value = $(this).val();
        if(value == '0'){
            $(this).siblings('label').find('.field-description').addClass('d-none');
            $(this).siblings('label').find('.field-description.default-level-description').removeClass('d-none');
        } else {
            $(this).siblings('label').find('.field-description').removeClass('d-none');
            $(this).siblings('label').find('.field-description.default-level-description').addClass('d-none');
        }
    })
</script>

<?php } else { ?>
    <div>
    <h4 class="notification_on_pages">
    <span class="badge bg-secondary"><?= __('admin.award_level_module_is_off') ?>
        <a href="<?= base_url('admincontrol/addons') ?>">
            <?= __('admin.admin_click_here_to_activate') ?>
        </a>
    </span>
    </h4>
</div>  
<?php } ?>