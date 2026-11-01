<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title m-0 text-center d-block w-100 "><?= __('admin.integration_mlm_info') ?></h6>
            <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mlm-settings">
                    
            </div>
            <div class="mlm-levels">
                <table class="table" id="tbl_refer_level">
                    <thead>
                        <tr>
                            <th style="vertical-align: top; border-right: 1px solid lightgrey;"><?= __('admin.level_mlm') ?></th>
                            <?php if(!$tool['vendor_id']): ?>
                                <th style="vertical-align: top; border-right: 1px solid lightgrey; text-align: center;">
                                    <?= __('admin.cpr_cost') ?><br>
                                    <?php if ($tool['referlevel']['reg_comission_type'] == 'disabled'): ?>
                                        <span class="form-control"><?= __('admin.select_registration_commission_plan') ?></span>
                                    <?php endif ?>
                                    <?php if ($tool['referlevel']['reg_comission_type'] == 'percentage'): ?>
                                        <span class="form-control refer-reg-symball-select" symbal='%'><?= __('admin.membership_registration_commission_perce') ?></span>
                                    <?php endif ?>
                                    <?php if ($tool['referlevel']['reg_comission_type'] == 'custom_percentage'): ?>
                                        <span class="form-control refer-reg-symball-select" symbal='%'><?= __('admin.registration_custom_commission_amount_perce') ?></span>
                                    <?php endif ?>
                                    <?php if ($tool['referlevel']['reg_comission_type'] == 'fixed'): ?>
                                        <span class="form-control refer-reg-symball-select" symbal='<?= $CurrencySymbol ?>'><?= __('admin.registration_fixed_amount') ?></span>
                                    <?php endif ?>

                                    <?php if ($tool['commission_type'] == 'default'): ?>
                                        <span class="form-control reg_comission_custom_amt"><?php echo isset($tool['referlevel']['reg_comission_custom_amt']) ? $tool['referlevel']['reg_comission_custom_amt'] : 0;?>
                                         </span>
                                    <?php endif ?>
                                </th>
                            <?php endif ?>
                            <th style="vertical-align: top; border-right: 1px solid lightgrey; text-align: center;">
                                <?= __('admin.cps_cost') ?><br>
                                <?php if ($tool['referlevel']['sale_type'] == 'percentage'): ?>
                                    <span class="form-control refer-symball-select" symbal='%'><?= __('admin.percentage') ?></span>
                                <?php endif ?>
                                <?php if ($tool['referlevel']['sale_type'] == 'fixed'): ?>
                                    <span class="form-control refer-symball-select" symbal='<?= $CurrencySymbol ?>'><?= __('admin.fixed') ?></span>
                                <?php endif ?>
                            </th>
                            <th style="vertical-align: top; border-right: 1px solid lightgrey; text-align: center;" colspan="2"><?= __('admin.clicks_count') ?> &amp; <?= __('admin.cpc_cost') ?></th>
                            <th style="vertical-align: top; text-align: center;"><?= __('admin.cpa_cost') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $tool_levels = isset($tool['referlevel']['levels']) ? (int)$tool['referlevel']['levels'] : 3;
                        for ($level =1; $level <= $tool_levels; $level++) { ?>
                            <tr>
                                <td style="border-right: 0.1px solid lightgrey;"><?= $level ?></td>
                                <?php if(!$tool['vendor_id']): ?>
                                    <td style="border-right: 0.1px solid lightgrey;">
                                        <div class="input-group">
                                            <span class="form-control"><?php echo $tool['referlevel_'. $level]['reg_commission'] ?></span>
                                            <div class="input-group-append"><span class="input-group-text refer-reg-symball"></span></div>
                                        </div>
                                    </td>
                                <?php endif ?>
                                <td style="border-right: 0.1px solid lightgrey;">
                                    <div class="input-group">
                                        <span class="form-control"><?php echo $tool['referlevel_'. $level]['sale_commition'] ?></span>
                                        <div class="input-group-append"><span class="input-group-text refer-symball"></span></div>
                                    </div>
                                </td>
                                <td><span class="form-control"><?php echo $tool['referlevel_'. $level]['commition'] ?></span></td>
                                <td style="border-right: 0.1px solid lightgrey;">
                                    <div class="input-group">
                                        <span class="form-control"><?php echo $tool['referlevel_'. $level]['ex_commition'] ?></span>
                                        <div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="form-control"><?php echo $tool['referlevel_'. $level]['ex_action_commition'] ?></span>
                                        <div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
        </div>
    </div>
</div>

<script type="text/javascript">
    function chnage_teigger() {
        var symbal = $(".refer-symball-select").attr("symbal");
        $(".refer-symball").html(symbal);
    }
    chnage_teigger();

    var refer_reg_symball_select = '<?= $tool['referlevel']['reg_comission_type']; ?>';
    function chnage_teigger2() {
        var symbal = $(".refer-reg-symball-select").attr("symbal");

        if(refer_reg_symball_select == "disabled")
            $(".refer-reg-symball").empty();
        else
            $(".refer-reg-symball").html(symbal);

        if(refer_reg_symball_select != "custom_percentage")
            $('.reg_comission_custom_amt').hide();
        else
            $('.reg_comission_custom_amt').show();
    }
    chnage_teigger2();
</script>
