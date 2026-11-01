<?php
include_once __DIR__ . "/header.inc.php";
?>
<script type="text/javascript">
//<![CDATA[
function conf()
{
	return confirm('Are you sure you to delete the Seaport selected');
}
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
function pviiClassNew(obj, new_style) { //v2.6 by PVII
  obj.className=new_style;
}
//]]>
</script>
<?php
include_once 'top.seaport.php'; // the header file specified to this section..

$fields['id'] = "center";
$fields['name'] = "center";
$fields['country'] = "center";

function add($posted)
{
	global $DB, $data, $user, $num;
		$DB->query("INSERT INTO `world`.`seaports` (`name`, `country`) VALUES (" . $DB->escape($posted['num']) . ", " . $DB->escape($posted['data']['country']) . ")");
		$id = $DB->insert_id();

	echo "New Seaport(s) Added<br>";
}

/***********************************************************************************************/
// This function edits a section, which is already present in the database..
/***********************************************************************************************/
function edit($posted)
{
	global $DB, $data, $user, $id;
	$DB->query("UPDATE `world`.`seaports` SET " . get_set_values($posted['data']) . " WHERE `id` = " . intval($posted ['id']) . " LIMIT 1");
	echo "Seaport Id ".$posted['id']." updated<br>";
}

/***********************************************************************************************/
// This function deletes a section, which is already present in the database..
/***********************************************************************************************/
function delete_value()
{
	global $DB, $data, $user, $id;
	$DB->query("DELETE FROM `world`.`seaports` WHERE `id` = " . intval($id) . " LIMIT 1");
	echo "Seaport Id $id deleted<br>";
}

/**********************************************************************************************/
// This function gets the data from the database and displays it.
/**********************************************************************************************/
function view()
{
	global $DB, $by, $order, $print, $fields, $start;
	if ($by == "") $by = "country";
	if ($order == "") $order = "asc";
	$orderby = 'ORDER BY ';
	($by == 'country') ? $orderby .= "c.name $order" : $orderby .= "s.$by $order";
	if (strcmp($start, '') == 0) $start = 0;
	$limit = 25; // max display per page..
	settype($limit, "float");
	display_letter();
	draw_header();
	$shaded = true;
	$result = $DB->query("SELECT `c`.`name` AS `country_full`, `s`.* FROM `world`.`seaports` AS `s` LEFT JOIN `world`.`country` AS `c` ON `s`.`country` = `c`.`code` " . $orderby . " LIMIT " . $start . ", " . $limit);
	$id = '';
	while ($row = $DB->fetch_array($result))
	{
		$id = $row['id'];
		// print each of the rows of the array..
		if (!$shaded)
		{
			echo "<tr class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">";
			$shaded = !$shaded;
		}
		else
		{
			echo "<tr class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
			$shaded = !$shaded;
		}
		echo '<td align="center">' . $row['id'] . '</td>';
		echo '<td align="center">' . $row['name'] . '</td>';
		echo '<td align="center">' . $row['country_full'] . '</td>';
		echo '<td align="center"><a href="seaports.php?action=edit&id=' . $id . '">Edit</td>';
		echo '<td align="center"><a href="seaports.php?action=delete&id=' . $id . '" onClick="return conf()">Delete</a></td>';
	}
	echo '</tr></table><br><br>';
	display_entries($start, $limit);
}

function show_alpha($letter)
{
	global $DB, $order, $by;
	if($by == '')
		$by = 'country';
	if($order == '')
		$order = 'asc';
	$orderby = 'ORDER BY ';
	($by == 'country') ? $orderby .= "c.name $order" : $orderby .= "s.$by $order";
	$qry = "SELECT c.name as country_full, s.* FROM `world`.`country` c, `world`.`seaports` s WHERE (c.name LIKE '$letter%' AND s.country = c.code) $orderby";
	$res = $DB->query($qry);
	display_letter();
	//display_entries($start,$limit);
	draw_header();
	$shaded = true;
	while($row = $DB->fetch_array($res)){
		$id = $row['id'];
		// print each of the rows of the array..
		if(!$shaded)
			echo "<tr class=\"TableRow\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRow')\">";
		else
			echo "<tr class=\"TableRowAlt\" onMouseOver=\"pviiClassNew(this,'TableRowOver')\" onMouseOut=\"pviiClassNew(this,'TableRowAlt')\">\n";
		$shaded = !$shaded;
		echo '<td align="center">'.$row['id'].'</td>';
		echo '<td align="center">'.$row['name'].'</td>';
		echo '<td align="center">'.$row['country_full'].'</td>';
		echo '<td align="center"><a href="seaports.php?action=edit&id='.$id.'">Edit</td>';
		echo '<td align="center"><a href="seaports.php?action=delete&id='.$id.'" onClick="return conf()">Delete</a></td>';
	}
}

/**********************************************************************************************/
// This function draws the header for the display of the sections.
/**********************************************************************************************/
function draw_header()
{
	global $fields, $order, $by, $query, $max, $at, $page, $script;
	global $status, $website, $print, $action, $value;
	$link = "website=$website&status=$status";
	echo '<br><table align="center" cellpadding="4" cellspacing="1">';
	echo '<tr bgcolor="#3399FF">';
	for ($i = 0; $i < sizeof($fields); $i++)
	{
		$field = key($fields);
		echo '<td align="center"' . ($by == $field ? ' bgcolor="#FF9900"' : '') . '><b>';
		echo "<a href=\"$script?query=" . urlencode($query) . "&max=$max&action=$action&value=$value&at=$at&page=$page";
		echo "&$link&by=$field&order=" . ($order == "desc" ? "asc" : "desc") . '" class="td_options">';
		echo ucwords(str_replace('_', ' ', $field));
		echo '</a>';
		if ($by == $field) echo '  <img src="../../' . $order . '.gif" width="7" height="7" border="0">  ';
		echo '</b></td>';
		next($fields);
	}
}

function display_letter()
{
	global $DB;
	echo '<table width=95% align=center cellpadding=7><td bgcolor=#FAFAFA><div align="center"><b>By Country Letter :</b> ';
	for ($i = A; ; $i++) {
		$qry = "SELECT * FROM `world`.`seaports`, `world`.`country` WHERE (country.name LIKE '$i%' AND seaports.country = country.code)";
		$res = $DB->query($qry);
		if($DB->num_rows($res) > 0)
			echo '<a href="?action=alpha&value='.$i.'">'.$i.'</a>&nbsp;';
		else
			echo '<font color="#999999">'.$i.'</font>&nbsp;';
		if ($i == "Z")break;
	}
	echo '</div></td></table>';
}

/***********************************************************************************************/
// This function draws the header for the total number of entries present in the database
/***********************************************************************************************/
function display_entries($start, $limit)
{
	global $DB, $fields, $order, $by, $query, $max, $at, $page, $script, $status, $website, $print, $action;
	$res = $DB->query_first("SELECT COUNT(*) FROM `world`.`seaports`");
	$pages = '';
	$count = $res[0];
	$limit = number_format($limit, 0, '.', '');
	$count = number_format($count, 0, '.', '');
	$pages = floor($count / $limit);
	if ($count != 0)
	{
		if ($count < $limit) echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' | </b>';
		else echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($start + $limit) . ' of ' . $count . ' | </b>';
	}
	$orig = $start;
	$new = 0;
	for ($i = 0; $i <= $pages; $i++)
	{
		if ($new == 0 && $orig == 0 || $orig == $new)
		{
			if ($count > $limit) echo '<b>' . ($start + 1) . ' - ' . ($start + $limit) . ' | </b>';
			$new = $start + $limit;
		}
		else
		{
			$link = "action=$action&start=$new&by=$by&order=$order";
			echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($new + $limit) . '</a> | ';
			$new+= $limit;
		}
	}
	echo '</center>';
}

echo '<link rel="stylesheet" type="text/css" href="../style.css" />';
$action = $_REQUEST['action'];
$value = $_REQUEST['value'];
switch ($action)
{
case "view":
	view();
	break;

case "add":
	include 'add_seaport.php';
	break;

case "edit":
	include 'edit_seaport.php';
	break;

case "do_add":
	add($posted);
	break;

case "do_edit":
	edit($posted);
	break;

case "delete":
	delete_value();
	break;

case "alpha":
	show_alpha($value);
	break;

case "":
	view();
	break;
}
?>