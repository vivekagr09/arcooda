<!-- __status.tpl -->

<!-- TODO: Split this template file here -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionStatus\');return false">Status</a>')?>

<div id="sectionStatus">
    <table class="table_fields">
        <tr>
            <th>Default Warehouse :</th>
            <td><input type="checkbox"  id="nda_signed" name="item[default_warehouse]" value="y" <?=('y' == $item['default_warehouse']) ? 'checked' : ''?>>
            </td>
        </tr>
        <tr style="line-height: 50px;">
            <th>Status :</th>
            <td><select class="wide" id="active" name="item[active]" onchange="<?=htmlspecialchars('this.style.backgroundColor = ("y" == this.value) ? "#0f0" : "#f00"')?>" style="background:<?=('y' == $item['active']) ? '#0f0' : '#f00'?>">
                <?=formatHTMLOptions(wareHouseGetOptionList('active'), $item['active']) ?>
            </select>
            </td>
        </tr>
</table>
    <br />
    <br />
</div>

<div style="background:#fff;text-align:center;padding:8px;display:none" id="sectionStatus_hideNote">Status can be viewed by Clicking "Status" above</div>

<!-- /__status.tpl -->
<script type="text/javascript">
$(function() {
    $("[name='item[last_connected]']").change(function() {
        if ($(this).is(':checked')) {
            $("#summary_display_to_customere").text("<?= date('d/m/Y')?>");
        } else {
            $("#summary_display_to_customere").text('');
        }  
    });
});
</script>
