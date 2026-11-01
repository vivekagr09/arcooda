<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberTemplateVars

$action    = $posted['action'];
$sections1 = array(
    'survey_types'   => array(
        'title'      => 'Member Survey Types',
        'table_name' => '`highwaygroup`.`new_members_survey_types`',
        'table_key'  => 'survey_type',
        'table_key2' => 'short_desc',
        'table_key3' => 'long_desc',
        'table_key4' => 'parent',
    ),
    'email_template'   => array(
        'title'      => 'Member Email Templates',
        'table_name' => '`highwaygroup`.`email_templates`',
        'table_key'  => 'template_id',
        'table_key2' => 'template_name',
    ),
);

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
            redirect('options.php?section=' . $section);
            die();
            break;
    }
}

include_once __DIR__ . '/header.inc.php';

$section = '';
$item_id = 0;
if ('' != $posted['section'] && array_key_exists($posted['section'], $sections1)) {
    $section = $posted['section'];
    $item_id = intval($posted[$sections1[$section]['table_key']]);
}
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
<table class="table_grid">
	<tr><th>Select an Option</th></tr>
<?php
    foreach ($sections1 as $key => $section) {
?>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td><a target="_blank" href="options.php?section=<?= $key ?>"><?= $section['title'] ?></a></td>
	</tr>
<?php
    }
?>
</table>

<br />
<br />

<?php
}

function optionList($section_id)
{
    global $DB, $sections1;
    if ('' == $section_id || !array_key_exists($section_id, $sections1)) {
        return;
    }

    $section  = $sections1[$section_id];
    $query    = $DB->query("SELECT * FROM " . $section['table_name'] . " ORDER BY " . $section['table_key'] . " ASC");
    $add_url  = 'options.php?action=add&section=' . $section_id;
    $edit_url = 'options.php?action=edit&section=' . $section_id . '&' . $section['table_key'] . '=';
?>
<table class="table_grid">
	<tr><th colspan="4"><?= $section['title'] ?></th></tr>
	<tr style="background-color:#E3EBF7;">
		<th>ID</th>
		<th>Name</th>
		<th>&nbsp;</th>
	</tr>
<?php
    while ($row = $DB->fetch_assoc($query)) {
?>
	<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
		<td><?= $row[$section['table_key']] ?></td>
		<td><?php hecho($row[$section['table_key2']])?></td>
		<td><a href="<?= $edit_url . $row[$section['table_key']] ?>">Edit</a></td>
	</tr>
<?php
    }
?>
</table>

<br />
<br />

<p style="text-align:center;"><a href="<?= $add_url ?>" target="_blank">Add New Item</a></p>

<br />
<br />

<?php
}

function addEditOption($section_id, $item_id = 0)
{
    switch ($section_id) {
        case 'email_template':
            addEditEmailTemplates($item_id);
            break;
        case 'survey_types':
            addEditGenericItem($section_id, $item_id);
            break;
        default:
            echo 'Function currently not handled';
    }
}

function addEditGenericItem($section_id, $item_id = 0)
{
    global $DB, $sections1;
    $section = $sections1[$section_id];
    $row     = $DB->query_first(sprintf(
        "SELECT * FROM %s WHERE %s = %d LIMIT 1",
        $section['table_name'],
        $section['table_key'],
        $item_id
    ), MYSQLI_ASSOC);
?>
<h5>Use this form to <?= (0 == $item_id) ? 'Add to' : 'Update' ?> <?= $section['title'] ?></h5>
<form accept-charset="utf-8" action="options.php?action=do_edit&section=<?= $section_id ?>" method="post">
<?php
    if ($item_id) {
?>
<input type="hidden" name="<?= $section['table_key'] ?>" value="<?= $item_id ?>" />
<?php
    }
?>
<table class="table_fields">
<?php
    for ($i = 2;array_key_exists($k = 'table_key' . $i, $section);$i++) {
?>
	<tr>
		<th><?= ucfirst($section[$k]) ?> :</th>
		<td><input class="intext" type="text" name="<?= $section[$k] ?>" value="<?php hecho($row[$section[$k]])?>" /></td>
    </tr>
<?php
    }
?>
</table>
<center><input type="submit" value="<?= (0 == $item_id) ? 'Add' : 'Update' ?>" /></center><br />
</form>

<br />
<br />

<?php
}

function doEdit($section_id, $posted)
{
    global $DB, $sections1;
    $section = $sections1[$section_id];

    if (!isset($posted['template_group'])) {
        $posted['template_group'] = array();
    }

    $row        = $DB->query_first(sprintf(
        "SELECT %s FROM %s WHERE %s = %d LIMIT 1",
        $section['table_key'],
        $section['table_name'],
        $section['table_key'],
        $posted[$section['table_key']]
    ), MYSQLI_ASSOC);
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
                if (is_array($posted[$field_id])) {
                    $values = formatSQLString(implode(',', $posted[$field_id]));
                } else {
                    $values = formatSQLString($posted[$field_id]);
                }

                $update_values[]          = $field_id . " = " . $values;
                $insert_values[$field_id] = $values;
            } else {
                $update_values[]          = $field_id . " = " . formatSQLString($posted[$field_id]);
                $insert_values[$field_id] = formatSQLString($posted[$field_id]);
            }
        }
    }

    if ($insert_new) {
        $sql = "INSERT INTO " . $section['table_name'] . " (" . implode(', ', array_keys($insert_values)) . ") VALUES (" . implode(', ', array_values($insert_values)) . ")";
    } else {
        $sql = "UPDATE " . $section['table_name'] . " SET " . implode(', ', $update_values) . " WHERE " . $section['table_key'] . " = " . intval($posted[$section['table_key']]) . " LIMIT 1";
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

    $types    = $DB->table_types($section['table_name']);
    $set_list = explode("','", substr($types['template_group'], 5, -2));

    $row = $DB->query_first("SELECT * FROM " . $section['table_name'] . " WHERE " . $section['table_key'] . " = " . intval($item_id) . " LIMIT 1", MYSQLI_ASSOC);
?>
<h5>Use this form to <?= (0 == $item_id) ? 'Add to' : 'Update' ?> <?= $section['title'] ?></h5>
<form accept-charset="utf-8" action="options.php?action=do_edit&section=<?= $section_id ?>" method="post">
<?php
    if (0 != $item_id) {
?>
<input type="hidden" name="<?= $section['table_key'] ?>" value="<?= $item_id ?>" />
<?php
    }
?>
<table align="center">
	<tr>
		<td class="left_title" style="width:25%;">Template Description :</td>
		<td><input class="intext" type="text" name="template_name" value="<?php hecho($row['template_name'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Template Availability :</td>
		<td><select name="template_group[]" multiple="multiple" size="5">
<?php
    foreach ($set_list as $set_key) {
?>
			<option value="<?php hecho($set_key)?>"<?= (strpos($row['template_group'], $set_key) !== false) ? ' selected="selected"' : '' ?>><?php hecho($set_key)?></option>
<?php
    }
?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Email Subject* :</td>
		<td><input class="intext" type="text" name="template_subject" value="<?php hecho($row['template_subject'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Email Content* :</td>
		<td><textarea name="template_text" rows="<?= max(0, count(explode("\n", $row['template_text']))) + 10 ?>"><?php hecho($row['template_text'])?></textarea></td>
	</tr>
</table>

<br /><br />

<center><input type="submit" value="<?= (0 == $item_id) ? 'Add' : 'Update' ?>" class="unfinished" /></center><br />

<br /><br />

<?php
    $test_text = hecho($row['template_text'], 1);
    $keywords  = array();
?>
<p style="text-align:left;width:95%;">
*The following items are member related:<br />
<table>
<?php
    $fields_list = array_keys(memberTemplateVars());
    $keywords    = array_merge($keywords, $fields_list);
    asort($fields_list);
    $chunked = array_chunk($fields_list, 3);
    foreach ($chunked as $chunk_row) {
?>
	<tr>
		<td><?php hecho($chunk_row[0])?></td>
		<td><?php hecho($chunk_row[1])?></td>
		<td><?php hecho($chunk_row[2])?></td>
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
<p style="text-align:left;width:512px;font-family:courier;background-color:white;border:1px solid #66bb88;padding:8px 8px 8px 8px;white-space:pre;"><?= wordwrap($test_text, 65) ?></p>

</form>

<br />
<br />

<?php
}
