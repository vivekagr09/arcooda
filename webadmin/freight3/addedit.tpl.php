<!-- addedit.tpl -->

<?php
/**
 * Add/Edit template
 *
 * Expects:
 *  $id - The Item ID
 *  $action - The action of the form (add or edit)
 */

/**
 * Recent changes display
 */
$temp = array_reverse($section_handler->commentSearch(array("generic_id" => $id), time() - 255));
if (count($temp))
{
?>
<div>
	Changes made in the last 5 mins :
	<ul>
<?php
	foreach($temp as $k)
	{
		$row = $section_handler->commentFromDatabase($k);
?>
		<li style="color:#<?php printf("%02x", min(255, 300 + $row["created_time"] - time())) ?>0000;"><?php hecho($row['comment_text']) ?></li>
<?php
	}
?>
	</ul>
</div>
<?php
}

/**
 * Item Locking Display
 */
$temp = $section_handler->getLock($id);
if ($temp["staff_id"] != 0 && $temp["staff_id"] != $_GLOBALS["staff"]["id"])
{
	$staff_list = $section_handler->getOptionList("staff_id");
?>
<div>This Entry is currently locked to <?php hecho($staff_list[$temp["staff_id"]]) ?>, please wait for <?php echo intval(($temp['expiry_time'] - time()) / 60) ?> Minutes <?php echo (($temp['expiry_time'] - time()) % 60) ?> Seconds and try again.</div>
<?php
}
elseif ($temp['staff_id'] == 0 && $action != "add")
{
	$section_handler->setLock($id, $_GLOBALS["staff"]["id"]);
}

switch($action)
{
case "edit":
	include __DIR__ . "/summary.tpl.php";
case "add":
	include __DIR__ . "/item.tpl.php";
	include __DIR__ . "/additional.tpl.php";
	include __DIR__ . "/online_freight_info.tpl.php";
	include __DIR__ . "/freight_summary.tpl.php";
	include __DIR__ . "/insurance.tpl.php";
	include __DIR__ . "/pricing.tpl.php";
	include __DIR__ . "/zones.tpl.php";
	include __DIR__ . "/dimensions.tpl.php";
	include __DIR__ . "/inventory.tpl.php";
	include __DIR__ . "/comments.tpl.php";
	include __DIR__ . "/status.tpl.php";

	if ($action == "add")
	{
?>
<div style="text-align:center;">
	<input type="button" value="Add Freight Service" onclick="form.submit()" />
</div>
<?php
	}
	else
	{
		$temp = $section_handler->getLock($id);
		if ($temp["staff_id"] == $_GLOBALS["staff"]["id"])
		{
?>
<div style="text-align:center;">
	<input type="button" value="Update Freight Service" onclick="form.submit()" />
</div>
<?php
		}
		else
		{
?>
<div style="text-align:center;">
	<input type="button" value="Update Freight Service" disabled="disabled" title="This entry is not locked to you and cannot be updated" />
</div>
<?php
		}
	}
	break;
}
?>

<input type="hidden" name="<?php hecho($section_handler->section_name . "[staff_id]") ?>" value="<?php hecho($_GLOBALS["staff"]["id"]) ?>" />

<!-- /addedit.tpl -->
