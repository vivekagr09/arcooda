<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';
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
        $id         = $item[$primary_key];
        $fields     = $DB->get_table_columns($table);
        $old_vals   = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %s LIMIT 1",
            $table,
            $primary_key,
            $DB->escape($id),
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
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %s LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $DB->escape($id),
                ));
            }
        } 
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

        $query = $DB->query(sprintf(
            "SELECT * FROM %s %s %s",
            '`options`',
            'WHERE `flag`=0',
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
        foreach ($_POST['data'] as $key => $dataval) {
            // Add the ID to the data array
            $data['variable'] = $key;
            $data['value'] = $dataval;
            // Prepare the metadata for update
            // Update the database
            frontMenuGenericToDatabase($data, '`options`','variable');
        }
        echo "<script>window.location='/webadmin/sitemeta?message=meta tag updated successfully'</script>";
    }

    
}
?>

<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript">
function SEOCheckMeta(type, input, productName) {
    var productNameChunk = productName.split(" ");

    var totalTicked = 0;
    var unmatchedWords = [];
    for(var i = 0; i < productNameChunk.length; i++) {
        if (input.value.match(new RegExp(productNameChunk[i], 'i'))) {
            document.getElementById(type + '-keyword-' + i).style.color = "gray";
            totalTicked++;
        } else {
            document.getElementById(type + '-keyword-' + i).style.color = "red";
            unmatchedWords.push(productNameChunk[i]);
        }
    }
    
    // Output unmatched words to the console
    console.log("Unmatched words:", unmatchedWords);

    if (totalTicked == productNameChunk.length) {
        document.getElementById(type + '-status').innerHTML = "Keywords all good";
        document.getElementById(type + '-status').style.color = "green";
    } else {
        document.getElementById(type + '-status').innerHTML = "Keywords still not used";
        document.getElementById(type + '-status').style.color = "black";
    }
}
function updateTotals(cyp, hwm) {
    if (cyp == null) cyp = false;
    if (hwm == null) hwm = false;

    var meta = getValue('data[META_KEYWORDS' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']');
    //now update the length of the words
    var keywords = meta.split(',');
    var len = 0;
    for (i = 0; i < keywords.length; i++) if (keywords[i] != '') len++;
    getElement('meta_title'+ ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data[META_TITLE' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
    getElement('meta_keys' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = len;
    getElement('meta_desc'+ ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data[META_DESCRIPTION' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
}
//]]>
</script>
<div class="news_content">
    <form action="" method="post">
        <?php 
           include __DIR__ . '/meta_tags.tpl.php';
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
