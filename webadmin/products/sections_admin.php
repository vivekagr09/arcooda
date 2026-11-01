<script type="text/javascript">
    function confirmdelete(){
        if(confirm("Are you sure you want to delete this category, then continue to the delete ?")){
            return true;
        }else{
            return false;
        }
    }

</script>
<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/
$section_code = "PRD_CFG";

include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

include_once __DIR__ . '/header.inc.php';


$query = $_REQUEST['query'];
$id    = $_REQUEST['id'] ?? 0;
$data  = $_REQUEST['data'];
$view  = $_GET['view'];
$table = 'product_types';

$script = 'sections_admin.php?table=' . $table;

include_once __DIR__ . '/sections_tree.inc.php';

$fields['tid']          = 'left';
$fields['name']         = 'left';
//$fields['num_products'] = 'right';
$fields['active']       = 'center';

$search_fields[] = 't.id';
$search_fields[] = 'name';

$id_field = 'id';

$actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script&action=delete&id=$id" onclick="return confirmdelete()">Delete</a>';

$items = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM %s",
    $table
), MYSQLI_ASSOC);

$img_location     = 'images/sections/';
$img_location_abs = $_SERVER['DOCUMENT_ROOT'] . '/' . $img_location;

//check_image_dir($img_location_abs, true);

$table_title = ucwords(str_replace('_', ' ', $table));

?>

<!-- <?=$table_title?> admin -->

<form action="<?=$script?>" method="post" enctype="multipart/form-data" name="admin_form">
	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="30%">
            <div align="center"><b><?=$table_title?></b> <span style="background-color:yellow;">(Export)</span></div>
          </td>
          <td bgcolor="#EEEEEE" width="70%">
            <div align="right">
			<a href="<?=$script?>&view=tree">Tree View</a> |
			<a href="<?=$script?>&action=add">Add New <?=$table_title?></a> |
			<a href="<?=$script?>&query=">List <?=$table_title . ' (' . $items['total'] . ')'?></a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars($_REQUEST["query"])?>" />
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->

<?php

$max = $_GET['max'];

/**
 * @param string $query
 *
 * @return void
 */
function search($query)
{
    global $DB;

    global $script, $max, $table, $fields, $id_field, $admin_lang, $search_fields;

    $at    = $_GET['at'];
    $order = $_GET['order'];
    $by    = $_GET['by'];

    $query = trim($query);
    $extra = '';

    //keywords
    $arrKeywords = explode(" ", $query);

    //search query by key words
    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

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
    $statement = "SELECT t.*, t.id as tid, count(p.id) as num_products
				  FROM $table t
				  LEFT JOIN products p ON p.type = t.id  WHERE 1=1 ";

    $group_by = ' GROUP BY t.id ';

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra . $group_by);
        $max    = $DB->num_rows($result);
    }

    $inc = 50;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'desc';
    }

    reset($fields);

    if (!isset($by)) {
        $by = 'tid';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $group_by . $order_by . $limit);

    $num_rows = $DB->num_rows($result);

    if ($num_rows > 0) {
        show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));

        draw_header_row();
        $shaded = false;
        while ($row = $DB->fetch_array($result)) {
            show_result($row, $shaded = !$shaded);
        }
        draw_header_end();
        //show more links
        show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", $inc, 4));
    } else {
        echo '<div align="center">No records</div>';
    }
}
if (!function_exists('get_links')) {
    function get_links($max, $url_link, $inc = 25, $max_page = 5, $var_at = 'at', $var_page = 'page')
    {
        global $$var_at, $$var_page;

        $start = $$var_at + 1;
        $end   = $$var_at + $inc;
        if ($end > $max) {
            $end = $max;
        }

        if ($max <= 0) {
            return;
        }

        if (strpos($url_link, "?") === false) {
            $url_link .= '?';
        }

        $contents['start_from'] = $start;
        $contents['ends_at']    = $end;
        $contents['max']        = $max;

        if (1 != $start) {
            $contents['first'] = "$url_link&$var_at=0&$var_page=0";
        }

        $very_last_at   = floor(($max - 1) / $inc) * $inc;
        $very_last_page = floor(($max - 1) / ($inc * $max_page));
        if ($start - 1 != $very_last_at) {
            $contents['last'] = "$url_link&$var_at=$very_last_at&$var_page=$very_last_page";
        }

        $next_page = intval($max_page * $$var_page * $inc);

        if ($next_page > 0) {
            $new_at                    = $next_page - $inc;
            $new_page                  = $$var_page - 1;
            $contents['url_prev_page'] = "$url_link&$var_at=$new_at&$var_page=$new_page";
        }

        for ($i = 0; $i < $max_page; $i++) {
            $count  = $i;
            $start  = $next_page + ($i * $inc) + 1;
            $end    = $next_page + ($i + 1) * $inc;
            $new_at = $next_page + $i * $inc;
            if ($end > $max || $new_at + $inc >= $max) {
                $end = $max;
                $i   = $max_page;
            }
            if ($max <= 0) {
                $start = 0;
            }

            $contents['links'][$i]['start'] = $start;
            $contents['links'][$i]['end']   = $end;
            if (!(($$var_at - $next_page) / $inc == $count)) {
                $contents['links'][$i]['url'] = "$url_link&$var_at=$new_at&$var_page=" . intval($$var_page);
            }

        }

        if ($end < $max) {
            $new_at                    = $new_at + $inc;
            $new_page                  = intval($$var_page + 1);
            $contents['url_next_page'] = "$url_link&at=$new_at&page=$new_page";
        }

        return $contents;
    }
}
if (!function_exists('show_links')) {
    function show_links($links)
    {
        //show more links.
        if (!(is_array($links) && count($links) > 1)) {
            return;
        }

        //the << link to a previous set of links
        echo "<DIV ALIGN='center'><FONT FACE='Verdana, Arial, Helvetica, sans-serif' SIZE='2'><b>Showing : $links[start_from] - $links[ends_at] of $links[max] | </b>";
        if (isset($links['url_prev_page']) && $prev_page = $links['url_prev_page']) {
            echo " <A HREF=\"$prev_page\"><<</A> | ";
        }

        //for each of the links
        for ($i = 0; $i < count($links['links']); $i++) {
            //get the link ID
            $key = key($links['links']);
            //if the link contains an URL, show it
            if (isset($links['links'][$key]['url']) && $url = $links['links'][$key]['url']) {
                echo " <A HREF=\"$url\">" . $links['links'][$key]['start'] . " - " . $links['links'][$key]['end'] . "</A>";
            } else
            //else just show where the records starts and ends
            {
                echo $links['links'][$key]['start'] . " - " . $links['links'][$key]['end'];
            }

            echo ($i < count($links['links']) - 1) ? " | " : "";
            next($links['links']);
        }
        //the >> link to the next set of links
        if (isset($links['url_next_page']) && $next_page = $links['url_next_page']) {
            echo " <A HREF=\"$next_page\">>></A> ";
        }

        echo '</FONT></DIV>';
    }
}
/**
 * @return void
 */
function draw_header_row()
{
    global $fields, $script, $max;
    $order = $_GET['order'];
    $by    = $_GET['by'];
    $at    = $_GET['at'];
    $page  = $_GET['page'];

    if (null == $by || '' == $by) {
        $by = 'id';
    }

    if (null == $order || '' == $order) {
        $order = 'desc';
    }

    reset($fields);

    echo '<br>';

    echo '<table align="center" cellspacing="1" cellpadding="3">' . "\n";
    echo '<tr bgcolor="#000066">' . "\n";
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" ' . ($by == $field ? 'bgcolor="#336699"' : '') . '><b>';
        echo "<a href=\"$script&query=" . urlencode($_REQUEST["query"]) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '" class="td_options">';
        echo $field;
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="/webadmin/' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</b></td>' . "\n";
        next($fields);
    }
    echo "<td>&nbsp;</td>\n";
    echo '</tr>' . "\n";

}

/**
 * @return void
 */
function draw_header_end()
{
    echo '</table><br>';
}

/**
 * @return bool
 */
function generateslug($slug,$table){
    global $DB;
    $return=true;
    $checkslug=$DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM %s where `slug`='%s'",
            $table,
            $slug
        ), MYSQLI_ASSOC);
    if($checkslug['total']>0){
        $return=false;
    }
    return $return;
}
function do_add()
{
    global $table_title, $data, $images, $table, $DB, $img_location;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['slug'])));
    $data['slug']=$slug;
    $is_available= generateslug($slug,$table);
    if($is_available){
         foreach ($data as $k => $v) {
            $str[$k] =  $DB->escape($v);
        }
        if (count($str)) {
            $statement =  $DB->query(sprintf(
                "INSERT INTO %s (`%s`) VALUES (%s)",
                $table,
                implode('`, `', array_keys($str)),
                implode(", ", $str)
            ));
            $id = $DB->insert_id();
    
            if (is_file($images['header_image'])) {
                $img = add_image($images['header_image'], 'header_' . $id);

                if ($img) {
                    $DB->query("UPDATE $table SET header_image = '$img' WHERE id = '$id'");
                }
            }

            if (is_file($images['icon_image'])) {
                $img = add_image($images['icon_image'], 'icon_' . $id);

                if ($img) {
                    $DB->query("UPDATE $table SET icon_image = '$img' WHERE id = '$id'");
                }
            }
            echo "New $table_title added.<br>";
        }else{
             echo "<p style='color:red'>Please try again later.</p><br>";
        }
    }else{
            echo "<p style='color:red'>Url slug allready exist.</p><br>";
    }
}

/**
 * @return void
 */
function do_edit()
{

    global $table_title, $table, $data, $images, $DB, $img_location;

    $id = $_REQUEST['id'];
    $checkslug=$DB->query_first(sprintf(
        "SELECT slug FROM %s where `id`=%d",
        $table,
        $id
    ), MYSQLI_ASSOC);
    $is_available=true;
    if($checkslug['slug'] !=$data['slug']){
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['slug'])));
        $data['slug']=$slug;
        $is_available= 1;//generateslug($slug,$table);
    }
    if($is_available){
         foreach ($data as $k => $v) {
            $str[] = "`" . $k . "` = " . $DB->escape($v);
        }
        $datastr= implode(", ", $str);
        $statement = "UPDATE $table SET " . $datastr . " WHERE id = '$id'";
        $result    = $DB->query($statement);

        if (is_file($images['header_image'])) {
            $img = add_image($images['header_image'], 'header_' . $id);

            if ($img) {
                $DB->query("UPDATE $table SET header_image = '$img' WHERE id = '$id'");
            }
        }

        if (is_file($images['icon_image'])) {
            $img = add_image($images['icon_image'], 'icon_' . $id);

            if ($img) {
                $DB->query("UPDATE $table SET icon_image = '$img' WHERE id = '$id'");
            }
        }
        deleteMysqlCacheData('product_types',$id);
        //echo "$id: updated<br>";
    }else{
         echo "<p style='color:red'>Url slug allready exist.</p><br>";
    }
    ?>
    <script>
        window.location ="<?= $_SERVER['HTTP_REFERER'].'&message=Category updated successfully' ?>";
    </script>  
<?php
}

/**
 * @return void
 */
function do_delete()
{
    global $DB, $table, $id_field;

    $id = $_REQUEST['id'];

    $statement = "DELETE FROM $table WHERE $id_field = '$id' AND parent > 0";
    if ('product_types' == $table && $id > 4) {
        $statement = "DELETE FROM $table WHERE $id_field = '$id' LIMIT 1";
    }
    deleteMysqlCacheData('product_types',$id);
    $DB->query($statement);

    echo "Product Type ID:$id Deleted.<br>";

}

/**
 * @param string[] $cols
 * @param bool     $shaded
 *
 * @return void
 */
function show_result($cols, $shaded)
{
    global $fields, $actions, $script;
    reset($fields);
    $id = $cols[key($fields)];
    if ($shaded) {
        echo "<tr bgcolor=\"#EEEEEE\" onmouseover=\"this.style.backgroundColor='#9DD6D2';\" onmouseout=\"this.style.backgroundColor='';\">\n";
    } else {
        echo "<tr bgcolor=\"#FFFFFF\" onmouseover=\"this.style.backgroundColor='#9DD6D2';\" onmouseout=\"this.style.backgroundColor='';\">\n";
    }
    for ($i = 0; $i < sizeof($fields); $i++) {
        $key = key($fields);
        echo "<td align=\"" . $fields[$key] . "\">";
        if($key=='name')
            echo '<a href="/webadmin/products_new/list.php?action=list&type='.$cols['id'].'" target="_blank">'.$cols[$key].'( '.$cols["num_products"].' ) </a></td>';
        else
        echo $cols[$key] . '</td>';
        next($fields);
    }
    echo '<td nowrap>';

    for ($i = 0; $i < sizeof($actions); $i++) {
        if (0 != $i) {
            echo " | ";
        }

        $str = addslashes($actions[$i]);
        eval("\$str = \"$str\";");
        echo "$str";

    }
    echo '</td>';
    echo "</tr>";
}

$action = $_REQUEST['action'];
if (trim($_REQUEST["query"]) == '') {
    switch ($action) {
        case "add":
            include __DIR__ . '/sections_add.inc.php';
            break;
        case "edit":
            include __DIR__ . '/sections_edit.inc.php';
            break;
        case "delete":
            include __DIR__ . '/section_delete.inc.php';
            break;
        case "do_add":
            do_add();
            break;
        case "do_edit":
            /*if (!locked($table, $id, true)) {*/
                do_edit();
           /* }*/

            break;
        case "do_delete":
            do_delete();
            break;

    }
}
if (isset($view)) {
    echo '<table align="center">';
    show_sections(0, '&nbsp;&nbsp;', true);
    echo '</table>';
}
if (isset($_REQUEST["query"])) {
    search(addslashes($_REQUEST["query"]));
}

//load_hidden_vars();

?>
</form>

<!--//<?=$table_title?> admin -->
<?php
include_once __DIR__ . '/footer.inc.php';
