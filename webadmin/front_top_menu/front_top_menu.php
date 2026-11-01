<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Top Menu</a>','<a onclick="addmenu();return false">Add New </a>', false)?>

<script type="text/javascript">
//<![CDATA[
function screenshots_rebuild() {
	$("#screenshots>tr").removeClass("alt").odd().addClass("alt");
}
function addScreenShot(data){
	console.log(data);
	data.forEach(addImages);
}
function screenshots_moveup(e) {
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.next().attr('ssid');
	var rankcurrent=$('#rank_'+currentrow).val();
	var rankprevious=$('#rank_'+previousrow).val();
	$('#rank_'+currentrow).val(rankprevious);
	$('#rank_'+previousrow).val(rankcurrent);
	$row.next().after($row);
	screenshots_rebuild();
}
function screenshots_movedown(e) {
	
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.prev().attr('ssid');
	var rankcurrent=$('#rank_'+currentrow).val();
	var rankprevious=$('#rank_'+previousrow).val();
	$('#rank_'+currentrow).val(rankprevious);
	$('#rank_'+previousrow).val(rankcurrent);
	$row.prev().before($row);
	screenshots_rebuild();
}
function front_menu_delete(rowid,dataid) {
	if (confirm('Are you sure you want to delete the menu??')) {
		var theRow = getElement("front_menu_rowa[" + rowid + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid){
        setValue("front_top_menu_delete", getValue("front_top_menu_delete") + "," + dataid);
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

html='<tr ssid="'+menu_count+'" id="front_menu_rowa['+menu_count+']">\
	<td>\
		<input type="hidden" name="front_menu['+menu_count+'][rank]" value="'+menu_count+'" id="rank_'+menu_count+'">\
		<img src="down_arrow.gif" onclick="screenshots_moveup(this)" />\
		<img src="up_arrow.gif" onclick="screenshots_movedown(this)" />\
	</td>\
	<td style="text-align:left"><input type="text" name="front_menu['+menu_count+'][title]"  style="width:90%"></td>\
	<td style="text-align:left"><input type="text" name="front_menu['+menu_count+'][url]"  style="width:90%"></td>\
	<td style="text-align:left"><select name="front_menu['+menu_count+'][navigation]"><option value="same">Same Page</option><option value="new">New Page</option></select></td>\
	<td style="text-align:left"><select name="front_menu['+menu_count+'][status]"><option value="y">Active</option><option value="n">De-Active</option></select></td>\
	<td><input type="button" value="Delete" onclick="front_menu_delete('+menu_count+',0)" /></td>\
</tr>'
	
$('#screenshots').append(html);
menu_count++;
	//screenshots_rebuild();
}
function rebuild()
{
	var table=getElement("screenshots");
	var nodes = table.getElementsByTagName("tr");

	if(nodes.length > 1) {
		for(i=0;i<nodes.length;i++) {
			var node = nodes[i];
			var imgs = node.getElementsByTagName("img");

			node.style.backgroundColor = (i%2==1) ? "#EEEEE0" : "#FFFFFF";

			imgs[0].style.visibility = ((i==0) ? 'visible' : ((i==(nodes.length-1)) ? 'hidden' : 'visible'));
			imgs[1].style.visibility = ((i==0) ? 'hidden' : ((i==(nodes.length-1)) ? 'visible' : 'visible'));
		}
	}
	else {
		node = nodes[0];
		var imgs = node.getElementsByTagName("img");
		node.style.backgroundColor = "#FFFFFF";
		imgs[0].style.visibility='hidden';
		imgs[1].style.visibility='hidden';
	}

	updateRankOrder();
	//alert(nodes.length);
}

function updateRankOrder()
{
	var table=getElement("screenshots");
	var nodes = table.getElementsByTagName("tr");
	var ranking = new Array(1);

	var count = 0;
	for(i=0;i<nodes.length;i++) {
		var node = nodes[i];
		if(node.getAttribute("ssid")){
			ranking[count] = node.getAttribute("ssid");
			count++;
		}
	}
	setValue("ss_order",ranking.join(","));
	//alert(getValue("ss_order"));
}

function moveUp(node)
{
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (newNode.getAttribute("position") - 1));

	var prevRow = node_before(node);
	var prevRowNode = prevRow.cloneNode("true");
	prevRowNode.setAttribute("position", (parseInt(prevRowNode.getAttribute("position")) + 1.0));
	var tableNode = node.parentNode;

	tableNode.replaceChild( newNode, prevRow);
	tableNode.replaceChild(prevRowNode, node);

	rebuild();
}

function moveDown(node)
{
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (parseInt(newNode.getAttribute("position")) + 1.0));

	var nextRow = node_after(node);

	var nextRowNode = nextRow.cloneNode("true");
	nextRowNode.setAttribute("position", (nextRowNode.getAttribute("position") - 1));
	var tableNode = node.parentNode;

	tableNode.replaceChild( newNode, nextRow);
	tableNode.replaceChild(nextRowNode, node);
	rebuild();
}
//]]>
</script>

<div id="sectionSS">


<table class="table_grid">
	
	<thead>
		<tr>
			<th style="width:10%">Rank</th>
			<th style="width:15%">Tital</th>
			<th style="width:20%">Url</th>
			<th style="width:5%">Navigation</th>
			<th style="width:5%">Status</th>
			<th style="width:5%">Delete</th></tr>
		</tr>
	</thead>
	<tbody id="screenshots">
		
<?php
$count = 0;
foreach ($top_menu as $shot) {
	$value = (('y' == $shot['active']) ? 'De-' : '') . 'Activate';
	$link  = (('y' == $shot['active']) ? 'de' : '') . 'activate_ss';
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="front_menu_rowa[<?=$count ?>]" ssid="<?=$count ?>">
		<td>
		<input type="hidden" name="front_menu[<?= $count ?>][id]" value="<?=$shot['id'] ?>">
		
		<input type="hidden" name="front_menu[<?= $count ?>][rank]" value="<?=$shot['rank'] ?>" id="rank_<?= $count ?>">
		<img src="down_arrow.gif" onclick="screenshots_moveup(this)" />
		<img src="up_arrow.gif" onclick="screenshots_movedown(this)" />
	</td>
	<td style="text-align:left">
		<input type="text" name="front_menu[<?= $count ?>][title]" value="<?=$shot['title'] ?>" style="width:90%">
		
	</td>
	<td style="text-align:left">
		<input type="text" name="front_menu[<?= $count ?>][url]" value="<?=$shot['url'] ?>" style="width:90%">
		
	</td>
	<td style="text-align:left">
		<select name="front_menu[<?= $count ?>][navigation]">
			<option value="same" <?=($shot['navigation']=='same')?'selected':'' ?>>Same Page</option>
			<option value="new" <?=($shot['navigation']=='new')?'selected':'' ?>>New Page</option>
		</select>
		
	</td>
	<td style="text-align:left">
		<select name="front_menu[<?= $count ?>][status]">
			<option value="y" <?=($shot['status']=='y')?'selected':'' ?>>Active</option>
			<option value="n" <?=($shot['status']=='n')?'selected':'' ?>>De-active</option>
		</select>
		
	</td>
	<td><input type="button" value="Delete" onclick="front_menu_delete(<?= $count ?>,<?=$shot['id']?>)" /></td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>

	<tfoot>
		<input type="hidden" name="front_top_menu_delete" value="" />
	</tfoot>
</table>
</div>

<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
