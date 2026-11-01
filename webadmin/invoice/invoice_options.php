<script type="text/javascript">
        function confirmNavigation(event, url) {
            event.preventDefault(); // Prevent the default link behavior

            var userConfirmed = confirm("Are you sure you want to delete this code?");
            if (userConfirmed) {
                window.location.href = url; // Navigate to the URL if user confirms
            }
        }
    </script>
<?php
include_once __DIR__ . '/common.inc.php';

$action    = $posted['action'];
$sections1 = array(
    'charges'          => array(
        'title'      => 'Invoice Charges',
        'is_delete'      => false,
        'table_name' => "`highwaygroup`.`charges`",
        'table_key'  => 'charge_id',
        'table_key2' => 'charge_name',
        'table_key3' => 'site_id',
    ),
    'warranty_terms'   => array(
        'title'      => 'Invoice Warranty Terms',
        'table_name' => "`highwaygroup`.`waranty_terms`",
        'is_delete'      => false,
        'table_key'  => 'id',
        'table_key2' => 'waranty',
    ),
    'payment_terms'    => array(
        'title'      => 'Invoice/Shiporder Payment Terms',
        'is_delete'      => false,
        'table_name' => "`highwaygroup`.`purchase_orders_payment`",
        'table_key'  => 'id',
        'table_key2' => 'title',
    ),
    'ship_terms'       => array(
        'title'      => 'Invoice/Shiporder Shipping Terms',
        'is_delete'      => false,
        'table_name' => "`highwaygroup`.`purchase_orders_terms`",
        'table_key'  => 'id',
        'table_key2' => 'term',
    ),
    'pack_requirement' => array(
        'title'      => 'Invoice/Shiporder Packing Requirements',
        'is_delete'      => false,
        'table_name' => "`highwaygroup`.`purchase_orders_packing`",
        'table_key'  => 'id',
        'table_key2' => 'requirement',
    ),
    'email_template'   => array(
        'title'      => 'Invoice Email Templates',
        'is_delete'      => false,
        'table_name' => "`highwaygroup`.`email_templates`",
        'table_key'  => 'template_id',
        'table_key2' => 'template_name',
    ),
    'harmonised_code'  => array(
        'title'      => 'Harmonised System Codes',
        'is_delete'      => true,
        'table_name' => "`world`.`harmonised_codes`",
        'table_key'  => 'code',
        'table_key2' => 'title',
    ),
);
$section = '';
$item_id = 0;
if ('' != $posted['section'] && array_key_exists($posted['section'], $sections1)) {
    $section = $posted['section'];
    $item_id = $posted[$sections1[$section]['table_key']];
}

//processing switch-case..
if ('harmonised_code' == $section && in_array($posted['action'], array('do_add', 'do_edit'))) {
    $row = $DB->query_first("SELECT " . $sections1[$section]['table_key'] . " FROM " . $sections1[$section]['table_name'] . " WHERE " . $sections1[$section]['table_key'] . "=" . formatSQLString($posted[$sections1[$section]['table_key']]) . " LIMIT 1");
    if ($row) {
        $DB->query("UPDATE " . $sections1[$section]['table_name'] . " SET title=" . formatSQLString($posted[$sections1[$section]['table_key2']]) . ", product_type=" . formatSQLString($posted['product_type']) . " WHERE " . $sections1[$section]['table_key'] . "=" . formatSQLString($posted[$sections1[$section]['table_key']]) . " LIMIT 1");
    } else {
        $DB->query("INSERT INTO " . $sections1[$section]['table_name'] . " (code,title,product_type) VALUES (" . formatSQLString($posted[$sections1[$section]['table_key']]) . "," . formatSQLString($posted[$sections1[$section]['table_key2']]) . "," . formatSQLString($posted['product_type']) . ")");
    }

    redirect('invoice_options.php?section=' . $section);
    die();
} elseif ('' != $section) {
    switch ($posted['action']) {
        case 'do_add':
            unset($posted[$sections1[$section]['table_key']]);
            //no break
        case 'do_edit':
            doEdit($section, $posted);
            redirect('invoice_options.php?section=' . $section);
            die();
            break;
        case 'delete':
            doDelete($section, $posted);
            redirect('invoice_options.php?section=' . $section);
            die();
            break;
    }
}

include_once __DIR__ . '/header.inc.php';

if ('' != $section) {
    switch ($posted['action']) {
        case 'add':
            $item_id = 0;
            //no break
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
<table class="spacetable" style="margin:auto">
	<tr><th>Select an Option</th></tr>
<?php
foreach ($sections1 as $key => $section) {
?>
	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
		<td style="padding:8px 8px 8px 8px;"><a target="_blank" href="invoice_options.php?section=<?=$key?>"><?=$section['title']?></a></td>
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
    if($section['is_delete']){
        $query    = $DB->query("SELECT * FROM " . $section['table_name'] . " WHERE `flag`=1  ORDER BY " . $section['table_key'] . " ASC");
    }else{
         $query    = $DB->query("SELECT * FROM " . $section['table_name'] . " ORDER BY " . $section['table_key'] . " ASC");
    }
    $add_url  = 'invoice_options.php?action=add&section=' . $section_id;
    $edit_url = 'invoice_options.php?action=edit&section=' . $section_id . '&' . $section['table_key'] . '=';
    $delete_url = 'invoice_options.php?action=delete&section=' . $section_id . '&' . $section['table_key'] . '=';
    $colspan  = ('charges' != $section_id) ? "3" : "4";
    $sites    = array(0 => 'All', 1 => 'Highwaygames', 2 => 'Highway Entertainment', 9 => 'Converters.tv', 11 => 'Highwaygroup', 16 => 'Car Converters');
?>
<table class="spacetable" style="margin:auto">
	<tr><th colspan="4"><?=$section['title']?></th></tr>
	<tr style="background-color:#E3EBF7;">
		<th style="background-color:transparent;">ID</th>
		<th style="background-color:transparent;width:90%;">Name</th>
<?php
    if ('charges' == $section_id) {
?>
		<th style="background-color:transparent;width:100%">Site</th>
<?php
    }
?>
		<th style="background-color:transparent;width:10%"">&nbsp;</th>
	</tr>
<?php
    while ($row = $DB->fetch_assoc($query)) {
?>
	<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
		<td style="text-align:center;padding:8px 8px 8px 8px;"><?=$row[$section['table_key']]?></td>
		<td style="padding:8px 8px 8px 8px;"><?=htmlspecialchars($row[$section['table_key2']])?></td>
<?php
        if ('charges' == $section_id) {
?>
		<td style="padding:8px 8px 8px 8px;"><?=$sites[$row[$section['table_key3']]]?></td>
<?php
        }
?>
		<td style="padding:8px 8px 8px 8px;">
            <a href="<?=$edit_url . $row[$section['table_key']]?>">Edit</a>
            <?php if($section['is_delete']) { ?>
                <a href="<?=$delete_url . $row[$section['table_key']]?>" onclick="confirmNavigation(event, this.href)">Delete</a>
            <?php } ?>
        </td>
	</tr>
<?php
    }
?>
</table>

<br />
<br />

<p style="text-align:center;"><a href="<?=$add_url?>" target="_blank">Add New Item</a></p>

<br />
<br />

<?php
}

function addEditOption($section_id, $item_id = 0)
{
    switch ($section_id) {
        case 'charges':
            addEditCharges($item_id);
            break;

        case 'email_template':
            addEditEmailTemplates($item_id);
            break;

        case 'payment_terms':
            //no break
        case 'warranty_terms':
            //no break
        case 'pack_requirement':
            //no break
        case 'ship_terms':
            addEditGenericItem($section_id, $item_id);
            break;

        case 'harmonised_code':
            addEditHarmonisedCode($item_id);
            break;

        default:
            echo 'Function currently not handled';
            break;
    }
}

function addEditGenericItem($section_id, $item_id = 0)
{
    global $DB, $sections1;
    $section = $sections1[$section_id];
    $row     = $DB->query_first("SELECT * FROM " . $section['table_name'] . " WHERE " . $section['table_key'] . "=" . intval($item_id) . " LIMIT 1", MYSQLI_ASSOC);

?>
<h5>Use this form to <?=(0 == $item_id) ? 'Add to' : 'Update'?> <?=$section['title']?></h5>
<form action="invoice_options.php?action=do_edit&section=<?=$section_id?>" method="post">
<?php
if (0 != $item_id) {
?>
<input type="hidden" name="<?=$section['table_key']?>" value="<?=$item_id?>" />
<?php
}
?>
<table style="margin:auto">
	<tr>
		<td class="left_title" style="width:25%;"><?=ucfirst($section['table_key2'])?> :</td>
		<td><input class="intext" type="text" name="<?=$section['table_key2']?>" value="<?=htmlspecialchars($row[$section['table_key2']])?>" /></td>
	</tr>
</table>
<div style="text-align:center"><input type="submit" value="<?=(0 == $item_id) ? 'Add' : 'Update'?>" /></div><br />
</form>

<br />
<br />

<?php
}

function addEditHarmonisedCode($item_id = '')
{
    global $DB, $sections1;
    $section = $sections1['harmonised_code'];
    $row     = $DB->query_first("SELECT * FROM " . $section['table_name'] . " WHERE " . $section['table_key'] . "=" . formatSQLString($item_id) . " LIMIT 1", MYSQLI_ASSOC);

?>
<h5>Use this form to <?=('' == $item_id) ? 'Add to the' : 'Update the'?> <?=$section['title']?></h5>
<form action="invoice_options.php?action=do_edit&section=harmonised_code" method="post">
<table style="margin:auto">
	<tr>
		<td class="left_title" style="width:25%;">Code :</td>
		<td><input type="text" name="<?=$section['table_key']?>" value="<?=htmlspecialchars($row[$section['table_key']])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Label :</td>
		<td><input class="intext" type="text" name="<?=$section['table_key2']?>" value="<?=htmlspecialchars($row[$section['table_key2']])?>" /></td>
	</tr>
    <tr>
        <td class="left_title" style="width:25%;">Product Type :</td>
        <td><input class="intext" type="text" name="product_type" value="<?=htmlspecialchars($row['product_type'])?>" /></td>
    </tr>
</table>
<p>Please note: These codes must match exactly to the standard predefined harmonised system codes.<br />You can find a list of codes at <a target="_blank" href="https://www.hscodelist.com/bbs/board.php?bo_table=HScodelist">https://www.hscodelist.com/bbs/board.php?bo_table=HScodelist</a></p>
<div style="text-align:center"><input type="submit" value="<?=('' == $item_id) ? 'Add' : 'Update'?>" /></div><br />
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

    $posted['template_group'] = implode(',', $posted['template_group']);

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

    if ('charges' == $section_id) {
        // now have to update the payment method table if the appropriate
        $sql                  = "SELECT site_id, payment_method_link, charge_percent, charge_amount FROM `highwaygroup`.`charges` WHERE charge_id=" . $posted['charge_id'] . " LIMIT 1";
        $row                  = $DB->query_first($sql, MYSQLI_ASSOC);
        $payment_method_links = array('PAYPAL', 'AMEX');
        if (0 != $row['site_id'] && in_array($row['payment_method_link'], $payment_method_links)) {
            $DB->query("UPDATE `highwaygroup`.`payment_methods` SET payment_charge_amount=" . $row['charge_amount'] . ", payment_charge_percent=" . $row['charge_percent'] . " WHERE payment_method_id=" . formatSQLString($row['payment_method_link']) . " AND site_id=" . $row['site_id']);
        }
    }
}
function doDelete($section_id, $posted)
{
    global $DB, $sections1;
    $section = $sections1[$section_id];

        $update_values[] = '`flag`' . "=" . 0;
        if($section_id == 'harmonised_code'){
            $sql = "UPDATE " . $section['table_name'] . " SET " . implode(',', $update_values) . " WHERE " . $section['table_key'] . "='" . $posted[$section['table_key']] . "' LIMIT 1";
        }else{
           $sql = "UPDATE " . $section['table_name'] . " SET " . implode(',', $update_values) . " WHERE " . $section['table_key'] . "=" . $posted[$section['table_key']] . " LIMIT 1"; 
        }
           
    $DB->query($sql);
}
function addEditCharges($item_id = 0)
{
    global $DB, $sections1;
    $section_id = 'charges';
    $section    = $sections1[$section_id];
    $row        = $DB->query_first("SELECT * FROM " . $section['table_name'] . " WHERE " . $section['table_key'] . "=" . intval($item_id) . " LIMIT 1", MYSQLI_ASSOC);

?>
<h5>Use this form to <?=(0 == $item_id) ? 'Add to' : 'Update'?> <?=$section['title']?></h5>
<form action="invoice_options.php?action=do_edit&section=<?=$section_id?>" method="post">
<?php
if (0 != $item_id) {
?>
<input type="hidden" name="<?=$section['table_key']?>" value="<?=$item_id?>" />
<?php
}

    $priority_list = array(1 => '1 - First', '2', '3', '4', '5', '6', '7', '8 (eg. Freight)', '9 (eg. GST)', '10 - Last (eg. Paypal Charge)');
?>
<table style="margin:auto">
	<tr>
		<td class="left_title" style="width:25%;">Charge Name :</td>
		<td><input class="intext" type="text" name="charge_name" value="<?=htmlspecialchars($row['charge_name'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Charge Priority :</td>
		<td><select name="charge_priority">
			<?=formatHTMLOptions($priority_list, $row['charge_priority'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Charge Percentage :</td>
		<td><input class="intext" type="text" name="charge_percent" value="<?=sprintf('%.2f', $row['charge_percent'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Charge Flagfall (AUD) :</td>
		<td><input class="intext" type="text" name="charge_amount" value="<?=sprintf('%.2f', $row['charge_amount'])?>" /></td>
	</tr>
</table>
<div style="text-align:center"><input type="submit" value="<?=(0 == $item_id) ? 'Add' : 'Update'?>" /></div><br />
</form>

<br />
<br />

<?php
}

function addEditEmailTemplates($item_id = 0)
{
    global $DB, $sections1;
    $section_id = 'email_template';
    $section    = $sections1[$section_id];

    $types    = $DB->table_types($section['table_name']);
    $set_list = explode("','", substr($types['template_group'], 5, -2));

    $row = $DB->query_first("SELECT * FROM " . $section['table_name'] . " WHERE " . $section['table_key'] . "=" . intval($item_id) . " LIMIT 1", MYSQLI_ASSOC);

?>
<h5>Use this form to <?=(0 == $item_id) ? 'Add to' : 'Update'?> <?=$section['title']?></h5>
<form action="invoice_options.php?action=do_edit&section=<?=$section_id?>" method="post">
<?php
if (0 != $item_id) {
?>
<input type="hidden" name="<?=$section['table_key']?>" value="<?=$item_id?>" />
<?php
}
?>
<table style="margin:auto">
	<tr>
		<td class="left_title" style="width:25%;">Template Description :</td>
		<td><input class="intext" type="text" name="template_name" value="<?=htmlspecialchars($row['template_name'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Template Availability :</td>
		<td><select name="template_group[]" multiple="multiple" size="5">
<?php
foreach ($set_list as $set_key) {
?>
			<option value="<?=htmlspecialchars($set_key)?>"<?=(strpos($row['template_group'], $set_key) !== false) ? ' selected="selected"' : ''?>><?=htmlspecialchars($set_key)?></option>
<?php
}
?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Email Subject* :</td>
		<td><input class="intext" type="text" name="template_subject" value="<?=htmlspecialchars($row['template_subject'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Email Content* :</td>
		<td><textarea name="template_text" rows="<?=max(0, count(explode("\n", $row['template_text']))) + 10?>"><?=htmlspecialchars($row['template_text'])?></textarea></td>
	</tr>
</table>

<br /><br />

<div style="text-align:center"><input type="submit" value="<?=(0 == $item_id) ? 'Add' : 'Update'?>" class="unfinished" /></div><br />

<br /><br />

<?php
    $test_text = htmlspecialchars($row['template_text']);
    $keywords  = array();
?>
<p style="text-align:left;width:95%;">
*The following items are invoice related:<br />
<table>
<?php
    $fields_list = array_keys(invoiceTemplateVars());
    $keywords    = array_merge($keywords, $fields_list);
    asort($fields_list);
    $chunked = array_chunk($fields_list, 3);
    foreach ($chunked as $chunk_row) {
?>
	<tr>
		<td><?=htmlspecialchars($chunk_row[0])?></td>
		<td><?=htmlspecialchars($chunk_row[1])?></td>
		<td><?=htmlspecialchars($chunk_row[2])?></td>
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

//    foreach(invoiceTemplateVars(invoiceFromDatabase(3229),invoiceProductsFromDatabase(3229),invoiceChargesFromDatabase(3229)) as $field=>$value)
//        $test_text=str_replace($field,$value,$test_text);
?>
Preview:
<p style="text-align:left;width:512px;font-family:courier;background-color:white;border:1px solid #66bb88;padding:8px 8px 8px 8px;white-space:pre;"><?=wordwrap($test_text, 65)?></p>

</form>

<br />
<br />

<?php
}
