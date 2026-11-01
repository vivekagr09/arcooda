<?php
include_once __DIR__ . '/common.inc.php';

$sections1 = array(
    'email_template' => array(
        'title'      => 'Newsletter Email Templates',
        'table_name' => '`highwaygroup`.`email_templates`',
        'table_key'  => 'template_id',
        'table_key2' => 'template_name',
        'where'      => "WHERE FIND_IN_SET('newsletters', template_group)",
    ),
);

$action  = $posted['action'];
$section = '';
$item_id = 0;
if ('' != $posted['section'] && array_key_exists($posted['section'], $sections1)) {
    $section = $posted['section'];
    $item_id = intval($posted[$sections1[$section]['table_key']]);
}

//processing switch-case..
if ('' != $section) {
    switch ($posted['action']) {
        case 'do_add':
            unset($posted[$sections1[$section]['table_key']]);
        case 'do_edit':
            doEdit($section, $posted);
            header('Location: newsletter_options.php?section=' . $section);
            die();
            break;
    }
}

include_once __DIR__ . '/header.inc.php';

if ('' != $section) {
    switch ($posted['action']) {
        case 'add':
            $item_id = 0;
        case 'edit':
            addEditOption($section, $item_id);
            break;

        default:
            optionList($section);
            break;
    }
} else {
    displayAll();
}

include_once __DIR__ . '/footer.inc.php';
die();

function displayAll()
{
    global $sections1;
?>
<table class="spacetable" align="center">
	<tr><th>Select an Option</th></tr>
<?php
foreach ($sections1 as $k => $v) {
?>
	<tr<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td style="padding:8px;"><a target="_blank" href="?section=<?php echo $k ?>"><?php echo $v['title'] ?></a></td>
	</tr>
<?php
}
?>
</table>
<br /><br />
<?php
}

function optionList($section_id)
{
    global $DB, $sections1;
    if ('' == $section_id || !array_key_exists($section_id, $sections1)) {
        return;
    }

    $section  = $sections1[$section_id];
    $query    = $DB->query("SELECT * FROM " . $section['table_name'] . ' ' . $section['where'] . " ORDER BY " . $section['table_key'] . " ASC");
    $add_url  = '?action=add&section=' . $section_id;
    $edit_url = '?action=edit&section=' . $section_id . '&' . $section['table_key'] . '=';
?>
<table class="spacetable" align="center">
	<tr><th colspan="4"><?php echo $section['title'] ?></th></tr>
	<tr style="background-color:#def;">
		<th style="background-color:transparent;">ID</th>
		<th style="background-color:transparent;width:100%;">Name</th>
		<th style="background-color:transparent;">&nbsp;</th>
	</tr>
<?php
while ($row = $DB->fetch_assoc($query)) {
?>
	<tr<?php echo ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td style="text-align:center;padding:8px;"><?php hecho($row[$section['table_key']]) ?></td>
		<td style="padding:8px;"><?php hecho($row[$section['table_key2']]) ?></td>
		<td style="padding:8px;"><a href="<?php hecho($edit_url . $row[$section['table_key']]) ?>">Edit</a></td>
	</tr>
<?php
}
?>
</table>
<br /><br />
<p style="text-align:center;"><a href="<?php hecho($add_url) ?>" target="_blank">Add New Item</a></p>
<br /><br />
<?php
}

function addEditOption($section_id, $item_id = 0)
{
    switch ($section_id) {
        case 'email_template':
            addEditEmailTemplates($item_id);
            break;

        default:
            echo 'Function currently not handled';
    }
}

function doEdit($section_id, $posted)
{
    global $DB, $sections1;
    $section    = $sections1[$section_id];
    $row        = $DB->query_first("SELECT " . $section['table_key'] . " FROM " . $section['table_name'] . " WHERE " . $section['table_key'] . "=" . intval($posted[$section['table_key']]) . " LIMIT 1", MYSQLI_ASSOC);
    $insert_new = true;
    if ('' != $row[$section['table_key']]) {
        $insert_new = false;
    }

    $fields = array();
    $query  = $DB->query("SHOW COLUMNS FROM " . $section['table_name']);
    while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
        $fields[$row[0]] = $row;
    }

    $DB->free_result($query);
    $insert_values = array();
    $update_values = array();
    foreach ($fields as $field_id => $data) {
        if (array_key_exists($field_id, $posted) && $field_id != $section['table_key']) {
            if (substr($data[1], 0, 4) == 'set(') {
                $values = formatSQLString($posted[$field_id]);
                if (is_array($posted[$field_id])) {
                    $values = formatSQLString(implode(',', $posted[$field_id]));
                }

                $update_values[]          = $field_id . "=" . $values;
                $insert_values[$field_id] = $values;
            } else {
                $update_values[]          = $field_id . "=" . formatSQLString($posted[$field_id]);
                $insert_values[$field_id] = formatSQLString($posted[$field_id]);
            }
        }
    }

    if ($insert_new) {
        $sql = "INSERT INTO " . $section['table_name'] . " (" . implode(',', array_keys($insert_values)) . ") VALUES (" . implode(',', array_values($insert_values)) . ")";
    } else {
        $sql = "UPDATE " . $section['table_name'] . " SET " . implode(',', $update_values) . " WHERE " . $section['table_key'] . "=" . intval($posted[$section['table_key']]) . " LIMIT 1";
    }

    if (count($update_values) > 0 && count($insert_values) > 0) {
        $DB->query($sql);
    }

}

function addEditEmailTemplates($item_id = 0)
{
    global $DB, $sections1;
    $section_id = 'email_template';
    $section    = $sections1[$section_id];
    $row        = $DB->query_first("SELECT * FROM " . $section['table_name'] . " WHERE " . $section['table_key'] . "=" . intval($item_id) . " LIMIT 1", MYSQLI_ASSOC);
?>
<h5>Use this form to <?php echo (0 == $item_id) ? 'Add to' : 'Update' ?> <?php echo $section['title'] ?></h5>
<form accept-charset="utf-8" action="?action=do_edit&section=<?php echo $section_id ?>" method="post">
<?php
if (0 != $item_id) {
?>
<input type="hidden" name="<?php echo $section['table_key'] ?>" value="<?php echo $item_id ?>" />
<input type="hidden" name="template_group" value="<?php hecho($row['template_group']) ?>" />
<input type="hidden" name="template_subject" value="<?php hecho($row['template_subject']) ?>" />
<?php
} else {
?>
<input type="hidden" name="template_group" value="newsletters" />
<?php
}
?>
<table align="center">
	<tr>
		<td class="left_title" style="width:25%;">Template Description :</td>
		<td><input class="intext" type="text" name="template_name" value="<?php hecho($row['template_name']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Template Content* :</td>
		<td><textarea name="template_text" rows="<?php echo max(0, count(explode("\n", $row['template_text']))) + 10 ?>"><?php hecho($row['template_text']) ?></textarea></td>
	</tr>
</table>
<br /><br />
<center><input type="submit" value="<?php echo (0 == $item_id) ? 'Add' : 'Update' ?>" /></center><br />
<br /><br />
<?php
$test_text = hecho($row['template_text'], 1);
    $keywords  = array();
?>
<p style="text-align:left;width:95%;">
*The following items are newsletter related:<br />
<table>
<?php
global $section_handler;
    $fields_list = array_keys($section_handler->templateVars());
    $keywords    = array_merge($keywords, $fields_list);
    asort($fields_list);
    $chunked = array_chunk($fields_list, 3);
    foreach ($chunked as $chunk_row) {
?>
	<tr>
		<td><?php hecho($chunk_row[0]) ?></td>
		<td><?php hecho($chunk_row[1]) ?></td>
		<td><?php hecho($chunk_row[2]) ?></td>
	</tr>
<?php
}
?>
</table>
<br />
Note: There may be some other unlisted fields that can be used<br />
</p>
<br /><br />
<?php
$keywords = array_unique($keywords);
    foreach ($keywords as $field) {
        $test_text = str_replace($field, '<span style="color:#66bb88;">' . $field . '</span>', $test_text);
    }

?>
Preview:
<p style="text-align:left;width:512px;font-family:courier;background-color:white;border:1px solid #66bb88;padding:8px 8px 8px 8px;white-space:pre;"><?php echo wordwrap($test_text, 65) ?></p>
</form>
<br /><br />
<?php
}
?>