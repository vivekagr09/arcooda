<?php 
/**
 *	Module: Product Builder
 *
 *	options_select.php
 *
 *	This page display all options to be selected.
 *	The multiple selection is allowed.
 *
 *	The javascript function needed: setOptionData(data)
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */
include_once "common.inc.php";
include_once "templates/option_header.tpl.php";

// Initiate Product Builder Module
$pb = new ProductBuilder($DB);

// Get all options
$options = $pb->getOptions();

// Serach
$name='';
if (isset($_GET['search_for']) && !empty($_GET['search_for'])) {
	$search_result = array();
	$name = strtolower($_GET['search_for']);
	foreach($options as $option) {
		if (strpos(strtolower($option['name']),$name) !== false) {
			$search_result[] = $option;
		}
	}

	$options = $search_result;
}
?>

<form action="" get="post">
	<table border="0" align="center" cellpadding="2" cellspacing="0" style="border:1px solid #FF9900;margin:1px auto;width:95%;text-align:center;">
		<tr><td style="background-color:#FFFFCC;color:#FF6600;font-size:16px;"><b>Search Options</b></td></tr>
		<tr><td style="background-color:#FFFFCC;">Enter <b>Option Name</b></td></tr>
		<tr><td style="background-color:#FFFFCC;">Search For: <input type="text" style="width:256px;" name="search_for" value="<?= $name ?>"></td></tr>
		<tr><td style="background-color:#FFFFCC;"><input type="submit" value="Search" ></td></tr>
	</table>
</form>

<br>
<div class="flagHeader"><span>Options List</span></div>
<br>

<?php
if (empty($options) === false) {
	echo "<table class='view-card-table'>";
	$col = 5;
	$_col = 0;
	foreach($options as $option) {
		if ($_col == 0) {
			echo "<tr>";
		}

		echo "<td style='width: 19%; border: 1px solid #c0c0c0;'>";
		echo "<table>";
		echo "<tr>";
		echo "<td><input type='checkbox' class='options-select-input' value='". $option['id'] ."' data-name='". ucwords($option['name']) ."'/></td>";
		echo "<td>". ucwords($option['name']) ."</td>";
		echo "</tr>";
		echo "</table>";
		echo "</td>";

		$_col++;

		if ($_col == $col) {
			echo "</tr>";
			$_col = 0;
		}
	}
	echo "</table>";
} else {
	echo "<div class='grid10' style='text-align:center;'>";
	echo "<span>No Result Found</span>";
	echo "</div>";
}
?>

<div style="text-align: center;" class="grid10">
<br>
<button type="button" onclick="selectValue();">Select</button>
<br>
</div>

<script type="text/javascript">
function selectValue() {
	var data = new Array();

	$('input.options-select-input:checked').each(function(){
		var optionVal = [$(this).val(), $(this).attr("data-name")];
		data.push(optionVal);
	});
	
	if (data.length > 0) {
		if(window.opener!=null && window.opener.setOptionData!=null)
			window.opener.setOptionData(data);
		else if(window.parent!=null && window.parent.setOptionData!=null)
			window.parent.setOptionData(data);
		else
			alert('Unable to send product back to form');
		window.close();
	} else {
		alert("Select the options.");
	}
}
</script>