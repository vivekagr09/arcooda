<style>
	/* Tooltip container */
	.tooltipdata {
			position: relative;
		display: inline-block;
		cursor: pointer;
	}

	/* Tooltip text */
	.tooltipdata .tooltiptext {
		visibility: hidden;
		width: 300px;
		text-align: left;
		border-radius: 5px;
		padding: 5px;
		position: absolute;
		z-index: 1;
		opacity: 0;
		transition: opacity 0.3s;
		background-color: #FFFFE1;
		color: #000000;
		border-color: #000000;
		border-width: 1px;
		border-style: solid;
	}

	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltipdata:hover .tooltiptext {
		visibility: visible;
		opacity: 1;
	}
</style>
<?php
include_once __DIR__ . "/common.inc.php";

moduleLoad('product'); //productFromDatabase

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
<center><?= formatHTMLTitle('Manuals List') ?></center>
<?php
	$meta_list=manualSearch($search_for,'',$like_tag);
	$meta_list=manualSort($meta_list,$order_by);
	if($order=='desc') $meta_list=array_reverse($meta_list);

	$items_per_page=50;
	$count_total=count($meta_list);
	$meta_list=array_chunk($meta_list,$items_per_page);
	$count_pages=count($meta_list);
	$page=max(0,min($count_pages-1,$page));
	$meta_list=$meta_list[$page];
	$count_display=count($meta_list);
	if($count_total>0)
	{
		$sort_url=$get_vars;
		unset($sort_url['order_by']);
		unset($sort_url['order']);
		$sort_url='?'.http_build_query($sort_url);

		$select_url=$get_vars;
		$select_url['action']= ($posted['return_message']=='test') ? 'select' : 'select_access';
		unset($select_url['id']);
		$select_url='.?'.http_build_query($select_url);

		$edit_url=$get_vars;
		$edit_url['action']='edit';
		unset($edit_url['meta_id']);
		$edit_url='.?'.http_build_query($edit_url);

		$view_url=$get_vars;
		$view_url['action']='view';
		unset($view_url['meta_id']);
		$view_url='.?'.http_build_query($view_url);

		$status_url=$get_vars;
		unset($status_url['meta_id']);
		$status_url='.?'.http_build_query($status_url);

		$page_url=$get_vars;
		unset($page_url['page']);
		$page_url='?'.http_build_query($page_url);
		$page_links=formatHTMLPageLinks($page_url.'&page=',$page,$count_display,$count_total,$items_per_page,'style="color:blue"');
		$sites = array(1=>'Highway Games', 2=>' Highway Entertainment', 9=>'Converters.tv', 16=>'Car Converters');
		$s_sites = array(1=>'HG', 2=>' HE', 9=>'CNV', 16=>'CC');
		$m_types = array(0=>'', 1=>'COE meta', 2=>'HG meta', 3=>'Factory meta', 4=>'Wiring Diagram/Schematics', 5=>'Service meta', 6=>'Dip Switch Settings', 7=>'Other');
		$active = array('y'=>'Yes', 'n'=>'No');
?>
	<center><?= $page_links ?></center><br />
	<br />

	<table style="width:95%" class="wide" align="center" cellspacing=0 cellpadding=2 border=0>
		<tr>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=created_at&order=<?= ($order_by=='created_at' && $order=='asc')?'desc':'asc' ?>">Creatd At</a></th>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=title&order=<?= ($order_by=='title' && $order=='asc')?'desc':'asc' ?>">Name of manual</a></th>
			<th style="text-align:center">File Type</th>
			<th style="text-align:center">Linked To</th>
			<th style="text-align:center">Comment</th>
			<th style="text-align:center">Status</th>
			<th style=""></th>
		</tr>
<?php
		$alt=true;
		foreach($meta_list as $meta_id)
		{
			$meta = manualFromDatabase($meta_id);
			$class='';
			if($alt=!$alt)		$class=' class="alt"';
			$product_list=getProductmanual($meta_id);
			// $product_tooltip='<h3>Products</h3>';
			// $product_tooltip .='<ul style="list-style-type:none;">';
			// foreach ($product_list as $key => $product) {
			// 	$pro=productFromDatabase($product);
			// 	$product_tooltip .='<li>'.$pro["product_name"].'</li>';
			// }
			// $product_tooltip .='</ul>';
			$product_tooltip='<div><h3>Products</h3>';
			$product_tooltip .='<ul style="list-style-type:none;">';
			foreach ($product_list as $key => $product) {
				$pro=productFromDatabase($product);
				$product_tooltip .='<li><a href="/webadmin/products_new/?action=edit&search_for='.$pro['id'].'&id='.$pro["id"].'" target="_blank">'.$pro["product_name"].'</a></li>';
			}
			$product_tooltip .='</ul></div>';
?>
		<tr <?= $class?><?php echo ($meta['status'] == 'y') ? 'style="background-color:rgb(144, 238, 160)"' : ''; ?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='<?php echo ($meta['status'] == 'y') ? 'rgb(144, 238, 160)' : ''; ?>';" >
			<td style="text-align:center"><?= date('d/m/Y' ,strtotime($meta['created_at'])) ?></td>
			<td style="text-align:center"><?= htmlspecialchars($meta['name']) ?></td>
			<td style="text-align:center"><?= htmlspecialchars(pathinfo($meta['file_name'], PATHINFO_EXTENSION)) ?></td>
			<!-- <td style="text-align:center"><span onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();" ><?= count($product_list) ?></span></td> -->
			<td style="text-align:center"><div class="tooltipdata">
				   <?= count($product_list) ?>
				    <div class="tooltiptext">
				       <?= $product_tooltip ?>
				    </div>
				</div>

			</td>
			<td style="text-align:center"><?= htmlspecialchars($meta['comment']) ?></td>
			<td style="text-align:center"><?= ($meta['status']=='y')?'Active':'In Active' ?></td>

<?php
			$fileUrl = '/webadmin/assets/owner/manual/' . (array_key_exists('file_name', $meta) ? htmlspecialchars($meta['file_name']) : '#');
			$downloadAttribute = '';
			
			if (array_key_exists('file_name', $meta)) {
				// Extract the file extension
				$fileExtension = pathinfo($meta['file_name'], PATHINFO_EXTENSION);
			
				// Define allowed inline file types
				$allowedInlineTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'mp4', 'webm']; // Add more inline types as needed
			
				// Check if the file type should be downloaded or opened inline
				if (!in_array(strtolower($fileExtension), $allowedInlineTypes)) {
					// Map specific file types to download as their respective types
					switch (strtolower($fileExtension)) {
						case 'doc':
						case 'docx':
							$downloadAttribute = 'download="' . htmlspecialchars($meta['name'] . '.' . $fileExtension) . '"';
							break;
						// Add more cases if needed
						default:
							$downloadAttribute = 'download="' . htmlspecialchars($meta['name']) . '"';
							break;
					}
				}
			}
			
			$links = [];
			$links[] = '<a href="https://www.arcooda.com/manual/view-' .$meta_id . '.html?type=manual" target="_blank">View</a>';			
			$links[] = '<a href="'.$edit_url.'&id='.$meta_id.'" target="_blank">Edit</a>';
			$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this manual?\')" href="'.$status_url.'&action=delete&id='.$meta_id.'">Delete</a>';
?>
			<td style="text-align:center" width="20%"><?= implode('  ',$links) ?></td>
		</tr>
<?php
		}
?>
	</table>
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