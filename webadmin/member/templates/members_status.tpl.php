<!-- members_status.tpl.php -->

<?php
$back_colour1 = ("not_active" == $member["status"]) ? '#f00' : '#0f0';
$back_colour2 = ('n' == $login['status']) ? '#f00' : '#0f0';
?>
<script type="text/javascript">
//<![CDATA[
function member_status_check(input) {
    if ($(input).is(":checked")) {
        $('#member-check-new').show();
        $('#member-check-old').hide();
    } else {
        $('#member-check-new').hide();
        $('#member-check-old').show();
    }
}
//]]>
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionStatus\');return false">Status</a>')?>
<div id="sectionStatus"><table class="wide">
	<tr>
		<td class="left_title quarter">Member Checked :</td>
		<td style="width:75%;">
			<input type="checkbox" value="true" name="member_check" onchange="member_status_check(this);" />
<?php
if ($member['check_staff_id']) {
    $staff_list = getStaffList();
?>
			<span class="member-check-label" id="member-check-old">
			    <span style="color:red;"><?=date(_('d/m/Y'), strtotime($member['check_date']))?> (by <?=htmlspecialchars($staff_list[$member['check_staff_id']])?>)</span>
			</span>
<?php
}
?>
			<span class="member-check-label" id="member-check-new" style="display:none;">
				<span style="color:red;"><?=date(_('d/m/Y'))?> (by <?=htmlspecialchars(ucwords($_GLOBALS['staff']['first_name']))?>)</span>
			</span>
		</td>
	</tr>
	<tr>
		<td class="left_title quarter">Flags :</td>
		<td style="width:75%;">
			<input type="checkbox" id="member_spammer" name="member[spammer]" value="y"<?=("y" == $member["spammer"]) ? ' checked="checked"' : ''?> /> <label for="member_spammer">Spam Member</label>&nbsp;&nbsp;
			<input type="checkbox" id="member_timewaster" name="member[timewaster]" value="y"<?=("y" == $member["timewaster"]) ? ' checked="checked"' : ''?> /> <label for="member_timewaster">Time Waster</label>&nbsp;&nbsp;
			<input type="checkbox" id="sales_related" name="member[sales_related]" value="y"<?=("y" == $member["sales_related"]) ? ' checked="checked"' : ''?> /> <label for="sales_related">Not Sales Related</label>&nbsp;&nbsp;
			<input type="checkbox" id="member_tester" name="member[tester]" value="y"<?=("y" == $member["tester"]) ? ' checked="checked"' : ''?> /> <label for="member_tester">Test Member</label>&nbsp;&nbsp;
		</td>
	</tr>
	<tr style="display:none;">
		<td class="left_title quarter">Login Status :</td>
		<td class="right_input" style="width:75%;"><select name="login[status]" style="background-color:<?=$back_colour2?>" onchange="this.style.backgroundColor = (this.value == 'n') ? '#f00' : '#0f0'">
			<?=formatHTMLOptions(array('y' => 'Active', 'n' => 'Inactive'), $login['status'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title quarter">Login Email :</td>
		<td class="right_input" style="width:75%;"><input readonly="readonly" type="text" name="login[email]" value="<?=$member['email_address']?>" /></td>
	</tr>
	<tr>
		<td class="left_title quarter">Password :</td>
		<td class="right_input" style="width:75%;">
			<div style="display:flex;">
			<div style="width:40%;">
			<input type="password" name="login[password]" value="" autocomplete="new-password" />
            </div>
			<div style="width:15%;margin-left:10px;">
			<?php  if(isset($member['id']) && $member['id'] > 0) { ?>
		<a href="#" onclick="openResetPasswordWindow(<?= $member['id'] ?> ); return false;" style="color: blue;"><em>Reset Password</em></a>
	<?php } ?>
			</div>
			<div style="width:25%;margin-left:10px;">
			<?php  if(isset($member['id']) && $member['id'] > 0) { ?>
		<a href="#" onclick="openShowResetLinkWindow(<?= $member['id'] ?>); return false;" style="color: blue;"><em>Show Password Reset Link</em></a>
	<?php } ?>
			</div>
			</div>
			
		
	</td>
	</tr>
	<tr>
		<td class="left_title quarter">Confirm Password :</td>
		<td class="right_input" style="width:75%;">
		<div style="width:40%;">
		<input type="password" name="login[c_password]" value="" autocomplete="new-password" />
		</div>
	</td>
            
			
	</tr>
	<tr style="line-height: 50px;">
		<td class="left_title quarter" >Status :</td>
		<td class="right_input" style="width:75%;"><select name="member[status]" style="background:<?=$back_colour1?>" onchange="this.style.backgroundColor = (this.value == 'not_active') ? '#f00' : '#0f0'">
			<?=formatHTMLOptions(array(
				"active"        => "Active",
				"not_active"    => "Not Active",
				"not_contacted" => "Active But Never Contacted",
			), $member['status'])?>
		</select></td>
	</tr>

</table></div>

<br />
<br />
<script type="text/javascript">
function openResetPasswordWindow(id) {
    window.open('reset_password.php?id='+id, 'Reset Password', 'width=700,height=600');
}
function openShowResetLinkWindow(id) {
    window.open('show_reset_link.php?id='+id, 'Show Reset Link', 'width=700,height=400');
}
</script>

<!-- /members_status.tpl.php -->