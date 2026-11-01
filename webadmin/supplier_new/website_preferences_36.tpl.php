<!-- website_preferences_36.tpl -->

<?=formatHTMLTitle('Highway Gaming Preferences')?>

<table class="table_fields"><tr>
    <th>Registered Products :</th>
    <td style="width:75%;">
<?php
if ($DB->query_first("SHOW DATABASES LIKE 'hwgamin_hwgamin'", MYSQLI_ASSOC)) {
    $split = explode(',', $member['hwgaming_products']);
    $query = $DB->query("SELECT `id`, `name` FROM `hwgamin_hwgamin`.`products` ORDER BY `name` ASC");
    while ($row = $DB->fetch_assoc($query)) {
?>
        <div style="display:inline-block;min-width:320px;">
            <input type="checkbox" name="hwgaming_products[]" value="<?=$row['id']?>"<?=(in_array($row['id'], $split)) ? ' checked="checked"' : ''?> style="width:auto;" />
            <?php hecho($row['name'])?>
        </div>
<?php
}
$DB->free_result($query);
?>
        <input type="hidden" name="hwgaming_products_update" value="on" />
<?php
} else {
?>
        Please use Highway Group webadmin to edit
<?php
}
?>
    </td>
</tr></table>

<!-- /website_preferences_36.tpl -->
