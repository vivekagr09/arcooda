<?php foreach($categories as $index => $category) { ?>
	<tr>
		<td><?= $category['id'] ?></td>
		<td><?= $category['name'] ?></td>
		<td><?= !empty($category['parent_name']) ? $category['parent_name'] : '<i class="text-muted">'.__('admin.no_parent_available').'</i>' ?></td>
		<td><?= $category['created_at'] ?></td>
		<td>
			<a class="btn btn-sm btn-primary" href="<?= base_url('integration/integration_category_add/'. $category['id']) ?>"><?= __('admin.edit') ?></a>
			<a class="btn btn-sm btn-danger" onclick="if(!confirm('<?= __('admin.are_you_sure') ?>')) return false" href="<?= base_url('integration/integration_category_delete/'. $category['id']) ?>"><?= __('admin.delete') ?></a>
		</td>
	</tr>
<?php } ?>

<?php if(empty($categories)) { ?>
<tr>
    <td colspan="100%" class="text-center">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <i class="fas fa-exchange-alt fa-5x text-muted"></i>
            <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
        </div>
    </td>
</tr>
<?php } ?>