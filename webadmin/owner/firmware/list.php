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
        th{
        	font-size: 12px;
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
<center><?= formatHTMLTitle('Firmware List' ,'<a href="/webadmin/owner/firmware/add.php" target="_blank">Add New</a>') ?></center>
<?php
	$meta_list=firmwareSearch($search_for,'',$like_tag);
	$meta_list=firmwareSort($meta_list,$order_by);
	if($order=='desc') $meta_list=array_reverse($meta_list);

	$items_per_page=50;
	$count_total=count($meta_list);
	if($count_total>0)
	{
	$meta_list=array_chunk($meta_list,$items_per_page);
	$count_pages=count($meta_list);
	$page=max(0,min($count_pages-1,$page));
	$meta_list=$meta_list[$page];
	$count_display=count($meta_list);
	
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

	<br />

	<table class="table_grid" align="center" cellspacing=0 cellpadding=2 border=0>
		<tr>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=created_at&order=<?= ($order_by=='created_at' && $order=='asc')?'desc':'asc' ?>">Created Date</a></th>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=name&order=<?= ($order_by=='name' && $order=='asc')?'desc':'asc' ?>">Firmware Name</a></th>
			<th style="text-align:center">Version</th>
			<th style="text-align:center">Firmware Date </th>
			<th style="text-align:center">File Type</th>
			<th style="text-align:center">Linked To</th>
			<th style="text-align:center">Online Notes</th>
			<th style="text-align:center">Admin Notes</th>
			<th style="text-align:center">Work Files</th>
			<th style="text-align:center">Status</th>
			<th style="width: 10%;"></th>
		</tr>
<?php
		$alt=true;
		foreach($meta_list as $meta_id)
		{
			$meta = firmwareFromDatabase($meta_id);
			$class='';
			if($alt=!$alt)		$class=' class="alt"';
			$product_list=getProductfirmware($meta_id);
			$product_tooltip='<div><h3>Products</h3>';
			$product_tooltip .='<ul style="list-style-type:none;">';
			foreach ($product_list as $key => $product) {
				$pro=productFromDatabase($product);
				$product_tooltip .='<li><a href="/webadmin/products_new/?action=edit&search_for='.$pro["id"].'&id='.$pro["id"].'" target="_blank">'.$pro["product_name"].'</a></li>';
			}
			$product_tooltip .='</ul></div>';
?>
		<tr <?= $class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor=''">
			<td style="text-align:center"><?= date('d/m/Y' ,strtotime($meta['created_at'])) ?></td>
			<td style="text-align:center"><a class="unfinished-"><?= htmlspecialchars($meta['name']) ?></a></td>
			<td style="text-align:center"><a href="/webadmin/assets/owner/firmware/<?php echo (array_key_exists('file_name', $meta)) ? htmlspecialchars($meta['file_name']) : '#' ?>"target="_blank" download ="<?= $meta['file_orig']?>"> <?= $meta['version'] ?></a></td>
			<td style="text-align:center"><?= $meta['firmware_date'] ? date('d/m/Y' ,strtotime($meta['firmware_date'])) : '' ?></td>
			<td style="text-align:center"><?= htmlspecialchars(pathinfo($meta['file_name'], PATHINFO_EXTENSION)) ?></td>
			<td style="text-align:center">
				<div class="tooltipdata">
				   <?= count($product_list) ?>
				    <div class="tooltiptext">
				       <?= $product_tooltip ?>
				    </div>
				</div>
			</td>
			<td style="text-align:center"> <span onmouseover="<?php hecho("tt_in(" . jecho($meta['comment'], 1) . ")") ?>" onmouseout="tt_out()"><i class="fas fa-regular fa-globe" style="color:green;font-size:18px;"></i></span></td>
			<td style="text-align:center">
				
				<?php if($meta['notes'] != NULL) { ?>
	                <span onmouseover="<?php hecho("tt_in(" . jecho($meta['notes'], 1) . ")") ?>" onmouseout="tt_out()"><i class="fas fa-regular fa-globe" style="color:red;font-size:18px;"></i></span>
	                
	            <?php } ?>
			</td>
			<td style="text-align:center">
				<?php 
					$admin_files = firmwareAdminFilesFromDatabase($meta['id']);
					if(count($admin_files)){ 
						foreach ($admin_files as $key => $filedata) { 
							foreach ($filedata as $key => $value) {
							$ext = strtolower(pathinfo($value['file_name'], PATHINFO_EXTENSION));
							$tooltip_image = '';
							switch ($ext) {
							    case 'pdf':
							        $icon = 'fas fa-file-pdf';
							        $color = 'red';
							        break;

							    case 'doc':
							    case 'docx':
							        $icon = 'fas fa-file-word';
							        $color = '#2B579A';
							        break;

							    case 'xls':
							    case 'xlsx':
							        $icon = 'fas fa-file-excel';
							        $color = 'green';
							        break;

							    case 'jpg':
							    case 'jpeg':
							    case 'png':
							    case 'gif':
							        $icon = 'fas fa-camera';
							        $color = 'black';
							        ob_start();
							         echo '<img src="addAdminFiles.php?action=view&main_id='.$value["id"].'" alt="" style="width:190px;height:190px;position:absolute;left:8px;" />';
							        $tooltip_image = ob_get_clean();
							        break;

							    default:
							        $icon = 'fas fa-file';
							        $color = 'gray';
							}
						?>

						<a href="addAdminFiles.php?action=view&main_id=<?= $value['id'].'' ?>" target="_blank"><i class="<?= $icon ?>" style="color:<?= $color ?>;font-size:18px;" onmouseover="<?php hecho('tt_in(' . jecho($tooltip_image, 1) . ');') ?>" onmouseout="tt_out();"></i></a>
				<?php }
					}
				 }
				?>

			</td>
			<td style="text-align:center;background:<?= ($meta['status']=='y')?'#BBFFBB':'#EEBBEE' ?>"><?= ($meta['status']=='y')?'Active':'Deactive' ?></td>

<?php

			$links = [];
			
			$links[] = '<a href="'.$edit_url.'&id='.$meta_id.'" target="_blank">Edit</a>';
			$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this firmware?\')" href="'.$status_url.'&action=delete&id='.$meta_id.'">Delete</a>';
?>
			<td style="text-align:center"><?= implode('  ',$links) ?></td>
		</tr>
<?php
		}
?>
	</table>
	<br/>
		<center><?= $page_links ?></center><br />
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