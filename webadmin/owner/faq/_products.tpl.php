<!-- videos.tpl -->
<style type="text/css">
    .flagHeader{
        width: 90%;
    }
</style>
<?php
function item_product_row($count = "[ROW_COUNT]", $row_data = array())
{
    static $alt = false;
    ob_start();
?>
    <tr id="item_product_rowa[<?=$count?>]"<?=(($alt = !$alt) ? ' class="alt"' : "")?>>

        <td style="white-space:nowrap;">
            <img src="<?= $row_data['image_url'] ?>?width=70" width=70>
        </td>
        <td style="padding:5px 10px;">
          <a href="/webadmin/products_new/edit.php?action=edit&id=<?= $row_data['id'] ?>"><?= $row_data['id'] ?></a>
            
        </td>
        <td style="text-align:center;">
           <a href="<?= $row_data['product_url'] ?>" target="_blank"><?= $row_data['product_name'] ?></a>
        </td>
        <td style="text-align:center" width="20%"><?= $row_data['product_active'] == 'y' ? 'Actove': 'InActive'?></td>
    </tr>
<?php
return ob_get_clean();
}

if (is_array($productListQuesions)) {
?>


<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionVideos\').add(\'#sectionVideos_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Questions")) . "</a>") ?>

<div id="sectionVideos">
    <table class="grid-view"  style="width:90%">
        <tr>
            <th></th>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
        </tr>
<?php
    $count    = 0;
    $affected = array();
    foreach ($productListQuesions as $product) {
        if(empty($product)){
            continue;
        }
        $pro=productFromDatabase($product);
        echo item_product_row($count++, $pro);
    }
?>
    </table>
    <br /><br />

</div>
<div id="sectionVideos_hideNote" style="background:#fff;text-align:center;padding:8px;display:none;">
    <?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Videos"), _("Videos")))?><br />
</div>

<?php
}
?>

<!-- /videos.tpl -->
