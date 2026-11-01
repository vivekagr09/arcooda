<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";

$get_vars=$posted;
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value))
			unset($get_vars[$key]);

$like_tag=false;
$search_for=(isset($posted['search_for']))?$posted['search_for']:'';
$page=intval($posted['page']);
$order_by=(isset($posted['order_by']))?$posted['order_by']:'id';
$order=$posted['order'];
if($order!='asc') $order='desc';
$by = $posted['by'];


$search_url=$get_vars;
$search_url['action']='list';
unset($search_url['search_for']);
$search_url='.?'.http_build_query($search_url);
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<center><?= formatHTMLTitle('Category List') ?></center>
	<center><?= $page_links ?></center><br />
	<br />

	<table style="width:95%" class="wide" align="center" cellspacing=0 cellpadding=2 border=0>
		<tr>
			<th style="text-align:center"><a href="">ID</a></th>
			<th style="text-align:center"><a href="">Name </a></th>
			<th style="text-align:center">Status</th>
			<th style=""></th>
		</tr>
	</table>
<?php

include_once __DIR__ . "/footer.inc.php";
?>