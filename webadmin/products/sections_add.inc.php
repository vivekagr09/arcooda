<script type="text/javascript">
//<![CDATA[
function doDataDirectory() {
    var index = document.forms["admin_form"].elements["data[parent]"].selectedIndex;
    document.forms["admin_form"].elements["data[img_location]"].value = "img_" + document.forms["admin_form"].elements["data[parent]"][index].text;
}

//]]>
</script>
<script>
function updateTags()
{
    setValue("data[meta_title]", getValue("data[name]"));
    setValue("data[meta_keywords]", formatMETA(getValue("data[name]") + ", " + getValue("data[description]"), "keyword"));
    setValue("data[meta_description]", getValue("data[description]"));
    setValue("data[url_keywords]", getValue("data[name]").split(/[\x00-\x20\x80-\xff]+/).join("-"));
}
function clearTags()
{
    setValue("data[meta_title]", "");
    setValue("data[meta_keywords]", "");
    setValue("data[meta_description]", "");
    setValue("data[url_keywords]", "");
}
</script>
<table class="table_fields">
    <tr>
        <th>Parent Type :</th>
        <td><select name="data[parent]" class="Wide" onChange="doDataDirectory()">
            <option value="0">--- None --</option>
            <?php show_sections_options(0, '&nbsp;&nbsp;&nbsp;', 0, -1,''); ?>
        </select></td>
    </tr>
    <tr>
        <th>New Type :</th>
        <td><input type="text" name="data[name]" size="20" class="wide" /></td>
    </tr>
    <tr>
        <th>Description :</th>
        <td><textarea name="data[description]" cols="20" rows="3" class="wide"></textarea></td>
    </tr>
    <tr>
        <th >Url Slug:</th>
        <td ><input type="text" name="data[slug]"  size="20" class="wide"  required></td>
    </tr>
</table>

<br />

<table class="table_fields">
    <tr>
        <th>Data Directory :</th>
        <td><input type="text" name="data[img_location]" size="20" class="wide" /></td>
    </tr>
    <tr>
        <th>Header Image :</th>
        <td>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
            <input type="file" name="images[header_image]" size="20" class="wide" />
        </td>
    </tr>
    <tr>
        <th>Icon Image :</th>
        <td>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
            <input type="file" name="images[icon_image]" size="20" class="wide" />
        </td>
    </tr>
    </table>

<br />

<table class="table_fields">
    <tr>
        <th>Ordering :</th>
        <td><input type="number" name="data[ordering]" size="20" class="wide" /></td>
    </tr>
    <tr>
        <th>Active :</th>
        <td><select name="data[active]" class="wide">
            <option value='y'>Yes</option>
            <option value='n'>No</option>
        </select></td>
    </tr>
</table>
    <!-- Under development Kelvin -->
<table class="table_fields">
    <tr><td colspan="2" nowrap="nowrap" class="centre"><b>Metatags</b></td></tr>
    <tr>
        <th ><b>Title:</b></th>
        <td><input type="text" name="data[meta_title]" size="20" class="wide" value="<?php echo ($item['meta_title'])?$item['meta_title']:$item['name']; ?>" /></td>
    </tr>
    <tr>
        <th class="left"><b>Keywords:</b></th>
        <td class="right"><input type="text" name="data[meta_keywords]" size="20" class="wide" value="<?php echo ($item['meta_keywords'])?$item['meta_keywords']:$item['description']; ?>" /></td>
    </tr>
    <tr>
        <th class="left"><b>Meta Description:</b></th>
        <td class="right"><textarea name="data[meta_description]" cols="20" rows="3" class="wide"><?php echo ($item['meta_description'])?$item['meta_description']:$item['description']; ?></textarea></td>
    </tr>
    <tr>
        <th class="left"><b>Url Keywords:</b></th>
        <td class="right"><input type="text" name="data[url_keywords]" size="20" class="wide" value="<?php echo ($item['url_keywords'])?$item['url_keywords']:$item['meta_keywords']; ?>" /></td>
    </tr>
    <tr><td colspan="2" class="top">
    <br />
    <div style="margin:auto;text-align:center;">
        <input type="button" value="Auto-Update Tags" onclick="updateTags()" />
        <input type="button" value="Clear Tags" onclick="clearTags()" />
    </div>
    </td></tr>
    <!-- Under development Kelvin -->
</table>
<br />

<div style="text-align:center;">
    <input type="hidden" name="action" value="do_add" />
    <input type="submit" name="add" value="Add" />
    <input type="reset" name="clear" value="Clear" />
</div>
