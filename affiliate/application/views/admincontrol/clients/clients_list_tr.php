<?php foreach ($clientslist as $clients) { ?>
  <tr>
    <td class="text-center"><?php echo $clients['id']; ?></td>
    <td>
      <?php
      $clientFullName = $clients['firstname'] . " " . $clients['lastname'];
      echo strlen($clientFullName) > 20 ? substr($clientFullName, 0, 20) . "..." : $clientFullName;
      ?>
    </td>
    <td><?php echo $clients['ref_user']; ?></td>
    <td class="text-center"><?php echo $clients['email']; ?></td>
    <td class="text-center"><?php echo $clients['phone']; ?></td>
    <td class="text-center"><?php echo $clients['username']; ?></td>
    <td class="text-center"><?php echo $clients['total_sale']; ?> / <?php echo c_format($clients['amount']); ?></td>
    <td class="text-center"><?php echo __('admin.type_' . $clients['type']); ?></td>
    <td class="text-center">
      <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#clientModel<?= $clients['id'] ?>">
        <i class="fas fa-info-circle" style="color:#ffffff"></i>
      </button>

      <!-- Modal -->
<div class="modal fade" id="clientModel<?= $clients['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= __('admin.client_info') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <strong><?= __('admin.firstname') ?></strong>
            <p><?php echo $clients['firstname']; ?></p>
          </div>
          <div class="col-md-6">
            <strong><?= __('admin.lastname') ?></strong>
            <p><?php echo $clients['lastname']; ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <strong><?= __('admin.refer_user') ?></strong>
            <p><?php echo $clients['ref_user']; ?></p>
          </div>
          <div class="col-md-6">
            <strong><?= __('admin.email') ?></strong>
            <p><?php echo $clients['email']; ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <strong><?= __('admin.phone') ?></strong>
            <p><?php echo $clients['phone']; ?></p>
          </div>
          <div class="col-md-6">
            <strong><?= __('admin.username') ?></strong>
            <p><?php echo $clients['username']; ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <strong><?= __('admin.sales') ?></strong>
            <p><?php echo $clients['total_sale']; ?> / <?php echo c_format($clients['amount']); ?></p>
          </div>
          <div class="col-md-6">
            <strong><?= __('admin.type') ?></strong>
            <p><?php echo __('admin.type_' . $clients['type']); ?></p>
          </div>
        </div>
        <div class="row">
        <div class="col-md-6">
            <strong><?= __('admin.country') ?></strong>
            <p>
            <?php
                $countryName = "<i class='text-muted'>" . __('admin.not_available') . "</i>";
                if (!empty($clients['ucountry'])) {
                    foreach ($countries as $key => $value) {
                        if ($clients['ucountry'] == $value->id) {
                            $countryName = $value->name;
                            break;
                        }
                    }
                }
                echo $countryName;
            ?>
            </p>
        </div>
          <div class="col-md-6">
            <strong><?= __('admin.state') ?></strong>
            <p><?php echo !empty($clients['state']) ? $clients['state'] : "<i class='text-muted'>" . __('admin.not_available') . "</i>"; ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <strong><?= __('admin.city') ?></strong>
            <p><?php echo !empty($clients['ucity']) ? $clients['ucity'] : "<i class='text-muted'>" . __('admin.not_available') . "</i>"; ?></p>
          </div>
          <div class="col-md-6">
            <strong><?= __('admin.postal_code') ?></strong>
            <p><?php echo !empty($clients['uzip']) ? $clients['uzip'] : "<i class='text-muted'>" . __('admin.not_available') . "</i>"; ?></p>
          </div>
          <div class="col-md-12">
              <strong><?= __('admin.full_address') ?></strong>
              <p>
                  <?php 
                  echo !empty($clients['twaddress']) ? nl2br($clients['twaddress']) : "<i class='text-muted'>" . __('admin.not_available') . "</i>"; 
                  ?>
              </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
      <a class="btn btn-info viewShipping" data-id="<?php echo $clients['id']; ?>" href="#" title="Click to view Shipping details"><i class="fa fa-shopping-cart" aria-hidden="true" style="color:#ffffff"></i></a>
      <a class="btn btn-danger deleteuser" data-url="<?php echo base_url(); ?>admincontrol/deleteusers/<?php echo $clients['id']; ?>/<?php echo $clients['type']; ?>" href="#"><i class="fa fa-trash-o cursors" aria-hidden="true" style="color:#ffffff"></i></a>
      <a class="btn btn-primary" onclick="return confirm('<?php echo __('admin.are_you_sure_to_edit'); ?>');" href="<?php echo base_url(); ?>admincontrol/addclients/<?php echo $clients['id']; ?>"><i class="fa fa-edit cursors" aria-hidden="true" style="color:#ffffff"></i></a>
    </td>
  </tr>
<?php } ?>

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
