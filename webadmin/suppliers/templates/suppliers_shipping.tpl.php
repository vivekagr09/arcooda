<!-- Section : Shipping Locations -->
<?php
return;

$yesno = array('y'=>'Yes','n'=>'No');
$default = array('0'=>'Not Selected');
?>
<div align="center">
	<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionLocation\');">Shipping Locations</a>');?>
</div>
<div id="sectionSupplier">
<table class="wide" align="center" style="width:98%">
	<tr>
		<td class="left_title" style="width:18%;">Seaport :</td>
		<td>
			<select name="supplier[seaport]" style="width:75%;"></select>
			<small><i>(<a href="/webadmin/world/seaport/seaports.php" target="_blank" style="color:blue">Add / Edit</a>)</i></small>
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:18%;">Airport :</td>
		<td>
			<select name="supplier[airport]" style="width:75%;"></select>
			<small><i>(<a href="/webadmin/world/airport/airports.php" target="_blank" style="color:blue">Add / Edit</a>)</i></small>
		</td>
	</tr>
</table>
</div>
<?php
if($supplier['country']!='') { ?>
<script>
setSelectOptions('supplier[airport]', countryAirports('<?=$supplier['country']?>'));
setSelectOptions('supplier[seaport]', countrySeaports('<?=$supplier['country']?>'));
<?php
if($supplier['seaport']!='') { ?> setValue('supplier[seaport]', '<?=$supplier['seaport']?>'); <?php
} ?>
<?php
if($supplier['airport']!='') { ?> setValue('supplier[airport]', '<?=$supplier['airport']?>'); <?php
} ?>
</script>
<?php
} ?>
<br />
