<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header text-center">
            <h5 class="modal-title text-center"><?= __('admin.integration_mlm_info') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mlm-settings">
            </div>
            <div class="mlm-levels">
                <table class="table" id="tbl_refer_level">
                    <thead>
                        <tr>
                            <th style="vertical-align: top; border-right: 1px solid lightgrey;"><?= __('admin.level_mlm') ?></th>
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
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
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
