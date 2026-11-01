<?php 
if(count($reviews)==0)
{
?>
<tr>
    <td colspan="100%" class="text-center mt-5">
        <div class="d-flex justify-content-center align-items-center flex-column mt-5">
            <i class="fas fa-exchange-alt fa-5x text-muted"></i>
            <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
        </div>
    </td>
</tr>
<?php

}
else
{

foreach($reviews as $review){ ?>
<tr>
	<?php if($vendormanagereviewimage == 1){?>
	<td>
      <div class="tooltip-copy">
        <img width="50px" height="50px" src="<?php echo $review['avatar']!="" ? base_url('assets/images/users/'. $review['avatar']) : base_url('assets/images/no-user_image.jpg') ?>" ><br>
      </div>
   </td>
   <?php }?>
   <td><?= $review['firstname'] ?></td>
   <td><?= $review['lastname'] ?></td>
   <td><?= $review['product_name'] ?></td>
   <td><?= $review['rating_comments'] ?></td>
   <td><?= $review['rating_number'] ?></td>
   <td><?= dateGlobalFormat($review['rating_created']) ?></td>  
   <td>
      <?php if($review['rating_created_by']==$user_id){ ?>
      <a href="<?= base_url('usercontrol/manage_review/'.$review['rating_id'])  ?>" class="btn btn-primary edit-button"><?= __("user.edit") ?></a>
      <?php }else {?>
      <a href="#" class="btn btn-primary edit-button disabled"  ><?= __("user.edit") ?></a>
      <?php }?>
      <a href="<?= base_url('usercontrol/deleteReview/'.$review['rating_id']);?>" class="btn btn-danger delete-button" onclick="return onDeleteReview(<?= $review['rating_id'] ?>);" ><?= __("user.delete") ?></a>
   </td>
</tr>
<?php } 
}?>
<script type="text/javascript">
	function onDeleteReview($rating_id)
	{
		if(!confirm("<?= __('user.are_you_sure') ?>")) 
		return false;
		else
		return true;	 
	}
</script>