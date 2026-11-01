<?php
$site_options = array();
$query        = $DB->query("SELECT * FROM options WHERE flag=1");
while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
    $site_options[] = $row;
}

switch ($action) {

    case 'list':
?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSiteOptions\');">Site Options</a>')?>

<div id="sectionSiteOptions">
	<table class="wide spacetable">
		<tr><th>Option</th><th>Value</th><th>Action</th></tr>
<?php
$alt = false;
        foreach ($site_options as $option) {
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
			<td style="width:20%;"><?=$option['variable']?></td>
			<td style="width:80%;">
<?php
if ((strlen($option['value']) == 7) && (substr($option['value'], 0, 1) == '#')) {
?>
				<span style="border:1px solid black;background-color:<?=$option['value']?>;width:16px;height:16px;">&nbsp;&nbsp;&nbsp;</span>
<?php
}
?>
				<?=nl2br(html_safe($option['value']))?>
			</td>
			<td style="width:0px;text-align:center;"><a href="?action=edit&opt_type=site&option_id=<?=$option['id']?>">Edit</a></td>
		</tr>
<?php
}
?>
		<form action="?action=add&opt_type=site" method="post">
		<tr class="alt"><td colspan="3" style="text-align:center;">
			<input type="submit" value="Add New Option" />
		</td></tr>
		</form>
	</table>
</div>

<br />

<?php
break;

    case 'add':
?>
<form action="?action=do_add&opt_type=site" method="post">
	<table class="wide">
		<tr>
			<td class="left_title">Option Name :</td>
			<td>
<?php
input_text('option_variable');
?>
			</td>
		</tr>
		<tr>
			<td class="left_title">Value :</td>
			<td>
<?php
input_blurb('option_value');
?>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="button" value="Go Back" onclick="javascript:history.back();" />
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
break;

    case 'edit':
        $row = $DB->query_first("SELECT * FROM options WHERE id='" . $posted['option_id'] . "' LIMIT 1");
?>
<form action="?action=do_edit&opt_type=site&option_id=<?=$posted['option_id']?>" method="post">
	<table class="wide">
		<tr>
			<td class="left_title">Option Name :</td>
			<td><?=html_safe($row['variable'])?></td>
		</tr>
		<tr>
			<td class="left_title">Old Value :</td>
			<td>
<?php
if ((strlen($row['value']) == 7) && (substr($row['value'], 0, 1) == '#')) {
?>
				<span style="border:1px solid black;background-color:<?=$row['value']?>;width:16px;height:16px;">&nbsp;&nbsp;&nbsp;</span>
<?php
}
?>
				<?=nl2br(html_safe($row['value']))?>
			</td>
		</tr>
		<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
		<tr>
			<td class="left_title">New Value :</td>
			<td>
<?php
        if (substr($row['variable'], -9) == '_currency') {
            input_currency("option_value", $row['value']);
        } elseif (substr($row['variable'], -8) == '_country') {
            input_country("option_value", $row['value']);
        } elseif (substr($row['variable'], -10) == '_available') {
            input_available("option_value", $row['value']);
        } elseif (substr($row['variable'], -10) == '_condition') {
            input_condition("option_value", $row['value']);
        } elseif (substr($row['variable'], 0, 7) == 'colour_') {
            input_colour("option_value", $row['value']);
        } elseif (substr($row['variable'], -6) == '_EMAIL') {
            input_blurb("option_value", $row['value']);
        } elseif (in_array($row['variable'], array('RESET_PASS_EMAIL', 'SIGNUP_SUCCESS_EMAIL'))) {
            input_blurb("option_value", $row['value']);
        } elseif (strpos($row['variable'], '_META_DESC') !== false) {
            input_blurb("option_value", $row['value']);
        } else {
            input_text("option_value", $row['value']);
        }
?>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="button" value="Go Back" onclick="javascript:history.back();" />
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
break;

    case 'do_edit':
        $sql = "UPDATE options SET value='" . addslashes($posted['option_value']) . "' WHERE id='" . $posted['option_id'] . "' LIMIT 1";
        $DB->query($sql);
        echo 'Successfully Updated Value';
        break;

    case 'do_add':
        $sql = "INSERT INTO options(variable,value) VALUES ('" . addslashes($posted['option_variable']) . "','" . addslashes($posted['option_value']) . "')";
        $DB->query($sql);
        echo 'Successfully Created New Variable';
        break;
}
?>