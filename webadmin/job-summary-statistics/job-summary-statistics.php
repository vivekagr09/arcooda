<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Job Summary Statistics</a>', false)?>

<script type="text/javascript">
//<![CDATA[


function front_menu_delete(rowid,dataid) {
	if (confirm('Are you sure you want to delete the Job Summary Statistics??')) {
		var theRow = getElement("front_menu_rowa[" + rowid + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid){
        setValue("job_handle_type_delete", getValue("job_handle_type_delete") + "," + dataid);
        }
	}
}

var menu_count="<?=count($top_menu); ?>";
function addmenu()
{
		var staffOptions = `<?php foreach ($all_staff_list as $id => $staff): ?>
			<option value="<?= $id ?>"><?= htmlspecialchars($staff) ?></option>
		<?php endforeach; ?>`

	var html = '<tr ssid="'+menu_count+'" id="front_menu_rowa['+menu_count+']">\
		<td style="text-align:left"><input type="text" name="front_menu['+menu_count+'][type]" style="width:90%"></td>\
		<td style="text-align:left"><input type="text" name="front_menu['+menu_count+'][target]" style="width:90%"></td>\
		<td style="text-align:left"><select name="front_menu['+menu_count+'][manager]">' + staffOptions + '</select></td>\
		<td><input type="button" value="Delete" onclick="front_menu_delete('+menu_count+', 0)" /></td>\
	</tr>';
		
	$('#screenshots').append(html);

}


//]]>
</script>
<style type="text/css">
	#statistics-table td{
		border: 1px solid black;
	}
	#statistics-table th{
		border: 1px solid black;
	}
</style>
<div id="sectionSS">


<table class="table_grid" id="statistics-table">
	<thead>
		<tr>
			<th style="width:40%">Section</th>
			<th style="width:10%">Weekly Target</th>
			<th style="width:5%">Manager</th>
		</tr>
	</thead>
	<tbody id="screenshots">
		
<?php
$count = 0;
$addbreak = 3;
foreach ($top_menu as $key => $shot) { 
	
	?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="front_menu_rowa[<?=$count ?>]" ssid="<?=$count ?>">

	<td style="text-align:center;">
		<input type="hidden" name="front_menu[<?= $count ?>][id]" value="<?=$shot['id'] ?>">
		<?=$shot['type'] ?>
		
	</td>
	<td style="text-align:center;">
		<select  name="front_menu[<?= $count ?>][target]" style="width:90%;text-align: center;">
			<?php 
				for($i=0;$i<=20;$i++){
					echo '<option '.($shot["target"] ==$i ? "selected":"").'>'.$i.'</option>';
				}
			?>
		</select>
		
	</td>
	<td style="text-align:left">
		<select name="front_menu[<?= $count ?>][manager]" >
			<?php foreach ($all_staff_list as $id => $staff): ?>
				<option value="<?= $id ?>" <?= ($id == $shot['manager']) ? "selected" : "" ?>>
					<?= htmlspecialchars($staff) ?>
				</option>
			<?php endforeach; ?>
		</select>
		
	</td>
	</tr>

<?php
	if($addbreak == $key){
		echo '<tr><td colspan="3" style="padding: 10px;"></td></tr>';
		continue;
	}
	$count++;
}
?>
	</tbody>

	<tfoot>
		<input type="hidden" name="job_handle_type_delete" value="" />
	</tfoot>
</table>
</div>

<br />

