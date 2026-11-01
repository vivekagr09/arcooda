<!-- _product_specification.tpl -->
<?php
$count_part_new = 1;
function product_monitor_part_row($count = '[ROW_COUNT]',$monitor_count='[MONITOR_COUNT]', $row_data = array())
{
    $name_key = 'item_monitor[' . $monitor_count . '][item_monitor_part][' . $count . ']';
    $hidden   = array(
        $name_key . '[id]'         => $row_data['id'],
        $name_key . '[product_id]' => $row_data['product_id'],
        $name_key . '[linking_id]'  => $row_data['linking_id'],
    );

    $class = array();
    if ($row_data['overflow']) {
        $class[] = 'monitor_part_overflow';
    }

    $class = implode(' ', $class);

    ob_start();
?>
<tr id="product_monitor_part_rowa_<?=$monitor_count?>_<?=$count?>"<?=(('' != $class) ? (' class="' . $class . '"') : '') . (($row_data['overflow']) ? ' style="display:none;"' : '')?>>
	<td>#<?=$row_data['linking_id']?></td>
    <td><?=str_replace("\n", "", formatHTMLInputs($hidden))?><a href=".?action=edit&id=<?=$row_data['linking_id']?>" target="_blank" name="<?=$name_key?>[product_name]"><?php hecho($row_data['product_name'])?></a></td>
    <td><input type="button" value="Delete" onclick="deletemonitor_part(<?=$count?>,<?=$monitor_count?>,<?=($row_data['id'])?$row_data['id']:0 ?>)" /></td>
</tr>
<?php
return ob_get_clean();
}

?>
<?php
function product_monitor_row($count = '[MONITOR_ROW_COUNT]', $row_data = array())
{
	static $alt = false;

    $name_key = 'item_monitor[' . $count . ']';
    $hidden   = array(
        $name_key . '[id]'         => $row_data['id'],
        $name_key . '[product_id]' => $row_data['product_id'],
    );

    $class = array();
    if (is_int($count) && ($count & 1)) {
        $class[] = 'alt';
    }

    if ($row_data['overflow']) {
        $class[] = 'monitor_part_overflow';
    }

    $class = implode(' ', $class);

    ob_start();
?>
	<tr <?=($alt = !$alt) ? 'class="alt"' : ''?> id="product_monitor_rowa[<?=$count?>]" ssid="<?=$count?>">
	<td style="text-align: center;">
		<input type="hidden" name="<?=$name_key?>[rank]" value="<?=$count ?>" id="rankmon_<?= $count ?>">
		<p>
		<img src="down_arrow.gif" onclick="monitor_moveup(this)" />
		<img src="up_arrow.gif" onclick="monitor_movedown(this)" />
	</p>
	<p style="padding-top:20px;">
		<input type="button" value="Delete" onclick="monitor_delete(<?= $count ?>,<?=$row_data['id']?>)" />
	</p>
	</td>
	<td>
    <table class="table_fields">
    	<tr><td colspan="2"><h3 style="margin-left: 20px; padding: 10px;">MONITOR #<?= $count ?></h3></td></tr>
    	<tr>
    		<?=str_replace("\n", "", formatHTMLInputs($hidden))?>
	    	<th>Monitor Size  (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=monitor_size" target="_blank">Add</a></i></small>):</th>
	        <td><select name="<?=$name_key?>[monitor_size]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('monitor_size'), $row_data['monitor_size'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Monitor Specifications  (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=monitor_specifications" target="_blank">Add</a></i></small>):</th>
	        <td><select name="<?=$name_key?>[monitor_specifications]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('monitor_specifications'), $row_data['monitor_specifications'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Touchscreen (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=monitor_touchscreen" target="_blank">Add</a></i></small>):</th>
	        <td><select name="<?=$name_key?>[monitor_touchscreen]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('monitor_touchscreen'), $row_data['monitor_touchscreen'])?>
	        </select></td>
	    </tr>
	    
	    <tr>
	    	<th>Other Monitor Options Available  (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=monitor_other_options" target="_blank">Add</a></i></small>):</th>
	        <td><select name="<?=$name_key?>[monitor_other_options]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('monitor_other_options'), $row_data['monitor_other_options'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Monitor Part Number  (<small><i><a style="cursor: pointer;" onclick="addmonitor_partProduct(<?= $count ?>);" >Link</a></i></small>):</th>
	       	<td>
	       		 <table class="table_grid" id="products_monitor_part_<?= $count ?>" style="width:100%;">
				        <tr>
				        	<th style="text-align: left;width:20%">ID</th>
				            <th style="text-align: left;width:60%">Name</th>
				            
				            <th style="text-align: left;width:20%"></th>
				        </tr>
				<?php
				    $item_monitor_part= productMonitorPartLinkingFromDatabase($row_data['id']);
				    $count_part_new=0;
				    foreach ($item_monitor_part as $monitor_part) {
				        $prod                     = productFromDatabase($monitor_part['linking_id']);
				        $data                     = $monitor_part;
				        $data['product_name']     = $prod['product_name'];
				        $data['view_url']         = $prod['view_url'];
				        $data['thumb_url']        = $prod['thumb_url'];
				        $data['overflow']         = false;
				        echo product_monitor_part_row($count_part_new,$count,$data);
				        $count_part_new++;
				    }
				?>
				    </table>
				    <br /><br />
	       	</td>
	    </tr>
    </table>
	</td>
	</tr>
<?php
return ob_get_clean();
}
$monitor_co=count($item_monitor)-1;
?>

<script type="text/javascript">
//<![CDATA[
var monitor_count="<?= count($item_monitor)+1;?>";
function addnewmonitor(){
	var row_string = <?=json_encode(product_monitor_row('[MONITOR_LINKING_COUNT]', ['product_id'=>$item['id']]))?>;
    row_string = row_string.replace(/\[MONITOR_LINKING_COUNT\]/g, monitor_count);
    $("#monitor_data").append(row_string);
    monitor_count++;
}
var partid=0;
var monitor_part_count = 200;
function dispalymonitor_partproductddata(dataarray){
    dataarray.item_list.forEach(productsmonitor_partData);
} 
function productsmonitor_partData(data) {
	console.log(partid);
    var rcount = "" + monitor_part_count;
    var monitor_part_table = getElement("products_monitor_part_"+partid+"");
    if (monitor_part_table != null) {
        var newRow1 = addTableRow(monitor_part_table, 'product_monitor_part_rowa_'+partid+'_' + rcount+'');
        //if (monitor_part_count & 1) newRow1.className = 'alt';
        if (data['product_id'] == null) data['product_id'] = data['id'];
        var linking_id = data['product_id'];
        var product_name = data['product_name'];
        var view_url = data['view_url'];
        var img_url = data['thumb_url'];
<?php
$count = 0;
foreach (explode("\n", product_monitor_part_row('[UPGRADE_LINKING_COUNT]','[MONITOR_LINKING_COUNT]', array(
    'product_id'       => $item['id'],
    'linking_id'        => '[LINKING_ID]',
    'product_name'     => '[PRODUCT_NAME]',
    'view_url'         => '[VIEW_URL]',
    'thumb_url'        => '[THUMB_URL]',
    'manufacturer_str' => '[MANUFACTURER_STR]',
))) as $row) {
    $row = trim($row);
    if (substr($row, 0, 3) == '<tr') {
        $count++;
    } elseif (substr($row, 0, 4) == '<td>') {
        echo "var newCell = addTableCell(newRow" . $count . ", " . strtr(
            jecho(substr($row, 4, -5), 1),
            array(
                "[UPGRADE_LINKING_COUNT]"     => '" + rcount + "',
                "[MONITOR_LINKING_COUNT]"     => '" + partid + "',
                '[LINKING_ID]'        => '" + linking_id + "',
                '[PRODUCT_NAME]'     => '" + product_name + "',
                '[VIEW_URL]'         => '" + view_url + "',
                '[THUMB_URL]'        => '" + img_url + "',
            )
        ) . ");\n";
    }
}
?>
    }
    monitor_part_count++;
}
function deletemonitor_part(id,monitorid,dataid) {
    if (confirm("Are you sure you want to unlink this product ??")) {
        var theRow = getElement("product_monitor_part_rowa_"+monitorid+"_" + id + "");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid>0)
        setValue("item_monitor_partof_deleted", getValue("item_monitor_partof_deleted") + "," + dataid);
    }
}
function monitor_delete(id,dataid) {
    if (confirm("Are you sure you want to delete this  ??")) {
        var theRow = getElement("product_monitor_rowa["+id+"]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid>0)
        setValue("item_monitor_deleted", getValue("item_monitor_deleted") + "," + dataid);
    }
}
function monitor_rebuild() {
	$("#monitor_data>tr").removeClass("alt").odd().addClass("alt");
}
function monitor_moveup(e) {
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.next().attr('ssid');
	var rankcurrent=$('#rankmon_'+currentrow).val();
	var rankprevious=$('#rankmon_'+previousrow).val();

	$('#rankmon_'+currentrow).val(rankprevious);
	$('#rankmon_'+previousrow).val(rankcurrent);
	$row.next().after($row);
	monitor_rebuild();
}
function monitor_movedown(e) {
	
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.prev().attr('ssid');
	var rankcurrent=$('#rankmon_'+currentrow).val();
	var rankprevious=$('#rankmon_'+previousrow).val();
	$('#rankmon_'+currentrow).val(rankprevious);
	$('#rankmon_'+previousrow).val(rankcurrent);
	$row.prev().before($row);
	monitor_rebuild();
}
function addmonitor_partProduct(count) {
	partid=count;
    var url = "master_products.php?return_message=monitor_part";
    url += "&multiple=y";
    url += "&search_for=";
    var monitor_part_list = sectionmonitor_partGetRows();
    var usedin_list = [<?=$item['id']?>];
    for (var i = 0; i < monitor_part_list.length; i++) usedin_list.push(getValue('item_monitor_part[' + monitor_part_list[i] + '][monitor_part_id]'));
    url += "&hide_list=" + usedin_list.join("|");
    // open product search page...
    window.open(url);
}
function sectionmonitor_partGetRows() {
    var result = [];
    $("[id^='product_monitor_part_rowa']").each(function() {
        result.push(this.id.substr(20, this.id.length - 21));
    });
    return result;
}
//]]>
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;product_specification&quot;);return false">Product Specifications</a>','<a onclick="addnewmonitor()" style="cursor:pointer;">Add Monitor</a>')?>

<div id="product_specification">
	<table class="table_fields"  id="monitor_data">
		<?php 

		$monitor_count=1;
		 foreach ($item_monitor as $monitor) {
		        echo product_monitor_row($monitor_count, $monitor);
		        $monitor_count++;
		    }
		?>
	</table>
	<input type="hidden" name="item_monitor_partof_deleted" value="" />
	<input type="hidden" name="item_monitor_deleted" value="" />

    <table class="table_fields">
    	<tr><td colspan="2"><h3 style="margin-left: 20px; padding: 10px;">CABINET</h3></td></tr>
    	<tr>
	    	<th>Cabinet Material  (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=cabinet_material" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[cabinet_material]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('cabinet_material'), $item['cabinet_material'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Control Panel Material  (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=cabinet_panel_material" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[cabinet_panel_material]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('cabinet_panel_material'), $item['cabinet_panel_material'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>LED Cabinet Lighting (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=cabinet_lighting" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[cabinet_lighting]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('cabinet_lighting'), $item['cabinet_lighting'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Joystick Brand  (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=cabinet_joystick_brand" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[cabinet_joystick_brand]">
	        	<option value="0"></option>
	            <?=formatHTMLOptions(productGetOptionList('cabinet_joystick_brand'), $item['cabinet_joystick_brand'])?>
	        </select></td>
	    </tr>
	    <tr>
	    	<th>Button Brand (<small><i><a href="/webadmin/products_new/options.php?action=list&option_name=cabinet_button_brand" target="_blank">Add</a></i></small>):</th>
	        <td><select name="item[cabinet_button_brand]">
	        	<option value="0"> </option>
	            <?=formatHTMLOptions(productGetOptionList('cabinet_button_brand'), $item['cabinet_button_brand'])?>
	        </select></td>
	    </tr>
    </table>
    <table class="table_fields">
    	<tr><td colspan="2"><h3 style=" padding: 10px;"></h3></td></tr>
    	<tr>
	    	<th>Color Options:</th>
	        <td>
	        	<ul style="list-style:none;">
	        		<?php 
	        			$machine_colours=explode(',', $item['machine_colours']);
	        			foreach (productGetOptionList('colour') as $key => $color) {
	        				$color_detail=getProductColourById($key);
	        			
	        		?>
	                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
	                	<div class="cat action" >
						   
						      <input type="checkbox" value="<?= $color_detail['id'] ?>" name="item[machine_colours][]" <?= (in_array($color_detail['id'], $machine_colours))?'checked':''?> style="width: 24px; height: 24px;">

						      <p style="padding:12px;background: <?= $color_detail['color_val']?>" onmouseover="<?php hecho("tt_in(" . jecho('<div>'.$color_detail['colour'].'<div>', 1))?>)" onmouseout="tt_out()">
						   </p>

						</div>
	         <!--            <input type="checkbox" name="item[machine_colours][]" value="<?= $color_detail['id'] ?>" <?= (in_array($color_detail['id'], $machine_colours))?'checked':''?>>  -->
	                </li>
	            <?php } ?>
	            </ul>
	        </td>
	    </tr>
    	<tr>
    		<th>Color Notes</th>
    		<td><input class="wide" name="item[color_comment]"  value="<?php hecho($item['color_comment'])?>" /></td></td>
    	</tr>
    </table>
</div>
<br/>
<div style="color:#fff;text-align:center;padding:8px;display:none;" id="product_specification_hideNote">
   Product Specifivations can be viewed by Clicking "Product Specifications" above<br />
</div>
<br/>
<!-- /_product_specification.tpl -->
