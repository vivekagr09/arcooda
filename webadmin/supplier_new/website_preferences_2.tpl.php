<!-- website_preferences_2.tpl -->

<?=formatHTMLTitle('Highway Entertainment Preferences')?>

<table class="table_fields">
    <tr>
        <th>
            Member Type :<br />
            <small style="color:red;">Discount applies to Spare Parts only</small>
        </th>
        <td><select class="discount_select" id="discount_2" name="discounts[2]"><?=formatHTMLOptions($discount_array, $member_discounts[2])?></select></td>
    </tr>
    <tr>
        <th>Date Joined :</th>
        <td><?=date('d/m/Y', $member['time_joined'])?></td>
    </tr>
    <tr>
        <th>Total Logins :</th>
        <td><?=count($chunked_logins['he'])?></td>
    </tr>
    <tr>
        <th>Last Login :</th>
        <td><?=$last_he_login?></td>
    </tr>
    <tr>
        <th>Total Orders :</th>
        <td><?=count($he_invoices)?></td>
    </tr>
    <tr>
        <th>Last Order :</th>
        <td><?=$last_he_order?></td>
    </tr>
</table>

<?php
if ('add' != $action) {
?>
<?=formatHTMLTitle('Highway Entertainment Special Pricing', '<a style="color:black;" href="#" onclick="addSpecialPrice(2,\'he\');return false;">Add</a>')?>

<table class="table_grid" id="he_spec" name="he_spec">
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
    $he_pricing = $member_specials[2];
    if (is_array($he_pricing)) {
        $count = 0;
        foreach ($he_pricing as $key => $product) {
?>
    <tr>
        <td><?=$product['product_id']?></td>
        <td><?=$product['product_name']?></td>
        <td><?=$product['min_order']?></td>
        <td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
        <td>$<?=sprintf('%.2f', $product['product_online'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_price'])?></td>
        <td><input type="button" value=" Remove " onclick="removeSpecialPrice('he',<?=$product['id']?>);return false;" /></td>
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

<!-- /website_preferences_2.tpl -->
