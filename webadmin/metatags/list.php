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
<center><?= formatHTMLTitle('Page META Tags') ?></center>
<?php
	$meta_list=metaSearch($search_for,'',$like_tag);
	$meta_list=metaSort($meta_list,$order_by);

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
		unset($select_url['meta_id']);
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
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=id&order=<?= ($order_by=='id' && $order=='asc')?'desc':'asc' ?>">ID</a></th>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=page&order=<?= ($order_by=='page' && $order=='asc')?'desc':'asc' ?>">Page Name</a></th>
			<th style="text-align:center"><a href="<?= $sort_url?>&order_by=meta_title&order=<?= ($order_by=='title' && $order=='asc')?'desc':'asc' ?>">Title</a></th>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=keys&order=<?= ($order_by=='keys' && $order=='asc')?'desc':'asc' ?>">Keywords</a></th>
			<th style="text-align:center"><a href="<?= $sort_url ?>&order_by=desc&order=<?= ($order_by=='desc' && $order=='asc')?'desc':'asc' ?>">Description</a></th>
			<th style=""></th>
		</tr>
<?php
		$alt=true;
		foreach($meta_list as $meta_id)
		{
			$meta = metaFromDatabase($meta_id);
			$class='';
			if($alt=!$alt)		$class=' class="alt"';
?>
		<tr <?= $class?>>
			<td style="text-align:center"><?= '#'.$meta['id'] ?></td>
			<td style="text-align:center"><?= htmlspecialchars($meta['page']) ?></td>
			<td style="text-align:center"><?= htmlspecialchars($meta['meta_title']) ?></a></td>
			<td style="text-align:center"><?= htmlspecialchars($meta['meta_keys']) ?></td>
			<!--<td style="text-align:center"><?= $m_types[$meta['meta_type']] ?></td>-->
			<td style="text-align:center"><?= htmlspecialchars($meta['meta_desc']) ?></td>

<?php
			$links=array();
			$links[] = '<a href="'.$edit_url.'&meta_id='.$meta_id.'" target="_blank">Edit</a>';
			$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this Page META Data?\')" href="'.$status_url.'&action=delete&meta_id='.$meta_id.'">Delete</a>';
?>
			<td style="text-align:center"><?= implode('<br />',$links) ?></td>
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