<?php if($award_level_status){ ?>

<h4 class="notification_on_pages">
    <button id="toggle-uploader" class="btn btn-light">
        <?= __('admin.award_level_message') ?>
    </button>
</h4>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5><?= __('admin.award_level') ?></h5>
                    <div>
                        <button id="toggle-uploader" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#cron-job-info-modal"><?= __('admin.cron_job_setting') ?></button>
                        <button id="toggle-uploader" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#check-award-level-modal"><?= __('admin.check_award_level') ?></button>
                        <a id="toggle-uploader" href="<?= base_url('admincontrol/create_award_level') ?>" class="btn btn-light"><?= __("admin.add_new") ?></a>
                    </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th><?= __('admin.level_number') ?></th>
                                <th><?= __('admin.jump_level') ?></th>
                                <th><?= __('admin.minimum_earning') ?></th>
                                <th><?= __('admin.sale_comission_rate') ?></th>
                                <th><?= __('admin.bonus') ?></th>
                                <th><?= __('admin.default_registration_level') ?></th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($award_level as $key => $value){ ?>
                                <tr>
                                    <td><?= $value['level_number'] ?></td>
                                    <td>
                                        <?php
                                            if($value['jump_level'] == '0')
                                                echo __('admin.default');
                                            else
                                                echo $value['jump_level_name'];
                                        ?>
                                    </td>
                                    <td><?= c_format($value['minimum_earning']); ?></td>
                                    <td><?= $value['sale_comission_rate'].'%'; ?></td>
                                    <td><?= c_format($value['bonus']); ?></td>
                                    <td><?= ($value['default_registration_level']) ? __('admin.default') : '' ?></td>
                                    <td>
                                        <a href="<?= base_url('admincontrol/update_award_level/'.$value['id']) ?>" class="btn btn-sm btn-primary">
                                          <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="<?= base_url('admincontrol/delete_award_level/'.$value['id']) ?>" class="btn btn-sm btn-danger btn-delete">
                                          <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <ul class="pagination justify-content-end">
                    <?= $pagination ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="cron-job-info-modal" class="modal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('admin.cron_job_setting') ?></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h5><?= __('admin.what_is_cron_job') ?></h5>
                        <p><?= __('admin.what_is_cron_job_answer') ?></p>

                        <h6><?= __('admin.to_add_cron_job_steps') ?>:</h6>

                        <ol>
                            <li><?= __('admin.to_add_cron_job_step1') ?></li>
                            <li><?= __('admin.to_add_cron_job_step2') ?></li>
                            <li><?= __('admin.to_add_cron_job_step3') ?></li>
                            <li><?= __('admin.to_add_cron_job_step4') ?>  <b><?= __('admin.once_per_minute') ?>(* * * * *)</b>.</li>
                            <li><?= __('admin.to_add_cron_job_step5') ?> <div> <code>curl <?= base_url('/cronJob/check_award_level') ?></code></div> </li>
                            <li><?= __('admin.to_add_cron_job_step6') ?></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
            </div>
        </div>
    </div>
</div>

<div id="check-award-level-modal" class="modal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('admin.check_award_level') ?></h5>
            </div>
            <div class="modal-body">
                <div class="step-1">
                    <h5><?= __('admin.are_you_sure_checking_user_award_level') ?></h5>
                    <h6><?= __('admin.take_longer_depending_user_available') ?></h6>
                </div>
                <div class="step-2" style="display:none;">
                    <h5><?= __('admin.wait_while_checking_user_award_level') ?></h5>
                    <div id="progressbar123">
                        <div></div>
                    </div>
                    <h6 class="text-success jumped" data-count="0" style="display:none;">0 <?= __('admin.user_jumped_to_level') ?></h6>
                    <h6 class="text-warning warning" style="display:none;font-size: 21px;"><?= __('admin.no_jumped_user_available') ?></h6>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary allow_to_check_award_level"><?= __('admin.yes_continue') ?></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var warning = true;
    var jumped_user = false;  // Add this line to track if any user jumped a level
    $(document).on('click','.allow_to_check_award_level', function(){
        $(this).hide();
        $('#check-award-level-modal .step-1').hide();
        $('#check-award-level-modal .step-2').show();
        $('#check-award-level-modal .modal-footer').hide();
        recursive_check_award_level();
    });

    function recursive_check_award_level(index = 1){
        $.ajax({
        type:"POST",
        url: '<?= base_url('admincontrol/check_award_level')?>',
        dataType:"json",
        data:{index:index},
        success: function(data){
            if(data.progress_percentage){
                $('#progressbar123').show();
                $('#progressbar123 > div').css('width',data.progress_percentage);
            }

            if(data.jumped){
                jumped_user = true;  // Update this flag when a user jumps a level
                warning = false;

                let existing_count = $('#check-award-level-modal .step-2 .jumped').data('count');
                $('#check-award-level-modal .step-2 .jumped').data('count',(existing_count+1));
                $('#check-award-level-modal .step-2 .jumped').text((existing_count+1)+' '+data.message);
                $('#check-award-level-modal .step-2 .jumped').show();
            }
            
            if(data.index){
                recursive_check_award_level(data.index);
            } else {
                // Display the warning message only if no user jumped a level
                if(!jumped_user){
                    $('#check-award-level-modal .step-2 h5').hide();
                    $('#progressbar123').hide();
                    $('#check-award-level-modal .step-2 h6.text-warning').show();
                }
                
                $('#check-award-level-modal .modal-footer').show();
                $('#check-award-level-modal .modal-footer').html('<button type="button" class="btn btn-secondary" onclick="window.location.reload()">'+'<?= __('admin.close') ?>'+'</button>');
            } 
            }
        });
    }

    $(".btn-delete").off('click').on('click', function(e) {
        e.preventDefault();
        var proceed = confirm('<?= __("admin.sure_delete") ?>');
        if (proceed) {
            let url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    if (result.status) {
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            html: result.message,
                        });
                        showPrintMessage(result.message, 'error');
                    }
                },
            });
        }
    });
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