<!-- status.tpl -->
<?php
function item_media_type_row($count = '[ROW_COUNT]', $item_content = array())
{ 
    ob_start();
    ?>
    <tr id="media_type_row_<?= $count ?>">
        <td style="width: 20%";>
            <select name="mediatype[<?=$count?>][media_type]" class="wide">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(socialMediaPartnerGetOptionList('media_type'), $item_content['media_type'])?>
            </select>
        </td>
        <th style="width: 15%;">Members :</th>
        <td style="width: 20%;">
            <input type="text" name="mediatype[<?=$count?>][member_count]" value="<?= $item_content['member_count'] ?>" class="wide">
        </td>
        <th style="width: 15%;">Media Link :</th>
        <td style="width: 30%;">
             <div><input type="text" name="mediatype[<?=$count?>][media_links]"style="width:60%;margin-bottom:5px" value="<?= $item_content['media_links'] ?>"> <a href="javascript:void(0);"  class="view_link">View</a>
             <a href="javascript:void(0);"  onclick="deleteMedia(<?= $count ?>)">Delete</a>
             </div>
        </td>
    </tr>
<?php 
return ob_get_clean();
}
?>
<?=formatHTMLTitle(
    '<a href="#" onclick="toggleSection(\'sectionDistributorType\');return false">Media Type</a>',
    ''//'<a style="color:black;" href=".?action=edit&amp;id=' . $id . '" target="_blank">Distributor Page Link</a>'
)?>

<div id="sectionDistributorType">
    <table class="table_fields">
        <tr>
            <th>Country :</th>
            <td><select name="item[country_location]" class="wide">
                <option value="">Not Selected</option>
                <?=formatHTMLOptions(socialMediaPartnerGetOptionList('country_location'), $item['country_location'])?>
            </select></td>
        </tr>
        <tr>
            <th>Category :</th>
            <td style="padding-top:10px;padding-bottom:10px">
<?php 
$category_selected = json_decode($item['category']) ?? [];
foreach (socialMediaPartnerGetOptionList('category') as $k => $v) { 
?>
                <div><input type="checkbox" id="item_category_<?=$k?>" name="item[category][]" value="<?=$k?>"<?=in_array($k, $category_selected) ? ' checked="checked"' : ''?> /><label for="item_category_<?=$k?>"><?=htmlspecialchars($v)?></label></div>
<?php
}
?>
            </td>
        </tr>
        <tr>
            <th><a href="javascript:void(0);" onclick="itemMediaAdd()">Add New</a> Media Type :</th>
            <td>
                <table class="table_fields" id="social_media_link" style="width: 100%;">
                     <?php 
                        $countKey = 0;
                        foreach ($media_type_links as $key => $mediaType) {
                            echo item_media_type_row($countKey,$mediaType);
                            $countKey++;
                        }
                     ?>       
                </table>
            </td>
        </tr>
    </table>
    <br />
    <br />
</div>
<script>
var medialink = 100;
function itemMediaAdd()
{
    $("#social_media_link").append(<?=strtr(json_encode(item_media_type_row()), ["[ROW_COUNT]" => '" + medialink + "'])?>);
    contacts++;
}
// Event delegation for dynamically added "View" links
document.getElementById('social_media_link').addEventListener('click', function(event) {
    if (event.target.classList.contains('view_link')) {
        let inputField = event.target.previousElementSibling;
        let url = inputField.value.trim();

        if (url) {
            if (!url.startsWith('http://') && !url.startsWith('https://')) {
                url = 'https://' + url; // Ensure valid URL
            }
            window.open(url, '_blank');
        } else {
            alert('Please enter a URL.');
        }
    }
   
});
function deleteMedia(mediaId) {
    if (confirm("Are you sure you want to delete this item?")) {
        let row = document.getElementById('media_type_row_'+mediaId);
        if (row) {
            row.remove();
        }
    }
}

</script>
<!-- /__distributor_type.tpl -->
