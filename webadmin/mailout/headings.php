<?php
include_once __DIR__ . '/common.inc.php';

function draw_insertable_headings()
{
    global $DB;

    $agent        = get_agent();
    $statement    = "SELECT * FROM mailout_types";
    $result       = $DB->query($statement);
    $mailout_type = array();
    $today_date   = date('jS F, Y', time());
    while ($row = $DB->fetch_array($result)) {
        $banner_file = $_SERVER['DOCUMENT_ROOT'] . '/' . $row['banner_image'];
        if (is_file($banner_file)) {
            $banner_info      = getimagesize($banner_file);
            $banner_image     = '<img src="http://' . $_SERVER['SERVER_NAME'] . '/' . $row['banner_image'] . '" border="0" ' . $banner_info[3] . ' alt="' . $row['type_name'] . '">';
            $row['html_desc'] = str_ireplace("[banner]", $banner_image, $row['html_desc']);
        } else {
            $row['html_desc'] = str_ireplace("[banner]", $row['type_name'], $row['html_desc']);
        }
        $row['html_desc'] = str_ireplace("[date]", $today_date, $row['html_desc']);
        $row['text_desc'] = str_ireplace("[date]", $today_date, $row['text_desc']);
        $row['text_desc'] = str_ireplace("[banner]", $row['type_name'], $row['text_desc']);
        $row['html_desc'] = unhtmlentities($row['html_desc']);
        $row['text_desc'] = unhtmlentities($row['text_desc']);
        $row['text_desc'] = str_replace("'", "\\'", $row['text_desc']);
        $row['html_desc'] = str_replace("'", "\\'", $row['html_desc']);
        $mailout_type[]   = $row;
    }
?>
<SCRIPT LANGUAGE="JAVASCRIPT">
<!--

function insert(object, j)
{
	var text = new Array();

<?php
for ($i = 0; $i < count($mailout_type); $i++) {
        echo "text[$i] = '" . $mailout_type[$i]['html_desc'] . "';\n";
    }

    if ('IE' == $agent['browser'] && intval($agent['version']) >= 5) {?>
	editor_insertHTML(object, text[j]);
<?php
} else {?>
	insertText(object, text[j]);
<?php
}?>

}

function insertPlainText(object, j)
{
	var text = new Array();
<?php
for ($i = 0; $i < count($mailout_type); $i++) {
        echo "text[$i] = '" . $mailout_type[$i]['text_desc'] . "';\n";
    }
?>
	insertText(object, text[j]);
}

function insertText(object, text)
{
	for (i = 0; i < document.mailout_admin.elements.length; i++)
	{
		if (document.mailout_admin.elements[i].type == 'textarea')
		{
			if (document.mailout_admin.elements[i].name == object)
			{
				document.mailout_admin.elements[i].value += text;
			}
		}
	}
}

//-->
</script>
<!-- inserting headings -->
<?php
for ($i = 0; $i < count($mailout_type); $i++) {
?>
	<a href="javascript:insert('message', <?=$i;?>);insertPlainText('text_message', <?=$i;?>);"><?=$mailout_type[$i]['type_name'];?></a>
<?php
if ($i < count($mailout_type) - 1) {
            echo ' | ';
        }

    }
?>
<!--//inserting headings -->

<?php
}

draw_insertable_headings();
?>
<div align="right"><b><a href="mailout_types_admin.php?query=" target="_blank">Add/Edit Types</a></b></div>