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
<center><?= formatHTMLTitle('File Assets List') ?></center>
<?php
	$asset_list=fileAssetsSearch($search_for,'',$like_tag);
	$asset_list=fileAssetsSort($asset_list,$order_by);
	if($order=='desc') $asset_list=array_reverse($asset_list);

	$items_per_page=50;
	$count_total=count($asset_list);
	if($count_total>0)
	{
	$asset_list=array_chunk($asset_list,$items_per_page);
	$count_pages=count($asset_list);
	$page=max(0,min($count_pages-1,$page));
	$asset_list=$asset_list[$page];
	$count_display=count($asset_list);
	
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
		unset($edit_url['asset_id']);
		$edit_url='.?'.http_build_query($edit_url);

		$view_url=$get_vars;
		$view_url['action']='view';
		unset($view_url['asset_id']);
		$view_url='.?'.http_build_query($view_url);

		$status_url=$get_vars;
		unset($status_url['asset_id']);
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
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=created_at&order=<?= ($order_by=='created_at' && $order=='asc')?'desc':'asc' ?>">Date</a></th>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=title&order=<?= ($order_by=='title' && $order=='asc')?'desc':'asc' ?>">File</a></th>
			<th style="text-align:center">File Name</th>
			<th style="text-align:center">Comment</th>
			<th style="text-align:center">Status</th>
			<th style="text-align:center">Staff</th>
			<th style=""></th>
		</tr>
<?php
		$alt=true;
		foreach($asset_list as $asset_id)
		{
			$data = fileAssetsFromDatabase($asset_id);
			$link_url='/fileassets/'.(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $data['name']), '-')).'/'.$data['slug'];
			$class='';
			if($alt=!$alt)		$class=' class="alt"';
?>
		<tr <?= $class?>>
			<td style="text-align:center"><?= date('d/m/Y' ,strtotime($data['created_at'])) ?></td>
			<td style="text-align:center"><a>
					<?php 
						switch ($data['filetype']) {
							case 'pdf':
							case 'pptx':
								echo '<a style="cursor:pointer;" title="view" href="'.$link_url.'" target="_blank"><img src="/svg/pdf.png" /></a>';
								break;
							case 'png':
							case 'jpg':
							case 'jpeg':
								echo '<a style="cursor:pointer;" title="view" href="'.$link_url.'" target="_blank"><img src="/svg/jpg.png" /></a>';
								break;
							case 'xlsx':
								echo '<a style="cursor:pointer;" title="view" href="'.$link_url.'" target="_blank"><img src="/svg/xls.png" /></a>';
								break;
							case 'docx':
							case 'doc':
								echo '<a style="cursor:pointer;" title="view" href="'.$link_url.'" target="_blank"><img src="/svg/doc.png" /></a>';
								break;
							default:
								echo '<a style="cursor:pointer;" title="view" href="'.$link_url.'" target="_blank"><img src="/svg/doc.png" /></a>';
								break;
								break;
						}


					?>

			</a></td>
			<td style="text-align:center"><?= $data['name'] ; ?></td>
			<td style="text-align:center"><?= htmlspecialchars($data['comment']) ?></td>
			<td style="text-align:center"><?= ucfirst($data['status'])?></td>
			<td style="text-align:center"><?= ucfirst(getStaffName($data['created_by']))?></td>

<?php
			$links=array();
			$links[] = '<a href="'.$link_url.'" target="_blank">Link</a>';
			$links[] = '<a href="'.$edit_url.'&id='.$asset_id.'" target="_blank">Edit</a>';
			$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this firmware?\')" href="'.$status_url.'&action=delete&id='.$asset_id.'">Delete</a>';
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