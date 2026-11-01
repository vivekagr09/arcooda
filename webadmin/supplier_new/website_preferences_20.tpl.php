<!-- website_preferences_20.tpl -->

<?=formatHTMLTitle('Arcade Spare Parts Preferences')?>

<table class="table_fields">
    <tr>
        <th>Member Type :</th>
        <td style="width:75%;"><select class="discount_select" id="discount_20" name="discounts[20]"><?=formatHTMLOptions($discount_array, $member_discounts[20])?></select></td>
    </tr>
    <tr>
        <th>Date Joined :</th>
        <td style="width:75%;"><?=date('d/m/Y', $member['time_joined'])?></td>
    </tr>
    <tr>
        <th>Total Logins</th>
        <td style="width:75%;"><?=count($chunked_logins['asp'])?></td>
    </tr>
    <tr>
        <th>Last Login :</th>
        <td style="width:75%;"><?=$last_asp_login?></td>
    </tr>
    <tr>
        <th>Total Orders :</th>
        <td style="width:75%;"><?=count($asp_invoices)?></td>
    </tr>
    <tr>
        <th>Last Order :</th>
        <td style="width:75%;"><?=$last_asp_order?></td>
    </tr>
</table>

<?php
if ('add' != $action) {
?>
<?=formatHTMLTitle('Arcade Spare Parts Special Pricing', '<a style="color:black;" href="#" onclick="addSpecialPrice(20,\'asp\');return false;">Add</a>')?>

<table class="table_grid" id="asp_spec" name="asp_spec">
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
    $asp_pricing = $member_specials[20];
    if (is_array($asp_pricing)) {
        $count = 0;
        foreach ($asp_pricing as $key => $product) {
?>
    <tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="spec_<?=$product['id']?>">
        <td><?=$product['product_id']?></td>
        <td><?=$product['product_name']?></td>
        <td><?=$product['min_order']?></td>
        <td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
        <td>$<?=sprintf('%.2f', $product['product_online'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_price'])?></td>
        <td><input type="button" value=" Remove " onclick="removeSpecialPrice('asp',<?=$product['id']?>);return false;" /></td>
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

<!-- /website_preferences_20.tpl -->
