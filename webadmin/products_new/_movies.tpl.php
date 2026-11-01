<!-- videos.tpl -->

<?php
function item_videos_row($count = "[ROW_COUNT]", $row_data = array())
{
    static $alt = false;

    $section_name = 'productlink';
    $name_key = $section_name . "[videos][" . $count . "]";
    $hidden   = array(
        $name_key . "[id]"   => $row_data["id"],
        //$name_key . "[product_id]" => $row_data["product_id"],
        $name_key . "[rank]" => $row_data["rank"],
        //$name_key . "[name]" => $row_data["name"],
        //$name_key . "[youtube_id]" => $row_data["youtube_id"],
        //$name_key . "[active]" => $row_data["active"],
        //$name_key . "[created]" => $row_data["created"],
        //$name_key . "[modified]" => $row_data["modified"],
    );
    $active_list = array("y" => "Yes", "n" => "No");
    ob_start();
?>
    <tr id="item_videos_rowa[<?=$count?>]"<?=(($alt = !$alt) ? ' class="alt"' : "")?>>
        <td style="white-space:nowrap;">
            <a href="#" onclick="item_videos_move('<?=$count?>',-1,1);return false"><img src="/webadmin/images/up_arrow.gif" alt="&uarr;" border="0" /></a>
            <a href="#" onclick="item_videos_move('<?=$count?>',1,0);return false"><img src="/webadmin/images/down_arrow.gif" alt="&darr;" border="0" /></a>
        </td>
        <td style="white-space:nowrap;">
            <?=formatHTMLInputs($hidden)?>
            <input class="wide" type="text" name="<?=htmlspecialchars($name_key . "[name]")?>" value="<?=htmlspecialchars($row_data["name"])?>" />
        </td>
        <td style="white-space:nowrap;">
            <input class="wide" type="text" name="<?=htmlspecialchars($name_key . "[youtube_id]")?>" value="<?=htmlspecialchars($row_data["youtube_id"])?>" />
        </td>
        <td style="white-space:nowrap;">
            <select class="wide" name="<?=htmlspecialchars($name_key . "[active]")?>"><?=formatHTMLOptions($active_list, $row_data["active"])?></select>
        </td>
        <td style="white-space:nowrap;">
            <select class="wide" name="<?=htmlspecialchars($name_key . "[members_only]")?>"><?=formatHTMLOptions($active_list, $row_data["members_only"])?></select>
        </td>
        <td style="white-space:nowrap;text-align:center;">
<?php
if (0 != $row_data["created_time"]) {
?>
        <input type="hidden" name="<?=htmlspecialchars($name_key . "[created]")?>" value="<?=htmlspecialchars($row_data["created"])?>" />
        <input type="hidden" name="<?=htmlspecialchars($name_key . "[modified]")?>" value="<?=htmlspecialchars($row_data["modified"])?>" />
        <?=date('jS M, Y', $row_data['created_time'])?>
<?php
} else {
?>
        Now
<?php
}
?>
        </td>
        <td><a href="#" onclick="return item_videos_delete(<?=$count?>)"><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0" /></a></td>
    </tr>
<?php
return ob_get_clean();
}

if (is_array($item_videos)) {
?>

<script type="text/javascript">
//<![CDATA[
var item_videos_count = <?=json_encode(count($item_videos) + 256)?>;
function item_videos_add()
{
    var row_string = <?=json_encode(item_videos_row())?>;
    row_string = row_string.replace(/\[ROW_COUNT\]/g, item_videos_count);
    $("#item_videos_table").append(row_string);
    var count = 0;
    $("[id^='item_videos_rowa[']").each(function() {
        $("[name$='[rank]']",this).val(count++);
    });
    $("[id^='item_videos_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    item_videos_count++;
}
function item_videos_move(row_id, direction, limit)
{
    var $the_row = $("[id='item_videos_rowa["+row_id+"]']");
    var $siblings = $the_row.parent().children();
    var position = 0;
    for (var i = 0; i < $siblings.length; i++) if ($siblings[i].id == $the_row[0].id) position = i;
    if (direction < 0 && position + direction >= limit)
    {
        $the_row.insertBefore($siblings.eq(position + direction));
    }
    else if (direction > 0 && position + direction <= $siblings.length - limit)
    {
        $the_row.insertAfter($siblings.eq(position + direction));
    }
    var count = 0;
    $("[id^='item_videos_rowa']").each(function() {
        $("[name$='[rank]']", this).val(count++);
    });
    $("[id^='item_videos_rowa']").removeClass("alt").filter(":odd").addClass("alt");
}
function item_videos_delete(row_id)
{
    if(confirm("Are you sure you want to delete this video ??"))
    {
        $("[id='item_videos_rowa["+row_id+"]']").remove();
        $("[id^='item_videos_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    }
    return false;
}
function item_videos_sort(column)
{
    var rows = [];
    var vals = [];
    $("[id^='item_videos_rowa[']").each(function() {
        var row_id = $(this).attr("id").substr(16);
        rows.push(row_id);
        var val = $("[name$='[" + column + "]']", this).text();
        if (val == "") val = $("[name$='[" + column + "]']", this).val();
        vals.push(val);
    });
    var reverse = true;
    var bubble = true;
    while(bubble)
    {
        bubble = false;
        for(var i = 0;i < (rows.length - 1);i++) for(var j = i + 1;j < rows.length;j++) if (vals[i] > vals[j])
        {
            temp = rows[i]; rows[i] = rows[j]; rows[j] = temp;
            temp = vals[i]; vals[i] = vals[j]; vals[j] = temp;
            bubble = true;
            reverse = false;
        }
    }
    if (reverse) rows = rows.reverse();
    console.log(rows);
    var table = $("#item_videos_table");
    for(var i = 0;i < rows.length;i++)
    {
        table.append($("[id='item_videos_rowa" + rows[i] + "']"));
    }
    var count = 0;
    $("[id^='item_videos_rowa[']").each(function() {
        $("[name$='[rank]']",this).val(count++);
    });
    $("[id^='item_videos_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    return false;
}
$(function() {
    $('.item_videos_add_button').css('display','').click(function() {
        item_videos_add();
        return false;
    });
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionVideos\').add(\'#sectionVideos_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Videos")) . "</a>", '<a href="#" class="item_videos_add_button" style="display:none;">Add Video</a>')?>

<div id="sectionVideos">
    <table class="table_grid" id="item_videos_table">
        <tr>
            <th></th>
            <th><a href="#" onclick="return item_videos_sort('name')">Title of Movie</a></th>
            <th><a href="#" onclick="return item_videos_sort('youtube_id')">Youtube Link</a></th>
            <th>Active</th>
            <th>Owners</th>
            <th>Date Added</th>
            <th></th>
        </tr>
<?php
$count    = 0;
    $affected = array();
    foreach ($item_videos as $videos) {
        $affected[] = $videos['id'];
        echo item_videos_row($count++, $videos);
    }
?>
    </table>
    <input type="hidden" name="<?=htmlspecialchars($section_name)?>[videos][affected]" value="<?=implode(',', $affected)?>" />
    <br /><br />

</div>
<div id="sectionVideos_hideNote" style="background:#fff;text-align:center;padding:8px;display:none;">
    <?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Videos"), _("Videos")))?><br />
</div>

<?php
}
?>

<!-- /videos.tpl -->
