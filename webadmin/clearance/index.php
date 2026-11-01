<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/common.inc.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/header.inc.php";
global $DB;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
if (!function_exists('productGenericsFromPost')) {
    function productGenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns($table);
        foreach ($posted as $k1 => $v1) {
            $item = array();
            foreach ($fields as $k2 => $v2) {
                if (array_key_exists($k2, $v1)) {
                    $item[$k2] = $v1[$k2];
                }
            }
            $result[$k1] = $item;
        }
        return $result;
    }
}
if (!function_exists('productScreenshotsFromPost')) {
    function productScreenshotsFromPost($posted,$files=[])
    {
        foreach ($posted as $key => $value) {
            if(array_key_exists($key, $files['name'] ?? [])){
                $file_orig = $files['name'][$key]['image'];
                $file_temp = $files['tmp_name'][$key]['image'];
                $error     = $files['error'][$key]['image'];
                $file_ext = substr($file_orig, strrpos($file_orig, '.'));
                if (substr($file_ext, 0, 1) == '.') {
                    $file_ext = substr($file_ext, 1);
                }
                $file_new  = "ss_" . time() . '_' . uniqid() . '.' . $file_ext;
                $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/clearance/';
                if (move_uploaded_file($file_temp, $dir . $file_new)) {
                    $posted[$key]['filename']=$file_new;
                    $posted[$key]['file_orig']=$file_orig;
                }
            }
        }
        return productGenericsFromPost($posted, '`clearance_screenshots`');
    }
}
if (!function_exists('productGenericToDatabase')) {
    function productGenericToDatabase(&$item, $table, $primary_key = 'id') {
        /*require_once "/home/arcooda/public_html/protected/cache/CacheService.php";
        error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        global $DB, $_GLOBALS;
        $id         = intval($item[$primary_key]);
        $fields     = $DB->table_columns($table);
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
        //$cacheService->deleteData($table, $id);
        return $id;
    }
}
if (!function_exists('productGenericsToDatabase')) {
    function productGenericsToDatabase( &$items, $table, $primary_key = 'id') {
        foreach ($items as &$v) {
            productGenericToDatabase($v, $table, $primary_key);
        }
    }
}
if (!function_exists('productScreenshotsToDatabase')) {
    function productScreenshotsToDatabase(&$items)
    {
        return productGenericsToDatabase($items, '`clearance_screenshots`', 'id');
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $DB->query("UPDATE `clearance_items` SET `description` = '".$_POST['item']['description']."',`meta_title` = '".$_POST['item']['meta_title']."',`meta_keywords` = '".$_POST['item']['meta_keywords']."',`meta_description` = '".$_POST['item']['meta_description']."',`modified_at` = '".date("y-m-d h:i:s")."',`modified_by` = ".$_GLOBALS['staff']['id'].",`active` = '".$_POST['item']['active']."' WHERE `id` = 1 LIMIT 1");

    if (array_key_exists('item_screenshots_deleted_val', $_POST)) {
        foreach (preg_split('/[^0-9]+/', $_POST['item_screenshots_deleted_val']) as $v) {
            if ($v) {
                $DB->query("DELETE  from `clearance_screenshots` WHERE `id` = $v LIMIT 1");
            }
        }
    }
    if (array_key_exists('item_screenshots', $_POST)) {
        $item_screenshots = productScreenshotsFromPost($_POST['item_screenshots'],$_FILES['item_screenshots']);
        productScreenshotsToDatabase($item_screenshots);
    }
}
$item = $DB->query_first("SELECT * FROM `clearance_items` WHERE `id` = 1  LIMIT 1", MYSQLI_ASSOC);
$item_screenshots = [];
$query = $DB->query(sprintf(
    "SELECT *  FROM `clearance_screenshots` WHERE `active` = 'y' ORDER BY `rank` ASC"
));
while ($row = $DB->fetch_assoc($query)) {
    $item_screenshots[] =$row;
}
?>
<script type="text/javascript">
//<![CDATA[
function link_insert(name) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<a href="http://www.WEBSITE" target="_blank">' + theElem.value.substring(a, b) + '</a>' + theElem.value.substring(b);
        theElem.selectionStart = a + 20;
        theElem.selectionEnd = a + 27;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<a href="http://www.WEBSITE" target="_blank">' + seltext.text + '</a>';
        else
            theElem.value += '<a href="http://www.WEBSITE" target="_blank">LINK</a>';
    }
}
function generic_insert(name, tag) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<' + tag + '>' + theElem.value.substring(a, b) + '</' + tag + '>' + theElem.value.substring(b);
        theElem.selectionStart = a + 2 + tag.length;
        theElem.selectionEnd = 2 + tag.length + b;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<' + tag + '>' + seltext.text + '</' + tag + '>';
        else
            theElem.value += '<' + tag + '>BOLDTEXT</' + tag + '>';
    }
}
function str_replace(str_normal, str_find, str_replace) {
    // case sensitive function at the moment...
    return (str_normal.split(str_find)).join(str_replace);
}

function getElement(name) {
    var theElem = document.getElementById(name);
    if (theElem != null) return theElem;
    var theElems = document.getElementsByName(name);
    if (theElems.length > 0) return theElems[0];
    return null;
}
function toggleSection(sectionName) {
    var theSect = getElement(sectionName);
    if (theSect == null) return;
    if (theSect.style.display == 'none')
        theSect.style.display = 'inline';
    else
        theSect.style.display = 'none';
}
//]]>
</script>
<link rel="stylesheet" type="text/css" href="style.css" />

<div class="product_content">
<form accept-charset="utf-8" action="" method="post" enctype="multipart/form-data">
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;Firmware&quot;);return false">Clearance  Offers</a>')?>

<div id="sectionDesc">
	<table class="table_fields" style="width:95%" align="center">
		<tr>
			<th>
                <div style="padding: 20px; text-align: left; border: 1px solid red; width: 30%;"><a href="/clearance" target="_blank">View Online</a></div>
                <br/>
                Clearance Text:
            </th>
			<td >
				<?php $desc     = ('' == $item['description']) ? '' : str_replace('<', '&lt;', str_replace('<br />', '', $item['description']))?>
				<textarea name="item[description]" rows="20"  oninput="adjustTextareaHeight(this);"><?=trim($desc)?></textarea>
				<br />
				<input type="button" value="Insert Link" onclick="javascript:link_insert('item[description]');" />
				<input type="button" value="Bold" onclick="javascript:generic_insert('item[description]','b');" />
				<input type="button" value="Italics" onclick="javascript:generic_insert('item[description]','i');" />
				<input type="button" value="Underline" onclick="javascript:generic_insert('item[description]','u');" />
				<input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('item[description]','li');" />
                <input type="button" value="YouTube" onclick="javascript:insertYouTubeIframe('item[description]');" />
                 <input type="button" value="Image" onclick="javascript:insertNewImage('item[description]');" />
                <span>
                    Text Color:
                    <input type="color" id="textColorPicker1" name="item[text_color]" value="#000000" 
                           onmousedown="saveSelection('item[description]');" 
                           onchange="applyTextColorWithPicker('item[description]','textColorPicker1');">
                </span>
			</td>
		</tr>
	</table>
</div>
<?php 
include __DIR__ . "/_meta_tags.tpl.php"; 
include __DIR__ . "/clearance_screenshots.tpl.php"; 
?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;Firmware&quot;);return false">Status</a>')?>
<div id="sectionSta">
	<table class="table_fields"align="center">
	<tr>
		<th>Active :</th>
		<td>
                <select name="item[active]" style="background: <?=('n' == $item['active']) ? '#FF0000' : '#00FF00'?>;" onchange="this.style.backgroundColor=((this.value=='n')?'#FF0000':'#00FF00');" onchange="updateSelect(this);return false;">
    			<option style="background: green;" value="y"<?=('y' == $item['active']) ? ' selected="selected"' : ''?>>Yes</option>
    			<option style="background:red" value="n"<?=('n' == $item['active']) ? ' selected="selected"' : ''?>>No</option>
    		</select>
        </td>
	</tr>
	</table>
</div>
<div align="center"><input type="submit" value="Edit" style="width:100px"/> </div>
</form>
</table>
</div>
<script type="text/javascript">
    $(function () {
        $('textarea').each(function () {
            adjustTextareaHeight(this);
        });
    });
    function applyTextColorWithPicker(textareaId , elementId) {
        const colorPicker = document.getElementById(elementId);
        const color = colorPicker.value; // Selected color value
        if (!color) return;

        const textarea = document.querySelector(`[name="${textareaId}"]`);
        if (!textarea) return;

        // Use saved selection range if available
        const selectionStart = savedSelectionStart;
        const selectionEnd = savedSelectionEnd;

        if (selectionStart !== null && selectionEnd !== null && selectionStart !== selectionEnd) {
            const selectedText = textarea.value.substring(selectionStart, selectionEnd);
            const coloredText = `<span style="color:${color};">${selectedText}</span>`;
            textarea.value =
                textarea.value.substring(0, selectionStart) +
                coloredText +
                textarea.value.substring(selectionEnd);

            // Update selection positions and focus the textarea
            textarea.selectionStart = selectionStart;
            textarea.selectionEnd = selectionStart + coloredText.length;
            textarea.focus();
        } else {
            alert("Please select text to apply the color.");
        }
    }
    function saveSelection(textareaId) {
        const textarea = document.querySelector(`[name="${textareaId}"]`);
        if (textarea) {
            savedSelectionStart = textarea.selectionStart;
            savedSelectionEnd = textarea.selectionEnd;
        }
    }
    function insertYouTubeIframe(textareaId) {
        const youtubeLink = prompt("Enter the YouTube video link:");
        if (!youtubeLink) return;

        const videoIdMatch = youtubeLink.match(/(?:https?:\/\/)?(?:www\.)?youtu(?:be\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|\.be\/)([a-zA-Z0-9_-]{11})/);
        if (!videoIdMatch || !videoIdMatch[1]) {
            alert("Invalid YouTube URL. Please try again.");
            return;
        }
        const videoId = videoIdMatch[1];
        const iframeHTML = `<iframe width="640" height="480"  src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;

        const textarea = document.querySelector(`[name="${textareaId}"]`);
        if (!textarea) return;

        const { selectionStart, selectionEnd } = textarea;

        if (selectionStart !== null && selectionEnd !== null) {
            textarea.value =
                textarea.value.substring(0, selectionStart) +
                iframeHTML +
                textarea.value.substring(selectionEnd);

            textarea.selectionStart = selectionStart;
            textarea.selectionEnd = selectionStart + iframeHTML.length;
            textarea.focus();
        } else {
            textarea.value += iframeHTML;
        }
    }
    function insertNewImage(textareaId) {
        const imageLink = prompt("Enter Copied image Url:");
        const caption = prompt("Enter Image Caption:");
        if (!imageLink) return;
        const imageHTML = `<a href="${imageLink}?width=700&height=700" target="_blank"><img src="${imageLink}?width=150&height=150" width="150" alt="${caption}" title="${caption}"></a>`;
        const textarea = document.querySelector(`[name="${textareaId}"]`);
        if (!textarea) return;

        const { selectionStart, selectionEnd } = textarea;

        if (selectionStart !== null && selectionEnd !== null) {
            textarea.value =
                textarea.value.substring(0, selectionStart) +
                imageHTML +
                textarea.value.substring(selectionEnd);

            textarea.selectionStart = selectionStart;
            textarea.selectionEnd = selectionStart + imageHTML.length;
            textarea.focus();
        } else {
            textarea.value += imageHTML;
        }
    }
</script>
