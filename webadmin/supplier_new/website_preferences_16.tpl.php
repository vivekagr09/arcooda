<!-- website_preferences_16.tpl -->

<?=formatHTMLTitle('Car Converters Preferences')?>

<table class="table_fields">
    <tr>
        <th>Member Type :</th>
        <td colspan="3"><select class="discount_select" id="discount_16" name="discounts[16]"><?=formatHTMLOptions($discount_array, $member_discounts[16])?></select></td>
    </tr>
    <tr>
        <th>Industry Code :</th>
        <td><input type="text" name="car[ic_code]" value="<?=$car_details['ic_code']?>" /></td>
        <th>Date Joined :</th>
        <td><?=date('d/m/Y', $member['time_joined'])?></td>
    </tr>
    <tr>
        <th>Sales Rep :</th>
        <td><select name="car[sales_rep]"><?=formatHTMLOptions(array('' => ''), $car_details['sales_rep'])?></select></td>
        <th>Total Logins</th>
        <td><?=count($chunked_logins['car'])?></td>
    </tr>
    <tr>
        <th>Mailing List :</th>
        <td><select name="car[send_newsletter]"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $car_details['send_newsletter'])?></select></td>
        <th>Last Login :</th>
        <td><?=$last_car_login?></td>
    </tr>
    <tr>
        <th>How Located :</th>
        <td><select name="car[heard_from]"><?=formatHTMLOptions(array('' => ''), $car_details['heard_from'])?></select></td>
        <th>Total Orders :</th>
        <td><?=count($car_invoices)?></td>
    </tr>
    <tr>
        <th>Last Order :</th>
        <td colspan="3"><?=$last_car_order?></td>
    </tr>
</table>

<?php
if ('add' != $action) {
?>
<?=formatHTMLTitle('Car Converters Special Pricing', '<a style="color:black;" href="#" onclick="addSpecialPrice(16,\'car\');return false;">Add</a>')?>

<table class="table_grid" id="car_spec" name="car_spec">
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
    $car_pricing = $member_specials[16];
    if (is_array($car_pricing)) {
        $count = 0;
        foreach ($car_pricing as $key => $product) {
?>
    <tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="spec_<?=$product['id']?>">
        <td><?=$product['product_id']?></td>
        <td><?=$product['product_name']?></td>
        <td><?=$product['min_order']?></td>
        <td>$<?=sprintf('%.2f', $product['product_onlineau'])?></td>
        <td>$<?=sprintf('%.2f', $product['product_online'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_priceau'])?></td>
        <td style="background-color:#7FFFD4;">$<?=sprintf('%.2f', $product['special_price'])?></td>
        <td><input type="button" value=" Remove " onclick="removeSpecialPrice('car',<?=$product['id']?>);return false;" /></td>
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

<!-- /website_preferences_16.tpl -->
