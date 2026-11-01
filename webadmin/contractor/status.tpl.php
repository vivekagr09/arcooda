<!-- status.tpl -->

<script type="text/javascript">
//<![CDATA[
$(function() {
    $("[name='contractor[status]']").change(function() {
        if ($(this).val() == '1') $(this).css('background', '#0f0').css('color', '#000');
        else $(this).css('background', '#f00').css('color', '#fff');
    }).change();
})
//]]>
</script>

<?= formatHTMLTitle('Status') ?>

<table class="table_fields">
    <tr>
        <th>Status :</th>
        <td><select name="contractor[status]">
            <?= formatHTMLOptions(array('1' => 'Active', '0' => 'Not Active'), $contractor['status']) ?>
        </select></td>
    </tr>
</table>
<br /><br />

<!-- /status.tpl -->
