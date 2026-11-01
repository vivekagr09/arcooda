<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Freight Tracking</a>','<a onclick="addfreight();return false">Add New </a>', false)?>

<script type="text/javascript">
//<![CDATA[
function screenshots_rebuild() {
	$("#screenshots>tr").removeClass("alt").odd().addClass("alt");
}
function addScreenShot(data){
	data.forEach(addImages);
}


function freight_tracking_open(rowid,dataid) {
	let url = getValue('freight_tracking['+rowid+'][url]');
	if(url != ''){
		window.open(url,'_blank');
	}
}

var freight_count="<?=count($freight_list)+1; ?>";
function addfreight()
{

html='<tr ssid="'+freight_count+'" id="freight_tracking_rowa['+freight_count+']">\
	<td style="text-align:left"><input type="text" name="freight_tracking['+freight_count+'][freight]"  style="width:90%"></td>\
	<td style="text-align:left"><input type="text" name="freight_tracking['+freight_count+'][url]"  style="width:90%"></td>\
	<td><input type="button" value="view" onclick="freight_tracking_open('+freight_count+',0)" /> <input type="button" value="Delete" onclick="freight_tracking_delete('+freight_count+',0)" /></td>\
</tr>'
	
$('#screenshots').append(html);
freight_count++;
	//screenshots_rebuild();
}
function freight_tracking_delete(rowid,dataid) {
	if (confirm('Are you sure you want to delete freight service?')) {
		var theRow = getElement("freight_tracking_rowa[" + rowid + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid){
        setValue("freight_tracking_value_delete", getValue("freight_tracking_value_delete") + "," + dataid);
        }
	}
}



//]]>
</script>

<div id="sectionSS">


<table class="table_grid">
	<thead>
		<tr>
			<th style="width:15%">Freight</th>
			<th style="width:40%">Url</th>
			<th style="width:10%"></th></tr>
		</tr>
	</thead>
	<tbody id="screenshots">
		
<?php
$count = 0;
foreach ($freight_list as $shot) {
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="freight_tracking_rowa[<?=$count ?>]" ssid="<?=$count ?>">
	<td style="text-align:left">
		<input type="hidden" name="freight_tracking[<?= $count ?>][id]" value="<?=$shot['id'] ?>" style="width:90%">
		<input type="text" name="freight_tracking[<?= $count ?>][freight]" value="<?=$shot['freight'] ?>" style="width:90%">
		
	</td>
	<td style="text-align:left">
		<input type="text" name="freight_tracking[<?= $count ?>][url]" value="<?=$shot['url'] ?>" style="width:90%">
		
	</td>
	<td><input type="button" value="view" onclick="freight_tracking_open(<?= $count ?>,<?=$shot['id']?>)" /> <input type="button" value="Delete" onclick="freight_tracking_delete(<?= $count ?>,<?=$shot['id']?>)" /></td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>

	<tfoot>
		<input type="hidden" name="freight_tracking_value_delete" value="" />
	</tfoot>
</table>
<div style="justify-content: center;margin-top: 20px;display: flex;"> 
	<?php
	$result = $DB->query_first(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE  deleted_at IS NULL AND freight_type = 'fix'",
                '`freight_tracking`',
            ));
	?>
	<input type="hidden" name="freight_tracking[<?= $count ?>][id]" value="<?=$result['id'] ?>" style="width:90%"> 
	<input type="hidden" name="freight_tracking[<?= $count ?>][freight]" value="Icon Link">
	<input type="hidden" name="freight_tracking[<?= $count ?>][freight_type]" value="fix" style="width:50%"><span style="padding: 5px;">Icon Link </span>  <input type="text" name="freight_tracking[<?= $count ?>][url]" value="<?=$result['url'] ?>" style="width:45%"><img src="plane.png">
</div>
</div>

<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
