<?php
/**
 * @var mixed[][] $production
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Production Menu</a>','<a onclick="addmenu();return false">Add New </a>', false)?>

<script type="text/javascript">
//<![CDATA[


function production_schedules_delete(rowid,dataid) {
	if (confirm('Are you sure you want to delete the production schedule??')) {
		var theRow = getElement("production_schedules_rowa[" + rowid + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid){
        setValue("production_schedules_items_delete", getValue("production_schedules_items_delete") + "," + dataid);
        }
	}
}
function screenshots_changeShotStatus(product_id, shot_id, action) {
	if (shot_id != 0) {
		window.location = '.?action=' + action + '&product_id=' + product_id + '&ss_id=' + shot_id;
	}
}
var menu_count="<?=count($top_menu); ?>";
function addmenu()
{

html='<tr ssid="'+menu_count+'" id="production_schedules_rowa['+menu_count+']">\
	<td style="text-align:left"><input type="text" name="front_menu['+menu_count+'][product_id]"  style="width:90%"></td>\
	<td style="text-align:center"><input type="text" name="front_menu['+menu_count+'][title]"  style="width:70%"><input type="button" onclick="freeGiftSearchProduct('+menu_count+')" value="Browse"></td>\
	<td style="text-align:left"><input type="date" name="front_menu['+menu_count+'][shipping_date]"  style="width:90%"></td>\
	<td style="text-align:left"><input type="text" name="front_menu['+menu_count+'][note]"  style="width:90%"></td>\
	<td style="text-align:left"><input type="checkbox" name="front_menu['+menu_count+'][status]"  style="width:90%"></td>\
	<td><input type="button" value="Delete" onclick="production_schedules_delete('+menu_count+',0)" /></td>\
</tr>'
	
$('#screenshots').append(html);
menu_count++;

}
//]]>

function freeGiftSearchProduct(rowid)
        {
        	var name = getValue("front_menu["+rowid+"][title]");
        	var id = getValue("front_menu["+rowid+"][product_id]");
        	var searchFor = '';
        	if(id){
        		searchFor = id;
        	}else{
        		searchFor = name;
        	}
            window.open('/webadmin/products_new/?action=list&return_message=select_promotion_'+rowid+'&search_for='+searchFor+'');
        }
function linked_products_data(data) {
    if (data == null) return;
    if (data['return_message'].substring(0, 17) == 'select_promotion_') {
        var row = data['return_message'].substring(17);
        setValue('front_menu[' + row + '][product_id]', data['item_list'][0]['id']);
        setValue('front_menu[' + row + '][title]', data['item_list'][0]['product_name']);
    }    
}
</script>

<div id="sectionSS">
<table class="table_grid">
	<thead>
		<tr>
			<th style="width:5%">ID</th>
			<th style="width:20%">Name of Machine</th>
			<th style="width:5%">Shipping Date</th>
			<th style="width:20%">Note</th>
			<th style="width:7%">Leave Active</th>
			<th style="width:4%">Delete</th></tr>
		</tr>
	</thead>
	<tbody id="screenshots">
<?php
$count = 0;
foreach ($top_menu as $shot) {
	$value = (('y' == $shot['active']) ? 'De-' : '') . 'Activate';
	$link  = (('y' == $shot['active']) ? 'de' : '') . 'activate_ss';
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="production_schedules_rowa[<?=$count ?>]" ssid="<?=$count ?>">
	<td style="text-align:left">
		<input type="hidden" name="front_menu[<?= $count ?>][id]" value="<?=$shot['id'] ?>">

		<input type="text" name="front_menu[<?= $count ?>][product_id]" value="<?=$shot['product_id'] ?>" style="width:90%">
	</td>
	<td tyle="text-align:left">
		<input type="text" name="front_menu[<?= $count ?>][title]" value="<?=$shot['title'] ?>" style="width:70%">
		<input type="button" onclick="freeGiftSearchProduct(<?= $count ?>)" value="Browse">
		
	</td>
	<td style="text-align:left">
		<input type="date" name="front_menu[<?= $count ?>][shipping_date]"  value="<?= date('Y-m-d', strtotime($shot['shipping_date'])) ?>" style="width:90%">
		
	</td>
	<td style="text-align:left">
		<input type="text" name="front_menu[<?= $count ?>][note]" value="<?=$shot['note'] ?>" style="width:90%">
		
	</td>
	<td style="text-align:center">
		<input type="checkbox" name="front_menu[<?= $count ?>][status]"  value="1" <?=($shot['status']==1)?'checked':'' ?> >
	</td>
	<td><input type="button" value="Delete" onclick="production_schedules_delete(<?= $count ?>,<?=$shot['id']?>)" /></td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>
	<tfoot>
		<input type="hidden" name="production_schedules_items_delete" value="" />
	</tfoot>
</table>
</div>
<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
