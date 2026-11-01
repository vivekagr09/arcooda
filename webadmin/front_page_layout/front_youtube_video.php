<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Front Page Youtube Movie</a>','<a onclick="addvideo();return false">Add New </a>', false)?>

<script type="text/javascript">
//<![CDATA[
function screenshots_rebuild() {
	$("#screenshots>tr").removeClass("alt").odd().addClass("alt");
}
function addScreenShot(data){
	data.forEach(addImages);
}


function screenshots_changeShotStatus(product_id, shot_id, action) {
	if (shot_id != 0) {
		window.location = '.?action=' + action + '&product_id=' + product_id + '&ss_id=' + shot_id;
	}
}
var menu_count="<?=count($youtube_video); ?>";
function addvideo()
{

html='<tr ssid="'+menu_count+'" id="youtubevideo_rowa['+menu_count+']">\
	<td>\
		<input type="hidden" name="youtubevideo['+menu_count+'][rank]" value="'+menu_count+'" id="rank_'+menu_count+'">\
		<img src="down_arrow.gif" onclick="screenshots_moveup(this)" />\
		<img src="up_arrow.gif" onclick="screenshots_movedown(this)" />\
	</td>\
	<td style="text-align:left"><input type="text" name="youtubevideo['+menu_count+'][title]"  style="width:90%"></td>\
	<td style="text-align:left"><input type="text" name="youtubevideo['+menu_count+'][link]"  style="width:90%"></td>\
	<td style="text-align:left" ><select name="youtubevideo['+menu_count+'][active]" style="width:90%;background-color: rgb(0, 255, 0);"  onchange="this.style.backgroundColor=((this.value==\'n\')?\'#FF0000\':\'#00FF00\');"><option value="y">Active</option><option value="n">De-Active</option></select></td>\
	<td>Now</td>\
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

function deleteYoutube(id) {
	var c = confirm("Are you sure you want to delete this youtube link?");
	if(c){
			window.location.href="/webadmin/front_page_layout/?action=do_delete&id="+id;
	}
}
function screenshots_rebuild() {
	$("#screenshots>tr").removeClass("alt").odd().addClass("alt");
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
	//screenshots_rebuild();
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
	//screenshots_rebuild();
}
//]]>
</script>

<div id="sectionSS">


<table class="table_grid">
	<thead>
		<tr>
			<th style="width:20%">Title of Movie</th>
			<th style="width:35%">Youtube Link</th>
			<th style="width:10%">Status</th>
			<th style="width:15%">Date Added</th>
			<th style="width:5%"></th>
		</tr>
	</thead>
	<tbody  id="screenshots">
		
<?php
$count = 0;
foreach ($youtube_video as $video) {
	$value = (('y' == $video['active']) ? 'De-' : '') . 'Activate';
	$link  = (('y' == $video['active']) ? 'de' : '') . 'activate_ss';
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="youtubevideo_rowa[<?=$count ?>]" ssid="<?=$count ?>">
	<td>
		<input type="hidden" name="youtubevideo[<?= $count ?>][rank]" value="<?=$video['rank'] ?>" id="rank_<?= $count ?>">
		<input type="hidden" name="youtubevideo[<?= $count ?>][id]" value="<?=$video['id'] ?>">
		<img src="down_arrow.gif" onclick="screenshots_moveup(this)" />
		<img src="up_arrow.gif" onclick="screenshots_movedown(this)" />
	</td>	
	<td style="text-align:left">
		
		<input type="text" name="youtubevideo[<?= $count ?>][title]" value="<?=$video['title'] ?>" style="width:90%">
		
	</td>
	<td style="text-align:left">
		<input type="text" name="youtubevideo[<?= $count ?>][link]" value="<?=$video['link'] ?>" style="width:90%">
		
	</td>
	<td style="text-align:left">
		<select name="youtubevideo[<?= $count ?>][active]" style="width: 90%;background-color:<?=($video['active']=='y')?'rgb(0, 255, 0)':'rgb(255, 0, 0)' ?>" onchange="this.style.backgroundColor=((this.value=='n')?'#FF0000':'#00FF00');" >
			<option value="y" <?=($video['active']=='y')?'selected':'' ?> >Active</option>
			<option value="n" <?=($video['active']=='n')?'selected':'' ?>>De-active</option>
		</select>
		
	</td>
	<td><?= date('d/m/Y',$video['created_time']) ?></td>
	<td><a style="cursor:pointer;" onclick="deleteYoutube(<?= $video['id'] ?>);"><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0"></a></td>
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
