<script type="text/javascript">
    var tel_input = false;
</script>

<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>

<form id="addClientForm" method="post" action="" enctype="multipart/form-data">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between">
                    <h5><?= __('admin.page_title_addclients') ?></h5>
                    <button id="toggle-uploader" class="btn btn-light" type="submit"><?= __('admin.save') ?></button>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.first_name') ?></label>
                        <div class="col-sm-10">
                            <input name="firstname" value="<?php echo $client->firstname; ?>" class="form-control" required="required" type="text">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.last_name') ?></label>
                        <div class="col-sm-10">
                            <input name="lastname" class="form-control" value="<?php echo $client->lastname; ?>" required="required" type="text">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.email') ?></label>
                        <div class="col-sm-10">
                            <input name="email" id="email" class="form-control" value="<?php echo $client->email; ?>" required="required" type="email">
                        </div>
                    </div>
                    
                    <div class="mb-3 row ">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.phone') ?></label>
                        <div class="col-sm-10 form-group">
                            <input type="hidden" name='countrycode' id="countrycode" value="" class="form-control" placeholder="">

                            <input onkeypress="return isNumberKey(event);" id="phone" class="form-control custom_input tel_input" name="phone" type="text" value="<?php echo $client->phone; ?>" <?php echo empty($client->phone) ?  'required="required"' : '';?>>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.username') ?></label>
                        <div class="col-sm-10">
                            <input 
                                name="username" 
                                id="username" 
                                class="form-control <?= isset($client->id) ? 'bg-secondary' : '' ?>" 
                                value="<?php echo $client->username; ?>" 
                                type="text" 
                                <?= isset($client->id) ? 'readonly' : '';?> 
                            >
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.status') ?></label>
                        <div class="col-sm-10">
                            <select name="status" class="form-control">
                                <option value="0"><?= __('admin.disable') ?></option>
                                <option value="1" <?= $client->status == '1' ? 'selected' : '' ?> ><?= __('admin.enable') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label"><?= __('admin.country') ?></label>
                        <div class="col-sm-10">
                            <select name="country" id="country" class="form-select form-control">
                                <option value=""><?= __('admin.select_country') ?></option>
                                <?php foreach ($countries as $key => $value) { ?>
                                    <option <?= $client->ucountry == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label"><?= __('admin.state') ?></label>
                        <div class="col-sm-10">
                            <select name="state" class="form-select form-control">
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label"><?= __('admin.city') ?></label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="<?= __('admin.city') ?>" name="ucity" class="form-control" value="<?= $client->ucity ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label"><?= __('admin.postal_code') ?></label>
                        <div class="col-sm-10">
                            <input class="form-control" name="uzip" placeholder="<?= __('admin.postal_code') ?>" type="text" value="<?= $client->uzip?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label"><?= __('admin.full_address') ?></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="twaddress" placeholder="<?= __('admin.full_address') ?>" rows="4"><?= $client->twaddress ?></textarea>
                    </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.password') ?></label>
                        <div class="col-sm-10">
                            <input name="password" id="password" class="form-control" value="" <?php echo empty($client->email) ?  'required="required"' : '';?> type="password">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 col-form-label"><?= __('admin.confirm_password') ?></label>
                        <div class="col-sm-10">
                            <input name="cnfrm_password" id="cnfrm_password" class="form-control" value="" type="password">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $( document ).ready(function() {
        window.tel_inputphone = intlTelInput(document.querySelector("#phone"), {
          initialCountry: "auto",
          utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
          separateDialCode:true,
          geoIpLookup: function(success, failure) {
            $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
              var countryCode = (resp && resp.country) ? resp.country : "US";
                 success(countryCode);
            });
          },
        });
    });
    $( document ).ready(function() {
        $("#addClientForm").submit(function(e){
            $this = $(this);
            
            var is_valid = 0;
            var need_valid = 0;

            $(".tel_input").each(function() {
               
                let this_is_valid = true;
                $(this).parents(".form-group").removeClass("has-error");
                $(this).parents(".form-group").find(".text-danger").remove();

                if(window["tel_input"+$(this).attr('id')]){
                    
                    var errorMap = ['<?= __('user.invalid_number') ?>','<?= __('user.invalid_country_code') ?>','<?= __('user.too_short') ?>','<?= __('user.too_long') ?>','<?= __('user.invalid_number') ?>'];
                    var errorInnerHTML = '';
                    if ($(this).val().trim()) {
                        need_valid++;
                        if (window["tel_input"+$(this).attr('id')].isValidNumber()) {

                            window["tel_input"+$(this).attr('id')].setNumber($(this).val().trim());

                            is_valid++;
                            this_is_valid = true;
                        } else {
                            var errorCode = window["tel_input"+$(this).attr('id')].getValidationError();
                            errorInnerHTML = errorMap[errorCode];
                            this_is_valid = false;
                        }
                    } else {
                        if($(this).attr('required') !== undefined) {
                            need_valid++;
                            this_is_valid = false;
                            errorInnerHTML = 'The Mobile Number field is required.'; 
                        }
                    }
                    
                    if(!this_is_valid){
                        $(this).parents(".form-group").addClass("has-error");
                        $(this).parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
                        
                    }
                }
            });
            if(is_valid == 1){
                $(".tel_input").each(function() {
                    if ($(this).val().trim() && window["tel_input"+$(this).attr('id')].isValidNumber()) {
                        country_id = window["tel_input"+$(this).attr('id')].getSelectedCountryData().dialCode;
                        
                        $("#countrycode").val(country_id);
                    }
                });
                return true;
            }else{
                return false;  
            }
            
            
        });
    });
</script>

<script type="text/javascript">
    function isNumberKey(evt)
    {
      var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode != 46 && charCode != 45 && charCode > 31
        && (charCode < 48 || charCode > 57))
         return false;

      return true;
    }
   renderStateAndCart('<?=$client->ucountry?>');
   $("#country").change(function(){
      renderStateAndCart($(this).val())
   });
   function renderStateAndCart(countryCode) {
      $.ajax({
         url:'<?= base_url('admincontrol/getState') ?>',
         type:'POST',

         data:{country_id:countryCode,isId:true},
         beforeSend:function(){$('[name="state"]').prop("disabled",true);},
         complete:function(){$('[name="state"]').prop("disabled",false);},
         success:function(html){

          $('[name="state"]').html(html);
          if('<?=$client->state?>' !="")
          $('[name="state"]').val('<?=$client->state?>')
       },
    });
   } 
</script>