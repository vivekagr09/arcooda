<?php
include_once('notify_common.inc.php');
include_once('templates/notify_header.tpl.php');

$site_id=intval($posted['site_id']);

?>
<form action="index.php?action=do_options" method="post">
<?php
if($site_id>0)
{
	$all_options=notifyOptionsFromDatabase();
	$options_list=array();
	foreach($all_options as $row)
		if($row['site_id']==$site_id)
			$options_list[$row['staff_id']][$row['action_id']]='y';
	echo formatHTMLTitle(htmlspecialchars($websites_list[$site_id]));
	include 'templates/notify_options.tpl.php';
}
else
{
	$all_options=notifyOptionsFromDatabase();
	foreach($websites_list as $site_id=>$site_name)
	{
		$options_list=array();
		foreach($all_options as $row)
			if($row['site_id']==$site_id)
				$options_list[$row['staff_id']][$row['action_id']]='y';
		echo formatHTMLTitle('<a href="javascript:void(0);" onclick="javascript:toggleSection('."'divSiteOptions_".$site_id."'".');">'.htmlspecialchars($websites_list[$site_id]).'</a>');
?>
	<div id="divSiteOptions_<?= $site_id ?>"<?= ($site_id!=11)?' style="display:none;"':'' ?>>
<?php
		include 'templates/notify_options.tpl.php';
?>
		<br />
		<br />
	</div>
<?php
	}
}
?>
	<input type="submit" value="Save Changes" />
</form>
<?php

include_once('templates/notify_footer.tpl.php');
?>