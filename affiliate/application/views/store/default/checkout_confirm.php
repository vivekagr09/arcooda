<?php if($order_comment_setting['status']){ ?>
	<?php foreach ($order_comment_setting['title'] as $key => $value) { ?>
		<fieldset>
			<legend><?= $value ?></legend>
			<div class="form-group required">
				<input type="hidden" name="comment[<?= $key ?>][title]" value="<?= $value ?>">
				<textarea name="comment[<?= $key ?>][comment]" id="comment_textarea<?= $key ?>" class="form-control" rows="5" placeholder="<?= __('store.comment') ?>"></textarea>
			</div>
		</fieldset>
	<?php } ?>
<?php } ?>