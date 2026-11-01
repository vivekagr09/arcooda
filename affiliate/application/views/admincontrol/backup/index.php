<div class="row row-cols-1 row-cols-md-2 g-4">
    <div class="col">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <h5><?= __('admin.upload_backup_file_zip') ?></h5>
            </div>
            <div class="card-body d-flex flex-column">
                <form enctype="multipart/form-data" method="POST" action="" class="mb-auto">
                    <div class="row gy-3">
                        <div class="col-lg-9 col-md-8">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="backup_file">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <button type="submit" class="btn btn-primary w-100"><?= __('admin.upload') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <h5><?= __('admin.reset_all_data') ?></h5>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="row gy-3 mb-auto">
                    <div class="col-lg-4 col-md-4">
                        <button class="btn btn-warning w-100 open-databascommieclear">
                            <?= __('admin.reset_commission_data') ?>
                        </button>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <button class="btn btn-danger w-100 open-databaseclear">
                            <?= __('admin.reset_all_script_data') ?>
                        </button>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <button onclick="window.location.href='<?= base_url("admincontrol/refactor_database"); ?>'" class="btn btn-primary w-100">
                            <?= __('admin.refactor_database_structure') ?>
                        </button>
                    </div>
                </div>
                <div class="mt-4">
                    <p id="result"></p>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mt-5">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5><?= __('admin.database_backup') ?></h5>
                <a id="toggle-uploader" class="btn btn-light" href="<?= base_url('admincontrol/backup/getbackup') ?>"><?= __('admin.get_backup') ?></a>
            </div>
            <div class="card-body d-flex flex-column">
                <?php if ($backups == null) { ?>
                    <div class="text-center mt-5">
                     <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                         <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                         <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                     </div>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive mb-auto">
                        <table id="tech-companies-1" class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?= __('admin.file_name') ?></th>
                                    <th><?= __('admin.date_time') ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $backup) { ?>
                                    <tr>
                                        <td>
                                            <div><?= $backup['file'] ?></div>
                                            <div class="text-muted"><b><?= __('admin.size') ?>:</b> <?= $backup['size'] ?></div>
                                        </td>
                                        <td><?= $backup['date'] ?></td>
                                        <td>
                                            <div class="d-grid gap-2">
                                                <a href="<?= base_url('admincontrol/backup/download?file_name=' . $backup['file']) ?>" class="btn btn-success"><?= __('admin.download') ?></a>
                                                <a href="<?= base_url('admincontrol/backup/restore?file_name=' . $backup['file']) ?>" class="btn btn-primary" onclick="return confirm('<?= __('admin.restore_file_confirm') ?>')"><?= __('admin.restore') ?></a>
                                                <a href="<?= base_url('admincontrol/backup/delete?file_name=' . $backup['file']) ?>" class="btn btn-danger" onclick="return confirm('<?= __('admin.delete_file_confirm') ?>')"><?= __('admin.delete') ?></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>




 
<div class="modal" id="model-databaseclear">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="content-view">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.reset_all_script_data') ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= __('admin.reset_all_script_data_warning'); ?>                    
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-primary cleandatabase"><?= __('admin.yes_reset_data'); ?></button>
                </div>
            </div>
            <div class="password-view d-none">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.reset_all_script_data'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label"><?= __('admin.enter_admin_password'); ?></label>
                        <input type="password" name="admin_password" id="admin_password" class="form-control">
                    </div>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-primary cleandatabase"><?= __('admin.yes_reset_data'); ?></button>
                </div>
            </div>

            <div class="finalconfirm-view d-none">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.reset_data_confirmation'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?= __('admin.reset_data_warning'); ?></p>
                    <p class="text-danger"><?= __('admin.reset_data_agreed'); ?></p>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-primary final-cleandatabase"><?= __('admin.erase_all_data'); ?></button>
                </div>
            </div>
            
        </div>
    </div>
</div>


<div class="modal" id="model-databascommieclear">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="content-view">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.reset_commission_data'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= __('admin.reset_commission_data_warning'); ?>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-warning databascommieclear"><?= __('admin.yes_reset_comm_data'); ?></button>
                </div>
            </div>
            <div class="password-view d-none">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.reset_all_commission_data'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label"><?= __('admin.enter_admin_password'); ?></label>
                        <input type="password" name="admin_password" class="form-control">
                    </div>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-warning databascommieclear"><?= __('admin.yes_reset_comm_data'); ?></button>
                </div>
            </div>

            <div class="finalconfirm-view d-none">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.reset_data_confirmation'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?= __('admin.reset_data_warning'); ?></p>
                    <p class="text-danger"><?= __('admin.reset_data_agreed'); ?></p>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-warning final-databascommieclear"><?= __('admin.erase_all_comm_data'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="model-clear_deposit">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="content-view">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.reset_deposit_data'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= __('admin.reset_deposit_data_confirmation'); ?>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-warning clear_deposit"><?= __('admin.yes_delete_deposit_data'); ?></button>
                </div>
            </div>
            <div class="password-view d-none">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.delete_deposit_data'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label"><?= __('admin.enter_admin_password'); ?></label>
                        <input type="password" name="admin_password" class="form-control">
                    </div>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-warning clear_deposit"><?= __('admin.yes_delete_deposit_data'); ?></button>
                </div>
            </div>

            <div class="finalconfirm-view d-none">
                <div class="modal-header">
                    <h4 class="modal-title m-0"><?= __('admin.delete_data_confirmation'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?= __('admin.vendor_deposit_delete_warning'); ?></p>
                    <p class="text-danger"><?= __('admin.vendor_deposit_delete_agreed'); ?></p>
                </div>

                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.cancel_action'); ?></button>
                    <button type="button" class="btn btn-warning final-clear_deposit"><?= __('admin.erase_vendor_deposit_data'); ?></button>
                </div>
            </div>
            
        </div>
    </div>
</div>




<script type="text/javascript">
	$(".open-databaseclear").on("click",function(){
        $("#model-databaseclear").modal("show");
        $container = $("#model-databaseclear");
        $container.find(".content-view").removeClass('d-none');
        $container.find(".password-view,.finalconfirm-view").addClass('d-none');
    });
    
    $(".final-cleandatabase").on("click",function(){
        $this = $(this);
        if(password_confirm){
            $.ajax({
                url:'<?= base_url("admincontrol/clear_tables") ?>',
                type:'POST',
                dataType:'json',
                data:{admin_password: $("#admin_password").val()},
                beforeSend:function(){$this.btn("loading");},
                complete:function(){$this.btn("reset");},
                success:function(json){
                    if(json['success']){window.location.reload();}
                    
                    $container.find(".has-error").removeClass("has-error");
                    $container.find("span.text-danger").remove();
                    
                    if(json['errors']){
                        $.each(json['errors'], function(i,j){
                            $ele = $container.find('[name="'+ i +'"]');
                            if($ele){
                                $ele.parents(".form-group").addClass("has-error");
                                $ele.after("<span class='text-danger'>"+ j +"</span>");
                            }
                        })
                    }
                },
            })
        }
    });

    var password_confirm  = false;
    $(".cleandatabase").on("click",function(){
        $this = $(this);
        $container = $("#model-databaseclear");

        if($container.find(".password-view").hasClass("d-none")){
            $container.find(".password-view").removeClass('d-none');
            $container.find(".content-view").addClass('d-none');
            return true;
        }

        $.ajax({
            url:'<?= base_url("admincontrol/clear_tables") ?>',
            type:'POST',
            dataType:'json',
            data:{admin_password: $("#admin_password").val(),password_confirm:true},
            beforeSend:function(){$this.btn("loading");},
            complete:function(){$this.btn("reset");},
            success:function(json){
                if(json['success']){
                    password_confirm  = true;
                    $container.find(".finalconfirm-view").removeClass('d-none');
                    $container.find(".content-view,.password-view").addClass('d-none');
                }
                
                $container.find(".has-error").removeClass("has-error");
                $container.find("span.text-danger").remove();

                if(json['status'] === 'error') {
                $('#model-databaseclear').modal('hide');;
                showPrintMessage(json['message'], 'error');}
                
                else if(json['errors']){
                    $.each(json['errors'], function(i,j){
                        $ele = $container.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ j +"</span>");
                        }
                    })
                }
            },
        })

    });
</script>

<script type="text/javascript">
	$(".open-databascommieclear").on("click",function(){
        $("#model-databascommieclear").modal("show");
        $container = $("#model-databascommieclear");
        $container.find(".content-view").removeClass('d-none');
        $container.find(".password-view,.finalconfirm-view").addClass('d-none');
    });
    
    $(".final-databascommieclear").on("click",function(){
        $this = $(this);
        if(password_confirm){
            $.ajax({
                url:'<?= base_url("admincontrol/clear_commission_tables") ?>',
                type:'POST',
                dataType:'json',
                data:{admin_password: $("#model-databascommieclear input[name='admin_password']").val()},
                beforeSend:function(){$this.btn("loading");},
                complete:function(){$this.btn("reset");},
                success:function(json){
                    if(json['success']){window.location.reload();}
                    
                    $container.find(".has-error").removeClass("has-error");
                    $container.find("span.text-danger").remove();
                    
                    if(json['errors']){
                        $.each(json['errors'], function(i,j){
                            $ele = $container.find('[name="'+ i +'"]');
                            if($ele){
                                $ele.parents(".form-group").addClass("has-error");
                                $ele.after("<span class='text-danger'>"+ j +"</span>");
                            }
                        })
                    }
                },
            })
        }
    });

    var password_confirm  = false;

    $(".databascommieclear").on("click",function(){
        $this = $(this);
        $container = $("#model-databascommieclear");

        if($container.find(".password-view").hasClass("d-none")){
            $container.find(".password-view").removeClass('d-none');
            $container.find(".content-view").addClass('d-none');
            return true;
        }

        $.ajax({
            url:'<?= base_url("admincontrol/clear_commission_tables") ?>',
            type:'POST',
            dataType:'json',
            data:{admin_password: $("#model-databascommieclear input[name='admin_password']").val(),password_confirm:true},
            beforeSend:function(){$this.btn("loading");},
            complete:function(){$this.btn("reset");},
            success:function(json){
                if(json['success']){
                    password_confirm  = true;
                    $container.find(".finalconfirm-view").removeClass('d-none');
                    $container.find(".content-view,.password-view").addClass('d-none');
                }
                
                $container.find(".has-error").removeClass("has-error");
                $container.find("span.text-danger").remove();

                if(json['status'] === 'error') {
                $('#model-databascommieclear').modal('hide');
                showPrintMessage(json['message'], 'error');}

                else if(json['errors']){
                    $.each(json['errors'], function(i,j){
                        $ele = $container.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ j +"</span>");
                        }
                    })
                }
            },
        })

    });
</script>

<script type="text/javascript">
    $(".open-clear_deposit").on("click",function(){
        $("#model-clear_deposit").modal("show");
        $container = $("#model-clear_deposit");
        $container.find(".content-view").removeClass('d-none');
        $container.find(".password-view,.finalconfirm-view").addClass('d-none');
    });
    
    $(".final-clear_deposit").on("click",function(){
        $this = $(this);
        if(password_confirm){
            $.ajax({
                url:'<?= base_url("admincontrol/clear_deposit_tables") ?>',
                type:'POST',
                dataType:'json',
                data:{admin_password: $("#model-clear_deposit input[name='admin_password']").val()},
                beforeSend:function(){$this.btn("loading");},
                complete:function(){$this.btn("reset");},
                success:function(json){
                    if(json['success']){window.location.reload();}
                    
                    $container.find(".has-error").removeClass("has-error");
                    $container.find("span.text-danger").remove();
                    
                    if(json['errors']){
                        $.each(json['errors'], function(i,j){
                            $ele = $container.find('[name="'+ i +'"]');
                            if($ele){
                                $ele.parents(".form-group").addClass("has-error");
                                $ele.after("<span class='text-danger'>"+ j +"</span>");
                            }
                        })
                    }
                },
            })
        }
    });

    var password_confirm  = false;

    $(".clear_deposit").on("click",function(){
        $this = $(this);
        $container = $("#model-clear_deposit");

        if($container.find(".password-view").hasClass("d-none")){
            $container.find(".password-view").removeClass('d-none');
            $container.find(".content-view").addClass('d-none');
            return true;
        }

        $.ajax({
            url:'<?= base_url("admincontrol/clear_deposit_tables") ?>',
            type:'POST',
            dataType:'json',
            data:{admin_password: $("#model-clear_deposit input[name='admin_password']").val(),password_confirm:true},
            beforeSend:function(){$this.btn("loading");},
            complete:function(){$this.btn("reset");},
            success:function(json){
                if(json['success']){
                    password_confirm  = true;
                    $container.find(".finalconfirm-view").removeClass('d-none');
                    $container.find(".content-view,.password-view").addClass('d-none');
                }
                
                $container.find(".has-error").removeClass("has-error");
                $container.find("span.text-danger").remove();
                
                if(json['errors']){
                    $.each(json['errors'], function(i,j){
                        $ele = $container.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ j +"</span>");
                        }
                    })
                }
            },
        })

    });
</script>

<script>
    $(document).on("click", ".import_demo_data", function(){
        
        if (confirm('<?= __('admin.import_demo_data_warning'); ?>')) 
        {
            $.ajax({
                url:'<?= base_url("admincontrol/import_demo_data") ?>',
                success: function() {
                    document.getElementById("result").innerHTML = '<?= __('admin.import_demo_data_successful'); ?>';
                }
            });
        }
        else
        {
            document.getElementById("result").innerHTML = '<?= __('admin.action_canceled'); ?>';
        } 
    });
</script>
