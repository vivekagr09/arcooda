   <section class="about-wrap-layout1">
      <div class="container">
         <div class="col-lg-10 col-sm-12 mx-auto">
            <div class="row">
             <div class="col-lg-12">
               <div class="about-box-layout1">
                  <h2 class="item-title"><?= __('store.profile') ?></h2>
               </div>
            </div>
            <div class="col-lg-12">

               <form id="frm_profile" method="post" action="<?php echo base_url('classified/profile') ?>" enctype="multipart/form-data">
                  <div class="row">
                     <div class="col-md-12 text-center">
                        <div class="form-group">
                           <?php 
                           $avatar = ($userDetails['avatar'] != '') ? base_url('assets/images/users/'.$userDetails['avatar']) : base_url('assets/store/default/img/blog1.png') ; 
                           ?>
                           <img id="blah" src="<?= $avatar ?>" class="img-profile-main img-thumbnail" alt="<?= __('store.profile') ?>">
                           <div class="fileUpload btn text-dark text-center w-100">
                              <span><i class="far fa-image mr-2"></i><?= __('store.choose_file') ?></span>
                              <input id="uploadBtn" name="avatar" class="upload" type="file" style="display:none;">
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="fiest_name" class="form-label"><?= __('store.firstname') ?></label>
                              <input type="text" class="form-control" id="fiest_name" placeholder="<?= __('store.firstname')?>" value="<?php echo $userDetails['firstname']; ?>" name="firstname">
                              <?php if(isset($this->session->flashdata('error')['firstname'])) { ?>
                                 <div class="text-danger"><?= $this->session->flashdata('error')['firstname'] ?></div>
                              <?php } ?>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                             <label for="last_name" class="form-label"><?= __('store.lastname') ?></label>
                             <input type="text" class="form-control" id="last_name" placeholder="<?= __('store.lastname') ?>" value="<?php echo $userDetails['lastname']; ?>" name="lastname">
                             <?php if(isset($this->session->flashdata('error')['lastname'])) { ?>
                              <div class="text-danger"><?= $this->session->flashdata('error')['lastname'] ?></div>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="email" class="form-label"><?= __('store.email') ?></label>
                           <input type="email" class="form-control" id="email" placeholder="<?= __('store.email') ?>"  value="<?php echo $userDetails['email']; ?>"  name="email">
                           <?php if(isset($this->session->flashdata('error')['email'])) { ?>
                              <div class="text-danger"><?= $this->session->flashdata('error')['email'] ?></div>
                           <?php } ?>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="PhoneNumber" class="form-label"><?= __('store.phone') ?></label>
                           <input type="hidden" name='PhoneNumberInput' id="phonenumber-input" value="" class="form-control" placeholder="<?= __('store.phone_number') ?>"  >

                           <input type="text" class="form-control" id="PhoneNumber" placeholder="<?= __('store.phone') ?>" onkeypress="return isNumberKey(event);" value="<?php echo $userDetails['PhoneNumber']; ?>" name="PhoneNumber"> 
                           <?php if(isset($this->session->flashdata('error')['PhoneNumber'])) { ?>
                              <div class="text-danger"><?= $this->session->flashdata('error')['PhoneNumber'] ?></div>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3">

                        </div><label><?= __('store.country') ?></label>
                        <select name="Country" class="custom-select form-control countries" id="Country" >
                           <option value="" selected="selected" ><?= __('store.select_country') ?></option>
                           <?php foreach($country as $countries): ?>
                              <option <?php  if( $userDetails['country'] == $countries->id) { ?> selected <?php }?> value="<?php echo $countries->id; ?>"><?php echo $countries->name; ?></option>
                           <?php endforeach; ?> 
                        </select>
                        <?php if(isset($this->session->flashdata('error')['Country'])) { ?>
                           <div class="text-danger"><?= $this->session->flashdata('error')['Country'] ?></div>
                        <?php } ?>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                          <label for="StateProvince" class="form-label"><?= __('store.state') ?></label>
                          <select class="form-select" aria-label="Default select example" name="StateProvince" id="StateProvince">
                          </select>
                          <?php if(isset($this->session->flashdata('error')['Country'])) { ?>
                           <div class="text-danger"><?= $this->session->flashdata('error')['StateProvince'] ?></div>
                        <?php } ?>
                     </div>
                  </div>

               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label for="zip_code" class="form-label"><?= __('store.postal_code') ?></label>
                        <input type="text" class="form-control" id="zip_code" placeholder="<?= __('store.postal_code') ?>" name="Zip" value="<?php echo $userDetails['zip']; ?>">
                        <?php if(isset($this->session->flashdata('error')['Zip'])) { ?>
                           <div class="text-danger"><?= $this->session->flashdata('error')['Zip'] ?></div>
                        <?php } ?>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                      <label for="city" class="form-label"><?= __('store.city') ?></label>
                      <input type="text" class="form-control" id="city" placeholder="<?= __('store.city') ?>"  value="<?php echo $userDetails['city'];?>" name="City" >
                      <?php if(isset($this->session->flashdata('error')['City'])) { ?>
                        <div class="text-danger"><?= $this->session->flashdata('error')['City'] ?></div>
                     <?php } ?>
                  </div>
               </div>

            </div>
            <div class="row">
             <div class="col-md-6">
               <div class="mb-3">
                 <label for="password" class="form-label"><?= __('store.password') ?></label>
                 <input type="password" class="form-control" id="password" placeholder="<?= __('store.password') ?>" name="new_password" name="password">
                 <?php if(isset($this->session->flashdata('error')['new_password'])) { ?>
                  <div class="text-danger"><?= $this->session->flashdata('error')['new_password'] ?></div>
               <?php } ?>
            </div>
         </div>
         <div class="col-md-6">
            <div class="mb-3">
               <label for="cpassword" class="form-label"><?= __('store.confirm_password') ?></label>
               <input type="password" class="form-control" id="cpassword" placeholder="<?= __('store.confirm_password') ?>" >
            </div>
         </div>
      </div>
      <div class="row">
         <div class="mb-3">
            <label class="form-label"><?= __('store.full_address') ?></label>
            <textarea class="form-control" name="twaddress"><?= isset($userDetails) ? $userDetails['twaddress'] : '' ?></textarea>
            <?php if($errors && isset($errors['twaddress'])) { ?>
               <div class="text-danger"><?php echo $errors['twaddress'] ?></div>
            <?php } ?>
         </div>
      </div>
      <div class="row">
        <div class="col-md-6 ">
         <div class="mt-4 ">
           <button type="submit" class="btn btn-primary float-right"><?= __('store.update')?></button>
        </div>
     </div>
  </div>
</form>


</div>

<div class="col-lg-12 mt-4">
   <div class="about-box-layout1">
      <h2 class="item-title"><?= __('store.shipping_details') ?></h2>
   </div>
</div>
<div class="col-lg-12">
   <form id="frm_shipping_address" action="<?php echo base_url('classified/shipping');?>" method="post">
      <div class="row">
         <div class="col-md-6">
            <div class="mb-3">
               <label for="address" class="form-label"><?= __('store.address') ?></label>
               <input type="text" class="form-control" name="address" id="address" placeholder="<?= __('store.address') ?>" value="<?= isset($shipping) ? $shipping['address'] : '' ?>">
               <?php if($errors && isset($errors['address'])) { ?>
                  <div class="text-danger"><?php echo $errors['address'] ?></div>
               <?php } ?>
            </div>
         </div>
         <div class="col-md-6">
            <div class="mb-3">
              <label for="country_id" class="form-label"><?= __('store.country') ?></label>
              <?php $selected =  isset($shipping) ? $shipping['country_id'] : '' ?>
              <select class="custom-select form-control" name="country" id="country_id">
               <?php foreach ($country as $key => $value) { ?>
                  <option <?= $selected == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
               <?php } ?>
            </select>
         </div>
      </div>
   </div>
   <div class="row">

      <div class="col-md-6">
         <div class="mb-3">
           <label for="state_id" class="form-label"><?= __('store.state') ?></label>
           <select class="form-select" aria-label="Default select example" name="state" id="state_id">
           </select>
        </div>
     </div>
     <div class="col-md-6">
      <div class="mb-3">
         <label for="city" class="form-label"><?= __('store.city') ?></label>
         <input class="form-control" name="city" type="text" value="<?= isset($shipping) ? $shipping['city'] : '' ?>">
         <?php if($errors && isset($errors['city'])) { ?>
            <div class="text-danger"><?php echo $errors['city'] ?></div>
         <?php } ?>
      </div>
   </div>
</div>
<div class="row">

   <div class="col-md-6">
      <div class="mb-3">
         <label for="zip_code" class="form-label"><?= __('store.postal_code') ?></label>
         <input class="form-control" name="zip_code" type="text" value="<?= isset($shipping) ? $shipping['zip_code'] : '' ?>">
         <?php if($errors && isset($errors['zip_code'])) { ?>
            <div class="text-danger"><?php echo $errors['zip_code'] ?></div>
         <?php } ?>
      </div>
   </div>
   <div class="col-md-6">
      <div class="mb-3">
         <label for="sphone" class="form-label"><?= __('store.phone') ?></label>
         <input type="hidden" id="phone-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />

         <input onkeypress="return isNumberKey(event);" id="phone" class="form-control" type="text" name="phone" value="<?= isset($shipping) ? $shipping['phone'] : '' ?>">
        <?php if($errors && isset($errors['phone'])) { ?>
         <div class="text-danger"><?php echo $errors['phone'] ?></div>
         <?php } ?>
      </div>
      <input type="hidden" name="shipping_address" value="1">  
   </div>

</div>
<div class="row">
   <div class="col-md-12 float-right">
      <div class="mt-4  text-right">
        <button type="submit" class="btn btn-primary float-right"><?= __('store.update')?></button>
     </div>
  </div>
</div>
</form>

</div>
</div>
</div>
</div>
</section>


<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css?v='.av()) ?>">
<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>

<script type="text/javascript">
   function readURL(input) {
      if (input.files && input.files[0]) {
         var reader = new FileReader();
         
         reader.onload = function(e) {
            jQuery('#blah').attr('src', e.target.result);
         }
         
         reader.readAsDataURL(input.files[0]);
      }
   }
   
   $(document).on('click', '.fileUpload span', function(){
      $('#uploadBtn').trigger('click');
   });

   document.getElementById("uploadBtn").onchange = function () {
      readURL(this);
   };
   var selected_state = '<?= isset($shipping) ? $shipping['state_id'] : '' ?>';
   var state = '<?= isset($userdetails) ? $userdetails['state'] : '' ?>';
   $(document).delegate('#country_id',"change",function(){
      $this = $(this);
      $.ajax({
         url:'<?= base_url('classified/getState') ?>',
         type:'POST',
         dataType:'json',
         data:{id:$this.val()},
         success:function(json){
            var html = '';
            $.each(json['states'], function(i,j){
               var s = '';
               if(selected_state && selected_state == j['id']){
                  s = 'selected';selected_state = 0;
               }
               html += "<option "+ s +" value='"+ j['id'] +"'>"+ j['name'] +"</option>";
            })
            $('[name="state"]').html(html);
         },
      })
   })
   $(document).delegate('#Country',"change",function(){
      $this = $(this);
      $.ajax({
         url:'<?= base_url('classified/getState') ?>',
         type:'POST',
         dataType:'json',
         data:{id:$this.val()},
         success:function(json){
            var html = '';
            $.each(json['states'], function(i,j){
               var s = '';
               if(state && state == j['id']){
                  s = 'selected';state = 0;
               }
               html += "<option "+ s +" value='"+ j['id'] +"'>"+ j['name'] +"</option>";
            })
            $('#StateProvince').html(html);
         },
      })
   })

   $('[name="country"],#Country').trigger("change");


   var tel_input = intlTelInput(document.querySelector("#PhoneNumber"), {
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

   window.errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>', '<?= __('store.mobile_number_is_required') ?>'];

   function isNumberKey(evt) {
     var charCode = (evt.which) ? evt.which : event.keyCode;
       if (charCode != 46 && charCode != 45 && charCode > 31
       && (charCode < 48 || charCode > 57))
        return false;

     return true;
   }

   $("#frm_profile").submit(function(){
      var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];
      is_valid = false;
      var errorInnerHTML = '';
      if ($("#PhoneNumber").val().trim()) {
         if (tel_input.isValidNumber()) {
            is_valid = true;
            $("#phonenumber-input").val("+"+tel_input.getSelectedCountryData().dialCode +' '+ $("#PhoneNumber").val().trim());
         } else {
            var errorCode = tel_input.getValidationError();
            errorInnerHTML = errorMap[errorCode];
         }
      } else {
         errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
      }
      $("#PhoneNumber").parents(".form-group").removeClass("has-error");
      $("#frm_profile .text-danger").remove();

      if(!is_valid){
         $("#PhoneNumber").parents(".form-group").addClass("has-error");
         $(".iti").after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
         return false;
      }
   });


   var tel_input_shipping = intlTelInput(document.querySelector("#phone"), {
          initialCountry: "auto",
          utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
          separateDialCode:true,
          geoIpLookup: function(success, failure) {
           $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            var countryCode = (resp && resp.country) ? resp.country : "";
            success(countryCode);
         });
      },
   });

   
   $("#frm_shipping_address").submit(function(){
      var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];
      is_valid = false;
      var errorInnerHTML = '';
      if ($("#phone").val().trim()) {
         if (tel_input_shipping.isValidNumber()) {
            is_valid = true;
            $("#phone-input").val("+"+tel_input_shipping.getSelectedCountryData().dialCode +' '+ $("#phone").val().trim());
         } else {
            var errorCode = tel_input_shipping.getValidationError();
            errorInnerHTML = errorMap[errorCode];
         }
      } else {
         errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
      }
      $("#phone").parents(".form-group").removeClass("has-error");
      $("#frm_shipping_address .text-danger").remove();

      if(!is_valid){
         $("#phone").parents(".form-group").addClass("has-error");
         $(".iti").after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
         return false;
      }
   });
</script>