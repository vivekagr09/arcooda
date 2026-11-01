<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

$posted = array_merge($_GET,$_POST,$_FILES);
$action = $posted['action'];
$type = $posted['type'];
/**********************************************************Functions Definitions ************************************************************************/
function showAddScreenshot($type)
{
	global $DB, $_GLOBALS;
	 $typeData  = $DB->query_first(sprintf(
                "SELECT * FROM %s  WHERE id=%d",
                '`highwaygroup`.`job_handle_type`',
                $type
            ), MYSQLI_ASSOC);
?>
<html>
	<head><title>Add Screenshot</title></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<table width="100%" border="0" cellspaing="2" cellpadding="2"><tbody><tr>
		    <td bgcolor="#EEEEEE" width="20%"><div align="center"><b><?= $typeData['type']?></b></div></td>
		</tr></tbody></table>
		<form enctype="multipart/form-data" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">URL:</td>
					<td style="text-align:left;">
						<input style="width:450px;font: 13px verdana;" type="url" name="url" required  />
					</td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:center;"><input type="submit" value="Add"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}

function addScreenshot($posted)
{
	global $_GLOBALS;
	global $DB;
	$action = 'create';
	if(isset($posted['url'])){
		  $DB->query(sprintf(
        "INSERT INTO `highwaygroup`.`job_handle` (`staff_id`, `type_id`, `action`, `site_id`,`link`) VALUES (%d, %d, %s, %d,%s)",
            $_GLOBALS["staff"]["id"],
            $posted['type'],
            $DB->escape($action),
            $DB->escape($_GLOBALS['site_settings']['site_id']),
            $DB->escape($posted['url'])
        ));

		?>
		<html>
			<head>
				<script type="text/javascript">
		//<![CDATA[
		//var data = <?= json_encode($data) ?>;
		//]]>
				</script>
			</head>
			<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
			<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
			<tr><td style="text-align:center;"><b>Link Added Successfully</b></td></tr>
			<tr><td align="center"><input type="button" onclick="window.opener.closeLinkPopup();window.close();" value="Close Window" /></td></tr>
			</table>
			</body>
		</html>
<?php	}
}

// Main Switch-Case...
switch($action)
{
	case "add":
		showAddScreenshot($type);
		break;
	case "do_add":
		addScreenshot($posted);
		break;
	case "":
	default:
		die("Invalid Selection!!");
}
