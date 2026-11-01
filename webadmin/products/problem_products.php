<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/header.inc.php');

$sql=	" SELECT".
		"	p.id,".
		"	p.price_flux,".
		"	pt1.parent,".
		"	pt1.name AS sub_name,".
		"	pt2.name AS root_name,".
		"	p.product_name,".
		"	p.cost_price,".
		"	p.cost_currency,".
		"	IF(p.price_flux='y', round(p.cost_price*(1+p.markup/100.0),2), p.sales_price) AS sales_price,".
		"	IF(p.price_flux='y', p.cost_currency, p.sales_currency) AS sales_currency,".
		"	IF(p.price_flux='y', 1+p.markup/100.0, round(((p.sales_price*c2.usfrom)/(p.cost_price*c1.usfrom)-1)*100.0,4)) as markup,".
		"	ROUND(IF(p.price_flux='y', p.cost_price*c1.usfrom*(p.markup/100.0), p.sales_price*c2.usfrom-p.cost_price*c1.usfrom),2) AS profit".
		" FROM products p".
		" INNER JOIN `highwaygroup`.`currency` AS `c1` ON `c1`.`code` = `p`.`cost_currency`".
		" INNER JOIN `highwaygroup`.`currency` AS `c2` ON `c2`.`code` = `p`.`sales_currency`".
		" INNER JOIN product_types pt1 ON pt1.id=p.type".
		" INNER JOIN product_types pt2 ON pt2.id=pt1.parent".
		" WHERE p.active='y'".
		" AND IF(p.price_flux='y', p.markup>-100.0 AND p.markup<0.0, (p.cost_price*c1.usfrom>p.sales_price*c2.usfrom) AND p.sales_price>0.0)".
		" ORDER BY profit ASC";
$query=$DB->query($sql);

$loss_products=array();
while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
	$loss_products[]=$row;

if(count($loss_products)>0)
{
?>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td bgcolor="#EEEEEE" width="20%">
			<div align="center"><b>Products Admin</b></div>
		</td>
		<td bgcolor="#EEEEEE" width="80%">
			<div align="right">
			</div>
		</td>
	</tr>
</table>
<br />

<h1 align="center">Problem Products</h1>
<p align="center">The following products are being offered for less money than they are worth:</p>
<br />

<table width="95%" align="center">
	<tr bgcolor="#000066">
		<td align="center" style="color:white;"><b>ID</b></td>
		<td align="center" style="color:white;"><b>Product Name</b></td>
		<td align="center" style="color:white;"><b>Cost Price</b></td>
		<td align="center" style="color:white;"><b>Sale Price</b></td>
		<td align="center" style="color:white;" bgcolor="#336699"><b>Profit (USD)</b></td>
		<td></td>
	</tr>
<?php
	foreach($loss_products as $product)
	{
		$edit_url='/webadmin/products/admin.php?table=products&parent='.$product['parent'].'&action=edit&id='.$product['id'];
		$view_url='/products/'.urlencode(str_replace(' ','_',str_replace('/','_',$product['root_name']))).'/'.urlencode(str_replace(' ','_',str_replace('/','_',$product['sub_name']))).'/'.urlencode(str_replace(' ','_',str_replace('/','_',$product['product_name']))).'_'.$product['id'].'.html';
?>
	<tr<?php echo ($alt=!$alt)?' bgcolor="#EEEEEE"':'' ?>>
		<td align="center" style="padding:8px 8px 8px 8px;"><?php echo $product['id'] ?></td>
		<td align="left" style="padding:8px 8px 8px 8px;"><a href="<?php echo $view_url ?>" target="_blank"><?php echo str_replace('<','&lt;',$product['product_name']) ?></a></td>
		<td align="right" style="padding:8px 8px 8px 8px;"><?php echo $product['cost_price'] ?> <?php echo $product['cost_currency'] ?></td>
		<td align="right" style="padding:8px 8px 8px 8px;"><?php echo ($product['price_flux']=='y')?'*':'' ?> <?php echo $product['sales_price'] ?> <?php echo $product['sales_currency'] ?></td>
		<td align="right" style="padding:8px 8px 8px 8px;"><?php echo $product['profit'] ?></td>
		<td align="center" nowrap="nowrap" style="padding:8px 8px 8px 8px;"><a href="<?php echo $edit_url ?>">Edit</a> | <a href="<?php echo $view_url ?>" target="_blank">View</a></td>
	</tr>
<?php
	}
?>
</table>

<p align="center">* Due to negative markup, some prices may not match those in the public area of the site</td>
<?php
}

include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/footer.inc.php');
?>