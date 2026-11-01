<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";
?>
<center><?= formatHTMLTitle('Custom Terms & Conditions List') ?></center>
<?php

unset($_GET['id']);
    $sql="SELECT id from `terms_condition_custom_templates` ORDER BY id ASC";
    $item_list = array();
    $query   = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $item_list[] = $row['id'];
    }
	$count_total=count($item_list);
	if($count_total>0)
	{
	$edit_url='.?action=edit';
?>
	<center><?= $page_links ?></center><br />
	<br />
	<div class="invoice_content">
		<table style="width:95%" class="wide spacetable" align="center" cellspacing=0 cellpadding=2 border=0>
			<tr>
				<th style="text-align:center">Date</th>
				<th style="text-align:center">Template Title</th>
				<th style="text-align:center">Products</th>
				<th style="text-align:center">Active/Deactive</th>
				<th style="text-align:center">Actions</th>
			</tr>
			<?php foreach ($item_list as $key => $item_id) { 
				$data=customTemplateFromDatabase($item_id);
				$total_product=customTemplateTotalProduct($item_id);
				$class=($key%2==0)?' class="alt"':'';
			?>
			<tr <?= $class?> <?= ($data['status']=='active')?'style="background-color:#BBFFBB"':''  ?>>
				<td style="text-align:center"><?= date('d/m/Y' ,strtotime($data['created_at'])) ?></td>
				<td style="text-align:center"><?= $data['template_name'] ?></td>
				<td style="text-align:center"><a href="/webadmin/products_new/list.php?custom_template=<?= $item_id ?>" target="_blank"><?= $total_product ?></a></td>
				<td style="text-align:center"><?= ($data['status']=='active')?'Active':'InActive' ?></td>
				<?php
					$links=array();
					$links[] = '<a href="'.$edit_url.'&id='.$item_id.'" target="_blank">Edit</a>';
					$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this template?\')" href=".?action=delete&id='.$item_id.'">Delete</a>';
					$links[] = ('active' == $data['status']) ? '<a href=".?action=do_toggle&id='.$item_id.'">De-Activate</a>' : '<a href=".?action=do_toggle&id='.$item_id.'">Activate</a>';
					?>
				<td style="text-align:center" width="20%"><?= implode('  ',$links) ?></td>
	    	</tr>
	    <?php } ?>
		</table>
	</div>
<?php
	}
	else
	{
?>
	No results were found
<?php
	}

include_once __DIR__ . "/footer.inc.php";
?>