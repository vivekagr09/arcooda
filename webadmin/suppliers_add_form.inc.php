<?php
$country_list = getCountryList(false);

$active_list = [
    'y' => 'Yes',
    'n' => 'No',
];

?>
<table class="table_fields">
    <tr>
        <th>Supplier :</th>
        <td><input class="wide" type="text" name="data[supplier]" value="" /></td>
    </tr>
    <tr>
        <th>Notes :</th>
        <td><?=formatHTMLTextarea('data[notes]', '', true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th><b>Address :</th>
        <td><?=formatHTMLTextarea('data[address]', '', true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>City :</th>
        <td><input class="wide" type="text" name="data[city]" value="" /></td>
    </tr>
    <tr>
        <th>State :</th>
        <td><input class="wide" type="text" name="data[state]" value="" /></td>
    </tr>
    <tr>
        <th>Postal Code :</th>
        <td><input class="wide" type="text" name="data[postal_code]" value="" /></td>
    </tr>
    <tr>
        <th>Country :</th>
        <td><select class="wide" name="data[country]">
            <option value=""></option>
            <?=formatHTMLOptions($country_list, '');?>
        </select></td>
    </tr>
    <tr>
        <th>Phone :</th>
        <td><?=formatHTMLTextarea('data[phone]', '', true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Fax :</th>
        <td><?=formatHTMLTextarea('data[fax]', '', true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Website :</th>
        <td><?=formatHTMLTextarea('data[website]', '', true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Email :</th>
        <td><?=formatHTMLTextarea('data[email]', '', true, ' class="wide"');?></td>
    </tr>
    <tr>
        <th>Active:</th>
        <td><select class="wide" name="data[active]">
            <?=formatHTMLOptions($active_list, 'y');?>
        </select></td>
    </tr>
</table>

<br />
<br />

<div style="text-align:center;">
    <input type="hidden" name="action" value="do_add" />
    <input type="submit" name="do_action" value="Add" />
    <input type="reset" name="clear" value="Clear" />
</div>
