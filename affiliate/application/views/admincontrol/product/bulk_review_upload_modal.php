<?php 
    if($action == 'confirm') {
    
    if(!empty($reviews)) {
?>

<textarea name="reviews_for_import" style="display:none;"><?= base64_encode(json_encode($reviews)); ?></textarea>
<table class="table">
  <thead>
    <tr>
      <th scope="col"><?= __('admin.rating_id') ?></th>
      <th scope="col"><?= __('admin.review_description') ?></th>
      <th scope="col"><?= __('admin.action') ?></th>
      <th scope="col"><?= __('admin.error_details') ?></th>
    </tr>
  </thead>
  <tbody>
      <?php foreach($reviews as $pro) { ?>
    <tr>
      <td><?= (!empty($pro['row']['products_id'])) ? $pro['row']['products_id'] : '#'; ?></td>
      <td><?= (!empty($pro['row']['rating_comments'])) ? $pro['row']['rating_comments'] : '<span class="text-muted">'.__('admin.not_available').'</span>'; ?></td>
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
        <div class="alert alert-danger"><?= __('adminadmin.no_review_available_in_xml_import') ?></div>
        <?php
    }
    
    } else { ?>
    
    <ul>
        <li><?= $created_products; ?> <?= __('admin.review_inserted_successfully') ?></li>
        <li><?= $updated_products; ?> <?= __('admin.review_updated_successfully') ?></li>
        <li><?= $failed_products; ?> <?= __('admin.review_not_created_or_updated') ?></li>
        <li><?= $skipped_products; ?> <?= __('admin.review_skipped_for_import') ?></li>
        <li><strong><?= __('admin.out_of') ?> <?= $total_products; ?></strong></li>
    </ul>
        
    <?php }
?>
