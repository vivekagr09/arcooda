<!-- list.tpl -->
<script type="text/javascript">
    //]]>
    function display_offer_list() {
        var url = "display_offer_list.php?";
        url += "&supplier_id=<?=$supplier_id?>";
        window.open(url);
    }
</script>
<?php
moduleLoad('invoice'); //invoiceSearch, invoiceSumValue
moduleLoad('member'); //memberLoginDetailsFromDatabase

$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" alt="" />';

$sort_url           = $_REQUEST;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '.?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $_REQUEST;
$edit_url['action'] = 'edit';
unset($edit_url['supplier_id']);
$edit_url = '.?' . http_build_query($edit_url) . '&supplier_id=';

$view_url           = $_REQUEST;
$view_url['action'] = 'view';
unset($view_url['supplier_id']);
$view_url = '.?' . http_build_query($view_url) . '&supplier_id=';

$unlock_url           = $_REQUEST;
$unlock_url['action'] = 'unlock';
unset($unlock_url['supplier_id']);
$unlock_url = '.?' . http_build_query($unlock_url) . '&supplier_id=';

$select_url           = $_REQUEST;
$select_url['action'] = 'select';
unset($select_url['supplier_id']);
$select_url = '.?' . http_build_query($select_url) . '&supplier_id=';

$country_list = getCountryList(false);
$site_list    = getWebsitesList();
?>

<?=formatHTMLTitle('Supplier List')?>

<div>
	<table class="table_grid">
		<tr>
			<th><a href="<?php hecho($sort_url . 'member_id')?>">Supplier ID</a><?= ('member_id' == $order_by) ? $ord_img : '' ?></th>
			<th><a href="<?php hecho($sort_url . 'name')?>">Supplier Name</a><?= ('name' == $order_by) ? $ord_img : '' ?></th>
            <th>Supplier To</th>
			<th><a >Product Types</a></th>
			<th><a href="">Inventory</a></th>
            <th><a href="">Offer List</a></th>
			<th><a href="<?php hecho($sort_url . 'country')?>">Country</a><?= ('country' == $order_by) ? $ord_img : '' ?></th>
			<th>Inquiries</th>
			<th>Invoices</th>
			<th>Open Orders</th>
            <th>Last Contact</th>
			<th>&nbsp;</th>
		</tr>
<?php
$supplier_cache  = supplierListFromDatabase($supplier_list);
$invoices_cache = []; 
$inquiries_cache = [];
$contact_cache = array();

$check_staff_list = supplierGetOptionList('check_staff_id');
foreach ($supplier_list as $supplier_id) {
        $supplier_purchase = [
            'working'   => 0,
            'supplier'   => 0,
            'order'    => 0,
            'cancelled'     => 0,
            'order-working'  => 0,
            'supplier-waiting' => 0,
            'payment' => 0,
            'goods'    => 0,
            'checking-goods' => 0,
            'processed' => 0,
        ];

        $res = $DB->query(sprintf(
            "SELECT `order_action`, COUNT(*) AS `total` FROM `purchase_order` where `supplier_id`=".$supplier_id." GROUP BY `order_action`"
        ));
        while ($row = $DB->fetch_assoc($res)) {
            $supplier_purchase[$row['order_action']] += $row['total'];
        }
        $DB->free_result($res);
    if (array_key_exists($supplier_id, $supplier_cache)) {
        $supplier = $supplier_cache[$supplier_id];
    } else {
        $supplier = supplierFromDatabase($supplier_id);
    }
    $member_invoices = array();
   /* if (array_key_exists($member_id, $invoices_cache)) {
        $member_invoices = $invoices_cache[$member_id];
    }*/

    $member_inquiries = array();
   /* if (array_key_exists($member_id, $inquiries_cache)) {
        $member_inquiries = $inquiries_cache[$member_id];
    }*/
    $member_contact = 0;
    /*if (array_key_exists($member_id, $contact_cache)) {
        $member_contact = $contact_cache[$member_id];
    } else {
        $member_contact = 0;
    }*/

    /*if (0 != $member_contact) {
        $member['modified_time'] = max($member['modified_time'], $member_contact);
    }*/
    //$row = getCountryInfo($member['signup_IP']);
    /*if ('--' != $row['a2']) {
        $member['signup_country'] = $row['a2'];
    }*/
    $supplier_lock   = supplierGetLock($supplier_id);
    //$member_logins = memberLoginDetailsFromDatabase($member_id);
    $supplier['total_invoices']  = intval($supplier_purchase['processed']);
    $supplier['total_inquiries'] =intval($supplier_purchase['working'] + $supplier_purchase['supplier']+$supplier_purchase['cancelled']);

    $supplier['invoice_list'] =[];// invoiceSearch(array('member_id' => $member_id, 'invoice_status' => 'processed'));
    $supplier['open_order'] = intval( $supplier_purchase['order'] + $supplier_purchase['order-working']+ $supplier_purchase['supplier-waiting']+ $supplier_purchase['payment']+ $supplier_purchase['goods']+$supplier_purchase['checking-goods']);
    /*if (count($member['invoice_list'])) {
        $member['order_total'] = '$' . convertCurrency(invoiceSumValue($member['invoice_list'], 'invoice_offerus'), 'USD') . ' USD';
    }*/

    /*$row = getCountryInfo($member['signup_IP']);
    if ('--' != $row['a2']) {
        $member['signup_country'] = $row['a2'];
    }*/
    $supplier_offer_list=totalSupplierStockFromDatabase($supplier_id);
    $supplier_inventory_list=totalSupplierInventoryFromDatabase($supplier_id);
    $inventory_list_url='';
    foreach($supplier_inventory_list as $list){
        $inventory_list_url .= (empty($inventory_list_url))?('?product_id[]='.$list.''):('&product_id[]='.$list.'');
    }
    $linkurl=(count($supplier_inventory_list)>0)?"'/webadmin/products_new/list.php".$inventory_list_url."'":'';
    $supplier_comments = supplierCommentSearch(array('supplier_id' => $supplier_id, 'comment_type' => 'warning'));
    
    $warning_tooltip='Supplier Warnings<br/>--------------------------<br/>';
    $warning_tooltip .='<table>';
            if (count($supplier_comments) > 0) {
                foreach ($supplier_comments as $comment_id) {
                    $comment = supplierCommentFromDatabase($comment_id);
                    $warning_tooltip .='<tr><td>Warning : </td><td>'.nl2br(hecho($comment['comment_text'], 1)).'</td></tr>';
                }
            }else{
                $warning_tooltip .='<tr><td>No any Warnings for supplier</td></tr>';
            }
    $warning_tooltip .='</table>';
    
    $supplier_tooltip ='<table>';
    $supplier_tooltip .='<tr><td>'.'<b>'.ucwords($supplier ["company"]).'</b><br>Attn:'.$supplier ['full_name'].'<br>'.$supplier['address_1'].'<br>'. $supplier['city'].','.$supplier['state'].' '.$supplier['post_code'].'<br>.'.$country_list[$supplier["country"]].'-' .$supplier['country'].'<br> <br>Ph:'.$supplier ['phone_number'].'<br>Email:'.$supplier ['email_address'].'</td></tr>';
           
    $supplier_tooltip .='</table>';
?>
		<?=($alt = !$alt) ? '<tr>' : '<tr class="alt">'?>
            <td style="text-align:center;white-space:nowrap;"  onmouseover='tt_in("<?= $supplier_tooltip ?>")' onmouseout="tt_out()"><?php hecho(supplierFormatNumber($supplier_id))?></td>
			<td style="text-align:center;white-space:nowrap;" onmouseover='tt_in("<?= $warning_tooltip ?>")' onmouseout="tt_out()"><p style="padding: 2px; border: 1px solid; border-color: red;cursor: pointer;width: 95%"><?php hecho($supplier['company'])?> <?php if($supplier['company_chinese']){ ?>
                 / 
                <?php hecho($supplier['company_chinese'])?>
            <?php } ?>
            </p>
            </td>
            <td>
                <?php
                    $company_array=[3=>'Coinopexpress',2=>'Fortune Wave',1=>'Channel Beat'];
                    foreach (explode(',', $supplier['supplier_type']) as $site_id) {
                        if ('' != $site_id) {
                    ?>  
                                    <img src="<?php hecho('/webadmin/images/sites/site_' . $site_id . '/logo_16.ico')?>" alt="<?php hecho($company_array[$site_id])?>" title="<?php hecho($company_array[$site_id])?>" />
                    <?php
                        }
                    }
                ?>
            </td>
			<td style="text-align:center;white-space:nowrap;"><?php 
                $producttype=explode(',', $supplier['product_type']);
                $producttypearray=['pcbs'=>'PCBs','parts'=>'Parts','machines'=>'Machines','merchandies'=>'Merchandies'];
                foreach ($producttype as $key => $value) {
                    if(count($producttype)==($key+1))
                    echo $producttypearray[''.$value.''];
                    else
                    echo $producttypearray[''.$value.''].', ';
                }
             ?></td>
			<td style="text-align:center;white-space:nowrap;"><?= (count($supplier_inventory_list)>0)?'<a href='.$linkurl.' target="_blank">':'' ?><?php hecho(count($supplier_inventory_list))?><?= (count($supplier_inventory_list)>0)?'</a>':'' ?></td>
            <td style="text-align:center;white-space:nowrap;"><?php hecho($supplier_offer_list)?></td>
			<td style="text-align:center;white-space:nowrap;"><i class="fi fi-<?=strtolower($supplier['country'])?>" title="<?php hecho($country_list[$supplier['country']])?>"></i></td>
			<td style="text-align:center;white-space:nowrap;"><?= ($supplier['total_inquiries']>0)?'<a href="/webadmin/purchase_order/?action=list&supplier_id='.$supplier_id.'&order_action=working|cancelled|supplier" target="_blank">':'' ?><?php hecho($supplier['total_inquiries'])?><?= ($supplier['total_inquiries']>0)?'</a>':'' ?></td>
			<td style="text-align:center;white-space:nowrap;"><?= ($supplier['total_invoices']>0)?'<a href="/webadmin/purchase_order/?action=list&supplier_id='.$supplier_id.'&order_action=processed" target="_blank">':'' ?><?php hecho($supplier['total_invoices'])?><?= ($supplier['total_invoices']>0)?'</a>':'' ?></td>
			<td style="text-align:center;white-space:nowrap;"><?= ($supplier['open_order']>0)?'<a href="/webadmin/purchase_order/?action=list&supplier_id='.$supplier_id.'&order_action=order|order-working|supplier-waiting|payment|goods|checking-goods" target="_blank">':'' ?><?php hecho($supplier['open_order'])?><?= ($supplier['open_order']>0)?'</a>':'' ?></td>
            <td style="text-align:center;white-space:nowrap;"><?=($supplier['modified']=='0000-00-00 00:00:00')?date('d-m-Y',strtotime($supplier['created'])):date('d-m-Y',strtotime($supplier['modified'])); ?></td>
			<td style="text-align:center;white-space:nowrap;">
<?php
if ($popup_window) {
?>
				<input type="button" value=" Select " onclick="<?php hecho('window.location = ' . jecho($select_url . $supplier_id, 1))?>" />
				<input type="button" value=" Edit " onclick="<?php hecho('window.open(' . jecho($edit_url . $supplier_id, 1))?>" />
<?php
} else {
        if (false === $supplier_lock || $supplier_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
?>
				<a href="<?php hecho($edit_url . $supplier_id)?>" target="_blank">Edit</a>
<?php
} else {
?>
				<a href="<?php hecho($edit_url . $supplier_id)?>" target="_blank">Edit</a>
                <!-- <a href="<?php hecho($view_url . $supplier_id)?>" target="_blank">View</a> -->
<?php
}
        if (false !== $supplier_lock && $supplier_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
?>
				<a href="<?php hecho($unlock_url . $supplier_id)?>">Unlock</a>
<?php
}
    }
?>
			</td>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberList_hideNote">List of Supplier can be viewed by Clicking "Supplier List" above<br /></div>

<!-- /list.tpl -->
