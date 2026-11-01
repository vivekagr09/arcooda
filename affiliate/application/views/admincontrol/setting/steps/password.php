<div class="modal" id="model-adminpassword">

    <div class="modal-dialog modal-dialog-centered modal-md">

        <div class="modal-content">

            <?php if($for == 'license'){ ?>

                <div class="modal-header">

                    <h5 class="modal-title"><?= __('admin.confirm_codecanyon_license') ?></h5>

                </div>

            <?php } ?>



            <div class="modal-body">

                <div class="form-group">

                    <label class="control-label"><?= __('admin.enter_admin_password') ?></label>

                    <input type="password" id="admin-password" class="password">

                </div>



                <div class="form-group">

                    <label class="control-label"><?= __('admin.codecanyon_licence') ?></label>

                    <input type="text" id="codecanyon-license" class="password">

                </div>

                <div class="form-group">
                    <label class='control-label'><?= __('admin.user_name') ?></label>
                    <input type='text' name='username' class='form-control' readonly="true" autocomplete="off">
                </div>

                <div class="mt-4 password-alert alert d-none alert-danger"></div>

            </div>

            <div class="modal-footer">

                <div class="text-center">

                    <button type="button" class="btn btn-primary btn-confirm-password" disabled="true"><?= __('admin.confirm') ?></button>

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.close') ?></button>

                </div>

            </div>

        </div>

    </div>

</div>





<script type="text/javascript">

    $(document).on('change','#codecanyon-license', function(){
        $this = $(this);
        $.ajax({
            url:'<?php echo base_url() ?>/install/codecanyon.php',
            type:'POST',
            dataType:'json',
            data:{
                code: $this.val()
            },
            success:function(json){
                $($this).parent().removeClass("has-error");
                $($this).parent().find("span.text-danger").remove();                
                if(json['errors']){
                    $('[name="username"]').val('');
                    $.each(json['errors'], function(i,j){
                       $($this).parent().addClass("has-error");
                       $($this).parent().append("<span class='text-danger'>"+ j +"</span>");
                    })
                }else{
                    if(json.response.buyer){
                        $('.btn-confirm-password').removeAttr('disabled');
                        $('input[name="username"]').val(json.response.buyer);
                    }
                }
            },
        })

        return false;
    });

    $(".btn-confirm-password").on("click",function(){

        $this = $(this);

        $.ajax({

            url:'<?= base_url("installversion/check_confirm_password") ?>',

            type:'POST',

            dataType:'json',

            data:{

                <?php if($for == 'license'){ 

                    echo "create_license_version : 1,";

                } ?>

                password:$("#admin-password").val(),

                codecanyon:$("#codecanyon-license").val()

            },

            beforeSend:function(){$this.btn("loading");},

            complete:function(){$this.btn("reset");},

            success:function(json){

                $(".password-alert").addClass('d-none');



                if(json['warning']){

                    $(".password-alert").html(json['warning']).removeClass('d-none');

                }



                if(json['success']){

                    $("#model-adminpassword").modal("hide");

                    <?php if($for == 'files'){ ?>

                        migrateFiles($(".btn-upload-database"));

                    <?php } elseif($for == 'license') { ?>

                        location.reload();

                    <?php } else { ?>

                        migrateDatabase($(".btn-upload-update"));

                    <?php } ?>

                }

            },

        })

    })

</script>