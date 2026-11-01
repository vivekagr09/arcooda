<?php 
    if($action == 'confirm') {
    
    if(!empty($products)) {
?>

<textarea name="product_for_import" style="display:none;"><?= base64_encode(json_encode($products)); ?></textarea>
<table class="table">
  <thead>
    <tr>
      <th scope="col"><?= __('admin.id') ?></th>
      <th scope="col"><?= __('admin.product_name') ?></th>
      <th scope="col"><?= __('admin.action') ?></th>
      <th scope="col"><?= __('admin.error_details') ?></th>
    </tr>
  </thead>
  <tbody>
      <?php foreach($products as $pro) { ?>
    <tr>
      <td><?= (!empty($pro['row']['product_id'])) ? $pro['row']['product_id'] : '#'; ?></td>
      <td><?= (!empty($pro['row']['product_name'])) ? $pro['row']['product_name'] : '<span class="text-muted">'.__('admin.not_available').'</span>'; ?></td>
      <td><?php
        if(isset($pro['message'])) {
            echo $pro['message']; 
        } else {
            echo "<span class='bg-danger text-white'>".__('admin.skip')."</span>";
        }
        ?></td>
      <td>
        <?php
        if(isset($pro['errors'])) {
            echo "<ul>";
            foreach($pro['errors'] as $err) {
                echo "<li>".$err."</li>";
            }
            echo "</ul>";
        }
        ?>
    </td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<?php
        
    } else {
        ?>
        <div class="alert alert-danger"><?= __('admin.no_product_available_in_excel_import') ?></div>
        <?php
    }
    
    } else { ?>
    
    <ul>
        <li><?= $created_products; ?> <?= __('admin.product_created_successfully') ?></li>
        <li><?= $updated_products; ?> <?= __('admin.product_updated_successfully') ?></li>
        <li><?= $failed_products; ?> <?= __('admin.product_not_created_or_updated') ?></li>
        <li><?= $skipped_products; ?> <?= __('admin.product_skipped_for_import') ?></li>
        <li><strong><?= __('admin.out_of') ?> <?= $total_products; ?></strong></li>
    </ul>
        
    <?php }
?>
