<?php
include_once __DIR__ . '/common.inc.php';

moduleOverride('serial', $_SERVER['DOCUMENT_ROOT'] . '/webadmin/serial/query.inc.php');

moduleLoad('serial');
unset($_GLOBALS['criteria_options']['serial']['active']);

$section_handler = new Serial_Handler();
$section_name    = $section_handler->section_name;

$posted     = array_merge($_GET, $_POST, $_FILES);
$action     = strtolower($posted['action']);
$name       = '';
$product_id = 0;
if (array_key_exists("name", $posted)) {
    $name = $posted['name'];
}

if (array_key_exists("product_id", $posted)) {
    $product_id = intval($posted['product_id']);
}

/**
 * @return void
 */
function showAddSerialno()
{
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Allocate Serial Number</title>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<h3>Serial Number Linking</h3>
		<form action="" method="post">
			<input type="hidden" name="action" value="search_serialno" />
			<table style="width:600px;border:1px solid blue;background-color:#99CCFF;margin:auto">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Product Name/Id/Serial #:</td>
					<td style="text-align:left;"><input style="width:450px;font: 13px verdana;" type="text" name="searchid" required="true" value="<?=$_REQUEST['searchid']?>"/></td>
				</tr>
				<tr style="margin-top: 10px;">
					<td style="text-align:right;">&nbsp;</td>
					<td style="text-align:center;"><input type="submit" value="Search" style="padding: 5px; margin: 5px;"/></td>
				</tr>
			</table>
		</form>
	</body>
</html>
<?php
}

/**
 * @param string          $search
 * @param Section_Handler $section_handler
 *
 * @return void
 */
function ShowsearchSerialNo($search, $section_handler)
{
    $criteria              = array();
    $criteria['any_text']  = preg_split('/[^a-zA-Z0-9]+/', $search);
    $criteria['member_id'] = 0;
    //$criteria['invoice_id']=0;
    $serialno = $section_handler->search($criteria);
    if (count($serialno)) {
        echo '<form><table class="table_grid" style="width:600px;border:1px solid black;margin-top:10px auto"><input type="hidden" name="action" value="add_serial_no" /><tr style="border:1px solid black;margin-top:10px;"><th>Select Serial Number</th><th style="text-align:left;">Serial Number</th></tr>';
        foreach ($serialno as $key => $v) {
            $item = $section_handler->fromDatabase($v);
?>
			 	<tr style="border:1px solid black;margin-top:10px;">
			 		<td width="30%" style="text-align:center"><input type="checkbox" name="memberSerialno[<?=$key?>][accpetno]" value="<?=$v['serial_id']?>"> </td>
			 		<td width="70%"><?=htmlspecialchars($item["serial_number"])?><?=($item["product_str"]) ? '-' . $item["product_str"] . '' : ''?>
			 		<input type="hidden" name="memberSerialno[<?=$key?>][serial_id]" value="<?=$item['serial_id']?>" >
			 		<input type="hidden" name="memberSerialno[<?=$key?>][product_id]" value="<?=$item['product_id']?>">
			 		<input type="hidden" name="memberSerialno[<?=$key?>][invoice_id]" value="<?=$item['invoice_id']?>" >
			 		<input type="hidden" name="memberSerialno[<?=$key?>][invoice_number]" value="<?=$item['invoice_number']?>" >
			 		<input type="hidden" name="memberSerialno[<?=$key?>][product_str]" value="<?=$item['product_str']?>">
			 		<input type="hidden" name="memberSerialno[<?=$key?>][serial_number]" value="<?=$item['serial_number']?>">
			 		<input type="hidden" name="memberSerialno[<?=$key?>][created]" value="<?=$item['created']?>">
			 		<input type="hidden" name="memberSerialno[<?=$key?>][id]" value="<?=$item['id']?>">
			 	</td>


			 	</tr>
<?php
        }
        echo '</table><input type="submit" value="Select Serial Number" style="padding: 5px; margin: 5px;"/></form>';
    } else {
        echo '<h3>No Serial Number </h3>';
    }
}

/**
 * @param mixed[] $posted
 *
 * @return void
 */
function addSerialNumber($posted)
{
    $countpro = 0;
    $data     = array();
    foreach ($posted['memberSerialno'] as $key => $value) {
        if (isset($value['accpetno'])) {
            $countpro++;
            $data[] = array(
				'serial_id'      => $value['serial_id'],
				'serial_number'  => $value['serial_number'],
				'product_id'     => $value['product_id'],
				'invoice_id'     => $value['invoice_id'],
				'invoice_number' => $value['invoice_number'],
				'product_str'    => $value['product_str'],
				'staff'          => $_SERVER['REMOTE_USER'],
				'created'        => date(_('d/m/Y'), strtotime($value['created'])),
				'id'             => $value['id'],
			);
        }
    }
	$jsonp = (array_key_exists('jsonp', $_REQUEST)) ? $_REQUEST['jsonp'] : 'addSerialNumber';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($data)?>;
function returnToOpener()
{
	if (window.opener && window.opener.<?=$jsonp?>) {
		window.opener.<?=$jsonp?>(data);
	} else if (window.parent && window.parent.<?=$jsonp?>) {
		window.parent.<?=$jsonp?>(data);
	} else {
		alert('Unable to send serial number back to form');
	}
	window.close();
}
//]]>
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;"><table style="width:30%;border:1px solid blue;background-color:#99CCFF;margin:auto">
		<tr><td style="text-align:center"><b><?=$countpro?> Serial Number  successfully added</b></td></tr>
		<tr><td style="text-align:center"><input type="button" onclick="returnToOpener()" value="Close Window" /></td></tr>
	</table></body>
</html>
<?php
}

// Main Switch-Case...
switch ($action) {
    case "add":
        showAddSerialno();
        break;
    case "do_add":
        addScreenshot($posted);
        break;
    case "search_serialno":
        showAddSerialno();
        ShowsearchSerialNo($_REQUEST['searchid'], $section_handler);
        break;
    case "add_serial_no":
        addSerialNumber($posted);
        break;
    case "":
    default:
        die("Invalid Selection!!");
}
