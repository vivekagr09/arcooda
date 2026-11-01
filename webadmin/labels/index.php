<?php
include("header.inc.php");

$label_list=$_POST['label_list'];

$product_list=array();
$quantity_list=array();

$comma_split=explode(',',$label_list);
if((count($comma_split)==1)&&($comma_split[0]=='')) $comma_split=array();
if(count($comma_split)>0)
{
	foreach($comma_split as $comma)
	{
		$multiple_split=explode('x',$comma);
		if(count($multiple_split)>1)
		{
			$qty=$multiple_split[0];
			$range=$multiple_split[1];
		}
		else
		{
			$qty=1;
			$range=$multiple_split[0];
		}
		$range_split=explode('-',$range);
		if(count($range_split)==1)
		{
			$product_list[]=trim($range_split[0]);
			$quantity_list[]=trim($qty);
		}
		else
		{
			for($i=trim($range_split[0]);$i<=trim($range_split[1]);$i++)
			{
				$product_list[]=$i;
				$quantity_list[]=trim($qty);
			}
		}
	}
}
?>
<?= formatHTMLTitle('Product List') ?>
<table>
	<form accept-charset="utf-8" action="index.php" method="post">
	<tr>
		<td style="text-align:center;">
			Please enter the list of product id's:<br />
			(Example: 795,826,1217-1281,5x860,5x861-877)
			<input type="text" class="intext" value="<?= str_replace('"','&quot;',stripslashes($_POST['label_list'])) ?>" name="label_list" /><br />
			<input type="submit" value="Submit" />
		</td>
	</tr>
	</form>
</table>

<?php
if(count($product_list)>0)
{
?>
<br />

<form accept-charset="utf-8" action="" method="post" target="_blank">

<?= formatHTMLTitle('Your Selection') ?>

<table class="wide spacetable">
	<tr>
		<th>ID</th>
		<th>Product Name</th>
		<th>Quantity</th>
	</tr>
<?php
	$post_list='';
	$quantity=reset($quantity_list);
	foreach($product_list as $product_id)
	{

		$product=$DB->query_first("SELECT * FROM products WHERE id=".intval($product_id));
		$post_list.=str_repeat(intval($product_id).'_',$quantity);
?>
	<tr<?= ($alt=!$alt)?' ':' class="alt"' ?>>
		<td style="text-align:right;"><?= $product_id ?></td>
		<td style="width:100%;"><?= str_replace('<','&lt;',stripslashes($product['product_name'])) ?></td>
		<td style="text-align:center;"><?= $quantity ?></td>
	</tr>
<?php
		$quantity=next($quantity_list);
	}
?>
	<input type="hidden" name="product_list" value="<?= substr($post_list,0,-1) ?>" />
</table>

<br />

<?= formatHTMLTitle('Print Options') ?>

<table>
	<tr>
<?php
	if(strlen(urlencode(substr($post_list,0,-1)))>1024)
	{
?>
		<td style="text-align:center;">
			<input type="image" src="part_labels.png" name="label_type" value="box_labels" onclick="javascript:this.form.action='part_labels.tpl.php';" /><br />
			Part Labels
		</td>
		<td style="text-align:center;">
			<input type="image" src="labels12.png" name="label_type" value="box_labels" onclick="javascript:this.form.action='labels12.rtf.php';" style="border:1px solid yellow;" /><br />
			12 Labels<br />(ID/Picture/Label/Qty)
		</td>
		<td style="text-align:center;">
			<input type="image" src="labels40.png" name="label_type" value="box_labels" onclick="javascript:this.form.action='labels40.rtf.php';" /><br />
			40 Labels<br />(ID/Label/Qty)
		</td>
		<td style="text-align:center;">
			<input type="image" src="labels72.png" name="label_type" value="box_labels" onclick="javascript:this.form.action='labels72.tpl.php';" /><br />
			72 Labels<br />(ID/Qty)
		</td>
		<td style="text-align:center;">
			<input type="image" src="labels112.png" name="label_type" value="box_labels" onclick="javascript:this.form.action='labels112.tpl.php';" /><br />
			112 Labels<br />(ID Only)
		</td>
<!--		<td style="text-align:center;">
			<img type="image" src="bar_codes.png" name="label_type" value="bar_codes" onclick="javascript:this.form.action='bar_codes.tpl.php';" /><br />
			Bar Codes
		</td> -->
<?php
	}
	else
	{
?>
		<td style="text-align:center;">
			<a href="part_labels.tpl.php?product_list=<?= urlencode(substr($post_list,0,-1)) ?>" target="_blank"><img src="part_labels.png" border="0" /></a><br />
			Part Labels
		</td>
		<td style="text-align:center;">
			<a href="labels12.rtf.php?product_list=<?= urlencode(substr($post_list,0,-1)) ?>" target="_blank" style="border:1px solid yellow;"><img src="labels12.png" border="0" /></a><br />
			12 Labels<br />(ID/Picture/Label/Qty)
		</td>
		<td style="text-align:center;">
			<a href="labels40.rtf.php?product_list=<?= urlencode(substr($post_list,0,-1)) ?>" target="_blank"><img src="labels40.png" border="0" /></a><br />
			40 Labels<br />(ID/Label/Qty)
		</td>
		<td style="text-align:center;">
			<a href="labels72.rtf.php?product_list=<?= urlencode(substr($post_list,0,-1)) ?>" target="_blank"><img src="labels72.png" border="0" /></a><br />
			72 Labels<br />(ID/Qty)
		</td>
		<td style="text-align:center;">
			<a href="labels112.rtf.php?product_list=<?= urlencode(substr($post_list,0,-1)) ?>" target="_blank"><img src="labels112.png" border="0" /></a><br />
			112 Labels<br />(ID Only)
		</td>
<!--		<td style="text-align:center;">
			<a href="bar_codes.tpl.php?product_list=<?= urlencode(substr($post_list,0,-1)) ?>" target="_blank"><img src="bar_codes.png" border="0" /></a><br />
			Bar Codes
		</td> -->
<?php
	}
?>
	</tr>
</table>
</form>
<?php
}

include("footer.inc.php");
?>