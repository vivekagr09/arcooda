<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberSpecialPriceFromDatabase

$member_id      = intval($posted['member_id']);
$site_id        = intval($posted['site_id']);
$return_message = $posted['return_message'];
$id             = intval($posted['id']);
$action         = $posted['action'];

if (!$member_id && !$site_id && 'do_delete' != $action) {
    die("Proper parameters not specified. Need a valid member_id and a valid site_id");
}

function displayTemplate($member_id, $site_id, $ret_msg, $id = 0)
{
    $_GET['return_message'] = 'true';
    include_once __DIR__ . '/header.inc.php';

    $special_pricing = memberSpecialPriceFromDatabase($member_id);
    $specials        = $special_pricing[$site_id];
    $sites           = array(
        1  => 'Highway Games',
        2  => 'Highway Entertainment',
        3  => 'Coinop Express',
        9  => 'Converters.tv',
        16 => 'Car Converters',
        20 => 'Arcade Spare Parts',
        39 => 'Arcooda',
    );
    $site_name       = $sites[$site_id];
?>
<script type="text/javascript">
//<![CDATA[
function setProductData(data) {
    if (data == null) return;
    if (data['return_message'] == 'select') {
        if (data['product_cost'] == null) data['product_cost'] = '0.00';
        data['sales_price'] = data['product_offer'];
        setValue('product_id', data['product_id']);
        if (data['sub_id'] != null) setValue('sub_id', data['sub_id']);
        setValue('product_display', data[((data['formatted_number'] != null && data['formatted_number'] != '') ? 'formatted_number' : 'product_id')]);
        setValue('product_name', data['product_name']);
        setValue('cost_price', data['product_cost']);
        setValue('product_online', data['sales_price']);
    }
}
function searchProduct(searchFor) {
    var currency = "USD";
    var site_id = getValue('site_id');
    var member_id = getValue('member_id');
    if (site_id == '1') site_id = '1|2';
    var url = '/webadmin/products/index.php?action=list';
    url += '&return_message=select';
    url += '&search_for=' + escape(searchFor);
    if (currency != null) url += '&currency=' + currency;
    if (site_id != null) url += '&site_id=' + site_id;
    if (member_id != null) url += '&member_id=' + member_id;
    var features = 'scrollbars, resizable';
    window.open(url, 'product_popup_2', features);
}
//]]>
</script>

<?= formatHTMLTitle('Add Member Special Price - ' . $site_name); ?>

<table class="table_fields">
    <tr>
        <th>Product ID/Name Search :</th>
        <td><input type="text" name="product_search" style="width:75%;" /> <input type="button" value="Search" onclick="searchProduct(getValue('product_search'));" /></td>
    </tr>
    <tr id="search_tr" style="display:none;"><td colspan="2"><div id="search_results">&nbsp;</div></td></tr>
</table>

<br />
<br />

<form accept-charset="utf-8" autocomplete="off" method="post" action="">
	<input type="hidden" name="member_id" value="<?= $member_id ?>" />
    <input type="hidden" name="site_id" value="<?= $site_id ?>" />
	<input type="hidden" name="action" value="do_add" />
    <input type="hidden" name="id" value="<?= $id ?>" />
    <input type="hidden" name="product_id" /><input type="hidden" name="sub_id" />

<table class="table_fields">
    <tr>
        <th>Product ID :</th>
        <td><input type="text" name="product_display" class="wide" /></td>
    </tr>
    <tr>
        <th>Product Name :</th>
        <td><input type="text" name="product_name" class="wide" /></td>
    </tr>
    <tr>
        <th>Minimum Order :</th>
        <td><input type="text" name="min_order" class="wide" value="1"/></td>
    </tr>
    <tr>
        <th>Cost Price (USD) :</th>
        <td><input type="text" name="cost_price" class="wide" /></td>
    </tr>
    <tr>
        <th>Online Price :</th>
        <td>
            <input type="text" name="product_online" style="width:33%;" /> <b>USD</b>
        </td>
    </tr>
    <tr>
        <th>Member Special Price (USD) :</th>
        <td><input type="text" name="special_price" class="wide" /></td>
    </tr>
</table>

<br />

<div style="text-align:center;"><input type="submit" value="Add Special Price" /></div>

</form>
<br />

<?php
    if (count($specials)) {
?>
	<?= formatHTMLTitle('Current Special Prices for Member ID#' . $member_id) ?>
	<table>
		<tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Min. Order</th>
            <th>Cost Price (USD)</th>
            <th>Online Price (USD)</th>
            <th>Special Price (USD)</th>
            <th>% Discount</th>
            <th>Profit</th>
<?php
        $count = 0;
        foreach ($specials as $id => $array) {
            $discount = round(100 * (1 - ($array['special_price'] / $array['product_online'])));
            $profit   = $array['special_price'] - $array['cost_price'];
?>
		<tr <?= $count++ % 2 == 1 ? 'class="alt"' : '' ?> style="text-align:center">
            <td><?= $array['product_id'] ?></td>
            <td><?= $array['product_name'] ?></td>
            <td><?= $array['min_order'] ?></td>
            <td><?= $array['cost_price'] ?></td>
            <td><?= $array['product_online'] ?></td>
            <td><?= $array['special_price'] ?></td>
            <td><?= $discount ?>%</td>
            <td><?= $profit ?></td>
		</tr>
<?php
        }
?>
	</table>
<?php
    } else {
?>
	No Special Pricing exists for this customer
<?php
    }
?>
</div>
</body>
</html>
<?php
}

/**
 * @param mixed[] $posted
 * @param int     $member_id
 * @param int     $site_id
 * @param int     $id
 *
 * @return mixed[]|false
 */
function do_add($posted, $member_id, $site_id, $id = 0)
{
    global $DB;
    $min_order  = intval($posted['min_order']);
    $product_id = intval($posted['product_id']);
    if (!$product_id) {
        die("Invalid Product ID supplied.");
    }

    $sql     = (($id > 0) ? "UPDATE" : "INSERT INTO") . " `highwaygroup`.`new_members_special_pricing` ";
    $allowed = array('product_id', 'sub_id', 'product_name', 'min_order', 'cost_price', 'product_online', 'special_price');
    $set     = array();
    foreach ($allowed as $field) {
        $set[] = $field . '=' . formatSQLString($posted[$field]);
    }

    if (0 == $id) {
        $set[] = 'member_id=' . $member_id . ', site_id=' . $site_id;
    }

    if (count($set)) {
        $sql .= " SET " . implode(', ', $set) . (($id > 0) ? " WHERE id=" . $id : '');
        //echo $sql;
        $DB->query($sql);
        if (0 == $id) {
            $id = $DB->insert_id();
        }

        $data                     = array('product_id' => $product_id, 'min_order' => $min_order, 'product_name' => htmlspecialchars($posted['product_name']));
        $data['cost_price']       = sprintf('%.2f', $posted['cost_price']);
        $data['product_online']   = sprintf('%.2f', $posted['product_online']);
        $data['product_onlineau'] = sprintf('%.2f', $posted['product_onlineau']);
        $data['special_price']    = sprintf('%.2f', $posted['special_price']);
        $data['special_priceau']  = sprintf('%.2f', $posted['special_priceau']);
        $data['id']               = $id;
        $data['return_message']   = $posted['return_message'];
        return $data;
    }
    return false;
}

function do_delete($id)
{
    global $DB;

    return $DB->query(sprintf(
        "DELETE FROM `highwaygroup`.`new_members_special_pricing` WHERE `id` = %d",
        $id
    ));
}

switch ($action) {
    case 'do_delete':
        if (do_delete($id)) {
?>
		<script type="text/javascript">
		var name='<?= $posted['return_message']; ?>';
		if(window.opener!=null && window.opener.deleteSpecialPrice!=null)
			window.opener.deleteSpecialPrice('<?= $posted['return_message'] ?>',<?= $id ?>);
		else if(window.parent!=null && window.parent.deleteSpecialPrice!=null)
			window.parent.deleteSpecialPrice('<?= $posted['return_message'] ?>',<?= $id ?>);
		else
			alert('Unable to delete special pricing.');
		window.close();
		</script>
<?php
        }
        break;
    case 'do_add':
        $data = do_add($posted, $member_id, $site_id, $id);
        if ($data) {
?>
<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($data)?>;
if (window.opener != null && window.opener.setSpecialData != null) window.opener.setSpecialData(data);
else if (window.parent != null && window.parent.setSpecialData != null) window.parent.setSpecialData(data);
else alert('Unable to send product back to form');
window.close();
//]]>
</script>
<?php
        }
        break;
    case '':
    default:
        displayTemplate($member_id, $site_id, $return_message, $id);
}