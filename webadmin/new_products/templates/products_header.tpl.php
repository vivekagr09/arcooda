<?php
$popup_window=(isset($_GET['return_message']))?true:false;

if(!$popup_window)
	include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/header.inc.php');

$get_vars=$_GET;

$list_url=$get_vars;
$list_url['action']='list';
$list_url='index.php?'.http_build_query($list_url);

?>
<link rel="stylesheet" type="text/css" href="style.css" />
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center">Products</div>
		</td>
		<td class="pageMenu" style="text-align:right;" width="80%">
			<a href="index.php?action=add">Add New Product</a> |
			<a href="index.php?action=list">List All Products</a>
		</td>
	</tr>
</table>

<div class="products_content" style="width:95%;" align="center">

<br />
