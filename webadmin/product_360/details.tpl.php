<?php
/**
 * @var int     $id
 * @var mixed[] $item
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->


<?= formatHTMLTitle('Product Details') ?>

<div>
	<table class="table_fields">
		<tr>
			<th><span style="color:red;"><small>*</small></span>Name :</th>
			<td><input class="wide" type="text" name="item[name]"  value="<?= $item['name']?>" style="width:98%;" /></td>
		</tr>
		
		<tr>
			<th>Comment :</th>
			<td><textarea name="item[comment]" onkeypress="textareaAutoSize(this, 3, 3, 20);" style="width:98%;" rows="5" value="<?= $item['name']?>"><?= $item['comment']?></textarea></td>
		</tr>
		<tr>
			<th>Side Image :</th>
			<td>
				<select class="form-control col-sm-9" id="side_images" name="item[side_images]" style="width:90%;" >
					<option value=0></option>
					<?= formatHTMLOptions(view360SideImageList(), $item['side_images']) ?>
				</select>
			</td>
		</tr>
	</table>
	<br />
	<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
