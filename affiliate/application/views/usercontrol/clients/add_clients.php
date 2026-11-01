<form class="form-horizontal" method="post" action=""  enctype="multipart/form-data">
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
            <div class="card-header bg-blue-payment">
                  <div class="card-title-white pull-left m-0"><?= __('admin.page_title_addclients') ?></div>
                     <div class="pull-right">
                      <button id="toggle-uploader" class="btn btn-primary" type="submit"><?= __('admin.save') ?>
               </button>
                </div>
            </div>
            <div class="card-body">
               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.first_name') ?></label>								
                  <div class="col-sm-9">									
                  	<input name="firstname" value="<?php echo $client->firstname; ?>" class="form-control" required="required" type="text">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.last_name') ?></label>								
                  <div class="col-sm-9">									
                  	<input name="lastname" class="form-control" value="<?php echo $client->lastname; ?>" required="required" type="text">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.email') ?> </label>								
                  <div class="col-sm-9">									
                  	<input name="email" id="email" class="form-control" value="<?php echo $client->email; ?>" required="required" type="email">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.phone') ?> </label>                       
                  <div class="col-sm-9">                          
                     <input name="phone" id="phone" class="form-control" value="<?php echo $client->phone; ?>" <?php echo empty($client->phone) ?  'required="required"' : '';?> type="text">
                  </div>
               </div>
               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.username') ?> </label>								
                  <div class="col-sm-9">									
                  	<input name="username" id="username" class="form-control" value="<?php echo $client->username; ?>" type="text" <?= isset($client->id) ? 'readonly':'';?>>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.status') ?> </label>
                  <div class="col-sm-9">
                     <select name="status" class="form-control">
                        <option value="0"><?= __('admin.disable') ?></option>
                        <option value="1" <?= $client->status == '1' ? 'selected' : '' ?> ><?= __('admin.enable') ?></option>
                     </select>
                  </div>
               </div>

               <div class="form-group  row">
                  <label  class="col-sm-3 col-form-label"><?= __('admin.country') ?></label>
                  <div class="col-sm-9">
                     <select name="country" id="country" class="custom-select form-control">
                        <option value=""><?= __('admin.select_country') ?></option>
                        <?php foreach ($countries as $key => $value) { ?>
                           <option <?= $client->ucountry == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><?= __('admin.state') ?></label>
                  <div class="col-sm-9">
                     <select name="state" class="custom-select form-control">
                     </select>
                  </div>
               </div>
               <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><?= __('admin.city') ?></label>
                  <div class="col-sm-9">
                     <input type="text" placeholder="<?= __('admin.city') ?>" name="ucity" class="form-control" type="text" value="<?= $client->ucity ?>">
                  </div>
               </div>

               <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><?= __('admin.postal_code') ?></label>
                  <div class="col-sm-9">
                     <input class="form-control" name="uzip" placeholder="<?= __('admin.postal_code') ?>" type="text" value="<?= $client->uzip?>">
                  </div>
               </div>


               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.password') ?> </label>								
                  <div class="col-sm-9">									
                  	<input name="password" id="password" class="form-control" value="" <?php echo empty($client->email) ?  'required="required"' : '';?> type="password">
                  </div>
               </div>
               
               <div class="form-group row">
                  <label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.confirm_password') ?> </label>								
                  <div class="col-sm-9">									
                  	<input name="cnfrm_password" id="cnfrm_password" class="form-control" value="" type="password">								
                  </div>
               </div>						
            </div>
         </div>
      </div>
   </div>
</form>
<script type="text/javascript">
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