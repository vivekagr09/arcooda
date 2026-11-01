<?php
if(is_array($options_list))
	foreach($options_list as $staff_id=>$sub_list)
		if(is_array($sub_list))
			foreach($sub_list as $action_id=>$value)
				if($value=='y')
					echo '<input type="hidden" name="notify_affected['.$site_id.']['.$staff_id.']['.$action_id.']" value="y" />'."\n";
?>
<table>
	<tr>
		<th></th>
<?php
	foreach($action_list as $value)
		echo '<th>'.htmlspecialchars($value)."</th>\n";
?>
	</tr>
<?php
	foreach($active_staff as $staff_id=>$staff_name)
	{
?>
	<tr>
		<th><?= htmlspecialchars($staff_name) ?></th>
<?php
		foreach($action_list as $action_id=>$action_name)
		{
?>
		<td style="text-align:center;"><input type="checkbox" name="notify_options[<?= $site_id ?>][<?= $staff_id ?>][<?= $action_id ?>]"<?= ($options_list[$staff_id][$action_id]=='y')?' checked="checked"':'' ?> /></td>
<?php
		}
?>
	</tr>
<?php
	}
	foreach($inactive_staff as $staff_id=>$staff_name)
		if(isset($options_list[$staff_id]))
		{
?>
	<tr>
		<th><?= htmlspecialchars($staff_name) ?></th>
<?php
			foreach($action_list as $action_id=>$action_name)
			{
?>
		<td style="text-align:center;"><?= ($options_list[$staff_id][$action_id]=='y')?('<input type="checkbox" name="notify['.$site_id.']['.$staff_id.']['.$action_id.']" checked="checked" />'):'' ?></td>
<?php
			}
?>
	</tr>
<?php
		}
?>
</table>
