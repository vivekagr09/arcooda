<!-- status.tpl -->
<script type="text/javascript">
//<![CDATA[
$(function() {
     $("[name='map_image']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_map_image_img").attr("src", e.target.result);
            $("#item_image__map_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
});
//]]>
</script>
<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(\'sectionDistributorMap\');return false">Distributor Map</a>',
    ''//'<a style="color:black;" href=".?action=edit&amp;id=' . $id . '" target="_blank">Distributor Page Link</a>'
)?>

<div id="sectionDistributorMap">
    <table class="table_fields">
        <tr>
            <th>Direct Map Link :</th>
            <td> https://www.arcooda.com/mapviewer/index.php?code= <input
                    type="text"
                    id="map_link"
                    name="item[map_link]"
                    value="<?= $item['map_link'] ?>"
                    class="wide"
                    style="width: 40%"
                />
            </td>
        </tr>
          <tr>
            <th>Upload New Logo :</th>
            <td>
                <input style="width:272px" name="map_image" type="file" accept="image/*" />
                <a href=".?action=map_image&amp;id=<?=$id?>&type=map_image" target="_blank" id="item_image__map_a">
                    <img id="item_map_image_img" src=".?action=map_image&amp;thumb=true&amp;id=<?=$id?>&type=map_image" style="vertical-align:middle;max-width:96px;max-height:96px" /></a>
            </td>
        </tr>
    </table>
    <br />
    <br />
</div>

<!-- /__distributor_type.tpl -->
