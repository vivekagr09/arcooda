<?php
/**
 *	Module: Product Builder
 *
 *	search_options.php
 *
 *	This page is to search option by option name
 *	
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */

include_once "common.inc.php";
include_once "templates/option_header.tpl.php";

$options = null;

if (isset($posted['query']) === true && empty($posted['query']) === false) {
    $pb = new ProductBuilder($DB);
    $options = $pb->searchOptionsByName($posted['query']);
}
?>

<br>
<br>
<form action="" method="get">
<table class="wide" align="center" style="border:1px solid #FF9900;margin:1px auto;width:95%" border="0" cellpadding="2" cellspacing="0">
	<tbody><tr>
		<td bgcolor="#FFFFCC">
			<div align="center"><strong><font color="#FF6600" size="4">Option Search</font></strong></div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC"><div align="center">Enter an <b>Option Name</b></div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC">
			<div align="center">
				<input type="text" name="query" size="30" value="">
				<input type="submit" value="Search">
			</div>
		</td>
	</tr>
</tbody></table>
</form>

<?php if ($options !== null) { ?>

<br>
<div class="flagHeader"><span>Search Result</span></div>
<br>

<?php if (empty($options) === false && count($options) > 0) { ?>

<table class="view-table">
	<tr>
		<th>
			<span>Date Added</span>
		</th>

		<th>
			<span>Option Name</span>
		</th>

		<th></th>
	</tr>

	<?php
	foreach($options as $option) {
		echo "<tr>";
		// Added Date
		echo "<td style='width: 10%;'>". date("d/m/y", strtotime($option['added_date'])) ."</td>";

		// Option name
		echo "<td>". ucwords($option['name']) ."</td>";

		// Edit and delete link
		echo "<td style='width: 10%;'>";
		echo "<a href='index.php?action=edit_option&option_id={$option['id']}' class='sort-link'>Edit</a>";
		echo "<span style='margin: 0 5px;'></span>";
		echo "<a class='sort-link' href='confirmation.php?section=option&id={$option['id']}'>Delete</a>";
		echo "</td>";

		echo "</tr>";
	}
	?>
</table>

<?php } else {?>
<p style='color: red; text-align: center;'><i>No Result found.</i></p>
<?php } ?>

<?php } //END IF ?>