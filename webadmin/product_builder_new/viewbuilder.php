<?php 
/**
 *	Module: Product Builder
 *
 *	viewbuilder.php
 *
 *	This page list all the builders from database
 *	
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */
include_once "common.inc.php";
include_once "templates/builder_header.tpl.php";	

// Initiate module
$pb = new ProductBuilder($DB);

// Order to be display
$order = "desc";

// Criteria to be filtered
$criteria = array();

// Pagination
$offset = (isset($posted['offset']) && empty($posted['offset']) === false) ? $posted['offset'] : 0;
$limit = 20;	// Maximum number of results

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

// Builders from database
$builders = $pb->getBuilders($limit, $offset, $criteria, $order);

?>

<!-- NOTE -->


<br>
<div class="flagHeader"><span>Products Built</span></div>
<br>

<table class="view-table">
	<tr>
		<th>
			<a class="sort-link" href="/webadmin/product_builder_new/viewbuilder.php?action=view_builder&order_by=added_date&order=<?= $link_order ?>"><span>Date Added</span></a>
		</th>

		<th>
			<span>Name of Builder</span>
		</th>

		<th>
			<span>Websites Linked</span>
		</th>

		<th>
			<span>Last Updated</span>
		</th>

		<th>
			<span>Staff</span>
		</th>

		<th>
			<a class="sort-link" href="/webadmin/product_builder_new/viewbuilder.php?action=view_builder&order_by=status&order=<?= $link_order ?>"><span>Status</span></a>
		</th>

		<th></th>
	</tr>

	<?php
	// Get all generic locks
	$locks = $pb->builderGetLocks();

	foreach($builders as $builder_key => $builder) {
		// Generic Id
		$gid = $builder_key + 1;

		// Check if this builder is locked
		$lock = false;
		if (array_key_exists($gid, $locks)) $lock = $locks[$gid];
		
		// Tool Tip for lock
		$lock_tooltip = '';

		// The class to change the style if it's locked
		$class="";

		// This builder is locked
		if ($lock !== false) {
			$class = "class='locked'";
			if ($lock['staff_id'] != $_GLOBALS['staff']['id']) {
				$lock_tooltip = htmlspecialchars('This option is currently locked to ' . $all_staff_list[$lock['staff_id']]);
			}
		}
		if ($lock['staff_id'] == $_GLOBALS['staff']['id']) $class = "class='mylock'";

		echo "<tr {$class}>";
		// Added Date
		echo "<td style='width: 10%;'>". date("d/m/y", strtotime($builder['added_date'])) ."</td>";

		// Builder Name
		echo "<td>". ucwords($builder['name']) ."</td>";

		// Websites
		echo "<td>";
		echo "<ul class='clr'>";
		foreach($builder['websites'] as $website) {
			echo "<li><a class='sort-link' target='_blank' href='{$website['link']}'>{$website['link']}</a></li>";
		}
		echo "</ul>";
		echo "</td>";

		// Last updated
		echo "<td style='width: 11%;'>". date("d/m/y", strtotime($builder['last_updated'])) ."</td>";

		// Staff ID
		echo "<td>". $all_staff_list[$builder['staff']] ."</td>";

		// Status
		echo "<td>". ((($builder['status']) == 1) ? "Active" : "Inactive") ."</td>";

		// Edit and delete link
		echo "<td style='width: 15%;'>";
		echo "<a class='sort-link' target='_blank' href='/webadmin/product_builder_new/builder/?builder_id=".$builder['id']."&preview'>Preview</a>";
		echo "<span style='margin: 0 5px;'></span>";

		if ($lock === false || $lock['staff_id'] == $_GLOBALS['staff']['id']) {
			echo "<a class='sort-link' target='_blank' href='/webadmin/product_builder_new/index.php?action=edit_builder&builder_id=".$builder['id']."&gid={$gid}'>Edit</a>";
			if ($lock !== false && $lock['staff_id'] == $_GLOBALS['staff']['id']) {
				echo "<span style='margin: 0 5px;'></span>";
				echo "<a class='sort-link' href='index.php?action=unlock_builder&gid={$gid}'>Unlock</a>";
			}
		} else {
			?><a onmouseover="<?= htmlspecialchars("tt_in(". json_encode($lock_tooltip) .");") ?>" onmouseout="tt_out();">Locked</a><?php
		}

		echo "</td>";

		echo "</tr>";
	}
	?>
</table>

<?php
$max = count($pb->getBuilders($id));
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
		echo " | <a href='viewbuilder.php?". http_build_query($get_vars) ."'>";
		echo ($i + 1) . " - " . (($max <= $limit) ? $max : ($i + $limit));
		echo "</a>";
	}
}

echo "</div>";
?>