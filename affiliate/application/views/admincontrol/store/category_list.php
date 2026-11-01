<?php foreach($categories as $index => $category){ ?>
	<tr>
		<td><img src="<?= $category['image_url'] ?>" style='width: 35px'></td>
		<td><?= $category['id'] ?></td>
		<td><?= $category['name'] ?></td>
		<td><?= $category['parent_name'] ? $category['parent_name'] : '<small class="text-muted">'.__('admin.none').'</small>' ?></td>
	    <td>
	    	<button class="btn-sm btn btn-primary" product-category='<?= $category['id'] ?>'><?= $category['total_product'] ?> <?= __('admin.products') ?></button>
	    </td>
	    <td><?= $category['created_at'] ?></td>
		<td>
			<a class="btn btn-sm btn-primary" href="<?= base_url('admincontrol/store_category_add/'. $category['id']) ?>"><?= __('admin.edit') ?></a>
			<a class="btn btn-sm btn-danger" onclick="if(!confirm('<?= __('admin.are_you_sure') ?>')) return false" href="<?= base_url('admincontrol/store_category_delete/'. $category['id']) ?>"><?= __('admin.delete') ?></a>
		</td>
	</tr>
       
<?php } ?>

<?php if(empty($category)){ ?>
	<tr>
    <td colspan="100%" class="text-center mt-5">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <i class="fas fa-exchange-alt fa-5x text-muted"></i>
            <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
        </div>
    </td>
</tr>
<?php } ?>