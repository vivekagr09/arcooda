<?php
/**
 *	Module: Product Builder
 *
 *	confirmation.php
 *
 *	The confirm to delete the options
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */
include 'common.inc.php';

$section = "";
$id = "";

if (isset($posted['section']) && empty($posted['section']) === false &&
	isset($posted['id']) && empty($posted['id']) === false) {
	$section = strtolower($posted['section']);
	$id = $posted['id'];
}

if ($section == "") {
	header("Location: 404");
	exit();
}

$pb = new ProductBuilder($DB);

if ($section == "option") {
	include "templates/option_header.tpl.php";
	$option = $pb->getOptionById($id);
?>

<h1 style='color: #ff0000; text-align: center;'>
	Do you want to delete this item?
</h1>

<table style='margin: auto;'>
	<tr style='background: #EEEEEE;'>
		<td style='text-align: right; padding: 5px;' colspan='2'><b>Option Name</b></td>
		<td><?= $option['name'] ?></td>
	</tr>

	<tr style='background: #FFFFFF;'>
		<th colspan="3">Options</th>
	</tr>

	<?php
	$i = 0;
	foreach($option['options'] as $optionKey => $optionVal) {
		foreach($optionVal as $val) {
			$optionLinkLabel = "";
			$optionValLabel = "";
			$optionValue = "";

			if ($optionKey == 0) {
				$optionData = "Link Product";
				$optionValLabel = "<b>ID:</b>";
				$optionValue = $val['product_id'];
			} else if ($optionKey == 1) {
				$optionData = "Link Text";
				$optionValue = $val['text'];
			} else {
				$optionData = "Link Photo";
				$optionValLabel = "<b>IMG:</b>";
				$optionValue = $val['img'] . "." . $val['ext'];
			}

			$rowColor = ($i++ % 2 == 0) ? "#FFFFFF" : "#EEEEEE";

			echo "<tr style='background: {$rowColor};'>";
			echo "<td style='padding: 5px;'>{$optionData}</td>";
			echo "<td>{$optionValLabel} {$optionValue}</td>";
			echo "<td><b>Price:</b> {$val['currency']}$ {$val['price']}</td>";
			echo "</tr>";
		}
	}
	?>
</table>

	<div style='text-align: center; margin: 10px 0;'>
		<button onclick="window.location.href='index.php?action=do_delete_option&option_id=<?= $id ?>'">Yes</button>
		<button onclick="window.location.href='index.php?action=view_option'">No</button>
	</div>

<?php
} else {
?>

<?php
}	// END IF
?>