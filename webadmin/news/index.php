<?php
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

//Don't know what this crap does
$page            = "news";
$fields[]        = 0;
$fields[]        = 1;
$fields[]        = 6;
$fields[]        = 7;
$active          = 3;
$active_type     = 3;
$search_fields[] = "news_title";
//End of unknown crap

include_once dirname(__FILE__) . "/common.inc.php";

if ("" != $get_vars["return_message"]) {
    switch ($get_vars["action"]) {
        case "select":
            header("Location: select.php?" . http_build_query($get_vars));
            break;
        default:
            header("Location: list.php?" . http_build_query($get_vars));
            break;
    }
    die();
}

include_once "header.inc.php";
?>
<p>Use this page to maintain the section in the database.
<hr />
<?php
if (!function_exists('sliderGenericsFromPost')) {
    function newsGenericsFromPost($posted, $table)
    {
         global $DB;
        if(!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->get_table_columns($table);
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if (!function_exists('newsGenericToDatabase')) {
    function newsGenericToDatabase(&$item, $table, $primary_key = 'id') {
        global $DB, $_GLOBALS;
        $id         = intval($item[$primary_key]);
        $fields     = $DB->get_table_columns($table);
        $old_vals   = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !=$old_vals[$k]) {
                    if (is_array($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape($item[$k]);
                    } else {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                   $set_vals['updated_at'] = "`updated_at` = NOW()";
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {
            $item['created_by'] = $_GLOBALS['staff']['id'];
            $item['created_at'] = formatDateTime();
            $insert_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (is_array($item[$k])) {
                        $insert_vals[$k] = $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $insert_vals[$k] = $DB->escape($item[$k]);
                    } else {
                        $insert_vals[$k] = "NULL";
                    }
                }
            }
            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    $table,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", $insert_vals)
                ));

                $id = $DB->insert_id();
            }
        }
        return $id;
    }
}

if (!function_exists('sliderGenericsDelete')) {
    function newsGenericsDelete($id, $table, $secondary_key = 'news_id') {
        global $DB;
        $DB->query(sprintf(
            "DELETE FROM %s  WHERE  `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
function my_trim($array)
{
    for ($i = 0; $i < count($array); $i++) {
        $array[$i] = trim($array[$i]);
    }

    return $array;
}
$posted   = array_merge($_GET, $_POST);
$get_vars = $posted;
foreach ($get_vars as $key => $item) {
    if (is_array($item)) {
        unset($get_vars[$key]);
    }
}

$total = $DB->query_first("SELECT COUNT(*) FROM `news`");
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="news_content">
<table cellpadding="2" cellspacing="2" style="width:100%;border:0px">
    <tr style="background-color:#EEEEEE">
        <td class="pageMenu" style="width:70%"><b> News </b></td>
        <td class="pageMenu" style="text-align:right;"><a href="?action=add">Add News</a> | <a href="?action=list">Show Entries (<?=$total[0];?>)</a></td>
    </tr>
</table>
<script type="text/javascript">
//<![CDATA[
function conf(id)
{
    return confirm("Are You sure you want to delete news item # " + id + "??");
}
//]]>
</script>
<br />

<?php
switch ($posted['action']) {
    case 'fixup':
        break;

    case 'process':
        newsProcessEditForm();

        // Fix for bad design
        // If it's stay in 'process', everytime the page is refreshed, new news will be added
        // Need to redirect to 'list'
        echo "<script type='text/javascript'>window.location.href='/webadmin/news/';</script>";
        exit();

        break;

    case 'activate':
        $DB->query("UPDATE `news` SET `archived` = 'n' WHERE `news_id` = " . intval($posted['news_id']) . " LIMIT 1");
        deleteMysqlCacheData('news',intval($posted['news_id']));
        break;

    case 'deactivate':
        $DB->query("UPDATE `news` SET `archived` = 'y' WHERE `news_id` = " . intval($posted['news_id']) . " LIMIT 1");
        deleteMysqlCacheData('news',intval($posted['news_id']));
        break;

    case 'status_active':
        $DB->query("UPDATE `news` SET `active` = 'y' WHERE `news_id` = " . intval($posted['news_id']) . " LIMIT 1");
        deleteMysqlCacheData('news',intval($posted['news_id']));
        break;

    case 'status_deactive':
        $DB->query("UPDATE `news` SET `active` = 'n' WHERE `news_id` = " . intval($posted['news_id']) . " LIMIT 1");
        deleteMysqlCacheData('news',intval($posted['news_id']));
        break;

    case 'delete':
        $DB->query("DELETE FROM `news` WHERE `news_id` = " . intval($posted['news_id']) . " LIMIT 1");
        deleteMysqlCacheData('news',intval($posted['news_id']));
        break;
}

switch ($posted['action']) {
    case 'add':
        newsShowEditForm(0);
        break;

    case 'edit':
        newsShowEditForm($posted['news_id']);
        break;

    default:
    case 'list':
        newsShowAdminList();
        break;
}

include_once "footer.inc.php";
return;

function newsProcessEditForm()
{
    global $_GLOBALS;
    global $DB;
    
    global $posted, $get_vars;

    $data1   = $posted["data1"];
    $news_id = intval($data1['news_id']);
    $row     = $DB->query_first("SELECT * FROM `news` WHERE `news_id` = " . $news_id . " LIMIT 1");
    if ('' == $data1['date_posted']) {
        $data1['date_posted'] = time();
    } else {
        $split = explode('/', $data1['date_posted']);
        if ($split[2] < 1970) {
            $split[2] = '20' . substr($split[2], -2);
        }

        $data1['date_posted'] = mktime(0, 0, 0, intval($split[1]), intval($split[0]), intval($split[2]));
    }
    //if($data1['short_desc_raw']!='on') $data1['short_description'] = nl2br($data1['short_description']);
    //if($data1['long_desc_raw']!='on') $data1['long_description'] = nl2br($data1['long_description']);
    if ($row) {
        $update = array();
        foreach (array("archived", "active", "news_caption", "short_description", "meta_desc", "meta_key", "news_title", "meta_title", "change_by","slug","date_schedule") as $k) {
            if (array_key_exists($k, $data1) && $data1[$k] != $row[$k]) {
                $update[$k] = "`" . $k . "` = " . formatSQLString($data1[$k]);
            }
        }
        if (array_key_exists("category", $data1)) {
            $update['category']="`category` = " . formatSQLString(implode(',', $data1['category']));
        }
        foreach (array("long_description" /*, "image", "thumbnail", "author"*/) as $k) {
            if (array_key_exists($k, $data1) && $data1[$k] != $row[$k]) {
                if ("" != $data1[$k]) {
                    $update[$k] = "`" . $k . "` = " . formatSQLString($data1[$k]);
                } else {
                    $update[$k] = "`" . $k . "` = NULL";
                }

            }
        }

        if (array_key_exists("date_posted", $data1)) {
            $update["date_added"] = "`date_added` = " . date("'Y-m-d'", $data1["date_posted"]);
        }

        foreach (array('meta_title', 'meta_key', 'meta_desc','slug') as $k) {
            if (array_key_exists($k, $data1) && $row[$k] != $data1[$k]) {
                $update["last_change"] = "`last_change` = NOW()";
                $update["change_by"]   = "`change_by` = " . formatSQLString($_GLOBALS['staff']['user']);
            }
        }

        if(!isset($posted["data1"]['archived'])){
            $update['archived'] = "`archived` = 'n'";
        }
        if(!isset($posted["data1"]['schedule_news'])){
             $update['date_schedule'] = "`date_schedule` = '0000-00-00'";
        } 
        if (count($update)) {
            $DB->query("UPDATE `news` SET " . implode(", ", $update) . " WHERE `news_id` = " . $news_id . " LIMIT 1");
            addValueInJobHandle(1,'update');
            deleteMysqlCacheData('news',$news_id);
        }

    } else {
        $insert = array();
        foreach (array("archived", "active", "news_caption", "short_description", "meta_desc", "meta_key", "news_title", "meta_title", "change_by",'slug') as $k) {
            if (array_key_exists($k, $data1)) {
                $insert[$k] = formatSQLString($data1[$k]);
            }
        }
        if (array_key_exists("category", $data1)) {
            $insert['category']=formatSQLString(implode(',', $data1['category']));
        }
        foreach (array("long_description" /*, "image", "thumbnail", "author"*/) as $k) {
            if (array_key_exists($k, $data1)) {
                if ("" != $data1[$k]) {
                    $insert[$k] = formatSQLString($data1[$k]);
                } else {
                    $insert[$k] = "NULL";
                }

            }
        }

        if (array_key_exists("date_posted", $data1)) {
            $insert["date_added"] = date("'Y-m-d'", $data1["date_posted"]);
        }

        $insert["last_change"] = "NOW()";
        $insert["change_by"]   = formatSQLString($_GLOBALS['staff']['user']);
        if (count($insert)) {
            $DB->query("INSERT INTO `news` (`" . implode("`, `", array_keys($insert)) . "`) VALUES (" . implode(", ", array_values($insert)) . ")");
        } else {
            $DB->query("INSERT INTO `news` () VALUES ()");
        }
        $data1['news_id'] = $DB->insert_id();
        addValueInJobHandle(1,'create');
        $news_id = intval($data1['news_id']);
        deleteMysqlCacheData('news',$news_id);
        $DB->query("INSERT INTO `news_comments` (`news_id`, `comment_date`, `user`, `comment_text`) VALUES (" . $news_id . ", NOW(), " . formatSQLString($_GLOBALS['staff']['user']) . ", 'News Story Created')");
    }
    if(isset($_POST['product_image']) && count($_POST['product_image'])){
            foreach ($_POST['product_image'] as $key => $image) {
                $image['news_id']=$data1['news_id'];
                $image['file_orig']=$image['name'];
                $image['default_image']=(!isset($image['default_image']))?'n':'y';
                $image['online']=(!isset($image['online']))?'n':'y';
                $images = newsGenericsFromPost($image,'`news_images`');
                newsGenericToDatabase($images,'`news_images`');
            }
        }
    if(isset($_POST['product_video'])  && count($_POST['product_video'])){
            foreach ($_POST['product_video'] as $key => $video) {
                $video['news_id']=$data1['news_id'];
                $videos = newsGenericsFromPost($video,'`news_videos`');
                newsGenericToDatabase($videos,'`news_videos`');
            }
        }
     if (array_key_exists('item_images_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['item_images_deleted_val']) as $v) {
                if ($v) {
                          newsGenericsDelete($v,'`news_images`','id');
                }
            }
        }
    if (array_key_exists('news_video_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['news_video_deleted_val']) as $v) {
                if ($v) {
                          newsGenericsDelete($v,'`news_videos`','id');
                }
            }
        }
    $tmp_main  = $_FILES['data2']['tmp_name']['image_main_file'];
    $tmp_thumb = $_FILES['data2']['tmp_name']['image_thumb_file'];
    if ('' == $tmp_thumb) {
        $tmp_thumb = $_FILES['data2']['tmp_name']['image_main_file'];
    }

    $b_main    = time().'_'.$news_id . '.jpg';
    $b_thum    = time().'_'.$news_id . '.jpg';
    $new_main  = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/image/' . $b_main;
    $new_thumb = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/thumb/' . $b_thum;
    if (move_uploaded_file($tmp_main, $new_main)) {
        $DB->query("UPDATE `news` SET `image` = '" . $b_main . "' WHERE `news_id` = " . $news_id . " LIMIT 1");
    }
    if (move_uploaded_file($tmp_thumb, $new_thumb)) {
        $DB->query("UPDATE `news` SET `image` = '" . $b_thum . "' WHERE `news_id` = " . $news_id . " LIMIT 1");
    }

    if ('' != $data1['new_comment']) {
        $DB->query("INSERT INTO `news_comments` (`news_id`, `comment_date`, `user`, `comment_text`) VALUES (" . $data1['news_id'] . ", NOW(), " . formatSQLString($_GLOBALS['staff']['user']) . ", " . formatSQLString($data1['new_comment']) . ")");
    }
    echo "<script>window.location.href='/webadmin/news/?action=edit&news_id=".$news_id."'</script>";
}
function newsShowEditForm($news_id)
{
    global $posted, $get_vars, $DB;
    $sql   = "SELECT *, UNIX_TIMESTAMP(last_change) as last_change FROM news WHERE news_id='" . $news_id . "'";
    $data1 = $DB->query_first($sql, MYSQLI_ASSOC);
    if ('' == $data1['date_posted']) {
        $data1['date_posted'] = time();
    }
    $news_images=[];
    $news_videos=[];
    $sql       = "SELECT * FROM news_images WHERE news_id='" . $news_id . "' order by `rank` ";
    $query     = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $news_images[] = $row;
    }
    $sql       = "SELECT * FROM news_videos WHERE news_id='" . $news_id . "' order by `rank` ";
    $query     = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $news_videos[] = $row;
    }
    include 'news_edit_form.inc.php';
}
function newsShowAdminList()
{
    global $posted, $get_vars, $DB;
    $page  = intval($posted['page']);
    $get_vals = $get_vars;
    unset($get_vals['news_id']);
    $get_vals['action'] = 'activate';
    $activate_url       = '?' . http_build_query($get_vals) . '&news_id=';
    $get_vals['action'] = 'deactivate';
    $deactivate_url     = '?' . http_build_query($get_vals) . '&news_id=';
    $get_vals['action'] = 'edit';
    $edit_url           = '?' . http_build_query($get_vals) . '&news_id=';
    $get_vals['action'] = 'delete';
    $delete_url         = '?' . http_build_query($get_vals) . '&news_id=';
    $get_vals['action'] = 'status_active';
    $active_url         = '?' . http_build_query($get_vals) . '&news_id=';
    $get_vals['action'] = 'status_deactive';
    $deactive_url       = '?' . http_build_query($get_vals) . '&news_id=';
    $get_vals           = $get_vars;
    unset($get_vals['page']);
    $page_url    = '?' . http_build_query($get_vals) . '&page=';
    $where       = array();
    $search_list = array('news_id', 'long_description', 'short_description', 'news_title');
    $find_list   = explode(' ', $posted['search_for']);
    foreach ($find_list as $find) {
        foreach ($search_list as $field) {
            $where[] = $field . " LIKE '%" . addslashes($find) . "%'";
        }
    }

    $news_list = array();
    $sql_count = "SELECT COUNT(*) AS count FROM news " . ((count($where) > 0) ? ('WHERE ' . implode(' OR ', $where)) : '');
    $row_count = $DB->query_first($sql_count, MYSQLI_ASSOC);
    // $sql       = "SELECT * FROM news " . ((count($where) > 0) ? ('WHERE ' . implode(' OR ', $where)) : '') . " ORDER BY date_schedule DESC, date_added DESC  LIMIT " . ($page * 50) . ",50";
    $sql = "SELECT * FROM news " . ((count($where) > 0) ? ('WHERE ' . implode(' OR ', $where)) : '') . " ORDER BY 
        CASE 
            WHEN date_schedule = '0000-00-00' THEN date_added 
            ELSE date_schedule 
        END DESC, date_added DESC LIMIT " . ($page * 50) . ", 50";
    $query     = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $news_list[] = $row;
    }

?>
<table style="width:95%;border:0px" align="center">
    <form accept-charset="utf-8" action="" method="post">
    <tr style="background-color:#F0F4F9" >
        <td width="100%">&nbsp;</td>
        <td style="white-space:nowrap;">
            Search
            <input type="text" style="width:192px;" name="search_for" value="<?=str_replace('"', '&quot;', $posted['search_for']);?>" />
            <input type="hidden" name="page" value="0" />
            <input type="submit" value="Go" />
        </td>
    </tr>
    </form>
</table>

<br />

<table cellspacing="1" cellpadding="2" style="width:95%" align="center" class="table_grid">
    <tr bgcolor="#000066" style="text-align:center">
        <th>ID</td>
        <th>Title</td>
        <th>Date&nbsp;Posted</td>
        <th>Date&nbsp;Schedule</td>
        <th>Front</td>
        <th>Active</td>
        <th>&nbsp;</td>
    </tr>
<?php
foreach ($news_list as $item) {
         //style="background-color:#EEEEEE"
    $url_news=sprintf(
            '/n/%s-%d.html',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $item['news_title']), '-')),
            $item['news_id']
        );
?>
    <tr<?=($alt = !$alt) ? '' : ' class="alt"';?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='<?=$item['active'] == 'y'? '#90EEA0':'' ?>';"  <?= $item['active'] == 'y' ? 'style="background-color:#90EEA0;line-height:20px;"' : 'style="line-height: 20px;"'?>>
        <td><?=$item['news_id'];?></td>
        <td style="text-align:left;"><a target="_blank" href="<?= $url_news ?>"><?=stripslashes(str_replace('<', '&lt;', $item['news_title']));?></a></td>
        <td style="white-space:nowrap;"><?= !empty($item['date_schedule']) && $item['date_schedule'] != '0000-00-00' && $item['date_schedule'] > date('Y-m-d') ? '' : (!empty($item['date_schedule']) && $item['date_schedule'] != '0000-00-00' ? date('d/m/Y', strtotime($item['date_schedule'])) : date('d/m/Y', strtotime($item['date_added']))); ?></td>
        <!-- <td style="white-space:nowrap;"><?=date('d/m/Y', strtotime($item['date_added']));?></td> -->
        <td style="white-space:nowrap; <?= ($item['date_schedule'] > date('Y-m-d'))?'background-color:pink':''?>" ><?= ($item['date_schedule'] == '0000-00-00' || $item['date_schedule'] < date('Y-m-d'))?'' : date('d/m/Y', strtotime($item['date_schedule']));?></td>
        <td style="text-align:center;">
            <?=('y' == $item['archived']) ? '<a href="' . $activate_url . $item['news_id'] . '">Yes</a>' : '<a href="' . $deactivate_url . $item['news_id'] . '">No</a>';?>
        </td>
        <td style="text-align:center;">
            <?=('y' == $item['active']) ? '<a href="' . $deactive_url . $item['news_id'] . '">Yes</a>' : '<a href="' . $active_url . $item['news_id'] . '">No</a>';?>
        </td>
        <!--<td style="text-align:center;"><?=$item['active'];?></td>-->
        <td style="white-space:nowrap;">
            <a href="<?=$edit_url . $item['news_id'];?>" target="_blank">Edit</a>
           <!--  | <a href="<?=$delete_url . $item['news_id'];?>" onclick="return conf(<?=$item['news_id'];?>)">Delete</a> -->
        </td>
    </tr>
<?php
}
?>
</table>

<?php
echo '<p align="right">';
    $pages = floor(($row_count['count'] + 49) / 50);
    if ($page > 0) {
        echo '[<a style="color:blue" href="' . $page_url . '0">Start</a>][<a style="color:blue" href="' . $page_url . ($page - 1) . '">Back</a>]';
    }

    if ($page < ($pages - 1)) {
        echo '[<a style="color:blue" href="' . $page_url . ($page + 1) . '">Next</a>][<a style="color:blue" href="' . $page_url . ($pages - 1) . '">End</a>]';
    }

    echo '</p>';
}
?>
</div>
