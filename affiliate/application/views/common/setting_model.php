<div class="modal-dialog modal-dialog-centered dashboard-setting" role="document">
    <div class="modal-content">
<div class="modal-header">
  <h5 class="modal-title"><?= $title ?></h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="setting-form">
        <input type="hidden" value="<?= $setting_key ?>" name="setting_key">
        <ul class="list-group list-group-flush">
            <?php foreach ($settings as $key => $d) : ?>
                <?php 
                    $settingK = 'admin_' . $key;
                    $value = isset($db_value[$settingK]) ? $db_value[$settingK] : 0;
                ?>

                <?php if ($d['type'] == 'switch') : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= $d['name'] ?></span>
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[<?= $settingK ?>]" value="0">
                            <input class="form-check-input" type="checkbox" <?= $value == '1' ? 'checked' : '' ?> name="settings[<?= $settingK ?>]" value='1'>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if ($d['type'] == 'number') : ?>
                    <li class="list-group-item">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><?= $d['name'] ?></span>
                            <input type="number" class="form-control" name="settings[<?= $settingK ?>]" value="<?= $value ?>">
                        </div>
                        <?php if (isset($d['help'])) : ?>
                            <p class="help-block"><?= $d['help'] ?></p>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>

            <?php endforeach; ?>
        </ul>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary save-settings"><?= __('admin.save') ?></button>
    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
</div>
    </div>
</div>

<script type="text/javascript">
    $(".save-settings").on('click', function () {
        $this = $(this);

        $.ajax({
            url: '<?= base_url('setting/saveSetting') ?>',
            type: 'POST',
            dataType: 'json',
            data: $("#setting-form").serialize(),
            beforeSend: function () { $this.btn("loading"); },
            complete: function () { $this.btn("reset"); },
            success: function (json) {
                if (json['success']) {
                    $("#setting-widzard").modal("hide");

                    <?php if ($setting_key == 'live_dashboard') : ?>
                        window.location.reload();
                    <?php endif; ?>
                    <?php if ($setting_key == 'live_log') : ?>

                        settings_clear = true;
                        last_id_integration_logs = 0;
                        last_id_integration_orders = 0;
                        last_id_newuser = 0;
                        last_id_notifications = 0;

                        $('.btn-count-notification .count-notifications').text(0);
                        $(".ajax-live_window").html('');

                        $(".live-wrap-empty-data").css('display', 'block');
                        $(".ajax-live_window").css('display', 'none');

                        getDashboard(false, false, 'clearlog');
                    <?php endif; ?>
                }
            },
        })
    })
</script>
