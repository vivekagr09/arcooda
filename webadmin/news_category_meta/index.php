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
                        $result[$k1] = html_entity_decode($v1);
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
if (isset($_GET['action']) && !empty($_GET['action'])) {
    if ($_GET['action'] == 'do_add') {
        $action = "do_add";
    } else if ($_GET['action'] == 'do_edit') {
        $action = "do_edit";

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
        }

    } else {
        die("Incorrect action");
    }
}
            $query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  %s",
                '`news_category_meta`',
                'ORDER BY `id` ASC'
            ));
         while ($row = $DB->fetch_assoc($query)) {
                if (is_null($primary_key)) {
                    $fetchdata[] = $row;
                } 
            }
            $DB->free_result($query);
$error = array();

if (!empty($_POST)) {
        // echo "<pre>"; print_r($_POST); die;
        // if(count($posted['metadata'])){
        //     foreach ($posted['metadata'] as $key => $data) {
        //         $metadata = frontMenuGenericsFromPost($data,'`news_category_meta`');
        //         frontMenuGenericToDatabase($metadata,'`news_category_meta`');
        //     }
        // }
        // Check if the 'data' array is present in the posted data
    if (isset($_POST['data']) && count($_POST['data'])) {
        foreach ($_POST['data'] as $key => $data) {
            // Add the ID to the data array
            $data['id'] = $key;

            // Prepare the metadata for update
            $metadata = frontMenuGenericsFromPost($data, '`news_category_meta`');

            // Update the database
            frontMenuGenericToDatabase($metadata, '`news_category_meta`');
        }
    }
    echo "<script>window.location.href='/webadmin/news_category_meta'</script>";
    
}
?>

<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript">
function SEOCheckMeta(type, input, productName) {
    var productNameChunk = productName.split(" ");

    var totalTicked = 0;

    for(var i = 0; i < productNameChunk.length; i++) {
        if (input.value.match(new RegExp(productNameChunk[i], 'i'))) {
            document.getElementById(type + '-keyword-' + i).style.color = "gray";
            totalTicked++;
        } else {
            document.getElementById(type + '-keyword-' + i).style.color = "red";
        }
    }

    if (totalTicked == productNameChunk.length) {
        document.getElementById(type + '-status').innerHTML = "Keywords all good";
        document.getElementById(type + '-status').style.color = "green";
    } else {
        document.getElementById(type + '-status').innerHTML = "Keywords still not used";
        document.getElementById(type + '-status').style.color = "black";
    }
}
function updateTags(){
    $("[name='data1[meta_title]']").val($("[name='data1[news_title]']").val()).change();
    $("[name='data1[meta_key]']").val(($("[name='data1[news_title]']").val() + $("[name='data1[short_desc]']").val()).replace(/[^a-z0-9\-\"]+/gi, ', ').replace(/[,\s]+$/, '')).change();
    $("[name='data1[meta_desc]']").val($("[name='data1[short_description]']").val()).change();
    $("[name='data1[slug]']").val($("[name='data1[news_title]']").val().replace(/[^a-z0-9]+/gi, '-')).change();
    return false;
}
function clearTags() {
    $("[name='data[meta_title]']").val("").change();
    $("[name='data[meta_key]']").val("").change();
    $("[name='data[meta_desc]']").val("").change();
    $("[name='data[slug]']").val("").change();
    return false;
}
function updateTotals(id,cyp, hwm) {
    if (cyp == null) cyp = false;
    if (hwm == null) hwm = false;

    var meta = getValue('data['+id+'][meta_key' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']');
    //now update the length of the words
    var keywords = meta.split(',');
    var len = 0;
    for (i = 0; i < keywords.length; i++) if (keywords[i] != '') len++;
    getElement('meta_title_'+id + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data['+id+'][meta_title' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
    getElement('meta_keys_'+id + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = len;
    getElement('meta_desc_'+id + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data['+id+'][meta_desc' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
}
//]]>
</script>
<div class="news_content">
    <form action="?action=<?= $action ?>&id=<?= $id ?>" method="post">
        <?php 
        foreach ($fetchdata as $data1){
             include __DIR__ . '/meta_tags.tpl.php';
        } 
       
        ?>
        <div style="text-align:center;">
            <button type="submit" style="padding: 12px 81px;cursor:pointer;margin-top:20px;" >Submit</button>
            <span style="float:right;margin-right: 50px;"><i><span style="color:red"><i><small>*</small></span> - Required Field</i></span>
        </div>
    </form>
<div>
<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>

<?php
include_once __DIR__ . '/footer.inc.php';
