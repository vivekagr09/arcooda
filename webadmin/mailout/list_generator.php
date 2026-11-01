<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

global $DB;

?>
<script type="text/javascript">
//<![CDATA[
function toggle_checkboxes(prefix, checked) {
	for (i = 0;i < document.mailout_admin.elements.length;i++) {
		var object = document.mailout_admin.elements[i];
		if (object.type == 'checkbox') {
			if (object.id == prefix) {
				if (checked > 0) {
					object.checked = true;
				} else {
					object.checked = false;
				}
			}
		}
	}
}
//]]>
</script>

<form accept-charset="utf-8" name="mailout_admin" method="post" action="email_list.php?action=gen">
<?php
/**
 * @param mixed[] $section
 *
 * @return void
 */
function show_checkbox_table($section)
{
    global $DB;

    $parent_id = $section['survey_type'];
?>
<table width="90%" cellspacing="0" cellpadding="4" style="margin:auto;border-style:none">
	<tr>
		<td width="25%" nowrap="nowrap" class="td_options" style="background:#999"><strong><?=ucwords($section['short_desc'])?></strong></td>
		<td width="75%"><input type="checkbox" name="all<?=$parent_id?>" value="-1" onClick="document.forms[0].elements['all<?=$parent_id?>'].value *= -1; toggle_checkboxes('<?=$parent_id?>',document.forms[0].elements['all<?=$parent_id?>'].value);" /> Select All&nbsp;</td>
	</tr>
	<tr><td colspan="2" class="box"><table width="100%" cellspacing="0" cellpadding="4" style="border-style:none">
<?php
    if ($query = $DB->query(sprintf(
        "SELECT `t`.`survey_type`, `t`.`long_desc` FROM `highwaygroup`.`new_members_survey_types` AS `t` INNER JOIN `highwaygroup`.`new_members_survey_types` AS `tp` WHERE `t`.`parent` = `tp`.`survey_type` AND `tp`.`survey_type` = %d",
        $parent_id
    ))) {
        $i = 0;
        while ($row = $DB->fetch_assoc($query)) {
            $i++;
            if ($i % 2 == 1) {
                echo "<tr>\n";
            }
?>
			<td width="1%"><input type="checkbox" id="<?=$parent_id?>" name="options[]" value="<?=$row['survey_type']?>" /></td>
			<td width="49%"><?=htmlspecialchars($row['long_desc'])?></td>
<?php
            if ($i % 2 == 0) {
                echo "</tr>\n";
            }
        }
        $DB->free_result($query);
    }
?>
	</table></td></tr>
</table>
<br /><br />
<?php
}

/**
 * @return void
 */
function show_type_checkbox()
{
    global $DB;

?>
<table width="90%" cellspacing="0" cellpadding="4" style="margin:auto;border-style:none">
	<tr>
		<td width="25%" nowrap="nowrap" class="td_options" style="background:#069"><strong>Email Type</strong></td>
		<td width="75%">&nbsp;</td>
	</tr>
	<tr><td colspan="2" class="box" style="background:#F0F4FF"><table width="100%" border="0" cellspacing="0" cellpadding="4">
<?php
    if ($query = $DB->query(sprintf(
        "SELECT `id`, `type_name` FROM `mailout_types`"
    ))) {
        $check  = true;
        while ($row = $DB->fetch_assoc($query)) {
?>
		<tr>
			<td width="5%"><input type="radio" id="type" name="type" value="<?=$row['id'];?>" <?=($check) ? ' checked="checked"' : '' ?> /></td>
			<td><?=htmlspecialchars($row['type_name']);?></td>
		</tr>
<?php
            $check = false;
        }
		$DB->free_result($query);
    }
?>
	</table></td></tr>
</table>
<br />
<br />
<?php
}

/**
 * @return void
 */
function show_maillist_checkbox()
{
?>
<table width="90%" cellspacing="0" cellpadding="4" style="margin:auto;border-style:none">
	<tr>
		<td width="25%" nowrap="nowrap" class="td_options" style="background:#390"><strong>Mailing List</strong></td>
		<td width="75%">&nbsp;</td>
	</tr>
	<tr><td colspan="2" class="box" style="background:#dbfbdd"><table width="100%" cellspacing="0" cellpadding="4" style="border-style:none">
		<tr>
			<td width="5%"><input type="checkbox" id="include_list" name="include_members" value="true" /></td>
			<td>Include customers</td>
		</tr>
		<tr>
			<td width="5%"><input type="checkbox" id="include_list" name="include_mailing" value="true" /></td>
			<td>Include mailout members</td>
		</tr>
	</table></td></tr>
</table>
<br />
<br />
<?php
}

/**
 * @return void
 */
function show_country_selection()
{
    global $DB;

?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td width="25%" nowrap bgcolor="#FFCC00" class="td_options"><strong>Country</strong></td>
		<td width="75%">No selection will select all countries</td>
	</tr>
	<tr><td colspan="2" class="box" bgcolor="#FFFFcc"><table width="100%" border="0" cellspacing="0" cellpadding="4"><tr><td><select name="country[]" style="width:400px" MULTIPLE size="5">
		<option value="" selected>Please Select a Country</option>
		<option value="">-------------------------------------------------</option>
		<?=formatHTMLOptions(getCountryList(false))?>
	</select></td></tr></table></td></tr>
</table>
<br />
<br />
<?php
}

// show_type_checkbox();

show_maillist_checkbox();

//specifically remove type 3

if ($query = $DB->query(sprintf(
    "SELECT `survey_type`, `short_desc` FROM `highwaygroup`.`new_members_survey_types` WHERE `parent` = '0' AND `survey_type` != 3"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        show_checkbox_table($row);
    }
    $DB->free_result($query);
}

show_country_selection();

?>
	<div style="text-align:center"><input type="submit" value="Create List >>" /></div>
	<br />
	<br />
</form>
<?php

//include_once '../footer.inc.php';
