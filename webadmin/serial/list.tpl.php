<?php
/**
 * @var Serial_Handler $section_handler
 * @var mixed[]        $get_vars
 * @var string         $action
 * @var string         $order_by
 * @var string         $order
 * @var int[]          $item_list
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('member'); //memberRequestsFromDatabase, memberFromDatabase

global $_GLOBALS;

$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '.?' . http_build_query($sort_url) . '&order_by=';

$product_url           = $get_vars;
$product_url['action'] = 'edit';
unset($product_url['product_str']);
$product_url = '/webadmin/products_new/?' . http_build_query($product_url) . '&id=';

$member_url           = $get_vars;
$member_url['action'] = 'edit';
unset($member_url['member_id']);
$member_url = '/webadmin/member/?' . http_build_query($member_url) . '&member_id=';

$invoice_url           = $get_vars;
$invoice_url['action'] = 'edit';
unset($invoice_url['invoice_id']);
$invoice_url = '/webadmin/invoice/?' . http_build_query($invoice_url) . '&invoice_id=';

$edit_url           = $get_vars;
$edit_url['action'] = 'delete';
unset($edit_url['id']);
$edit_url = '.?' . http_build_query($edit_url) . '&id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['id']);
$view_url = '.?' . http_build_query($view_url) . '&id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['id']);
$unlock_url = '.?' . http_build_query($unlock_url) . '&id=';
?>

<?php
if ("print" != $action) {
    echo formatHTMLTitle('<a href="#" onclick="$(\'#sectionList\').add(\'#sectionList_hideNote\').toggle(\'slow\');return false">' . htmlspecialchars(_("Machines Serial Number")) . '</a>');
}

?>

<div id="sectionList">
	<table class="table table_grid">
		<tr>
			<th><a href="<?=htmlspecialchars($sort_url . 'serial_id')?>"><?=htmlspecialchars(_("Serial #"))?></a><?=('serial_id' == $order_by) ? $ord_img : ''?></th>
			<th style="width:25%"><?=htmlspecialchars(_("Machine Name"))?></th>
			<th><a href="<?=htmlspecialchars($sort_url . 'product_str')?>"><?=htmlspecialchars(_("Machine #"))?></a><?=('product_str' == $order_by) ? $ord_img : ''?></th>
			<th><a href="<?=htmlspecialchars($sort_url . 'invoice_id')?>"><?=htmlspecialchars(_("Invoice #"))?></a><?=('invoice_id' == $order_by) ? $ord_img : ''?></th>
			<th><?=htmlspecialchars(_("Sold / Registered To"))?></th>
			<th><?=htmlspecialchars(_("Date of Invoice"))?></th>
<?php
if ("print" != $action) {
?>
			<th></th>
<?php
}
?>
		</tr>
<?php
foreach ($item_list as $id) {
    $item      = $section_handler->fromDatabase($id);
    $item_lock = $section_handler->getLock($id);

    $class     = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    /*
    if ('y' == $item['active']) {
        $class = ' class="highlight"';
    }
    */

    if (false !== $item_lock) {
        $class = ' class="locked"';
    }

    if ($item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
        $class = ' class="mylock"';
    }

    if (0 != $item['member_id']) {
        $member_requests = memberRequestsFromDatabase($item['member_id']);
    } else {
        $member_requests = [['comments' => 'Invoice is not linked to a member']];
    }

    $customer_tooltip   = [$item['bill_address_full']];
    $customer_highlight = false;
    if (count($member_requests)) {
        $customer_tooltip[] = '';
        $customer_tooltip[] = "Member Warnings :";
        foreach ($member_requests as $request) {
            if ('' != $request['comments']) {
                $customer_tooltip[] = '-' . $request['comments'];
            }
        }

        $customer_highlight = true;
    }
    $customer_tooltip = nl2br(implode("\n", $customer_tooltip));
?>
		<tr<?=$class?>>
			<td style="white-space:nowrap"><?=htmlspecialchars($item["serial_number"])?></td>
			<td style="text-align:left;padding-left:4px"><a target="_blank" class="unfinished" href="<?=htmlspecialchars($product_url . urlencode($item['product_id']))?>" title="<?=htmlspecialchars($item["product_str"])?>"><span style="height:1.4em;display:block;overflow:hidden"><?=htmlspecialchars(($item["product_name"])?$item["product_name"]:$item["product_str"])?></span></a></td>
			<td style="white-space:nowrap;"><a target="_blank"  href="<?=htmlspecialchars($product_url . urlencode($item['product_id']))?>" title="<?=htmlspecialchars($item["product_str"])?>"><?=($item["product_id"])?$item["product_id"]:''?></td></a>
<?php
    if (0 != $item["invoice_id"] || 0 != $item["member_id"]) {
?>
			<td style="white-space:nowrap;<?=($item['invoice_id'] || $item["member_id"])?'':'background:#fa9'?>">
                <?php if($item["invoice_id"]){ ?>
                <a target="_blank" href="<?=htmlspecialchars($invoice_url . $item['invoice_id'])?>"><?=($item['invoice_number'])?$item['invoice_number']:''?>
                </a>
            <?php }else{
                echo '';
            } ?>
            </td>
 <?php
    if (0 != $item["invoice_id"]) {
?>   
			<td style="white-space:nowrap;" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($customer_tooltip) . ')')?>" onmouseout="tt_out()">
<?php
        if ($customer_highlight) {
?>
				<div style="border:2px solid #FF1493;height:57px"><br />
<?php
        }
        if (0 != $item['member_id']) {
?>
					<a target="_blank" href="<?=htmlspecialchars($member_url . $item['member_id'])?>">
<?php
        }
?>
						<?=htmlspecialchars($item["bill_name"])?>
<?php
        if (0 != $item['member_id']) {
?>
					</a>
<?php
        }
        if ($customer_highlight) {
?>
				</div>
<?php
        }
?>
			</td>
<?php
        }else{
                $member=memberFromDatabase($item['member_id']);
?>
            <td><a href="/webadmin/member/?action=edit&amp;member_id=<?=$item['member_id']?>" target="_blank"><?=htmlspecialchars($member['first_name'])?> <?=htmlspecialchars($member['last_name'])?></a></td>
<?php
        }
?>
			<td style="white-space:nowrap;"><?=($item['time_to_use'])?date('d/m/Y', $item['time_to_use']):date('d/m/Y', $item['modified_time'])?></td>
<?php
    } else {
?>
			<td style="white-space:nowrap;background:#fa9">Stock</td>
			<td style="white-space:nowrap;background:#fa9">Stock</td>
			<td style="white-space:nowrap;background:#fa9">Stock</td>
<?php
    }
    if ("print" != $action) {
?>
			<td style="text-align:center">
<?php
        if (false === $item_lock || $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
?>
				<!-- <a href="<?=htmlspecialchars($edit_url . $id)?>" target="_blank"><?=htmlspecialchars(_("Edit"))?></a> -->
                <a href="<?=htmlspecialchars($edit_url . $id)?>" onclick=" return confirmdeelete()" target="_blank"><?=htmlspecialchars(_("Remove"))?>
<?php
        } else {
?>
				<!-- <a href="<?=htmlspecialchars($view_url . $id)?>" target="_blank"><?=htmlspecialchars(_("View"))?></a> -->
<?php
        }
        if (false !== $item_lock && $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
?>
				<a href="<?=htmlspecialchars($unlock_url . $id)?>"><?=htmlspecialchars(_("Unlock"))?></a>
<?php
        }
?>
			</td>
<?php
    }
?>
		</tr>
<?php
}
?>
	</table>
	<br /><br />
</div>
<script type="text/javascript">
    function confirmdeelete(){
        if (confirm("This will remove all links to this Serial number including product/member/invoice linking.\n\
It will release the serial number as free serial number. ")) {
            return true;
        }else{
            return false;
        }
    }
</script>
<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
