<!--
Created and modified by Mingze X@ Feb 2, 2015;

Last Update: March 10, 2015

Update Log: 1. "Delete" button enabled
			2. Changed interaction method when deleting a screenshot

Finished: 70%;

Unfinished Job: 1. Arrows
				2. "Update" function in edit screen session.

This file is used for coinexpress webadmin to manage products screenshots, based on products_screenshots_new.tpl@AGS

 -->
<?php
/*function productScreenshots($id, $active = false)
{
	global $DB, $_GLOBALS;
	$id = 0;
	$id = intval($id);
	if (!$id) return false; //If this product doesn't exist, then return false
	$product = productFromDatabase($id);//If this product exist, then retrieve product info from database.
	//find product img locations
	$statement = "SELECT product_screenshots.filename FROM product_screenshots, products WHERE product_screenshots.id = '$id'";

	$product_filenames = $DB->query_first($statement); //img_location

	$DB->free_result($statement);
	$result= array();
	$result=$product_filenames;
	return $result;
}*/
// You need to get the right $ss value, which is used in the following code. The $ss is defined in /home/agsales/public_html/webadmin/products/products_edit.php

//The productScreenshots function can be fond in /home/agsales/public_html/webadmin/products/query_products.inc.php 30081 line

$statement = "SELECT * FROM product_screenshots WHERE product_screenshots.id = '$id' ORDER BY product_screenshots.rank ASC ";
$product_filenames = $DB->query($statement);
$ss=$DB->fetch_array($product_filenames);
$countss=count($ss);
?>
<script>
function rebuild()
{
	var table = getElement("screenshots");
	var nodes = table.getElementsByTagName("tr");
	if (nodes.length > 1)
	{
		for (i = 0; i < nodes.length; i++)
		{
			var node = nodes[i];
			var imgs = node.getElementsByTagName("img");
			node.style.backgroundColor = (i % 2 == 1) ? "#EEEEE0" : "#FFFFFF";
			imgs[0].style.visibility = ((i == 0) ? "visible" : ((i == (nodes.length - 1)) ? "hidden" : "visible"));
			imgs[1].style.visibility = ((i == 0) ? "hidden" : ((i == (nodes.length - 1)) ? "visible" : "visible"));
		}
	}
	else
	{
		node = nodes[0];
		var imgs = node.getElementsByTagName("img");
		node.style.backgroundColor = "#FFFFFF";
		imgs[0].style.visibility = "hidden";
		imgs[1].style.visibility = "hidden";
	}
	updateRankOrder();
}
function moveUp(node)
{
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (newNode.getAttribute("position") - 1));
	var prevRow = node_before(node);
	var prevRowNode = prevRow.cloneNode("true");
	prevRowNode.setAttribute("position", (parseInt(prevRowNode.getAttribute("position")) + 1.0));
	var tableNode = node.parentNode;
	var reattachee = tableNode.replaceChild(newNode, prevRow);
	var reattachee = tableNode.replaceChild(prevRowNode, node);
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
	var reattachee = tableNode.replaceChild(newNode, nextRow);
	var reattachee = tableNode.replaceChild(nextRowNode, node);
	rebuild();
}
function deleteScreenshot(id,ss_id)
{
	if (confirm("Are you sure you want to delete the screenshot??"))
		window.open("screenshot_admin_new.php?id=" + id + "&ss_id=" + ss_id + "&action=delete", "", "width=700,height=250");
}
function screenshotsCopied()
{
	setValue('products[screenshot_copied]','y');
	var elem=getElement('copy_screenshot');
	var elem2=getElement('copy_screenshot2');
	if(elem!=null && elem2!=null) { elem.style.display='none'; elem2.style.display='none'; }
}

function openSSWindow(url,w,h)
{
	var features= 'scrollbars=yes,width='+w+', height='+h;
	features += ', top='+((screen.height - h) / 2)+', left='+((screen.width  - w)  / 2);
	window.open(url,'',features);
}


</script>
<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSS\');">Product Screenshots</a>','',false);?>
<input type="hidden" name="products[screenshot_copied]" value="<?php echo $product['screenshot_copied']?>" />
<br />

<div id="sectionSS">
<input type="hidden" name="ss_order" value="">

<table class="wide" style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;" align="center" style="text-align: center" id="ref_shots">
		<tr>
			<th style="background-color:#FFCFAF;font-weight:bold;font-size:12" colspan="8">Reference Screenshots</th>
		</tr>
		<tr style="background-color:#E3EBF7;font-weight:bold;text-align:center;">
			<td style="width:10%">Rank</td>
			<td style="width:5%">Image</td>
			<td style="width:auto">Description</td>
			<td style="width:5%">Attach Date</td>
			<td style="width:5%">Attached By</td>
			<td style="width:5%">File Type</td>
			<td style="width:10%">File Size</td>
			<td style="width:5%">Edit/Delete</td></tr>
		</tr>

	<tbody id="screenshots">
<?php
$count=0;
$shot=$ss;
if(is_array($shot))
{
	while($shot['filename'] != "")
	{
?>
		<tr <?php echo "bgcolor=\"".(($count % 2 == 1) ? "#FFFFFF" : "#EEEEE0")."\"";?> id="screenshot[<?php echo $shot['ss_id']?>]" ssid="<?php echo $shot['ss_id']?>">
			<td>
<?php
if($count != (count($shot) - 1)) { ?>
				<img src="/webadmin/down_arrow.gif" onclick="moveDown(this.parentNode.parentNode)">
<?php
}
				else {
?>
				<img src="/webadmin/down_arrow.gif" onclick="moveDown(this.parentNode.parentNode)" style="visibility:hidden">
<?php
} ?>

<?php
if($count != 0) { ?>
				<img src="/webadmin/up_arrow.gif" onclick="moveUp(this.parentNode.parentNode)">
<?php
}
				else {
?>
				<img src="/webadmin/up_arrow.gif" onclick="moveUp(this.parentNode.parentNode)" style="visibility:hidden">
<?php
} ?>
			</td>
			<td>
				<a href="<?php $_SERVER["DOCUMENT_ROOT"]?>/webadmin/gogetmemyimage.php?i=<?php echo $shot['filename']?>" target="_blank">
				<img src="<?php $_SERVER["DOCUMENT_ROOT"]?>/webadmin/gogetmemyimage.php?i=<?php echo $shot['filename']?>" width="96" height="96">
				</a>
			</td>
			<td style="text-align: left">
				<a style="color:blue" href="<?php $_SERVER["DOCUMENT_ROOT"]?>/webadmin/gogetmemyimage.php?i=<?php echo $shot['filename']?>" target="_blank"><?php echo htmlspecialchars($shot['caption'])?></a>
			</td>
			<td><?php echo date("d/m/Y", strtotime($shot['date_added']))?></td>
			<td><?php echo $shot['staff']?></td>
			<td><?php $info = explode(".", $shot['filename']); echo strtoupper($info[count($info)-1])?></td>
			<td><?php echo formatFileSize(filesize($_SERVER['DOCUMENT_ROOT'] . '/'.$shot['filename']));?></td>
			<td>
				<input type="button" value="Edit" onclick="window.open('screenshot_admin_new.php?table=product_screenshots&amp;action=edit&amp;id=<?php echo $id?>&amp;ss_id=<?php echo $shot['ss_id'] ?>','','width=850,height=400')">
				<input type="button" value="Delete" onclick="deleteScreenshot(<?php echo $shot['id']?>,<?php echo $shot['ss_id']?>)">
			</td>
		</tr>
<?php
		$count++;
		$shot=$DB->fetch_array($product_filenames);
	}
}
?>
	</tbody>

	<tfoot>
		<tr id="foot" <?php echo $count%2==1?'class="alt"':''?>>
			<td colspan=8 style="text-align:center">
				<input type="button" value="Add Screenshot" onclick="window.open('screenshot_admin_new.php?table=product_screenshots&amp;action=add&amp;id=<?php echo $id ?>','','width=850,height=400')" />
			</td>
		</tr>
	</tfoot>
</table>
<br />

<!-- /products_screenshots.tpl -->
