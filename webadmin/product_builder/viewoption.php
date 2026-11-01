<?php 
/**
 *	Module: Product Builder
 *
 *	viewoption.php
 *
 *	This page list all the options from database
 *	
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */

include_once "common.inc.php";
include_once "templates/option_header.tpl.php";

// Initiate module
$pb = new ProductBuilder($DB);

// Order to be displayed
$order = "desc";

// Criteria to be filtered
$criteria = array();

// Pagination
$offset = (isset($posted['offset']) && empty($posted['offset']) === false) ? $posted['offset'] : 0;
$limit = 20;	// Maximum number of result

if (isset($posted['order_by']) && empty($posted['order_date']) === false) {
	$criteria[] = strtolower($posted['order_by']);
} else {
	$criteria[] = "added_date";
}

if (isset($posted['order'])) {
	if (strtolower($posted['order']) == "asc" || strtolower($posted['order']) == "desc") {
		$order = strtolower($posted['order']);
	}
}

$link_order = ($order == "desc") ? "asc" : "desc";

// Options from database
$options = $pb->getOptions($limit, $offset, $criteria, $order,null);

?>

<br>
<div class="flagHeader"><span>View Option</span></div>
<br>

<table class="view-table">
	<tr>
		<th>
			<a class="sort-link" href="/webadmin/product_builder/viewoption.php?action=view_option&order_by=added_date&order=<?= $link_order ?>"><span>Date Added</span></a>
		</th>

		<th>
			<span>Option Name</span>
		</th>
		<th>Type</th>
		<th></th>
		<th></th>
	</tr>

	<?php
	// Get all generic locks
	$locks = $pb->optionGetLocks();

	foreach($options as $option_key => $option) {
		// Generic Id
		$gid = $option_key + 1;

		// Check if this option is locked
		$lock = false;
		if (array_key_exists($gid, $locks)) $lock = $locks[$gid];
		
		// Tool Tip for lock
		$lock_tooltip = '';

		// The class to change the style if it's locked
		$class="";

		// This option is locked
		if ($lock !== false) {
			$class = "class='locked'";
			if ($lock['staff_id'] != $_GLOBALS['staff']['id']) {
				$lock_tooltip = htmlspecialchars('This option is currently locked to ' . $all_staff_list[$lock['staff_id']]);
			}
		}
		if ($lock['staff_id'] == $_GLOBALS['staff']['id']) $class = "class='mylock'";

		echo "<tr {$class}>";
		// Added Date
		echo "<td style='width: 10%;'>". date("d/m/y", strtotime($option['added_date'])) ."</td>";

		// Option name
		echo "<td>". ucwords($option['name']) ."</td>";
		echo "<td>". ucwords($option['type']) ."</td>";
		// Edit and delete link
		echo "<td style='width: 10%;'>";
		if ($lock === false || $lock['staff_id'] == $_GLOBALS['staff']['id']) {
			echo "<a target='_blank' href='index.php?action=edit_option&option_id={$option['id']}&gid={$gid}' class='sort-link'>Edit</a>";
			echo "<span style='margin: 0 5px;'></span>";
			echo "<a  href='index.php?action=do_delete_option&option_id={$option['id']}&gid={$gid}' class='sort-link' onclick=\"return confirm('Do you want to delete this option ?')\">Remove</a>";
			if ($lock !== false && $lock['staff_id'] == $_GLOBALS['staff']['id']) {
				echo "<span style='margin: 0 5px;'></span>";
				echo "<a class='sort-link' href='index.php?action=unlock_option&gid={$gid}'>Unlock</a>";

			}
		} else {
			?><a onmouseover="<?= htmlspecialchars("tt_in(". json_encode($lock_tooltip) .");") ?>" onmouseout="tt_out();">Locked</a><?php
		}
		echo "</td>";
		echo '<td>';
			echo "<a  href='index.php?action=clone_option&option_id={$option['id']}&gid={$gid}' class='sort-link' onclick=\"return confirm('This will create a clone of this Option – please press ok to continue')\">Clone</a>";
		echo '</td>';
		echo "</tr>";
	}
	?>
</table>

<?php
$max = count($pb->getOptions($id));
$currentpage = ($offset + 1);
$currentpage .= " - ";
$currentpage .= ($max <= $limit) ? $max : ($offset + $limit);

echo "<div style='text-align: center; padding: 10px;'>";

echo "<b>Showing: {$currentpage} of {$max}</b>";

for($i = 0; $i < $max; $i += $limit) {
	if ($i == $offset) {
		echo " | <span>{$currentpage}</span>";
	} else {
		$get_vars['offset'] = $i;
		echo " | <a href='viewoption.php?". http_build_query($get_vars) ."'>";
		echo ($i + 1) . " - " . (($max <= $limit) ? $max : ($i + $limit));
		echo "</a>";
	}
}

echo "</div>";
?>



<?php include_once "templates/option_footer.tpl.php" ?>