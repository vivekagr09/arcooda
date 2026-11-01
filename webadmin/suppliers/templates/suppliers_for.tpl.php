<div align="center">
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionFor\');">' . $dis_name . ' Status</a>', '');?>
</div>
<div id="sectionFor">
<table class="wide" align="center" style="width:98%">
	<tr>
		<td class="left_title" style="width: 18%;"><?=$dis_name?> For :</td>
		<td style="text-align: left">
			<input name="supplier_for[39]" type="checkbox" <?=(in_array('39', $sites)) ? 'checked="checked"' : ''?>>
			<img src="/webadmin/images/sites/site_39/logo_16.png">&nbsp;&nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;"><?=$dis_name?> Type :</td>
<?php
$type_ex      = explode('|', $_GET['type']);
$sup_selected = (in_array('supp', $type_ex)) ? true : false;
$man_selected = (in_array('man', $type_ex)) ? true : false;
if ('' != $supplier['type']) {
    $sup_selected = $man_selected = false;
}

?>
		<td>
			<input type="radio" name="supplier[type]" value="supp" <?=($sup_selected || 'supp' == $supplier['type']) ? 'checked' : ''?> />Supplier Only&nbsp;&nbsp;
			<input type="radio" name="supplier[type]" value="man" <?=($man_selected || 'man' == $supplier['type']) ? 'checked' : ''?> />Manufacturer Only&nbsp;&nbsp;
			<input type="radio" name="supplier[type]" value="both" <?=$supplier['type'] == 'both' ? 'checked' : ''?>/>Both Supplier and Manufacturer&nbsp;&nbsp;
	</tr>
	<tr>
		<td class="left_title" style="width: 18%">Active :</td>
		<td>
			<select name="supplier[active]">
<?php
$options = array('y' => 'Yes', 'n' => 'No')?>
			<?= formatHTMLOptions($options, $supplier['active']) ?>
			</select>
		</td>
	</tr>
</table>
</div>
<br />