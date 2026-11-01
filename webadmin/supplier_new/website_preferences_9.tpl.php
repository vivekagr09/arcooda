<!-- website_preferences_9.tpl -->

<?=formatHTMLTitle('Converters.tv Preferences')?>

<table class="table_fields">
    <tr>
        <th>Member Type :</th>
        <td style="width:75%;"><select class="discount_select" id="discount_9" name="discounts[9]"><?=formatHTMLOptions($discount_array, $member_discounts[9])?></select></td>
    </tr>
    <tr>
        <th>Date Joined :</th>
        <td style="width:75%;"><?=date('d/m/Y', $member['time_joined'])?></td>
    </tr>
    <tr>
        <th>Total Logins</th>
        <td style="width:75%;"><?=count($chunked_logins['cnv'])?></td>
    </tr>
    <tr>
        <th>Last Login :</th>
        <td style="width:75%;"><?=$last_cnv_login?></td>
    </tr>
    <tr>
        <th>Total Orders :</th>
        <td style="width:75%;"><?=count($cnv_invoices)?></td>
    </tr>
    <tr>
        <th>Last Order :</th>
        <td style="width:75%;"><?=$last_cnv_order?></td>
    </tr>
</table>

<?php
if ('add' != $action) {
?>
<?=formatHTMLTitle('Converters.tv Special Pricing', '<a style="color:black;" href="#" onclick="addSpecialPrice(9,\'cnv\');return false;">Add</a>')?>

<table class="table_grid" id="cnv_spec" name="cnv_spec">
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Online (AUD)</th>
        <th>Online (USD)</th>
        <th>Special Price (AUD)</th>
        <th>Special Price (USD)</th>
        <th>&nbsp;</th>
    </tr>
<?php
    $cnv_pricing = $member_specials[9];
    if (is_array($cnv_pricing)) {
        $count = 0;
        foreach ($cnv_pricing as $key => $product) {
?>
    <tr>
        <td><?=$product['product_id']?></td>
        <td><?=$product['product_name']?></td>
        <td><?=$product['min_order']?></td>
        <td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
        <td>$<?=sprintf('%.2f', $product['product_online'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_price'])?></td>
        <td><input type="button" value=" Remove " onclick="removeSpecialPrice('cnv',<?=$product['id']?>);return false;" /></td>
    </tr>
<?php
            $count++;
        }
    }
?>
</table>
<?php
}
?>

<!-- /website_preferences_9.tpl -->
