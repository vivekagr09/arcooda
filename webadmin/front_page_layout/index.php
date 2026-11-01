<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';

$action = "do_add";
$id = 0;
if (!function_exists('frontMenuGenericsFromPost')) {
    function frontMenuGenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
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
if (!function_exists('frontMenuGenericToDatabase')) {
    function frontMenuGenericToDatabase(&$item, $table, $primary_key = 'id') {
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

if (!function_exists('frontMenuGenericsDelete')) {
    function frontMenuGenericsDelete($id, $table, $secondary_key = 'view_id') {
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
if (!function_exists('frontYoutubeGenericsDelete')) {
    function frontYoutubeGenericsDelete($id, $table, $secondary_key = 'id') {
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
if (isset($_GET['action']) && !empty($_GET['action'])) {
    if ($_GET['action'] == 'do_add') {
        $action = "do_add";
    } else if ($_GET['action'] == 'do_edit') {
        $action = "do_edit";

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
        }

    } else if($_GET['action'] == 'do_delete' && !empty($_GET['id']) ){
        frontYoutubeGenericsDelete($_GET['id'],'`front_page_youtube_video`');
    } else {
        die("Incorrect action");
    }
}
$youtube_video=[];
            $query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  %s",
                '`front_page_youtube_video`',
                'ORDER BY `rank` ASC'
            ));
         while ($row = $DB->fetch_assoc($query)) {
                if (is_null($primary_key)) {
                    $youtube_video[] = $row;
                } 
            }
$DB->free_result($query);
$page_layout=[];
            $query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  %s",
                '`front_page_layout`',
                'ORDER BY `id` ASC'
            ));
         while ($row = $DB->fetch_assoc($query)) {
                if (is_null($primary_key)) {
                    $page_layout[] = $row;
                } 
            }
$DB->free_result($query);
$error = array();

if (!empty($_POST)) {
        if(count($posted['youtubevideo'])){
            foreach ($posted['youtubevideo'] as $key => $menu) {
                $videos = frontMenuGenericsFromPost($menu,'`front_page_youtube_video`');
                frontMenuGenericToDatabase($videos,'`front_page_youtube_video`');
            }
        }
        if(count($posted['page_layout'])){
            foreach ($posted['page_layout'] as $key => $menu) {
                $layout = frontMenuGenericsFromPost($menu,'`front_page_layout`');
                frontMenuGenericToDatabase($layout,'`front_page_layout`');
            }
        }
    echo "<script>window.location.href='/webadmin/front_page_layout'</script>";
    
}
function url($name,$id)
{
    return sprintf(
        '/brand/%s-%d',
        urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-')),
        $id
    );
}
?>


<form action="?action=<?= $action ?>&id=<?= $id ?>" method="post">
    <?php 
     include __DIR__ . '/front_page_layout.php';
    include __DIR__ . '/front_youtube_video.php';
    ?>
    <div style="text-align:center;">
        <button type="submit" style="padding: 12px 81px;cursor:pointer;" >Submit</button>
        <span style="float:right;margin-right: 50px;"><i><span style="color:red"><i><small>*</small></span> - Required Field</i></span>
    </div>
</form>

<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>

<?php
include_once __DIR__ . '/footer.inc.php';
