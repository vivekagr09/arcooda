<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'payelayout\');return false">Front Page layout</a>', false)?>

<div id="payelayout">


<table class="table_grid">
	<thead>
		<tr>
			<th style="width:20%">Front Page Layout</th>
			<th style="width:10%">Status</th>
		</tr>
	</thead>
	<tbody >
		
<?php
$count = 0;
foreach ($page_layout as $layout) {
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> >
		
	<td style="text-align:left">
		<input type="hidden" name="page_layout[<?= $count ?>][id]" value="<?=$layout['id'] ?>">
		<?=$layout['name'] ?>
		
	</td>
	<td style="text-align:left">
		<select name="page_layout[<?= $count ?>][active]" style="width: 90%;background-color:<?=($layout['active']=='y')?'rgb(0, 255, 0)':'rgb(255, 0, 0)' ?>" onchange="this.style.backgroundColor=((this.value=='n')?'#FF0000':'#00FF00');" >
			<option value="y" <?=($layout['active']=='y')?'selected':'' ?> >Active</option>
			<option value="n" <?=($layout['active']=='n')?'selected':'' ?>>De-active</option>
		</select>
		
	</td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>
</table>
</div>

<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
