<?php
$section_code = "PRD_CFG";
include_once __DIR__ . '/common.inc.php';

if ("product_search" != $action) {
	include_once __DIR__ . '/header.inc.php';
}

include 'config.inc.php';
include '../new_products/functions/query_products.inc.php';
include 'query_attachment.inc.php';

$attachment_dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products/attachments';

include_once 'query_attachment.inc.php';

//$attachment_dir='/home/hwent/public_html/webadmin/manuals';

$item = $DB->query_first("SELECT COUNT(*) as total FROM product_attachments");
?>
<!-- parts screenshot admin -->
<?php
if ("product_search" != $action) {
?>
<form action="" method="post">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>HGR Manuals</b></div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<!--<a href="?action=add">Add New Manual</a> | -->
			<a href="?action=search">Search</a> |
			<a href="?action=summary">View Summary</a> |
			<a href="?action=view">View All (<?php echo $item['total'] ?>)</a>
			</div>
          </td>
        </tr>
      </table>
<?php
}
//By Item Type
$sql = " SELECT" .
    "	pt.id" .
    "	pt.name," .
    "	COUNT(pa.attachment_id) AS count" .
    " FROM product_attachments pa" .
    " INNER JOIN products p ON p.id=pa.product_id" .
    " INNER JOIN product_types pt ON pt.id=p.type" .
    " GROUP BY p.type";

//displayByProductType(getProductTypeHierarchy(),0);

$fields['id']             = 'center';
$fields['product_name']   = 'center';
$fields['type_of_manual'] = 'center';
$fields['notes']          = 'center';
$fields['file_type']      = 'center';
$fields['size']           = 'center';
$fields['uploaded_by']    = 'center';
$fields['date_added']     = 'center';
$fields['']               = 'center';

/*function displayByProductType($item,$key=0)
{
global $DB;
if(is_array($item))
{
$row=$DB->query_first("SELECT name FROM product_types WHERE id=".$key);
 */
?>
<!--<i><?php echo str_replace('<', '&lt;', $row['name']) ?></i><br />
<div style="padding:0px 0px 0px 16px;">-->
<?php
//        array_walk($item,'displayByProductType');
?>
<!--</div>-->
<?php
/*    }
else
{
$sql=    " SELECT".
"    pt.name,".
"    COUNT(pa.attachment_id) AS count".
" FROM product_attachments pa".
" INNER JOIN products p ON p.id=pa.product_id".
" INNER JOIN product_types pt ON pt.id=p.type".
" WHERE p.type=".$item.
" GROUP BY p.type";
$row=$DB->query_first($sql);
$total+=$row['count'];
if($row) echo '<a href="attachment_admin.php?parent='.$item.'">'.$row['name'].' ('.$row['count'].')</a><br />';
}
}

function cleanupHierarchy(&$item,$key=0)
{
if(is_array($item))
if(count($item)==0)
$item=$key;
else
array_walk($item,'cleanupHierarchy');
}

function getProductTypeHierarchy()
{
global $DB;
$sql="SELECT id,name FROM product_types";
$product_types=array();
$query=$DB->query($sql);
while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
$product_types[$row['id']]=$row['name'];

//Build a hierarchy
$hierarchy=array();
foreach($product_types as $type_id=>$type_name)
{
$child=array();
while($type_id!=0 && !in_array($type_id,$child))
{
$child[]=$type_id;
$row=$DB->query_first("SELECT parent FROM product_types WHERE id=".$type_id);
$type_id=$row['parent'];
}
$child=&array_reverse($child);

$test=&$hierarchy;
foreach($child as $key)
{
if(!isset($test[$key])) $test[$key]=array();
$test=&$test[$key];
}
}

cleanupHierarchy($hierarchy);
return $hierarchy;
}

function purge_ss_cache_entry($ss_id, $pid)
{
// Kill cache entries
$fname = $_SERVER['DOCUMENT_ROOT'] . "/../img_cache/image.pid-$pid.ssid-$ss_id.wmp-?.thumb-?.*";
// Unlink anything that matches
foreach(glob($fname) as $fn)
{
echo "removing cache entry: $fn<br />\r\n";
unlink($fn);
}
}

function search_parts($query)
{
global $DB, $at, $fields, $order, $by;
global $script, $table, $search_fields;

$query = trim($query);

//keywords
$arrKeywords = explode(" ",$query);

//search query by key words
for ($index = 0;$index < count($arrKeywords); $index++)
{
$word = trim(strtolower($arrKeywords[$index]));

if (!empty($word) && $word != '|')
{
$extra .= " AND ";

//do a regular expression search on each of the search fields, case insensitive
for ($i = 0; $i < count($search_fields); $i++)
{
$extra .= $i == 0? '(':' OR ';
$extra .= " LOWER($search_fields[$i]) REGEXP \"$word\" ";
}
if (count($search_fields) > 0)
$extra .= ')';
}
}//end search query keywords

//search statement
$statement = "SELECT s.*, p.product_name FROM product_screenshots s, products p WHERE p.id = s.id ";

//if max is not yet set
if (!isset($max))
{
$result = $DB->query($statement.$extra);
$max = $DB->num_rows($result);
}

$inc = 25;
$at = intval($at);

if (!isset($order))
$order = 'desc';

if (!isset($by))
$by = key($fields);

$order_by = " ORDER BY $by $order";
$limit = " LIMIT $at, $inc ";
//echo $statement.$extra;
$result = $DB->query($statement.$extra.$order_by.$limit);

show_links(get_links($max, "$script&query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));

draw_header_row();
$shaded = true;
//echo "<pre>".$statement.$extra.$order_by.$limit."</pre>";
while ($row = $DB->fetch_array($result))
{
show_result($row, $shaded = !$shaded);
}
draw_header_end();
//show more links
show_links(get_links($max, "$script&query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));
}
 */

function draw_header_row()
{
    global $fields, $order, $by, $max, $at, $page, $script, $start, $action, $type, $parent, $alpha;

    reset($fields);
    echo "<br>";
    echo '<table width="99%" align="center">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"?max=$max&start=$start&at=$at&page=$page&action=$action&type=$type&parent=$parent&alpha=$alpha&query=" . $_REQUEST["query"];
        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo ucfirst($field);
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="/webadmin/' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        '</b></td>';
        next($fields);
    }
    echo '</tr>';

}

function draw_header_end()
{
    echo '</table>';
    echo "<br>";
}

/*function show_result($cols, $shaded)
{
global $fields, $actions, $script;

reset($fields);
$ss_id = $cols[key($fields)];
$id = $cols['id'];
echo $shaded?'<tr>':'<tr bgcolor="#EEEEEE">';
for ($i = 0; $i < sizeof($fields); $i++)
{
echo "<td align=\"".$fields[key($fields)]."\">";
echo ($cols[key($fields)]).'</td>';
next($fields);
}
echo '<td nowrap>';
for ($i = 0;$i < sizeof($actions);$i++)
{
if ($i != 0)
echo " | ";
$str = addslashes($actions[$i]);
eval ("\$str = \"$str\";");
echo "$str";

}
echo '</td>';
echo "</tr>";
}

function add_new_ss()
{
global $DB, $part_id, $part_id_cus, $part_ss, $part_ss_cap,$img_location, $ss_type;
global $table;

if ($part_id_cus != '')
{
//        $statement= "SELECT COUNT(id) as ids FROM ".$table." WHERE id = '".intval($part_id_cus)."'";
$statement= "SELECT COUNT(id) as ids FROM products WHERE id = '".intval($part_id_cus)."'";
$ids = $DB->query_first($statement);
if ($ids[ids] > 0)
{
$id = $part_id_cus;
}
}
if (!isset($id))
$id = $part_id;

purge_ss_cache_entry($ss_id, $id);

$statement = "SELECT t.img_location FROM product_types t, products p WHERE t.id = p.type AND p.id = '$id'";
$product_details = $DB->query_first($statement);

$img_location = $product_details['img_location']."/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'].'/'.$img_location;

$temp = $DB->query_first("SELECT MAX(rank) FROM product_screenshots WHERE id='$id'");
$maxRank = intval($temp[0]);

for ($i = 0;$i < sizeof($part_ss);$i++)
{
$key = key($part_ss);

$source_file = $part_ss[$key];
if (file_exists($source_file) && is_file($source_file))
{
$img_file = process_image($source_file, find_ss_count($id), 200, 200, $img_location_abs);

if ($img_file)
{
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
'".addslashes(htmlspecialchars($part_ss_cap[$key]))."',
'$ss_type[$key]',
'".++$maxRank."',
NOW())";

$DB->query($statement);
} else {echo "Something went wrong...";}
}

next($part_ss);
}

}

function find_ss_count($id)
{
global $DB,$img_location, $table;

$statement = "SELECT filename FROM product_screenshots WHERE id = '$id'";
$ss_count = $DB->query($statement);

while($row = $DB->fetch_array($ss_count))
$files[] = $row[filename];

if(!is_array($files) || count($files) <= 0)
{
return 'ss_'.$id.'_1';
}

$count = count($files);
$test_id = 'ss_'.$id.'_'.$count;

while (eregi_in_array($img_location.$test_id.'.', $files))
{
$count++;
$test_id = 'ss_'.$id.'_'.$count;
}

return $test_id;
}

//===========================================================================
// search an array value using eregi
//
// PARAMS: the needle and the array
// RETURN: true if found, false otherwise
//===========================================================================
function eregi_in_array($needle, $array)
{
if (!is_array($array))
{
return preg_match('/' . quotemeta($needle) . '/i', $array);
}

for ($i = 0; $i < count($array); $i++)
{
if (eregi_in_array($needle, $array[$i]))
{
return true;
}
}
}

function edit_ss()
{
global $DB, $part_ss, $part_ss_cap, $ss_id, $img_location, $table, $ss_type, $rank;

$part = $DB->query_first("SELECT id,rank FROM product_screenshots WHERE ss_id = '$ss_id'");

$id = $part['id'];

$statement = "SELECT t.img_location FROM product_types t, products p WHERE t.id = p.type AND p.id = '$id'";
$product_details = $DB->query_first($statement);

$img_location = $product_details['img_location']."/";
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

$img_clause = '';
if (file_exists($part_ss) && is_file($part_ss))
{
$img_file = process_image($part_ss, find_ss_count($part[id]), 200,200,$img_location_abs);
if ($img_file)
{
purge_ss_cache_entry($ss_id, $id);
$img_clause = "filename = '$img_location$img_file', ";
}

}
$temp = $DB->query_first("SELECT MAX(rank) AS max_rank FROM product_screenshots WHERE id=$id");
// check if a valid rank is specified
if($rank < 0)
{
echo htmlspecialchars("Invalid rank specified. rank must be >= 0");
return;
}
$sql = "SELECT COUNT(*) FROM product_screenshots WHERE id=$id AND rank=$rank";
$result = $DB->query($sql);
$currentRank = $part['rank'];
// swap ranks with previous rank holder
if($DB->num_rows($result) > 0 && $currentRank != $rank)
{
$sql = "UPDATE product_screenshots SET rank=$currentRank WHERE id=$id AND rank=$rank";
$DB->query($sql);
}
$statement = "UPDATE product_screenshots SET
$img_clause
ss_type = '$ss_type',
caption='".addslashes(htmlspecialchars($part_ss_cap))."',
rank = '".intval($rank)."'
WHERE ss_id = '$ss_id'";

$DB->query($statement);
echo "SS_ID: $ss_id updated<br>";
}

function delete_ss()
{
global $ss_id, $DB, $table;

$part = $DB->query_first("SELECT id,rank FROM product_screenshots WHERE ss_id = '$ss_id'");
purge_ss_cache_entry($ss_id, $part['id']);

$statement = "DELETE FROM $table WHERE ss_id = '$ss_id'";
$DB->query($statement);
echo "Screenshot $id deleted.<br>";

}*/

function add_new_manual()
{
    global $DB, $attachment_dir;
    $posted = array_merge($_GET, $_POST);

    $file_temp     = $_FILES['image_file']['tmp_name'];
    $file_orig     = strtolower($_FILES['image_file']['name']);
    $file_desc     = $posted['image_desc'];
    $manual_type   = $posted['manual_type'];
    $product_id    = $posted['product_id'];
    $product_name  = $posted['product_name'];
    $attachment_id = '';
    if ('' != $product_id && 'No product Found' != $product_name) {
        $before = $DB->query_first("SELECT COUNT(*) as total FROM product_attachments WHERE product_id = '$product_id'");

        $product   = $DB->query_first("SELECT * FROM products p,product_types pt WHERE p.id = '$product_id' AND pt.id=p.type");
        $file_orig = strtolower($_FILES['image_file']['name']);
        $size      = filesize($_FILES['image_file']['tmp_name']);
        $extension = substr($file_orig, strrpos($file_orig, '.') + 1);
        $letter    = getLetter($before['total']);
        $new_file  = $product_id . $letter . '_' . str_replace(" ", "_", $product['product_name']) . '_' . $product['name'] . '.' . $extension;
        //$new_file=$product_id.'_'.$product['name'].'_'.$product['name'].'.'.$extension;
        //$new_file='prod_attach'.$attachment_id.'.'.$extension;

        if (is_uploaded_file($_FILES['image_file']['tmp_name']) && 0 == $_FILES['image_file']['error'] && move_uploaded_file($_FILES['image_file']['tmp_name'], $attachment_dir . '/' . $new_file)) {
            $query         = $DB->query("INSERT INTO product_attachments(product_id) VALUES(" . $product_id . ")");
            $attachment_id = $DB->insert_id();

            $DB->query("UPDATE product_attachments SET user='" . $user_details['first_name'] . ' ' . $user_details['last_name'] . "', attach_date=NOW(), file_name=" . formatSQLString($new_file) . ", file_original=" . formatSQLString($_FILES['image_file']['name']) . ", description=" . formatSQLString($file_desc) . ", filetype='" . $extension . "', manual_type='" . $manual_type . "', size='" . $size . "' WHERE attachment_id=" . $attachment_id);

            echo 'Manual for Product ID ' . $product_id . ' was added successfully.';
        } else {
            echo 'Manual wasn\'t added for Product ID ' . $product_id . ', as no file was selected to upload.';
            productAttachmentDelete($attachment_id);
            $action = 'add';
        }
    } else {
        //show_summary();
        echo 'Product Manual was not added as no Product ID was provided or Product ID is invalid';
    }
}
function edit_manual($attachment_id)
{
    global $_GLOBALS;
    global $DB;

    global $attachment_dir;

    $posted = array_merge($_GET, $_POST);

    $file_temp   = $_FILES['image_file']['tmp_name'];
    $file_orig   = strtolower($_FILES['image_file']['name']);
    $file_desc   = $posted['image_desc'];
    $manual_type = $posted['manual_type'];
    $product_id  = $posted['product_id'];
    if ('' != $file_temp) {
        //if($file_desc=='') $file_desc=$file_orig;
        $before = $DB->query_first("SELECT * FROM product_attachments WHERE attachment_id = '$attachment_id'");
        //$query=$DB->query("INSERT INTO product_attachments(product_id) VALUES(".$product_id.")");
        //$attachment_id=$DB->insert_id();
        $file_orig = strtolower($_FILES['image_file']['name']);
        $size      = filesize($_FILES['image_file']['tmp_name']);
        $extension = substr($file_orig, strrpos($file_orig, '.') + 1);
        //$product = $DB->query_first("SELECT * FROM products p,product_types pt WHERE p.id = '$product_id' AND pt.id=p.type");
        $new_file = substr($before['file_name'], 0, strpos($before['file_name'], ".")) . '.' . $extension; // keep the same name, just change the extension

        unlink($attachment_dir . '/' . $before['file_name']); // delete the old file, before uploading new file..
        if (is_uploaded_file($_FILES['image_file']['tmp_name']) && move_uploaded_file($_FILES['image_file']['tmp_name'], $attachment_dir . '/' . $new_file)) {
            $DB->query("UPDATE `product_attachments` SET `user` = " . $DB->escape($_GLOBALS['staff']['user']) . ", `attach_date` = NOW(), `file_name` = " . formatSQLString($new_file) . ", `file_original` = " . formatSQLString($_FILES['image_file']['name']) . ", `description` = " . formatSQLString($file_desc) . ", `filetype` = '" . $extension . "', `manual_type` = '" . $manual_type . "', `size` = '" . $size . "' WHERE `attachment_id` = " . $attachment_id);
        } else {
            productAttachmentDelete($attachment_id);
            $action = 'add';
        }
    } else {
        $DB->query("UPDATE product_attachments SET product_id = '$product_id', manual_type='$manual_type', description='" . addslashes($file_desc) . "' WHERE attachment_id='$attachment_id'");
    }

    echo 'The Manual for product id ' . $product_id . ' successfully modified.';
    echo '<script>if(this.opener) this.opener.location = this.opener.location;</script>';
}
function delete_manual($attachment_id, $product_id)
{
    if (productAttachmentDelete($attachment_id)) {
        echo 'Attachment ID ' . $attachment_id . ' has been deleted from the database.';
    } else {
        echo 'There has been an error in deleting the attachment. Please try again later.';
    }

}

function display_result($data, $shaded)
{
    global $DB, $fields, $order, $by, $max, $at, $page, $script, $start;
    $id = $data['part_id'];
    //productAttachmentsFTPToDatabase($id);
    echo $shaded ? '<tr>' : '<tr bgcolor="#EEEEEE">';
    $prod = productFromDatabase($id);
    echo '<td align="' . $fields['id'] . '"><a href="http://local.highway.net.au/parts/' . $prod['type_name'] . '/' . $id . '" target="_blank">H' . $id . '</a></td>';
    echo '<td align="' . $fields['product_name'] . '">' . $data['part_name'] . '</td>';
    echo '<td align="' . $fields['type_of_manual'] . '"><a href="viewimage_hg.php?product_id=' . $data['product_id'] . '&attachment_id=' . $data['attachment_id'] . '" target="_blank">' . (('' != $data['manual_type']) ? typeofManual($data['manual_type']) : 'Not Selected') . '</a></td>';
    echo '<td align="' . $fields['notes'] . '">' . $data['description'] . '</td>';
    echo '<td align="' . $fields['file_type'] . '">' . $data['filetype'] . '</td>';
    $size = (0 == $data['size']) ? '' : human_file_size($data['size']);
    echo '<td align="' . $fields['size'] . '">' . $size . '</td>';
    echo '<td align="' . $fields['uploaded_by'] . '">' . $data['user'] . '</td>';
    echo '<td align="' . $fields['date_added'] . '">' . date("d/m/y", strtotime($data['attach_date'])) . '</td>';
    echo '<td align="' . $fields['extra'] . '"><a href="viewimage_hg.php?product_id=' . $data['product_id'] . '&attachment_id=' . $data['attachment_id'] . '" target="_blank">VIEW </a></td>';
    echo '</tr>';

}
function view_all_manuals($display_summ = true)
{
    global $DB, $fields, $order, $by, $max, $at, $page, $script, $start, $type;

    $limit = 100;
    if ('' != $type) {
        $limit = 10000000;
    }

    if ('' == $start) {
        $start = 0;
    }

    if ($display_summ) {
        show_summary();
    }

    echo '<br /><br />';

    display_entries($start, $limit);
    if ('' == $by || 'product_name' == $by) {
        $by = 'part_name';
    } else if ('id' == $by) {
        $by = 'pa.product_id';
    } else if ('type_of_manual' == $by) {
        $by = 'pa.manual_type';
    } else if ('notes' == $by) {
        $by = 'pa.description';
    } else if ('file_type' == $by) {
        $by = 'pa.filetype';
    } else if ('uploaded_by' == $by) {
        $by = 'pa.user';
    } else if ('date_added' == $by) {
        $by = 'pa.attach_date';
    }

    if ('' == $order) {
        $order = 'asc';
    }

    $sql = '';
    if ('' != $type) {
        $sql = "SELECT * FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.part_type='$type') ORDER BY $by $order LIMIT $start,$limit";
    } else {
        $sql = "SELECT * FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id ORDER BY $by $order LIMIT $start,$limit";
    }

    $res    = $DB->query($sql);
    $shaded = false;

    if ('part_name' == $by) {
        $by = 'product_name';
    } else if ('pa.product_id' == $by) {
        $by = 'id';
    } else if ('pa.manual_type' == $by) {
        $by = 'type_of_manual';
    } else if ('pa.description' == $by) {
        $by = 'notes';
    } else if ('pa.filetype' == $by) {
        $by = 'file_type';
    } else if ('pa.user' == $by) {
        $by = 'uploaded_by';
    } else if ('pa.attach_date' == $by) {
        $by = 'date_added';
    }

    draw_header_row();

    while ($row = $DB->fetch_array($res)) {
        display_result($row, $shaded);
        $shaded = !$shaded;
    }

    draw_header_end();
    show_letter();

    echo '<br/><div align="center" style="font-size:14px"><a href="attachment_admin.php?action=' . $action . '" target="_blank">Back to COE Manuals</a></div>';

}

function typeofManual($code)
{
    $array = array('coe' => 'COE Manual', 'hgr' => 'HG Manual', 'fm' => 'Factory Manual', 'wd' => 'Wiring Diagram/Schematics', 'sm' => 'Service Manual', 'dss' => 'Dip Switch Settings', 'oth' => 'Other');
    return $array[$code];
}

function display_entries($start, $limit, $query = '')
{
    global $DB, $user, $fields, $order, $by, $max, $at, $page, $script, $status, $website, $print, $type, $parent, $action, $alpha;
    $table = 'product_attachments';
    $qry   = "";
    $like  = "LIKE '$alpha%'";
    if ('~' == $alpha) {
        $like = "NOT REGEXP '^[[:alpha:]].*'";
    }

    if ('' != $query) {
        $qry = "SELECT COUNT(*) FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id AND p.part_name LIKE '%$query%' LIMIT 0,$limit";
        if (is_numeric($query)) {
            $qry = "SELECT COUNT(*) FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id AND p.part_id = '$query' LIMIT 0,$limit";
        }

    } else if ('' == $type) {
        if ('alpha' == $action) {
            $qry = "SELECT COUNT(*) FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id AND p.part_name $like LIMIT 0,$limit";
        } else {
            $qry = "SELECT COUNT(*) FROM $table LIMIT 0,$limit";
        }

    } else {
        if ('alpha' == $action) {
            $qry = "SELECT COUNT(*) FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.part_type='$type' AND p.product_name $like) LIMIT 0,$limit";
        } else {
            $qry = "SELECT COUNT(*) FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.part_type='$type') LIMIT 0,$limit";
        }

    }
    $res   = $DB->query_first($qry);
    $pages = '';
    $count = $res[0];
    $limit = number_format($limit, 0, '.', '');
    $count = number_format($count, 0, '.', '');
    $pages = floor($count / $limit);
    if (0 != $count) {
        if ($count < $limit) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' </b>';
        } else if ($start + $limit > $count) {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($count) . ' of ' . $count . ' | </b>';
        } else {
            echo '<center><b>Showing: ' . ($start + 1) . ' - ' . ($start + $limit) . ' of ' . $count . ' | </b>';
        }

    }
    $orig = $start;
    $new  = 0;
    for ($i = 0; $i <= $pages; $i++) {
        if (0 == $new && 0 == $orig || $orig == $new) {
            if ($count > $limit) {
                if ($i == $pages) {
                    echo '<b>' . ($start + 1) . ' - ' . ($count) . ' | </b>';
                } else {
                    echo '<b>' . ($start + 1) . ' - ' . ($start + $limit) . ' | </b>';
                }

            }
            $new = $start + $limit;
        } else {
            $link = "action=$action&start=$new&by=$by&order=$order&type=$type&parent=$parent";
            if ($i == $pages) {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($count) . '</a>';
            } else {
                echo '<a href="' . $page . '?' . $link . '">' . ($new + 1) . ' - ' . ($new + $limit) . '</a> | ';
            }

            $new += $limit;
        }
    }
    echo '</center>';
}

function show_summary()
{
    global $DB, $fields, $order, $by, $max, $at, $page, $script, $start, $action, $parent, $type;
?>
	<script type="text/javascript" src="../expandToggle.js"></script>
	<script>
	function getElement(name)
	{
		var elem=document.getElementById(name);
		if(elem) return elem;
		elem=document.getElementsByName(name);
		if(elem.length==0) return null;
		return elem[0];
	}
	function expandPart(part)
	{
		// firstly have to hide other parts
		var obj = getElement(part+'Child');
		expandIt(part);
		if(obj.style.display == 'none')
			getElement('imEx').src = '/webadmin/triDown.gif';
		else
			getElement('imEx').src = '/webadmin/triUp.gif';
	}
	</script>
<?php
$row_count = 0;
    $style     = ('show' == $parent) ? 'style="display:inline"' : 'style="display:none"';
?>
	<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
		<tr>
			<td width="25%" align="left"><a href="#" onclick="javascript:expandPart('list');return false;"><img name="imEx" src="/webadmin/<?php echo $parent == 'show' ? 'triUp.gif' : 'triDown.gif' ?>" width="16" height="16" border="0" alt="Expand/Collapse All"><b>List By Item Types</b></a></td>
		</tr>
	</table>
	<br />
	<div id="listChild" <?php echo $style ?>>
	<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
$sql = "SELECT * FROM parts_types ORDER BY type_name";
    $res = $DB->query($sql);
    $i   = 0;
    while ($row = $DB->fetch_array($res)) {
        //$items = $DB->query_first("SELECT COUNT(*) as total FROM product_attachments pa, products p WHERE (p.type = '".$row2['id']."' AND p.id=pa.product_id)");
        if ($count % 4 == 0) {
            echo '<tr>';
        }

        echo ($row['type_id'] == $type) ? '<td bgcolor="#FFCC00">' : '<td width="25%">';
        echo '<a href="?action=' . $action . '&type=' . $row['type_id'] . '&parent=show">' . $row['type_name'] . '</a>&nbsp;&nbsp;<small>(<a href="?action=print&type=' . $row['type_id'] . '&parent=' . $parent . '" style="color:dodgerblue" target="_blank">Print</a>)</small></td>';
        if ($count % 4 == 3) {
            echo "</tr>\n";
        }

        $count++;
    }
    echo '</table></div>';
    $i++;
}

function show_letter()
{
    global $DB, $parent, $type;
    echo '<br /><br /><table width=98% align=center cellpadding=7><td bgcolor=#FAFAFA><div align="center"><b>By Letter :</b> ';
    $numeric = false;
    $qry     = "SELECT * FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.part_name NOT REGEXP '^[[:alpha:]].*')";
    if ('' != $type) {
        $qry = "SELECT * FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.part_type='$type' AND p.part_name NOT REGEXP '^[[:alpha:]].*')";
    }

    $res = $DB->query($qry);
    if (mysqli_num_rows($res) > 0) {
        echo '<a href="?action=alpha&alpha=~&parent=' . $parent . '&type=' . $type . '" target="_blank">#</a>&nbsp;';
    } else {
        echo '<font color="#999999">#</font>&nbsp;';
    }

    for ($i = A;; $i++) {
        $qry = "SELECT * FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id AND p.part_name LIKE '$i%'";
        if ('' != $type) {
            $qry = "SELECT * FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.part_type='$type' AND p.part_name LIKE '$i%')";
        }

        //$qry = "SELECT * FROM product_attachments pa, products p WHERE pa.product_id = p.id AND p.product_name LIKE '$i%'";
        $res = $DB->query($qry);
        if (mysqli_num_rows($res) > 0) {
            echo '<a href="?action=alpha&alpha=' . $i . '&parent=' . $parent . '&type=' . $type . '" target="_blank">' . $i . '</a>&nbsp;';
        } else {
            echo '<font color="#999999">' . $i . '</font>&nbsp;';
        }

        if ("Z" == $i) {
            break;
        }

    }

    echo '</div></td></table>';
}

function show_alpha($letter)
{
    global $DB, $fields, $order, $by, $max, $at, $page, $script, $start, $type;

    $limit = 100000000; // basically to make it display all possible products!!!!
    show_summary();
    display_entries(0, $limit);
    if ('' == $by || 'product_name' == $by) {
        $by = 'part_name';
    } else if ('id' == $by) {
        $by = 'pa.product_id';
    } else if ('type_of_manual' == $by) {
        $by = 'pa.manual_type';
    } else if ('notes' == $by) {
        $by = 'pa.description';
    } else if ('file_type' == $by) {
        $by = 'pa.filetype';
    } else if ('uploaded_by' == $by) {
        $by = 'pa.user';
    } else if ('date_added' == $by) {
        $by = 'pa.attach_date';
    }

    if ('' == $order) {
        $order = 'asc';
    }

    $like = "LIKE '$letter%'";
    if ('~' == $letter) {
        $like = "NOT REGEXP '^[[:alpha:]].*'";
    }

    $sql = '';
    if ('' != $type) {
        $sql = "SELECT * FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.type='$type' AND p.part_name $like) ORDER BY $by $order";
    } else {
        $sql = "SELECT * FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id AND p.part_name $like ORDER BY $by $order";
    }

    if ('part_name' == $by) {
        $by = 'product_nam';
    } else if ('pa.product_id' == $by) {
        $by = 'id';
    } else if ('pa.manual_type' == $by) {
        $by = 'type_of_manual';
    } else if ('pa.description' == $by) {
        $by = 'notes';
    } else if ('pa.filetype' == $by) {
        $by = 'file_type';
    } else if ('pa.user' == $by) {
        $by = 'uploaded_by';
    } else if ('pa.attach_date' == $by) {
        $by = 'date_added';
    }

    draw_header_row();

    $res    = $DB->query($sql);
    $shaded = false;
    while ($row = $DB->fetch_array($res)) {
        display_result($row, $shaded);
        $shaded = !$shaded;
    }

    draw_header_end();

    show_letter();
}

function search($query)
{
    global $DB, $by, $order;

    display_entries(0, 10000000, $query);
    if ('' == $by || 'product_name' == $by) {
        $by = 'part_name';
    } else if ('id' == $by) {
        $by = 'pa.product_id';
    } else if ('type_of_manual' == $by) {
        $by = 'pa.manual_type';
    } else if ('notes' == $by) {
        $by = 'pa.description';
    } else if ('file_type' == $by) {
        $by = 'pa.filetype';
    } else if ('uploaded_by' == $by) {
        $by = 'pa.user';
    } else if ('date_added' == $by) {
        $by = 'pa.attach_date';
    }

    if ('' == $order) {
        $order = 'asc';
    }

    $like = "LIKE '%$query%'";
    $sql  = "SELECT * FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id AND p.part_name $like ORDER BY $by $order";
    if (is_numeric($query)) {
        $sql = "SELECT * FROM product_attachments pa, parts p WHERE pa.product_id = p.part_id AND p.part_id = '$query' ORDER BY $by $order ";
    }

    if ('part_name' == $by) {
        $by = 'product_name';
    } else if ('pa.product_id' == $by) {
        $by = 'id';
    } else if ('pa.manual_type' == $by) {
        $by = 'type_of_manual';
    } else if ('pa.description' == $by) {
        $by = 'notes';
    } else if ('pa.filetype' == $by) {
        $by = 'file_type';
    } else if ('pa.user' == $by) {
        $by = 'uploaded_by';
    } else if ('pa.attach_date' == $by) {
        $by = 'date_added';
    }

    draw_header_row();

    $res    = $DB->query($sql);
    $shaded = false;
    while ($row = $DB->fetch_array($res)) {
        display_result($row, $shaded);
        $shaded = !$shaded;
    }

    draw_header_end();
}
function product_search($qry)
{
    global $DB;

    $sql = '';
    if (is_numeric($qry)) {
        $sql = "SELECT * FROM parts WHERE part_id = '$qry'";
        $row = $DB->query_first($sql);
        echo "<script>this.opener.display_result('" . (($row) ? $row['part_id'] : $qry) . "', \"" . (($row) ? $row['part_name'] : 'No product Found') . "\");this.close();</script>";
    } else {
        $sql = "SELECT * FROM parts WHERE part_name LIKE '%$qry%'";
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<script>
function display_data(id,name)
{
	this.opener.display_result(id,name);
	this.close();
}

</script>
<div id="inqcontent">
<table class="wide spacetable" align="center" cellspacing=2 cellpadding=4>
	<tr><th>ID</th><th>Product Name</th><th>Select</th></tr>
<?php
$res    = $DB->query($sql);
        $shaded = false;
        if (mysqli_num_rows($res) > 0) {
            while ($item = $DB->fetch_array($res)) {
                //$product_details = productFromDatabase($item['id'],3);
?>
		<tr <?php echo (!$shaded) ? '' : 'class="alt"' ?>>
			<td style="text-align:center"><?php echo $item['part_id'] ?></td>
			<td style="width:100%;text-align:left"><!--<a href="<?php echo $product_details['product_url'] ?>" target="_blank">--><?php echo stripslashes($item['part_name']) ?></a></td>
			<td style="text-align:center"><input type="button" value="Select" onclick="display_data('<?php echo $item['part_id'] ?>','<?php echo $item['part_name'] ?>');" /></td>
		</tr>
<?php
$shaded = !$shaded;
            }
        } else {
            echo '<tr><td colspan="5" style="text-align:center"><br />No products found on this page<br /><br /></td></tr>';
        }

?>
	</table>
</div>
<?php
}
    //echo 'QRY executed = '.$sql;
}

/* ---------------------------------------------------------FORMATTING FUNCTIONS ------------------------------------------------*/

function formatHTMLOptions($inarray, $sel = '')
{
    $outtext = '';
    if (count($inarray)) {
        foreach ($inarray as $key => $item) {
            $outtext .= '<option value="' . htmlspecialchars($key) . '"' . (($key == $sel) ? ' selected="selected"' : '') . '>' . htmlspecialchars($item) . "</option>\n";
        }
    }

    return $outtext;
}
function human_file_size($size)
{
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
}
function getLetter($number)
{
    $array = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    return $array[$number];
}
function formatSQLString($instring)
{
    $outstring = "'" . addslashes($instring) . "'";
    $outstring = str_replace(chr(13) . chr(10), '\n', $outstring);
    $outstring = str_replace(chr(13), '\n', $outstring);
    $outstring = str_replace(chr(10), '\n', $outstring);
    return $outstring;
}

/* -----------------------------------------------------------------END OF FORMATTING FUNCTIONS ---------------------------------------------*/

switch ($action) {
    /*case "do_add":
    add_new_manual();
    break;
    case "do_edit":
    edit_manual($_REQUEST['attachment_id']);
    break;
    case "do_delete":
    delete_manual($_POST['attachment_id'],$_POST['orig_product_id']);
    break;
    case "add":
    //add_manual_page();
    include 'edit_manual_form.inc.php';
    break;
    case "edit":
    include 'edit_manual_form.inc.php';
    break;*/
    case "summary":
        show_summary();
        if ('' != $type) {
            view_all_manuals(false);
        }

        break;
    case "view":
        view_all_manuals();
        break;
    case "search":
        $inc = false;
        include 'manual_search_hg.php';
        break;
    case "do_search":
        $inc = false;
        include 'manual_search_hg.php';
        $_REQUEST["query"] = $_POST['query'];
        if ('' == $_REQUEST["query"]) {
            $_REQUEST["query"] = $_REQUEST['query'];
        }

        search($_REQUEST["query"]);
        break;
    /*case "product_search":
    product_search($_REQUEST['search']);
    break;*/
    case "print":
        echo '<script>window.location="list_manuals_hg.php?parent=' . $_REQUEST['parent'] . '&type=' . $_REQUEST['type'] . '"</script>';
        break;
    case "alpha":
        show_alpha($_REQUEST['alpha']);
        break;
    case "":
        $inc = false;
        include 'manual_search.php';
        break;
}

?>
</form>

<!--//parts screenshot admin -->
<?php
include '../footer.inc.php';
