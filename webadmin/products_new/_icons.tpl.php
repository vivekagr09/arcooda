<!-- _icons.tpl -->

<?php
/**
 * Product Icons template
 *
 * Inputs:
 * - $item
 * - - icons
 *
 * Overwrites:
 * - $temp
 * - $temp2
 */

if (!isset($item) || !is_array($item)) {
    return;
}

?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionIcons&quot;);return false">Icons</a>')?>

<div id="sectionIcons">
    <table class="table_fields"><tr>
        <th>Icons</th>
        <td>
            <input type="hidden" name="item[icons]" value="" />
            <ul style="list-style:none;">
<?php
$temp2 = explode(',', $item['icons']);
foreach (productGetOptionList('icons') as $temp) {
    //TODO: Remove the basename once the database is fixed
?>
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <img style="max-width:32px;max-height:32px;" src="/webadmin/assets/icon/image/<?=basename($temp['img'])?>" onmouseover="tt_in('<h5><?=$temp['name']?><h5><h6><?=$temp['text']?></h6>');" onmouseout="tt_out();"/>
                    <br />
                    <input type="checkbox" name="item[icons][]" value="<?=$temp['id']?>" <?=(in_array($temp['id'], $temp2)) ? 'checked' : ''?> />
                </li>
<?php
}
unset($temp);
unset($temp2);
?>
            </ul>
        </td>
    </tr></table>
    <br />
    <br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionIcons_hideNote">
    Icons can be viewed by Clicking "Icons" above<br />
</div>

<!-- /_icons.tpl -->
