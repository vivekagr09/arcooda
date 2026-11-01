<?= formatHTMLTitle('Carousel Footer') ?>
<table class="table_fields">
	<tr>
		<th><span style="color:red;"><small>*</small></span>Button Text :</th>
		<td><input class="wide" type="text" name="item[button_text]"  value="<?= $item['button_text']?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<th><span style="color:red;"><small>*</small></span>Button Color :</th>
		<td><input class="wide" type="color" name="item[button_color]"  value="<?= $item['button_color']?>" style="width:98%;" /></td>
	</tr>
	<tr>
		<th>Button Active :</th>
		<td><select class="wide" name="item[button_active]">
                <?= formatHTMLOptions(['y'=>'Yes','n'=>'No'], $item['button_active']) ?>
        </td>
	</tr>
	<tr>
		<th>Scroll Type :</th>
		<td><select class="wide" name="item[scroll_type]">
                <?= formatHTMLOptions(['no navigation'=>'No Navigation','bullet navigation'=>'Bullet Navigation','scroll bar'=>'Scroll Bar','next previous'=>'Next Previous'], $item['scroll_type']) ?>
        </td>
	</tr>
</table>