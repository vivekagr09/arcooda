<?php 
    if($action == 'confirm') {
    
    if(!empty($reviews)) {
?>

<textarea name="reviews_for_import" style="display:none;"><?= base64_encode(json_encode($reviews)); ?></textarea>
<table class="table">
  <thead>
    <tr>
      <th scope="col"><?= __('user.rating_id') ?></th>
      <th scope="col"><?= __('user.review_description') ?></th>
      <th scope="col"><?= __('user.action') ?></th>
      <th scope="col"><?= __('user.error_details') ?></th>
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
            echo "<span class='badge bg-danger'>".__('admin.skip')."</span>";
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
        <div class="alert alert-danger"><?= __('user.no_review_available_in_xml_import') ?></div>
        <?php
    }
    
    } else { ?>
    
    <ul>
        <li><?= $created_products; ?> <?= __('user.review_inserted_successfully') ?></li>
        <li><?= $updated_products; ?> <?= __('user.review_updated_successfully') ?></li>
        <li><?= $failed_products; ?> <?= __('user.review_not_created_or_updated') ?></li>
        <li><?= $skipped_products; ?> <?= __('user.review_skipped_for_import') ?></li>
        <li><strong><?= __('user.out_of') ?> <?= $total_products; ?></strong></li>
    </ul>
        
    <?php }
?>
