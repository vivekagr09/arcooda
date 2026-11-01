<?php
include_once __DIR__ . '/common.inc.php';

$action  = $posted["action"];
$generic = $posted['generic'];
if ('' == $generic) {
    $action = 'add';
}

switch ($action) {
    case 'do_add':
        if (5 == $posted['generic']) {
            header('Location: /webadmin/bulk_offers/?return_message=add_bulk');
            die();
        }
        doAdd($posted);
        break;
    case 'add':
    default:
        showAddGenericOptions();
        break;
}

function showAddGenericOptions()
{
    global $_GLOBALS;
    $options = array(0 => 'Machines', 1 => 'PCBs', 2 => 'Merchandise', 3 => 'Parts', 4 => 'Freight');
    if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/webadmin/bulk_offers')) {
        $options[5] = 'Bulk Offer';
    }

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form accept-charset="utf-8" action="" method="post">
			<input type="hidden" name="action" value="do_add" />
			<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
<?php
    foreach ($options as $key => $option) {
?>
				<tr>
					<td style="text-align:right;"><input type="radio" name="generic" value="<?= $key ?>" /></td>
					<td style="text-align:left;"><?= $option ?></td>
				</tr>
<?php
    }
?>
			</table>
			<br />
			<center><input type="submit" value=" Add Generic " /></center>
		</form>
</body></html>
<?php
}

function doAdd($posted)
{
    $options       = array(0 => 'Generic Machine', 1 => 'Generic PCB', 2 => 'Generic Merchandise', 3 => 'Generic Part', 4 => 'Generic Freight');
    $generic_codes = array(0 => 'GMA', 1 => 'GPC', 2 => 'GME', 3 => 'GPA', 4 => 'GFR');
    $name          = $options[$posted['generic']];
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <script type="text/javascript">
//<![CDATA[
if (window.opener.addGeneric) window.opener.addGeneric();
if (window.opener.product_count != '')
{
	window.opener.setValue('sup_code[' + (window.opener.product_count - 1) + ']', <?php jecho($generic_codes[$posted['generic']])?>);
	window.opener.setValue('product_name[' + (window.opener.product_count - 1) + ']', <?php jecho($name)?>);
	window.opener.setValue('generic_code[' + (window.opener.product_count - 1) + ']', <?php jecho($generic_codes[$posted['generic']])?>);
}
window.close();
//]]>
        </script>
    </head>
    <body>
    </body>
</html>
<?php
}
