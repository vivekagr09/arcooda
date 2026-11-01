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

$table        = 'shipping_seacargo';
$section_code = 'FREIGHT';

include_once __DIR__ . '/header.inc.php';

$script = 'seacargo_admin.php?view=' . urlencode($_REQUEST['view']);

$fields['id']             = 'center';
$fields['date']           = 'center';
$fields['supplier']       = 'center';
$fields['service_type']   = 'center';
$fields['terms']          = 'center';
$fields['ship_from']      = 'center';
$fields['ship_to']        = 'center';
$fields['price']          = 'center';
$fields['s_price']        = 'center';
$fields['weight']         = 'center';
$fields['c_weight']       = 'center';
$fields['tit']            = 'center';
$fields['type']           = 'center';
$fields['container_type'] = 'center';
$fields['cust_name'] = 'center';
$fields['notes']     = 'center';

$search_fields[] = 'ship_from';
$search_fields[] = 'ship_to';

$id_field = 'id';

$actions[] = '<a href="$script&action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script&action=delete&id=$id">Delete</a>';
$items     = $DB->query_first('SELECT COUNT(*) AS `total` FROM ' . $table);

$gets = get_vars();
?>
<!-- suppliers admin -->

<link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

<form accept-charset="utf-8" autocomplete="off" method="post" action="<?=$script?>" name="admin_form">
	<br />
	<table width="100%" border="0" cellspacing="2" cellpadding="2"><tr>
          <td class="pageMenu" width="20%"><div align="center">Seacargo</div></td>
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

    $query = trim($query);

    //keywords
    $arrKeywords = explode(' ', $query);

    //search query by key words
    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word) && '|' != $word) {
            $extra .= ' AND ';

            //do a regular expression search on each of the search fields, case insensitive
            for ($i = 0; $i < count($search_fields); $i++) {
                $extra .= 0 == $i ? '(' : ' OR ';
                $extra .= " LOWER($search_fields[$i]) REGEXP \"$word\" ";
            }
            if (count($search_fields) > 0) {
                $extra .= ')';
            }

        }
    } //end search query keywords
    //search statement
    $statement = "SELECT * FROM $table";

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
    global $DB, $ship_from, $ship_to, $container_type, $currency;
?>
	<table align="center" border='1'>
		<tr><td colspan='2' class='tableHeader' tooltip="This tool will help calculate freight costs">Seacargo Quick Search</td><tr>
		<tr>
			<td class="left">Ship From:</td>
			<td class="right">
				<select name="ship_from" style="width:200px" tooltip="This is where a package will be sent from">
<?php
$result = $DB->query('SELECT * FROM shipping_location ORDER BY name ASC');
    echo "<option value='ALL'>ALL</option>\n";
    while ($row = $DB->fetch_array($result)) {
        if (trim($row['code']) == $ship_from) {
            echo "<option value='$row[code]' selected>$row[name]</option>\n";
        } else {
            echo "<option value='$row[code]'>$row[name]</option>\n";
        }
    }
?>
			</td>
		</tr>
		<tr>
			<td class="left">Ship To:</td>
			<td class="right">
				<select name="ship_to" style="width:200px" tooltip="This is where a package will be sent to">
<?php
$result = $DB->query('SELECT * FROM shipping_location ORDER BY name ASC');
    echo "<option value='ALL'>ALL</option>\n";
    while ($row = $DB->fetch_array($result)) {
        if (trim($row['code']) == $ship_to) {
            echo "<option value='$row[code]' selected>$row[name]</option>\n";
        } else {
            echo "<option value='$row[code]'>$row[name]</option>\n";
        }
    }
?>
			</td>
		</tr>
		<tr>
			<td class="left">Container Type:</td>
			<td class="right">
				<select name="container_type" style="width:200px" tooltip="">
				<?php if ('LCL' == $container_type) {?>
  					<option value='ALL'>ALL</option>
					<option value='LCL' selected>LCL</option>
					<option value='20ft'>20ft</option>
					<option value='40ft'>40ft</option>
					<option value='40hq'>40ft HQ</option>
				<?php } else if ('20ft' == $container_type) {?>
  					<option value='ALL'>ALL</option>
					<option value='LCL'>LCL</option>
					<option value='20ft' selected>20ft</option>
					<option value='40ft'>40ft</option>
					<option value='40hq'>40ft HQ</option>
				<?php } else if ('40ft' == $container_type) {?>
  					<option value='ALL'>ALL</option>
					<option value='LCL'>LCL</option>
					<option value='20ft'>20ft</option>
					<option value='40ft' selected>40ft</option>
					<option value='40hq'>40ft HQ</option>
				<?php } else if ('40hq' == $container_type) {?>
    					<option value='ALL'>ALL</option>
					<option value='LCL'>LCL</option>
					<option value='20ft'>20ft</option>
					<option value='40ft'>40ft</option>
					<option value='40hq' selected>40ft HQ</option>
				<?php } else {?>
  					<option value='ALL' selected>ALL</option>
					<option value='LCL'>LCL</option>
					<option value='20ft'>20ft</option>
					<option value='40ft'>40ft</option>
					<option value='40hq'>40ft HQ</option>
				<?php }?>


			</td>
		</tr>
		<tr>
					<td class="left">
						Currency:
					</td>
					<td class="right">
						<select name="currency" style="width:200px" tooltip="This is the currency that you would like to view the prices in">
                            <option value="USD">U.S Dollars</option>
                            <?=formatHTMLOptions(getCurrencyList(false), 'USD')?>
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
    global $fields, $order, $by, $query, $max, $at, $page, $script;

    reset($fields);
?>
<br />
<table cellpadding="4" cellspacing="1" align="center">
	<tr class="tableHeader">
<?php
for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo '<td align="center" >';
        echo "<a style=\"color: white\" href=\"$script&query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
        echo "&by=$field&order=" . ('desc' == $order ? 'asc' : 'desc') . '">';
        $temp = ucfirst(str_replace('_', ' ', $field));
        if ('Tit' == $temp) {
            echo 'Transit time';
        } else if ('Price' == $temp) {
            echo 'Total Cost';
        } else if ('S price' == $temp) {
            echo 'Sale Price';
        } else if ('C weight' == $temp) {
            echo 'Cubic Weight';
        } else {
            echo $temp;
        }
        echo '</a>';
        if ($by == $field) {
            echo '  <img src="../' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo '</td>';
        next($fields);
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
    global $fields, $actions, $script, $DB;

    reset($fields);
    $id = $cols[key($fields)];
    echo $shaded ? '<tr class="tableRowAlt">' : '<tr class="tableRow">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        echo "<td nowrap align=\"" . $fields[key($fields)] . "\">";
        if ('ship_from' == $field || 'ship_to' == $field) {
            $statement  = "SELECT * FROM shipping_location WHERE code = '" . htmlspecialchars($cols[key($fields)]) . "'";
            $code_query = $DB->query_first($statement);
            echo $code_query[name];
        } else if ('terms' == $field) {
            $row = $DB->query_first("SELECT * FROM shipping_seacargo WHERE id = '$id'");
            echo ('checked' == $row['fob'] ? 'FOB<br/>' : '');
            echo ('checked' == $row['exw'] ? 'Ex Works<br/>' : '');
            echo ('checked' == $row['dtd'] ? 'Door To Door<br/>' : '');
            echo ('checked' == $row['dta'] ? 'Door To Port<br/>' : '');
        } else {
            $output = htmlspecialchars($cols[key($fields)]);
            echo $output = nl2br($output);
        }
        echo '</td>';
        next($fields);
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

    $data        = $_POST['data'];
    $data['fob'] = ('on' == $data['fob'] ? 'checked' : '');
    $data['exw'] = ('on' == $data['exw'] ? 'checked' : '');
    $data['dtd'] = ('on' == $data['dtd'] ? 'checked' : '');
    $data['dta'] = ('on' == $data['dta'] ? 'checked' : '');

    $sql = 'INSERT INTO ' . $table . "(date, ship_from, ship_to, goods_name, weight, c_weight, quantity, price, s_price, currency, cust_name, type, service_type, container_type, tit, supplier, carrier, notes, fob, exw, dtd, dta) VALUES ('" . addslashes(htmlspecialchars($data[date1])) . "', '" . addslashes(htmlspecialchars($data[ship_from])) . "', '" . addslashes(htmlspecialchars($data[ship_to])) . "', '" . addslashes(htmlspecialchars($data[goods_name])) . "', '" . addslashes(htmlspecialchars($data[weight])) . "', '" . addslashes(htmlspecialchars($data[c_weight])) . "', '" . addslashes(htmlspecialchars($data[quantity])) . "', '" . addslashes(htmlspecialchars($data[price])) . "', '" . addslashes(htmlspecialchars($data[s_price])) . "', '" . addslashes(htmlspecialchars($data[currency])) . "', '" . addslashes(htmlspecialchars($data[cust_name])) . "', '" . addslashes(htmlspecialchars($data[type])) . "', '" . addslashes(htmlspecialchars($data[service_type])) . "', '" . addslashes(htmlspecialchars($data[container_type])) . "', '" . addslashes(htmlspecialchars($data[tit])) . "', '" . addslashes(htmlspecialchars($data[supplier])) . "', '" . addslashes(htmlspecialchars($data[carrier])) . "', '" . addslashes(htmlspecialchars($data[notes])) . "', '" . $data['fob'] . "', '" . $data['exw'] . "', '" . $data['dtd'] . "', '" . $data['dta'] . "');";
    $DB->query($sql);
?>
<font color="#FF0000"><b>New Shipment Added.</b></font><br />
<?php
}

function do_edit()
{
    global $DB, $id, $table;

    $data        = $_POST['data'];
    $data['fob'] = ('on' == $data['fob'] ? 'checked' : '');
    $data['exw'] = ('on' == $data['exw'] ? 'checked' : '');
    $data['dtd'] = ('on' == $data['dtd'] ? 'checked' : '');
    $data['dta'] = ('on' == $data['dta'] ? 'checked' : '');
    $sql         = "UPDATE $table SET date = '" . addslashes(htmlspecialchars($data[date1])) . "', ship_from = '" . addslashes(htmlspecialchars($data[ship_from])) . "', ship_to = '" . addslashes(htmlspecialchars($data[ship_to])) . "', goods_name = '" . addslashes(htmlspecialchars($data[goods_name])) . "', weight = '" . addslashes(htmlspecialchars($data[weight])) . "', c_weight = '" . addslashes(htmlspecialchars($data[c_weight])) . "', quantity = '" . addslashes(htmlspecialchars($data[quantity])) . "', price = '" . addslashes(htmlspecialchars($data[price])) . "', s_price = '" . addslashes(htmlspecialchars($data[s_price])) . "', currency = '" . addslashes(htmlspecialchars($data[currency])) . "', cust_name = '" . addslashes(htmlspecialchars($data[cust_name])) . "', type = '" . addslashes(htmlspecialchars($data[type])) . "',service_type = '" . addslashes(htmlspecialchars($data[service_type])) . "', container_type = '" . addslashes(htmlspecialchars($data[container_type])) . "', tit = '" . addslashes(htmlspecialchars($data[tit])) . "', supplier = '" . addslashes(htmlspecialchars($data[supplier])) . "', carrier = '" . addslashes(htmlspecialchars($data[carrier])) . "', notes = '" . addslashes(htmlspecialchars($data[notes])) . "', fob = '" . $data['fob'] . "', exw = '" . $data['exw'] . "', dtd = '" . $data['dtd'] . "', dta = '" . $data['dta'] . "'  WHERE id = '" . $id . "';";
    $DB->query($sql);

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
    $sql = "UPDATE `coinop`.`freight_forwarders_quotes` SET `term_flags` = '" .
        ('checked' == $data['fob'] ? 'fob,' : '') . ('checked' == $data['exw'] ? 'exw,' : '') . ('checked' == $data['dtd'] ? 'dtd,' : '') . ('checked' == $data['dta'] ? 'dta' : '') .
        "', `ship_weight`='" . $data['weight'] . "', `cube_weight`='" . $data['c_weight'] . "', `tot_total`='" . $data['price'] . "', `sal_total`='" . $data['s_price'] . "', `transit_time`='" . $data['tit'] . "', `container_flags`='" . $data['container_type'] . "' WHERE id='$qt_id'";
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
        include 'seacargo_add_form.inc.php';
        break;
    case 'edit':
        include 'seacargo_edit_form.inc.php';
        break;
    case 'delete':
        include 'seacargo_delete_form.inc.php';
        break;
    case 'search':
        include 'seacargo_errors.php';
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
include '../../footer.inc.php';
?>
