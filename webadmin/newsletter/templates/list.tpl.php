<!-- list.tpl -->

<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$sort_url=$get_vars;
$sort_url['action']='list';
$sort_url['order']=($order=='desc')?'asc':'desc';
unset($sort_url['order_by']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$statistics_url=$get_vars;
$statistics_url['action']='statistics';
unset($statistics_url['id']);
$statistics_url='index.php?'.http_build_query($statistics_url).'&id=';

$emails_url=$get_vars;
$emails_url['action']='preview_list';
unset($emails_url['id']);
unset($emails_url['email_status']);
$emails_url='index.php?'.http_build_query($emails_url).'&id=';

$toggle_url=$get_vars;
$toggle_url['action']='toggle';
unset($toggle_url['id']);
$toggle_url='index.php?'.http_build_query($toggle_url).'&id=';

$edit_url=$get_vars;
$edit_url['action']='edit';
unset($edit_url['id']);
$edit_url='index.php?'.http_build_query($edit_url).'&id=';

$makenew_url=$get_vars;
$makenew_url['action']='add';
unset($makenew_url['id']);
$makenew_url='index.php?'.http_build_query($makenew_url).'&id=';

$view_url=$get_vars;
$view_url['action']='view';
unset($view_url['id']);
$view_url='index.php?'.http_build_query($view_url).'&id=';

$unlock_url=$get_vars;
$unlock_url['action']='unlock';
unset($unlock_url['id']);
$unlock_url='index.php?'.http_build_query($unlock_url).'&id=';

$select_url=$get_vars;
$select_url['action']='select';
unset($select_url['id']);
$select_url='index.php?'.http_build_query($select_url).'&id=';

$emails_url=$get_vars;
$emails_url['action']='view_list';
unset($emails_url['id']);
unset($emails_url['email_status']);
$emails_url='index.php?'.http_build_query($emails_url).'&id=';

$orders_url = $get_vars;
$orders_url['action'] = 'list';
unset($orders_url['newsletter_id']);
$orders_url = '/webadmin/invoice/?' . http_build_query($orders_url) . '&newsletter_id=';
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?php echo formatHTMLTitle('<a href="#" onclick="$(\'#sectionList\').add(\'#sectionList_hideNote\').toggle(\'slow\');return false">Newsletter List</a>') ?>

<div id="sectionList">
	<table class="wide spacetable">
		<tr>
			<th><a href="<?php echo formatHTMLSafe($sort_url.'date_added') ?>">Date Created</a><?php echo ($order_by=='date_added')?$ord_img:'' ?></th>
			<th style="width:100%;"><a href="<?php echo formatHTMLSafe($sort_url.'subject') ?>">Title</a><?php echo ($order_by=='subject')?$ord_img:'' ?></th>
			<th><a href="<?php echo formatHTMLSafe($sort_url.'date_sent') ?>">Date Sent</a><?php echo ($order_by=='date_sent')?$ord_img:'' ?></th>
			<th><a href="<?php echo formatHTMLSafe($sort_url.'staff_id') ?>">Sent By</a><?php echo ($order_by=='staff_id')?$ord_img:'' ?></th>
			<th>Total Newsletter</th>
			<th>Total Viewed</th>
			<th>Total Clicked</th>
			<th>Total Orders</th>
			<th>Display Online</th>
			<th>&nbsp;</th>
		</tr>
<?php
$staff_list = $section_handler->getOptionList('staff_id');
foreach($item_list as $id)
{
	$item=$section_handler->fromDatabase($id);
	$item_lock=$section_handler->getLock($id);

	$class='';
	if($alt=!$alt)		$class=' class="alt"';
	//if($item['active']=='y')	$class=' class="highlight"';
	if($item_lock!==false)	$class=' class="locked"';
	if($item_lock['staff_id']==$_GLOBALS['staff']['id'])		$class=' class="mylock"';

	$item_members_list=$section_handler->mailinglistFromDatabase($id);
	$display_list = $section_handler->getOptionList("display_online");

	$item_status=array();
	foreach($item_members_list as $item_member) $item_status[$item_member['email_status']]++;


	$status_labels = array('listed'=>'Remaining');
	foreach($item_status as $k=>$v) if(!isset($status_labels[$k])) $status_labels[$k]=ucwords($k);
?>
		<tr<?php echo $class ?>>
			<td style="text-align:center;"><?php echo date("d/m/y",$item['time_added']) ?></td>
			<td style="text-align:center;"><a href="<?php echo formatHTMLSafe($view_url.$id) ?>" target="_blank"><?php echo formatHTMLSafe($item['subject']) ?></a></td>
			<td style="text-align:center;"><?php echo ($item['time_sent']!=0)?date("d/m/y",$item['time_sent']):'-' ?></td>
			<td style="text-align:center;"><?php echo formatHTMLSafe($staff_list[$item['staff_id']]) ?></td>
			<td style="text-align:center;"><a href="<?php echo formatHTMLSafe($emails_url . $id) ?>" target="_blank"><?php echo intval(array_sum($item_status)) ?></a></td>
			<td style="text-align:center;"><a href="<?php echo formatHTMLSafe($emails_url . $id . '&email_status=viewed|clicked|ordered') ?>" target="_blank"><?php echo intval($item_status['viewed'] + $item_status['clicked'] + $item_status['ordered']) ?></a></td>
			<td style="text-align:center;"><a href="<?php echo formatHTMLSafe($emails_url . $id . '&email_status=clicked|ordered') ?>" target="_blank"><?php echo intval($item_status['clicked'] + $item_status['ordered']) ?></a></td>
			<td style="text-align:center;"><a href="<?php echo formatHTMLSafe($orders_url . $id . '&email_status=ordered') ?>" target="_blank"><?php echo count(invoiceSearch(array("newsletter_id" => $id))) ?></a></td>
			<td style="text-align:center;"><a href="<?php echo formatHTMLSafe($toggle_url . $id) ?>"><?php echo formatHTMLSafe($display_list[$item['display_online']]) ?></a></td>
			<td style="text-align:center;">
<?php
	if($popup_window)
	{
?>
				<input type="button" value=" Select " onclick="window.location='<?php echo formatHTMLSafe($select_url.$id) ?>'" />
				<input type="button" value=" Edit " onclick="window.open('<?php echo formatHTMLSafe($edit_url.$id) ?>');" />
<?php
	}
	else
	{
		if($item_lock===false || $item_lock['staff_id']==$staff_id)
		{
			if(($item_status['listed']>0 || $item_status['sending']>0) && $item['active'] == 'y')
			{
?>
				<a href="index.php?action=cont_newsletter&amp;id=<?php echo $id ?>">Send</a>
<?php
			}
			//if((array_sum($item_status) - $item_status['listed']) == 0)
			{
?>
				<a href="<?php echo formatHTMLSafe($edit_url.$id) ?>" target="_blank">Edit</a>
<?php
			}
		}
		else
		{
?>
				<a href="<?php echo formatHTMLSafe($view_url.$id) ?>" target="_blank">View</a>
<?php
		}
		if((array_sum($item_status) - $item_status['listed'])>0)
		{
?>
				<a href="<?php echo formatHTMLSafe($statistics_url.$id) ?>">Statistics</a>
<?php
		}
		if($item_lock!==false && $item_lock['staff_id']==$_GLOBALS['staff']['id'])
		{
?>
				<a href="<?php echo formatHTMLSafe($unlock_url.$id) ?>">Unlock</a>
<?php
		}
?>
				<a target="_blank" href="<?php echo formatHTMLSafe($makenew_url.$id) ?>">Make&nbsp;New</a>
<?php
	}
?>
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionList_hideNote">List of Newsletters can be viewed by Clicking "Newsletter List" above<br /></div>

<!-- /list.tpl -->
