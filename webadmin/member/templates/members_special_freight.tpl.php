<!-- status.tpl -->

<script type="text/javascript">
//<![CDATA[
$(function() {
    $("[name='member[site_id]']").change(function() {
        if ($(this).val() == '3') $(this).css('background', '#0f0').css('color', '#000');
        else $(this).css('background', '#f00').css('color', '#fff');
    }).change();
})
//]]>
</script>

<?=formatHTMLTitle('Special Freight Needs')?>

<table class="table_fields">
    <tr>
        <th>Special freight needs :</th>
        <td><input type="checkbox"  name="member[special_freight_needs]" value="y" <?=('y' == $member['special_freight_needs']) ? 'checked' : ''?> />  <span>(if this box is ticked, the below text will be used as default for all orders)</span></td>
    </tr>
    <tr>
        <th>Default Freights Needs/Notes :</th>
        <td><textarea name="member[special_request]"  class="form-control"  rows="5" placeholder="Type any special requests here"><?=$member['special_request']?></textarea></td>
    </tr>
</table>
<br /><br />

<!-- /status.tpl -->
