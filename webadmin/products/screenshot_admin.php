<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

/*
admin_save.php:       screenshot_admin.php?action=add&id=$id
admin.php:            screenshot_admin.php?action=add&id=$id
admin_new.php:        screenshot_admin.php?action=add&id=$id
search_products.php:  screenshot_admin.php?action=add&id=$id

edit_form.inc.php:    screenshot_admin.php?action=add&id=$id
edit_form.inc.php:    screenshot_admin.php?popup&action=add&id=$id
edit_form.inc.php:    screenshot_admin.php?popup&action=edit&ss_id=$ss_id&id=$id
edit_form.inc.php:    screenshot_admin.php?popup&action=delete&ss_id=$ss_id
screenshot_admin.php: screenshot_admin.php<?= $gets ?>
edit_form.inc.php:    screenshot_admin.php?query=$id
screenshot_admin.php: screenshot_admin.php

/webadmin/header.inc.php:       /webadmin/products/screenshot_admin.php
/webadmin/HEADER.INC.PHP:       /webadmin/products/screenshot_admin.php
/webadmin/header_old.inc.php:   /webadmin/products/screenshot_admin.php
/webadmin/header.inc.php.prebb: /webadmin/products/screenshot_admin.php
 */
$section_code = "PRD_CFG";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once __DIR__ . '/config.inc.php';

// Clean out the screenshots
if ("purge_ss" == $_REQUEST['action']) {
    if ($_REQUEST['ss'] && $_REQUEST['p']) {
        purgeSSCacheEntry($_REQUEST['ss'], $_REQUEST['p']);
    }
    die("Goodbye.");
}

$action = $_REQUEST['action'];
$id     = $_REQUEST['id'];
$ss_id  = $_REQUEST['ss_id'];
$query  = $_REQUEST['query'];

$fields = array(
    'ss_id'        => 'center',
    'product_name' => 'left',
    'caption'      => 'left',
    'rank'         => 'center',
    'filename'     => 'left',
);

$product_details = $DB->query_first(sprintf(
    "SELECT `t`.`img_location` FROM `products` AS `p` INNER JOIN `product_types` AS `t` ON `p`.`type` = `t`.`id` WHERE `p`.`id` = %d LIMIT 1",
    $id
), MYSQLI_ASSOC);

$img_location     = $product_details['img_location'] . '/';
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM `product_screenshots`", MYSQLI_ASSOC);

?>
<!-- parts screenshot admin -->
<form action="?<?=htmlspecialchars(http_build_query($_GET))?>" method="post" enctype="multipart/form-data">
<?php
if (!array_key_exists('popup', $_REQUEST)) {
?>
	<br />
	<table width="100%" border="0" cellspacing="2" cellpadding="2"><tr>
		<td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Products Screenshots</b></div></td>
		<td bgcolor="#EEEEEE" width="80%"><div align="right">
			<a href="?action=add">Add New Screenshots</a> |
			<a href="?query=">List All Screenshots (<?=$items['total']?>)</a>
		</div></td>
	</tr></table>
	<!-- search box -->
	<p align="right">
		<input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($_REQUEST["query"]))?>" />
		<input type="submit" name="search" value="Search" />
	</p>
	<!-- //search box -->
<?php
}

function purgeSSCacheEntry($ss_id, $pid)
{
    // Kill cache entries
    $fname = sys_get_temp_dir() . "/image.pid-" . $pid . ".ssid-" . $ss_id . ".wmp-?.thumb-?.*";
    // Unlink anything that matches
    foreach (glob($fname) as $fn) {
        echo "removing cache entry: " . $fn . "<br />\n";
        unlink($fn);
    }
}

function searchParts($search_for)
{
    global $DB, $fields;

    $where = '';
    if (array_key_exists('search_for', $_REQUEST)) {
        $whereAND = array();
        foreach (preg_split('/\\s+/', trim($search_for)) as $index => $word) {
            $whereOR = array();
            foreach (array(
                '`ss_id`',
                '`product_name`',
                '`caption`',
                '`filename`',
            ) as $field) {
                $whereOR[] = $field . " LIKE " . formatSQLLikeString($word);
            }
            $whereAND[] = '(' . implode(' OR ', $whereOR) . ')';
        }
        $where = implode(' AND ', $whereAND);
    }

    $statement = sprintf(
        "SELECT `s`.*, `p`.`product_name` FROM `product_screenshots` AS `s` LEFT JOIN `products` AS `p` ON `s`.`id` = `p`.`id`%s",
        ($where) ? (' WHERE ' . $where) : ''
    );

    $by = 'ss_id';
    if (array_key_exists('by', $_REQUEST) && array_key_exists($_REQUEST['by'], $fields)) {
        $by = $_REQUEST['by'];
    }

    $order = 'DESC';
    if (array_key_exists('order', $_REQUEST) && 'ASC' == $_REQUEST['order']) {
        $order = $_REQUEST['order'];
    }

    $inc = 25;

    $at = 0;
    if (array_key_exists('at', $_REQUEST)) {
        $at = intval($_REQUEST['at']) * $inc;
    }

    $query = $DB->query($statement);
    $max   = $DB->num_rows($query);
    $DB->free_result($query);

    show_links(get_links($max, "?search_for=" . urlencode($search_for) . "&by=$by&order=$order", 25, 4));
    drawHeaderRow();

    $query = $DB->query(sprintf(
        "%s ORDER BY %s %s LIMIT %d, %d",
        $statement,
        $by,
        $order,
        $at,
        $inc
    ));
    $shaded = true;
    while ($row = $DB->fetch_assoc($query)) {
        showResult($row, $shaded = !$shaded);
    }
    $DB->free_result($query);

    drawHeaderEnd();
    show_links(get_links($max, "?search_for=" . urlencode($search_for) . "&by=$by&order=$order", 25, 4));
}

function drawHeaderRow()
{
    global $fields;
?>
<br />
<table class="table_grid">
    <tr>
<?php
foreach ($fields as $field => $align) {
?>
        <th <?=($_REQUEST['by'] == $field) ? ' bgcolor="#336699"' : ''?>>
            <a href="?search_for=<?=urlencode($_REQUEST["search_for"])?>&amp;by=<?=$field?>&amp;order=<?=("ASC" == $_REQUEST['order']) ? "DESC" : "ASC"?>" class="td_options"><?=htmlspecialchars($field)?></a>
            <?=($_REQUEST['by'] == $field) ? ('<img src="/webadmin/' . (("ASC" == $_REQUEST['order']) ? "asc" : "desc") . '.gif" width="7" height="7" border="0" />') : ''?>
        </th>
<?php
}
?>
        <th>&nbsp;</th>
    </tr>
<?php
}

function drawHeaderEnd()
{
?>
</table>
<br />
<?php
}

function showResult($cols, $shaded)
{
    global $fields;
?>
    <tr<?=($shaded) ? ' class="alt"' : ''?>>
<?php
foreach ($fields as $field => $align) {
?>
        <td style="text-align:<?=$align?>;"><?=htmlspecialchars($cols[$field])?></td>
<?php
}
?>
        <td style="white-space:nowrap;">
            <a href="?action=edit&amp;ss_id=<?=$cols['ss_id']?>&amp;id=<?=$cols['id']?>">Edit</a> |
            <a href="?action=delete&amp;ss_id=<?=$cols['ss_id']?>">Delete</a>
        </td>
    </tr>
<?php
}

function addNewSS()
{
    global $DB;

    $part_id     = $_POST['part_id'];
    $part_id_cus = $_POST['part_id_cus'];

    $part_ss     = $_FILES['part_ss']['tmp_name'];
    $part_ss_cap = $_POST['part_ss_cap'];
    $ss_type     = $_POST['ss_type'];

    if ('' != $part_id_cus) {
        $ids = $DB->query_first(sprintf(
            "SELECT COUNT(`id`) AS `ids` FROM `products` WHERE `id` = %d",
            $part_id_cus
        ), MYSQLI_ASSOC);
        if ($ids['ids'] > 0) {
            $id = $part_id_cus;
        }
    }
    $id = $part_id;

    $row = $DB->query_first(sprintf(
        "SELECT `t`.`img_location` FROM `products` AS `p` INNER JOIN `product_types` AS `t` ON `p`.`type` = `t`.`id` WHERE `p`.`id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC);

    $img_location     = $row['img_location'] . "/";
    $img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

    $row = $DB->query_first(sprintf(
        "SELECT MAX(`rank`) AS `rank` FROM `product_screenshots` WHERE `id` = %d",
        $id
    ), MYSQLI_ASSOC);
    $maxRank = intval($row['rank']);

    foreach ($part_ss as $key => $source_file) {
        if (file_exists($source_file) && is_file($source_file)) {
            $img_file = process_image($source_file, findSSCount($id), 200, 200, $img_location_abs);
            if ($img_file) {
                $DB->query(sprintf(
                    "INSERT INTO `product_screenshots` (`id`, `filename`, `caption`, `ss_type`, `rank`, `date_added`) VALUES (%d, %s, %s, %s, %d, NOW())",
                    $id,
                    formatSQLString($img_location . $img_file),
                    formatSQLString($part_ss_cap[$key]),
                    formatSQLString($ss_type[$key]),
                    ++$maxRank
                ));
            } else {
                echo "Something went wrong...";
            }
        }
        next($part_ss);
    }
    if (isset($_GET['popup'])) {
        echo "<button onclick='window.close(); window.onunload=window.opener.location.reload();'>Close this window</button>";
    }
}

function findSSCount($id)
{
    global $DB, $img_location;

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

    while (eregiInArray($img_location . $test_id . '.', $files)) {
        $count++;
        $test_id = 'ss_' . $id . '_' . $count;
    }

    return $test_id;
}

//===========================================================================
// search an array value using eregi
//
// PARAMS: the needle and the array
// RETURN: true if found, false otherwise
//===========================================================================
function eregiInArray($needle, $array)
{
    if (!is_array($array)) {
        return preg_match("/" . quotemeta($needle) . "/i", $array);
    }

    for ($i = 0; $i < count($array); $i++) {
        if (eregiInArray($needle, $array[$i])) {
            return true;
        }
    }
}

function editSS()
{
    global $DB, $part_ss, $part_ss_cap, $ss_id, $img_location, $ss_type, $rank;

    $part = $DB->query_first("SELECT id,rank FROM product_screenshots WHERE ss_id = '$ss_id'");

    $id = $part['id'];

    $statement       = "SELECT t.img_location FROM product_types t, products p WHERE t.id = p.type AND p.id = '$id'";
    $product_details = $DB->query_first($statement);

    $img_location     = $product_details['img_location'] . "/";
    $img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

    $img_clause = '';
    if (file_exists($part_ss) && is_file($part_ss)) {
        $img_file = process_image($part_ss, findSSCount($part[id]), 200, 200, $img_location_abs);
        if ($img_file) {
            purgeSSCacheEntry($ss_id, $id);
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
    echo "SS_ID: $ss_id updated<br>";

    if (isset($_GET['popup'])) {
        echo "<button onclick='window.close(); window.onunload=window.opener.location.reload();'>Close this window</button>";
    }
}

function deleteSS()
{
    global $ss_id, $DB;

    $part = $DB->query_first("SELECT id,rank FROM product_screenshots WHERE ss_id = '$ss_id'");
    purgeSSCacheEntry($ss_id, $part['id']);

    $statement = "DELETE FROM `product_screenshots` WHERE ss_id = '$ss_id'";
    $DB->query($statement);
    echo "Screenshot $id deleted.<br>";

    if (isset($_GET['popup'])) {
        echo "<button onclick='window.close(); window.onunload=window.opener.location.reload();'>Close this window</button>";
    }

}

switch ($action) {
    case "do_add":
        addNewSS();
        break;
    case "do_edit":
        editSS();
        break;
    case "do_delete":
        deleteSS();
        break;
    case "add":
        $id = $_GET['id'];
        include __DIR__ . '/ss_add_form.inc.php';
        break;
    case "edit":
        $ss_id = $_GET['ss_id'];
        include __DIR__ . '/ss_edit_form.inc.php';
        break;
    case "delete":
        $ss_id = $_GET['ss_id'];
        include __DIR__ . '/ss_delete_form.inc.php';
        break;
    default:
        searchParts($_REQUEST["query"]);
        break;
}

?>
</form>

<!--//parts screenshot admin -->
<?php
include_once __DIR__ . '/footer.inc.php';
