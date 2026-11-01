<?php
include_once('permissions_common.inc.php');
include_once('templates/permissions_header.tpl.php');

$site_id=intval($posted['site_id']);
$username=$posted['username'];
if($username!='') $active_staff=array($username=>getStaffName($username));

$action=$posted['action'];
if($action=='do_update')
	permissionsToDatabase($posted['permissions'],$site_id);

$sites=array(1=>'Highway Games', 2=>'Highway Entertainment', 9=>'Converters.tv', 11=>'Highway Group', 16=>'Car Converters', 17=>'Boxer Machine', 20=>'Arcade Spare Parts');
$sites=getWebsitesList();
if($site_id == 0)
{
	// no site selected so print out the site list..
?>
<table border="1">
	<tr><th>Select A Site to edit permissions for</th></tr>
<?php
	foreach($sites as $site_id=>$site)
	{
?>
	<tr><td class="alt"><a target="_blank" href="?site_id=<?php echo $site_id?>"><?php echo formatHTMLSafe($site)?></a></td></tr>
<?php
	}
?>
</table>
<?php
}
else
{
?>
	<script>
	function toggleCheckboxes(section_id,check)
	{
		var startName='permissions[';
		var endName=']['+section_id+']';
<?php
		foreach($all_staff_list as $user=>$user_name)
		{
?>
		var theElem=getElement(startName+'<?php echo $user ?>'+endName);
		if(theElem) theElem.checked=check;
<?php
		}
?>
	}
	</script>
	<form action="" method="post">
	<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'site_'.$site_id.'\');">'.$sites[$site_id].'</a>');?>
	<input type="hidden" name="action" value="do_update" />
	<input type="hidden" name="site_id" value="<?php echo $site_id?>" />
	<div id="site_<?php echo $site_id?>" id="site_<?php echo $site_id?>">
<?php
	$sections=permissionSections($site_id);
	$admins=permissionSections($site_id,true);
	//if($_SERVER['REMOTE_USER']=='Jwalit') print_array($admins);
	$permissions=permissionsFromDatabase($site_id);
	//print_array($permissions);
?>
		<table class="wide" border="1">
			<tr>
				<th>&nbsp;</th>
				<?php foreach($sections as $section) { ?><th><?php echo $section?></th><?php } ?>
				<?php foreach($admins as $section) { ?><th style="background-color:#DFDFDF;"><?php echo $section?></th><?php } ?>
				<th>&nbsp;</th>
			</tr>
<?php
	$count=0;
	foreach($active_staff as $user=>$staff_name)
	{
		$staff=staffFromDatabase(staffFromUser($user));
		$time_started=$staff['start_time'];
		$days=formatDaysOpen($time_started);
		$backColour=($days<=7) ? "#02FAFA" : "";
		$colourStr=($backColour!="") ? "background-color:".$backColour : "";
?>
			<tr <?php echo ($count%2)?'style="background-color:#EAEAEA;"':''?> id="<?php echo $user?>" name="<?php echo $user?>"><th><?php echo $staff_name?></th>
<?php
		foreach($sections as $section_id=>$section)
		{
			$checked='';
			if(is_array($permissions[$user]))
				$checked=(in_array($section_id,$permissions[$user]))?"checked":'';
?>
				<td style="text-align:center;<?php echo $colourStr?>"><input type="checkbox" name="permissions[<?php echo $user?>][<?php echo $section_id?>]" <?php echo $checked?>></td>
<?php
		}

		foreach($admins as $section_id=>$section)
		{
			if($backColour=="") $backColour="#DFDFDF";
			$checked='';
			if(is_array($permissions[$user]))
				$checked=(in_array($section_id,$permissions[$user]))?"checked":'';
?>
				<td style="text-align:center;background-color:<?php echo $backColour?>;"><input type="checkbox" name="permissions[<?php echo $user?>][<?php echo $section_id?>]" <?php echo $checked?>></td>
<?php
		}
?>
				<th><?php echo $staff_name?></th>
			</tr>
<?php
		$count++;
	}
?>
			<tr>
				<th>&nbsp;</th>
				<?php foreach($sections as $section_id=>$section) { ?>
				<td style="text-align:center;background-color:#AAAAAA"><input type="checkbox" onclick="toggleCheckboxes(<?php echo $section_id?>,this.checked)" /></td>
				<?php } ?>
				<?php foreach($admins as $section_id=>$section) { ?>
				<td style="text-align:center;background-color:#AAAAAA"><input type="checkbox" onclick="toggleCheckboxes(<?php echo $section_id?>,this.checked)" /></td>
				<?php } ?>
				<th>&nbsp;</th>
			</tr>
		</table>
		<br />
		<center><input type="submit" value=" Update " style="width:150px;"/></center>
	</div>
	</form>
	<br />
	<div align="left" style="padding-left:20px;">
	<i>Notes :<br /></i>
	<li>Rows highlighted in <span style="background-color:#02FAFA;">blue</span> are staff started here less then 7 Days old.</li>
	</div>
<?php
}
?>