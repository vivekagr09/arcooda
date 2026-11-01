<!-- _product_reference.tpl -->

<?php
/**
 * Product Reference template
 *
 * Inputs:
 * - $item
 */

if (!isset($item) || !is_array($item)) {
    return;
}

if (array_key_exists('reference_id', $item) && $item['reference_id']) {
?>
    <input type="hidden" name="item[reference_id]" value="<?=$item['reference_id']?>" />
<?php
$reference = productFromDatabase($item['reference_id']);
    if (in_array($reference['section'], array(1, 2, 5))) {
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionReference&quot;);return false">Product Reference</a>')?>

<div id="sectionReference">
    <table class="table_fields">
        <tr>
            <th>PCBs & Kits Type :</th>
            <td>
<?php
$temp    = productGetOptionList('pcb_type');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['pcb_type']]);
?>
            </td>
        </tr>
        <tr>
            <th>Controller Type :</th>
            <td>
<?php
$temp    = productGetOptionList('controller_type');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['controller_type']]);
?>
            </td>
        </tr>
        <tr<?=in_array($reference['controller_type'], array(3)) ? '' : ' style="display:none;"'?>>
            <th>No Of Joysticks :</th>
            <td>
<?php
$temp    = productGetOptionList('joysticks');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['joysticks']]);
?>
            </td>
        </tr>
        <tr<?=in_array($reference['controller_type'], array(3)) ? '' : ' style="display:none;"'?>>
            <th>No Of Buttons :</th>
            <td>
<?php
$temp    = productGetOptionList('buttons');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['buttons']]);
?>
            </td>
        </tr>
        <tr<?=in_array($reference['controller_type'], array(4)) ? '' : ' style="display:none;"'?>>
            <th>Type Of Steering :</th>
            <td>
<?php
$temp    = productGetOptionList('steering');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['steering']]);
?>
            </td>
        </tr>
        <tr<?=in_array($reference['controller_type'], array(4)) ? '' : ' style="display:none;"'?>>
            <th>Shifter :</th>
            <td>
<?php
$temp    = productGetOptionList('shifter');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['shifter']]);
?>
            </td>
        </tr>
        <tr id="about_pedal_configuration"<?=in_array($reference['controller_type'], array(4)) ? '' : ' style="display:none;"'?>>
            <th>Pedal Configuration :</th>
            <td>
<?php
$temp    = productGetOptionList('pedal_configuration');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['pedal_configuration']]);
?>
            </td>
        </tr>
        <tr id="about_gun_type"<?=in_array($reference['controller_type'], array(2)) ? '' : ' style="display:none;"'?>>
            <th>Force Feedback :</th>
            <td>
<?php
$temp    = productGetOptionList('gun_type');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['gun_type']]);
?>
            </td>
        </tr>
        <tr id="about_other"<?=in_array($reference['controller_type'], array(1)) ? '' : ' style="display:none;"'?>>
            <th>Other Type :</th>
            <td><?php hecho($reference['controller_type_other'])?></td>
        </tr>
        <tr id="about_screen_size">
            <th>Screen Size :</th>
            <td>
<?php
$temp    = productGetOptionList('screen_size');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['screen_size']]);
?>
            </td>
        </tr>
        <tr id="about_jamma_wiring">
            <th>Jamma Wiring :</th>
            <td>
<?php
$temp     = productGetOptionList('jamma_wiring');
        $temp[''] = 'Not Selected';
        hecho($temp[$reference['jamma_wiring']]);
?>
            </td>
        </tr>
        <tr id="about_screen_orientation">
            <th>Screen Orientation :</th>
            <td>
<?php
$temp    = productGetOptionList('screen_orientation');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['screen_orientation']]);
?>
            </td>
        </tr>
        <tr id="about_screen_resolution">
            <th>Screen Resolution :</th>
            <td>
<?php
$temp    = productGetOptionList('screen_resolution');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['screen_resolution']]);
?>
            </td>
        </tr>
        <tr id="about_linking">
            <th>Linking :</th>
            <td>
                <?=('y' == $reference['linking']) ? ('Yes - ' . $reference['num_linking']) : ''?>
                <?=('n' == $reference['linking']) ? 'No' : ''?>
                <?=(in_array($reference['linking'], array('y', 'n'))) ? '' : 'Not Selected'?>
            </td>
        </tr>
        <tr id="about_game_play">
            <th>Game Play :</th>
            <td>
<?php
$temp    = productGetOptionList('game_play');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['game_play']]);
?>
            </td>
        </tr>
        <tr id="about_no_players">
            <th>No. Of Players :</th>
            <td>
<?php
$temp    = productGetOptionList('no_players');
        $temp[0] = 'Not Selected';
        hecho($temp[$reference['no_players']]);
?>
            </td>
        </tr>
        <tr>
            <th>Manufacturer :</th>
            <td>
<?php
//$temp=productGetOptionList('manufacturer');
        $manufacturer_list = productGetOptionList('manufacturer');
        $manufacturer_list[0] = 'None';
        hecho($manufacturer_list[$reference['manufacturer']]);
?>
            </td>
        </tr>
        <tr>
            <th>Date Product Released :</th>
            <td>
<?php
$list = explode(',', date('jS,F,Y', strtotime($reference['date_available'])));
?>
                <?=('1st' != $list[0]) ? $list[0] : ''?>
                <?=('1st' != $list[0] || 'January' != $list[1]) ? ($list[1] . ',') : ''?>
                <?=('1970' != $list[2]) ? $list[2] : 'Not Selected'?>
            </td>
        </tr>
    </table>
    <br />

    <div style="text-align:center;">
        <input type="button" value="HG Reference" onclick="window.open('https://www.highwaygames.com/webadmin/products/?action=edit&amp;product_id=<?=$reference['id']?>');" />
    </div>
    <br /><br />

</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionReference_hideNote">
    Product reference can be viewed by Clicking "Product Reference" above<br />
</div>

<?php
}
}
?>

<!-- /_product_reference.tpl -->
