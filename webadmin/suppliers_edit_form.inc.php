<?php
$supplier = $DB->query_first(sprintf(
    "SELECT * FROM `suppliers` WHERE `id` = %d LIMIT 1",
    $id
), MYSQLI_ASSOC);

$country_list = getCountryList(false);

$active_list = [
    'y' => 'Yes',
    'n' => 'No',
];

?>
<table class="table_fields">
    <tr>
        <th>Supplier :</th>
        <td><input class="wide" type="text" name="data[supplier]" value="<?php hecho($supplier['supplier']);?>" /></td>
    </tr>
    <tr>
        <th>Notes :</th>
        <td><?=formatHTMLTextarea('data[notes]', $supplier['notes'], true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th><b>Address :</th>
        <td><?=formatHTMLTextarea('data[address]', $supplier['address'], true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>City :</th>
        <td><input class="wide" type="text" name="data[city]" value="<?php hecho($supplier['city']);?>" /></td>
    </tr>
    <tr>
        <th>State :</th>
        <td><input class="wide" type="text" name="data[state]" value="<?php hecho($supplier['state']);?>" /></td>
    </tr>
    <tr>
        <th>Postal Code :</th>
        <td><input class="wide" type="text" name="data[postal_code]" value="<?php hecho($supplier['postal_code']);?>" /></td>
    </tr>
    <tr>
        <th>Country :</th>
        <td><select class="wide" name="data[country]">
            <option value=""></option>
            <?=formatHTMLOptions($country_list, $supplier['country']);?>
        </select></td>
    </tr>
    <tr>
        <th>Phone :</th>
        <td><?=formatHTMLTextarea('data[phone]', $supplier['phone'], true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Fax :</th>
        <td><?=formatHTMLTextarea('data[fax]', $supplier['fax'], true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Website :</th>
        <td><?=formatHTMLTextarea('data[website]', $supplier['website'], true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Email :</th>
        <td><?=formatHTMLTextarea('data[email]', $supplier['email'], true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Active:</th>
        <td><select class="wide" name="data[active]">
            <?=formatHTMLOptions($active_list, $supplier['active']);?>
        </select></td>
    </tr>
</table>

<br />
<br />

<div style="text-align:center;">
    <input type="hidden" name="id" value="<?=htmlspecialchars($id)?>" />
    <input type="hidden" name="action" value="do_edit" />
    <input type="submit" name="do_action" value="Update" />
    <input type="reset" name="clear" value="Reset" />
</div>
