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
			<td><textarea name="item[comment]" onkeypress="textareaAutoSize(this, 3, 3, 20);" style="width:98%;" rows="5" value="<?= $item['comment']?>"><?= $item['comment']?></textarea></td>
		</tr>
		<tr>
			<th><span style="color:red;"><small>*</small></span>Slider Height :</th>
			<td><input class="wide" type="text" name="item[page_height]"  value="<?= $item['page_height']?>" style="width:98%;" /></td>
		</tr>
	</table>
	<br />
	<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
