<!-- members_authentication.tpl.php -->

<?php
function authentication_row($key, $row = array())
{
    static $alt = false;
    $main_key   = 'member_authentication[' . $key . ']';

    ob_start();
?>
<table class="table_fields wide" id="<?=$main_key?>">
	<tr>
		<th style="width:25%;">Active :</th>
		<td><select name="<?=htmlspecialchars($main_key . '[active]')?>" onchange="<?=htmlspecialchars('$(this).css("background", $(":selected", this).css("background"));$(' . json_encode("[id^='" . $main_key . "[password_row']") . ').toggle($(this).val() == "y")')?>" style="background:#<?=("n" == $row["active"]) ? 'f00' : '0f0'?>;">
			<option value="y" style="background:#0f0;"<?=("y" == $row["active"]) ? ' selected="selected"' : ''?>>Active</option>
			<option value="n" style="background:#f00;"<?=("n" == $row["active"]) ? ' selected="selected"' : ''?>>Not-Active</option>
		</select></td>
		<td style="width:16px;" rowspan="4"><a href="#" onclick="<?=htmlspecialchars('authentication_row_delete(' . json_encode($key) . ');return false')?>"><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0" /></a></td>
	</tr>
	<tr>
		<th>Login Username :</th>
		<td>
			<input type="hidden" name="<?=htmlspecialchars($main_key . '[email_address]')?>" value="<?=htmlspecialchars($row['email_address'])?>" />
			<input class="wide" style="width:100%;" maxlength="64" type="text" name="<?=htmlspecialchars($main_key . '[email_address1]')?>" value="<?=htmlspecialchars($row['email_address'])?>" />
		</td>
	</tr>
	<tr id="<?=htmlspecialchars($main_key . "[password_row1]")?>"<?=("n" == $row["active"]) ? ' style="display:none;"' : ''?>>
		<th>Login Password :</th>
		<td><input class="wide" style="width:100%;" type="password" name="<?=htmlspecialchars($main_key . '[password1]')?>" value="" autocomplete="new-password" /></td>
	</tr>
	<tr id="<?=htmlspecialchars($main_key . "[password_row2]")?>"<?=("n" == $row["active"]) ? ' style="display:none;"' : ''?>>
		<th>Confirm Password :</th>
		<td><input class="wide" style="width:100%;" type="password" name="<?=htmlspecialchars($main_key . '[password2]')?>" value="" autocomplete="new-password" /></td>
	</tr>
</table>
<?php
return ob_get_clean();
}

if (is_array($member_authentication)) {
?>
<script type="text/javascript">
//<![CDATA[
var member_authentication_count = <?=count($member_authentication) + 256?>;
function authentication_add_row()
{
	var $the_row = $("#members_authentication_table").append(<?=json_encode(authentication_row("[ROW_COUNT]"))?>.replace(/\[ROW_COUNT\]/g, member_authentication_count));

	var alt = false;
	$the_row.parent().children().each(function () {
		$(this).toggleClass('alt', alt = !alt);
	});

	member_authentication_count++;
}
function authentication_row_delete(row_id)
{
	var $the_row = $("[id='member_authentication[" + row_id + "]']");
	var $siblings = $the_row.parent().children();
	$the_row.remove();
	var alt = false;
	$siblings.each(function () {
		$(this).toggleClass('alt', alt = !alt);
	});
	return false;
}
$(function () {
	$('.members_authentication_add_button').css('display', '').click(function () {
		authentication_add_row();
		return false;
	});
});
//]]>
</script>

<?=formatHTMLTitle('Authentication', '<a href="#" class="members_authentication_add_button" style="display:none;">Add Password</a>')?>

<div id="sectionAuthentication">
	<div id="<?=htmlspecialchars("members_authentication_table")?>">
<?php
$count                          = 0;
    $member_authentication_affected = array();
    foreach ($member_authentication as $item_authentication) {
        $member_authentication_affected[] = $item_authentication['email_address'];
        echo authentication_row($count++, $item_authentication);
    }
?>
	</div>
	<input type="hidden" name="members_authentication_affected" value="<?=htmlspecialchars(implode(',', $member_authentication_affected))?>" /><br /> <!-- Note: unhandled commas -->
	<br />
</div>

<?php
}
?>

<!-- /members_authentication.tpl -->
