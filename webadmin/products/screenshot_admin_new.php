<!--
Modified by Mingze X@ Feb 23, 2015;

Last Update: March 10, 2015

Update Log: 1. Screenshot upload user interface was updated.
			2. Upload function is fixed and ready to use.
			3. Removed "Active" tags, coinexpress does not have active status for screenshots...

Finished: 60%;

Unfinished Job: Edit

This file is used for coinexpress webadmin to upload products screenshots, based on screenshot_admin.php@COE

To increase the efficiency and make the UI more concise, less usefull sections were removed, e.g. search field, side nav bars etc.

 -->

<?php
$section_code = "PRD_CFG";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once __DIR__ . '/config.inc.php';

$action = strtolower($action);
$id     = intval($id);
?>

<script>

</script>
<?php
function showAddScreenshot($id) //action=add

{
?>
	<html>
		<head><title>Add Screenshot</title></head>

		<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<form enctype="multipart/form-data" action="" method="post">
				<input type="hidden" name="action" value="do_add">
				<input type="hidden" name="id" value="<?php echo $id ?>" />
				<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
					<tr>
						<td style="text-align:right;font: bold 14px verdana;"> Product ID:</td>
						<td style="text-align:left;"><input  type="text" name="part_id_cus" style="width:100px;" value="<?php hecho($id)?>"/></td>
					</tr>
					<tr>
						<td style="text-align:right;font: bold 14px verdana;">File:</td>
						<td style="text-align:left;">
                            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                            <input style="width:450px;font: 13px verdana;" type="file" name="image_file" accept="image" />
                        </td>
					</tr>
					<tr>
						<td style="text-align:right;font: bold 14px verdana;">Caption:</td>
						<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="text" name="image_caption"/></td>
					</tr>
					<tr>
						<td style="text-align:right;font: bold 14px verdana;">Screenshot Type:</td>
						<td style="text-align:left;">
<?php
$options = array(1 => 'Reference');
?>
							<select style="width:365px;font: 13px verdana;" name="ss_type" disabled>
								<?php echo formatHTMLOptions($options) ?>
							</select>
						</td>
					</tr>
					<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Attach Image"/></td></tr>
				</table>
	</body>
	</form>
	</html>
<?php
}
?>

<?php
function showEditScreenshot($ss_id) //action=edit

{
    global $DB, $table;
    $ss = $DB->query_first("SELECT * FROM $table WHERE ss_id = '$ss_id'");
?>
<html>
	<head><title>Edit Screenshot</title></head>
	<form action="screenshot_admin_new.php" method="post">
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
			<input type="hidden" name="action" value="do_edit" />
			<input type="hidden" name="ss_id" value="<?php echo $ss_id ?>" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Current Picture:</td>
					<td style="text-align:left;"> <input type="text" name="old_pic" size="40" value="<?php echo ($ss[filename]) ?>" style="width:400px" disabled><a href="/webadmin/gogetmemyimage.php?i=<?php echo $ss['filename'] ?>">Link</a></td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Caption:</td>
					<td style="text-align:left;">
						<input type="text" name="part_ss_cap" size="40" style="width:400px" value="<?php echo ($ss[caption]) ?>">
					</td>
				</tr>
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Screenshot Type:</td>
					<td style="text-align:left;">

					      <select name="ss_type" style="width:400px">
<?php
$result = $DB->query("SELECT * FROM screenshot_type ORDER BY type ASC");

    while ($row = $DB->fetch_array($result)) {
        echo '<option value="' . $row[id] . '"';
        if ($ss[ss_type] == $row[id]) {
            echo " selected ";
        }

        echo '>' . stripslashes($row[type]) . '</option>';
    }

?>
					      </select>

					</td>
				</tr>
				<!--tr>
					<td style="text-align:right;font: bold 14px verdana;">Active :</td>
					<td style="text-align:left;"><select style="width:365px;font: 13px verdana;" name="active">
						<?php echo formatHTMLOptions(array('y' => 'Yes', 'n' => 'No'), $shot['active']) ?>
					</select></td>
				</tr-->
				<tr>
				    <td nowrap align="right"><b>Rank :</b></td>
				    <td>
				      <input type="text" name="rank" size="40" value="<?php echo $ss['rank'] ?>">
				    </td>
				</tr>
				<tr>
				    <td nowrap>&nbsp;</td>
				    <td>
				      <p>&nbsp;</p>
				      <p>
						<input type="hidden" name="ss_id" value="<?php echo $ss_id ?>">
						<input type="hidden" name="action" value="do_edit">
				        <input type="submit" name="add" value="Update">
				        <input type="reset" name="clear" value="Clear">
				      </p>
				    </td>
				  </tr>

			</table>
</body>
</form>
</html>
<?php
}

function edit_ss($posted) //action=do_edit

{
    global $DB, $part_ss, $part_ss_cap, $ss_id, $img_location, $table, $ss_type, $rank;

    $part = $DB->query_first("SELECT id,rank FROM product_screenshots WHERE ss_id = '$ss_id'");

    $id = $part['id'];

    $statement       = "SELECT t.img_location FROM product_types t, products p WHERE t.id = p.type AND p.id = '$id'";
    $product_details = $DB->query_first($statement);

    $img_location     = $product_details['img_location'] . "/";
    $img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

    $img_clause = '';
    if (file_exists($part_ss) && is_file($part_ss)) {
        $img_file = process_image($part_ss, find_ss_count($part[id]), 200, 200, $img_location_abs);
        if ($img_file) {
            purge_ss_cache_entry($ss_id, $id);
            $img_clause = "filename = '$img_location$img_file', ";
        }

    }
    $temp = $DB->query_first("SELECT MAX(rank) AS max_rank FROM product_screenshots WHERE id=$id");
    // check if a valid rank is specified
    if ($rank < 0) {
        echo htmlspecialchars("Invalid rank specified. rank must be >= 0");
        return;
    }
    $sql         = "SELECT COUNT(*) FROM product_screenshots WHERE id=$id AND rank=$rank";
    $result      = $DB->query($sql);
    $currentRank = $part['rank'];
    // swap ranks with previous rank holder
    if ($DB->num_rows($result) > 0 && $currentRank != $rank) {
        $sql = "UPDATE product_screenshots SET rank=$currentRank WHERE id=$id AND rank=$rank";
        $DB->query($sql);
    }
    $statement = "UPDATE product_screenshots SET
					$img_clause
					ss_type = '$ss_type',
					caption='" . addslashes(htmlspecialchars($part_ss_cap)) . "',
					rank = '" . intval($rank) . "'
				WHERE ss_id = '$ss_id'";

    $DB->query($statement);
?>

	<html>
		<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
			<table align="center" style="width:50%;border:1px solid blue;background-color:#99CCFF;">
			<tr>
				<td style="text-align:center;"><b>Screenshot for Product ID #<?php echo $id ?> (#<?php echo $ss_id ?>)Was Successfully Added.</b></td>
			</tr>
			<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.close();" /></td></tr>
			</table>
		</body>
	</html>
<?php
//echo "SS_ID: $ss_id updated<br>";
}

?>


<?php
function addScreenshot($posted) //action=do_add

{
    global $DB, $part_id_cus, $image_file, $image_caption, $img_location, $ss_type;
    global $table;

    if ('' != $part_id_cus) {
        $statement = "SELECT COUNT(id) as ids FROM products WHERE id = '" . intval($part_id_cus) . "'";
        $ids       = $DB->query_first($statement);
        if ($ids[ids] > 0) {
            $id = $part_id_cus;
        }
    }

    //??? purge_ss_cache_entry($ss_id, $id);

    $statement = "SELECT product_types.img_location FROM product_types, products WHERE product_types.id = products.type AND products.id = '$id'";

    $product_details = $DB->query_first($statement);

    $img_location     = $product_details['img_location'] . "/";
    $img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location; //absolute image location

    $temp    = $DB->query_first("SELECT MAX(rank) FROM product_screenshots WHERE id='$id'");
    $maxRank = intval($temp[0]);

    $source_file = $image_file;

    $img_file = process_image($source_file, find_ss_count($id), 200, 200, $img_location_abs);

    if ($img_file) {
        $statement = "INSERT INTO $table (
						id,
						filename,
						caption,
						ss_type,
						rank,
						date_added)
					VALUES (
						'$id',
						'$img_location$img_file',
						'" . addslashes(htmlspecialchars($image_caption)) . "',
						'$ss_type',
						'" . ++$maxRank . "',
						NOW())";

        $DB->query($statement);
?>
	<html>
		<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
			<table align="center" style="width:50%;border:1px solid blue;background-color:#99CCFF;">
			<tr>
				<td style="text-align:center;"><b>Screenshot for Product ID #<?php echo $id ?> Was Successfully Added.</b></td>
			</tr>
			<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.close();" /></td></tr>
			</table>
		</body>
	</html>
<?php
} else {
?>
		<html>
		<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
			<table align="center" style="width:50%;border:1px solid blue;background-color:#99CCFF;">
			<tr>
				<td style="text-align:center;"><b>Something Went Wrong, Screenshot Was NOT Successfully Added.</b></td>
			</tr>
			<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.close();" /></td></tr>
			</table>
		</body>
	</html>
<?php
}
}
?>

<?php
function find_ss_count($id)
{
    global $DB, $img_location, $table;

    $statement = "SELECT filename FROM product_screenshots WHERE id = '$id'";
    $ss_count  = $DB->query($statement);

    while ($row = $DB->fetch_array($ss_count)) {
        $files[] = $row[filename];
    }

    if (!is_array($files) || count($files) <= 0) {
        return 'ss_' . $id . '_1';
    }

    $count   = count($files);
    $test_id = 'ss_' . $id . '_' . $count;

    while (eregi_in_array($img_location . $test_id . '.', $files)) {
        $count++;
        $test_id = 'ss_' . $id . '_' . $count;
    }

    return $test_id;
}
?>

<?php
//===========================================================================
// search an array value using eregi
//
// PARAMS: the needle and the array
// RETURN: true if found, false otherwise
//===========================================================================
function eregi_in_array($needle, $array)
{
    if (!is_array($array)) {
        return preg_match("/" . quotemeta($needle) . "/i", $array);
    }

    for ($i = 0; $i < count($array); $i++) {
        if (eregi_in_array($needle, $array[$i])) {
            return true;
        }
    }
}
?>

<?php
function search_parts($query)
{
    global $DB, $at, $fields, $order, $by;
    global $script, $table, $search_fields;

    $query = trim($query);

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words
    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        $word = str_replace('(', '\\\(', $word);
        $word = str_replace(')', '\\\)', $word);

        if (!empty($word) && '|' != $word) {
            $extra .= " AND ";

            //do a regular expression search on each of the search fields, case insensitive
            for ($i = 0; $i < count($search_fields); $i++) {
                $extra .= 0 == $i ? '(' : ' OR ';
                $extra .= " LOWER($search_fields[$i]) REGEXP \"$word\" ";
            }
            if (count($search_fields) > 0) {
                $extra .= ')';
            }

        }
    } //end search query keywords

    //search statement
    $statement = "SELECT s.*, p.product_name FROM product_screenshots s, products p WHERE p.id = s.id ";

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 25;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'desc';
    }

    if (!isset($by)) {
        $by = key($fields);
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    //echo $statement.$extra;
    $result = $DB->query($statement . $extra . $order_by . $limit);

    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));

    draw_header_row();
    $shaded = true;
    //echo "<pre>".$statement.$extra.$order_by.$limit."</pre>";
    while ($row = $DB->fetch_array($result)) {
        show_result($row, $shaded = !$shaded);
    }
    draw_header_end();
    //show more links
    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));
}

?>

<?php

function delete_ss()
{
    global $ss_id, $DB;

    //$part = $DB->query_first("SELECT id,rank FROM product_screenshots WHERE ss_id = '$ss_id'");
    //purge_ss_cache_entry($ss_id, $part['id']);

    $statement = "DELETE FROM product_screenshots WHERE ss_id = '$ss_id'";
    $DB->query($statement);
?>
	<html>
	<head></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:50%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Screenshot for Product ID #<?php echo $id ?> (#<?php echo $ss_id ?>) Successfully Removed.</b></td></tr>
	<tr><td align="center"><input type="button" value="Close Window" onclick="javascript:window.close();" /></td></tr>
	</table>
	</body>
</html>
<?php

}

?>


<?php
switch ($action) {
    case "add":
        showAddScreenshot($id);
        break;
    case "do_add":
        addScreenshot($posted);
        break;
    case 'edit':
        showEditScreenshot($ss_id);
        break;
    case 'do_edit':
        edit_ss($posted);
        break;
    case "delete":
        delete_ss();
        break;
    case "view":
        break;
    case "view_product":
        break;
    case "":
    default:
        die("Invalid Selection!!");
}
?>