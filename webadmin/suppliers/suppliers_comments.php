<?php
return;

include_once('suppliers_common.php');
//include_once('templates/suppliers_header.tpl.php');

$posted = array_merge($_GET, $_POST);

$supplier_id=intval($posted['supplier_id']);
$comments=supplierCommentsFromDatabase($supplier_id);
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="supplier_content">
<table style="width:95%" border="0" cellspacing="0" cellpadding="4" align="center">
	<tr>
		<th colspan="4" align="center" class="box"><strong>Previous Admin Comments for Supplier ID <?=$supplier_id?></strong></td>
	</tr>
	<tr style="background-color:#E3EBF7">
		<td width="5%" align="center" nowrap class="bottomLeft"><b>Date</b></td>
		<td width="10%" align="center" nowrap class="bottomLeft"><b>User</b></td>
		<td width="85%" align="center" nowrap class="bottomLeftRight"><b>Comments</b></td>
	</tr>
<?php
	$count = 0;
	foreach($comments as $comment){?>
	<tr <?=($count%2==1)?'class="alt"':''?>>
		<td nowrap class="bottomLeft" style="text-align:center"><?=date("d M, Y",strtotime($comment['date']))?></td>
		<td nowrap class="bottomLeft" style="text-align: center"><?=$comment['staff']?></td>
		<td class="bottomLeftRight" style="text-align:left"><?=htmlspecialchars($comment['comments'])?></td>
	</tr>
<?php
		$count++;
	}
?>
</table>
</div>