<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Conrad Neilands
 ** Filename......:
 ** Last changed..:    27/3/03
 ** Notes.........:
 ***********************************************************************/

include_once __DIR__ . '/common.inc.php';

$table        = 'shipping_aircargo';
$section_code = 'FREIGHT';

include_once __DIR__ . '/header.inc.php';

$script = 'aircargo_admin.php?view=' . urlencode($_REQUEST['view']);

$fields['id']           = 'center';
$fields['date']         = 'center';
$fields['supplier']     = 'center';
$fields['service_type'] = 'center';
$fields['terms']        = 'center';
$fields['ship_from']    = 'center';
$fields['ship_to']      = 'center';
$fields['price']        = 'center';
$fields['s_price']      = 'center';
$fields['weight']       = 'center';
$fields['c_weight']     = 'center';
$fields['tit']          = 'center';
$fields['type']         = 'center';

$fields['cust_name'] = 'center';
$fields['notes']     = 'center';

$search_fields[] = 'ship_from';
$search_fields[] = 'ship_to';

$id_field = 'id';

$items = $DB->query_first('SELECT COUNT(*) AS `total` FROM ' . $table);

$gets = get_vars();
?>
<!-- suppliers admin -->

<link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

<form accept-charset="utf-8" autocomplete="off" method="post" action="<?=$script?>" name="admin_form">
	<br />
	<table width="100%" border="0" cellspacing="2" cellpadding="2"><tr>
		<td class="pageMenu" width="20%"><div align="center">Aircargo</div></td>
		<td class="pageMenu" width="80%"><div align="right">
			<a href="<?=htmlspecialchars($script . '&action=add')?>">Add Shipment</a> |
			<a href="<?=htmlspecialchars($script . '&query=')?>">List All Shipments (<?=$items['total']?>)</a>
		</div></td>
	</tr></table>
	<br />
	<p>
		<br />

<?php
function search($query)
{
    global $DB, $at, $fields, $order, $by, $table;
    global $script, $search_fields;

    $whereAND = array();
    foreach (preg_split('/[%_|\s]+/', trim($query)) as $word) {
        $word    = $DB->escape('%' . $word . '%');
        $whereOR = array();
        foreach ($search_fields as $v) {
            $whereOR[] = $v . ' LIKE ' . $word;
        }

        $whereAND[] = '(' . implode(' OR ', $whereOR) . ')';
    }
    $statement = sprintf(
        'SELECT * FROM %s%s',
        $table,
        (count($whereAND)) ? ' WHERE ' . implode(' AND ', $whereAND) : ''
    );

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 25;
    $at  = intval($at);

    if (!isset($order)) {
        $order = 'desc';
    }

    if (!isset($by)) {
        $by = 'date';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $group_by . $order_by . $limit);

    searchbox();
    echo '<blockquote>';
    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));

    draw_header_row();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        show_result($row, $shaded = !$shaded);
    }
    draw_header_end();
    //show more links
    show_links(get_links($max, "$script&query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));
    echo '</blockquote>';
}
function display()
{
    searchbox();
}
function searchbox()
{
    global $DB, $ship_from, $ship_to, $service_type, $currency;
?>
<table align="center" border="1">
	<tr><td colspan="2" class="tableHeader" title="This tool will help calculate freight costs">Aircargo Quick Search</td><tr>
	<tr>
		<td class="left">Ship From:</td>
		<td class="right"><select name="ship_from" style="width:200px;" title="This is where a package will be sent from">
			<option value="ALL">ALL</option>
<?php
$query = $DB->query('SELECT * FROM `shipping_location` ORDER BY `name` ASC');
    while ($row = $DB->fetch_assoc($query)) {
?>
			<option value="<?=htmlspecialchars($row['code'])?>"<?=($ship_from == $row['code']) ? ' selected="selected"' : ''?>><?=htmlspecialchars($row['name'])?></option>
<?php
}
    $DB->free_result($query);
?>
		</select></td>
	</tr>
	<tr>
		<td class="left">Ship To:</td>
		<td class="right"><select name="ship_to" style="width:200px;" title="This is where a package will be sent to">
			<option value="ALL">ALL</option>
<?php
$query = $DB->query('SELECT * FROM `shipping_location` ORDER BY `name` ASC');
    while ($row = $DB->fetch_assoc($query)) {
?>
			<option value="<?=htmlspecialchars($row['code'])?>"<?=($ship_to == $row['code']) ? ' selected="selected"' : ''?>><?=htmlspecialchars($row['name'])?></option>
<?php
}
    $DB->free_result($query);
?>
		</select></td>
	</tr>
	<tr>
		<td class="left">Service Type:</td>
		<td class="right"><select name="service_type" style="width:200px;">
			<option value="ALL">ALL</option>
<?php
foreach (array(
        'direct'   => 'Direct',
        'indirect' => 'Indirect',
    ) as $k => $v) {
?>
			<option value="<?=htmlspecialchars($k)?>"<?=($service_type == $k) ? ' selected="selected"' : ''?>><?=htmlspecialchars($v)?></option>
<?php
}
?>
		</select></td>
	</tr>
	<tr>
		<td class="left">Currency:</td>
		<td class="right"><select name="currency" style="width:200px;" title="This is the currency that you would like to view the prices in">
			<option value="USD">U.S. Dollars</option>
            <?=formatHTMLOptions(getCurrencyList(false), $currency)?>
		</select></td>
	</tr>
	<tr><td colspan="2" align="center">
		<input type="hidden" name="action" value="search" />
		<input type="submit" target="_blank" value="Search" />
	</td></tr>
</table>
<?php
}
function draw_header_row()
{
    global $fields, $order, $by, $max, $at, $page, $script;

    reset($fields);
?>
<br />
<table cellpadding="4" cellspacing="1" align="center">
	<tr class="tableHeader">
<?php
foreach ($fields as $field => $v) {
?>
		<td align="center">
			<a style="color:white;" href="<?=htmlspecialchars($script . '&' . http_build_query(array(
            'query' => $_REQUEST['query'],
            'max'   => $max,
            'at'    => $at,
            'page'  => $page,
            'by'    => $field,
            'order' => ('desc' == $order) ? 'asc' : 'desc',
        )))?>"><?=htmlspecialchars(strtr(ucfirst(str_replace('_', ' ', $field)), array(
            'Tit'      => 'Transit Time',
            'Price'    => 'Total Cost',
            'S price'  => 'Sale Price',
            'C weight' => 'Cubic Weight',
        )))?></a>
<?php
if ($by == $field) {
?>
<img src="../<?=$order?>.gif" width="7" height="7" border="0" />
<?php
}
?>
		</td>
<?php
}
?>
		<td>&nbsp;</td>
	</tr>
<?php
}

function draw_header_end()
{
?>
</table>
<br />
<?php
}

function show_result($cols, $shaded)
{
    global $fields, $script, $DB;
    static $alt = true;

    reset($fields);
    $id = $cols[key($fields)];

?>
<tr class="tableRow<?=($alt = !$alt) ? 'Alt' : ''?>">
<?php
foreach ($fields as $k => $v) {
?>
	<td nowrap="nowrap" align="<?=$v?>">
<?php
if ('ship_from' == $k || 'ship_to' == $k) {
            $statement  = "SELECT * FROM shipping_location WHERE code = '" . stripslashes($cols[$k]) . "'";
            $code_query = $DB->query_first($statement);
            echo $code_query[name];
        } elseif ('terms' == $k) {
            $row = $DB->query_first(sprintf(
                "SELECT * FROM `shipping_aircargo` WHERE `id` = %d",
                $id
            ), MYSQLI_ASSOC);
            echo ('checked' == $row['fob'] ? 'FOB<br />' : '');
            echo ('checked' == $row['exw'] ? 'Ex Works<br />' : '');
            echo ('checked' == $row['dtd'] ? 'Door To Door<br />' : '');
            echo ('checked' == $row['dta'] ? 'Door To Airport<br />' : '');
        } else {
            echo nl2br(htmlspecialchars($cols[$k]));
        }

?>
	</td>
<?php
}
?>
	<td nowrap="nowrap">
		<a href="<?=htmlspecialchars($script . '&action=edit&id=' . $id)?>">Edit</a> |
		<a href="<?=htmlspecialchars($script . '&action=delete&id=' . $id)?>">Delete</a>
	</td>
</tr>
<?php
}

function do_add()
{
    global $DB, $table;
    $DB->query(sprintf(
        "INSERT INTO `%s` (`date`, `ship_from`, `ship_to`, `goods_name`, `weight`, `c_weight`, `quantity`, `price`, `s_price`, `currency`, `cust_name`, `type`, `service_type`, `tit`, `supplier`, `carrier`, `notes`, `fob`, `exw`, `dtd`, `dta`) VALUES (%s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '%s', '%s', '%s', '%s')",
        $table,
        $DB->escape($_POST['data']['date1']),
        $DB->escape($_POST['data']['ship_from']),
        $DB->escape($_POST['data']['ship_to']),
        $DB->escape($_POST['data']['goods_name']),
        $DB->escape($_POST['data']['weight']),
        $DB->escape($_POST['data']['c_weight']),
        $_POST['data']['quantity'],
        $DB->escape($_POST['data']['price']),
        $DB->escape($_POST['data']['s_price']),
        $DB->escape($_POST['data']['currency']),
        $DB->escape($_POST['data']['cust_name']),
        $DB->escape($_POST['data']['type']),
        $DB->escape($_POST['data']['service_type']),
        $DB->escape($_POST['data']['tit']),
        $DB->escape($_POST['data']['supplier']),
        $DB->escape($_POST['data']['carrier']),
        $DB->escape($_POST['data']['notes']),
        ('on' == $_POST['data']['fob']) ? 'checked' : '',
        ('on' == $_POST['data']['exw']) ? 'checked' : '',
        ('on' == $_POST['data']['dtd']) ? 'checked' : '',
        ('on' == $_POST['data']['dta']) ? 'checked' : ''
    ));
?>
<font color="#FF0000"><b>New Shipment Added.</b></font><br />
<?php
}

function do_edit()
{
    global $DB, $id, $table;

    $DB->query(sprintf(
        "UPDATE `%s` SET `date` = %s, `ship_from` = %s, `ship_to` = %s, `goods_name` = %s, `weight` = %s, `c_weight` = %s, `quantity` = %d, `price` = %s, `s_price` = %s, `currency` = %s, `cust_name` = %s, `type` = %s, `service_type` = %s, `tit` = %s, `supplier` = %s, `carrier` = %s, `notes` = %s, `fob` = '%s', `exw` = '%s', `dtd` = '%s', `dta` = '%s' WHERE `id` = %d LIMIT 1",
        $table,
        $DB->escape($_POST['data']['date1']),
        $DB->escape($_POST['data']['ship_from']),
        $DB->escape($_POST['data']['ship_to']),
        $DB->escape($_POST['data']['goods_name']),
        $DB->escape($_POST['data']['weight']),
        $DB->escape($_POST['data']['c_weight']),
        $_POST['data']['quantity'],
        $DB->escape($_POST['data']['price']),
        $DB->escape($_POST['data']['s_price']),
        $DB->escape($_POST['data']['currency']),
        $DB->escape($_POST['data']['cust_name']),
        $DB->escape($_POST['data']['type']),
        $DB->escape($_POST['data']['service_type']),
        $DB->escape($_POST['data']['tit']),
        $DB->escape($_POST['data']['supplier']),
        $DB->escape($_POST['data']['carrier']),
        $DB->escape($_POST['data']['notes']),
        ('on' == $_POST['data']['fob']) ? 'checked' : '',
        ('on' == $_POST['data']['exw']) ? 'checked' : '',
        ('on' == $_POST['data']['dtd']) ? 'checked' : '',
        ('on' == $_POST['data']['dta']) ? 'checked' : '',
        $id
    ));

    $after = $DB->query_first(sprintf(
        "SELECT * FROM `%s` WHERE `id` = %d LIMIT 1",
        $table,
        $id
    ), MYSQLI_ASSOC);
    $qt_id = $after['qt_id'];
    if (0 != $qt_id) {
        editQuotes($data, $qt_id);
    }

?>
<font color="#FF0000"><b>ID <?=$id?> updated.</b></font><br />
<?php
}

function editQuotes($data, $qt_id)
{
    global $DB;
    $serv = ('direct' == $data['service_type'] ? '`d_flag`=\'checked\', `id_flag`=\'\'' : '`id_flag`=\'checked\', `d_flag`=\'\'');
    $sql  = "UPDATE `coinop`.`freight_forwarders_quotes` SET `term_flags` = '" .
        ('checked' == $data['fob'] ? 'fob,' : '') . ('checked' == $data['exw'] ? 'exw,' : '') . ('checked' == $data['dtd'] ? 'dtd,' : '') . ('checked' == $data['dta'] ? 'dta' : '') .
        "', `ship_weight`='" . $data['weight'] . "', `cube_weight`='" . $data['c_weight'] . "', `tot_total`='" . $data['price'] . "', `sal_total`='" . $data['s_price'] . "', `transit_time`='" . $data['tit'] . "', $serv WHERE id='$qt_id'";
    $DB->query($sql);
}

function do_delete()
{
    global $id, $DB, $table;

    $statement = "DELETE FROM $table WHERE id = '" . $id . "'";
    $DB->query($statement);

    echo "<font color='#FF0000'><b>ID:$id Deleted.</b></font><br>";
}

switch ($action) {
    case 'do_add':
        do_add();
        break;
    case 'do_edit':
        do_edit();
        break;
    case 'do_delete':
        do_delete();
        break;
    case 'add':
        include 'aircargo_add_form.inc.php';
        break;
    case 'edit':
        include 'aircargo_edit_form.inc.php';
        break;
    case 'delete':
        include 'aircargo_delete_form.inc.php';
        break;
    case 'search':
        include 'aircargo_errors.php';
        break;
    case 'mainpage':
        searchbox();
        break;
    default:
        search(htmlspecialchars($_REQUEST['query']));
        break;
}
?>
</form>

<!--//suppliers admin -->
<?php
include_once __DIR__ . '/../../footer.inc.php';
?>