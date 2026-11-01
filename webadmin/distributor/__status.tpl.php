<!-- __status.tpl -->

<script type="text/javascript">
//<![CDATA[
$(function() {
     $("[name='item_image']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_image_img").attr("src", e.target.result);
            $("#item_image_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionCompanyLogo\');return false">Company Logo</a>')?>

<div id="sectionCompanyLogo">
    <table class="table_fields">
        <tr>
            <th>Upload New Logo :</th>
            <td>
                <input style="width:272px" name="item_image" type="file" accept="image/*" />
                <a href=".?action=item_image&amp;id=<?=$id?>" target="_blank" id="item_image_a"><img id="item_image_img" src=".?action=item_image&amp;thumb=true&amp;id=<?=$id?>" style="vertical-align:middle;max-width:96px;max-height:96px" /></a>
            </td>
        </tr>
    </table>
    <br />
    <br />
</div>

<div style="background:#fff;text-align:center;padding:8px;display:none" id="sectionCompanyLogo_hideNote">Company Logo can be viewed by Clicking "Company Logo" above</div>

<!-- TODO: Split this template file here -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionStatus\');return false">Status</a>')?>

<div id="sectionStatus">
    <table class="table_fields"><tr>
        <th>Status :</th>
        <td><select class="wide" id="active" name="item[active]" onchange="<?=htmlspecialchars('this.style.backgroundColor = ("y" == this.value) ? "#0f0" : "#f00"')?>" style="background:<?=('y' == $item['active']) ? '#0f0' : '#f00'?>">
            <?=formatHTMLOptions(distributorGetOptionList('active'), $item['active']) ?>
        </select></td>
    </tr></table>
    <br />
    <br />
</div>

<div style="background:#fff;text-align:center;padding:8px;display:none" id="sectionStatus_hideNote">Status can be viewed by Clicking "Status" above</div>

<!-- /__status.tpl -->
