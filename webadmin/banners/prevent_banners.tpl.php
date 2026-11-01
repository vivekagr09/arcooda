<?php
/**
 * @var mixed[] $item
 */
?>
<!-- prevent_banners.tpl -->

<?php
$prevent_banners = array();
if ('' != $item['prevent_banners']) {
    $prevent_banners = explode(',', $item['prevent_banners']);
}
$chunks = bannerChunk(bannerSearch(), 'prevent_banners');
if (is_array($chunks[$item['id']])) {
    $prevent_banners = array_merge($prevent_banners, $chunks[$item['id']]);
}
$prevent_banners = array_unique($prevent_banners);
rsort($prevent_banners);

/**
 * @param mixed   $count
 * @param mixed[] $row_data
 *
 * @return string|false
 */
function prevent_banners_row($count = '[ROW_COUNT]', $row_data = array())
{
    static $alt = false;

    $name_key = 'prevent_banners[' . $count . ']';
    $hidden   = array($name_key . '[id]' => $row_data['id']);
    ob_start();
?>
<tr id="prevent_banners_rowa[<?=$count?>]"<?=(($alt = !$alt) ? ' class="alt"' : '') . (($row_data['hidden']) ? ' style="display:none;"' : '')?>>
  <td style="white-space:nowrap;">
    <?=formatHTMLInputs($hidden)?>
    <a target="_blank" href="<?=htmlspecialchars(".?action=edit&id=" . $row_data['id'])?>" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode('<img src="' . htmlspecialchars($row_data['image_url']) . '" alt="' . htmlspecialchars($row_data['text']) . '" />'))?>)" onmouseout="tt_out()"><span name="<?=$name_key?>[banner_number]"><?=bannerFormatNumber($row_data['id'])?></span></a>
  </td>
  <td style="width:100%;"><a target="_blank" href="<?=htmlspecialchars(".?action=edit&id=" . $row_data['id'])?>" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode('<img src="' . htmlspecialchars($row_data['image_url']) . '" alt="' . htmlspecialchars($row_data['text']) . '" />'))?>)" onmouseout="tt_out()"><span name="<?=$name_key?>[text]"><?=htmlspecialchars($row_data['text'])?></span></a></td>
  <td><a href="#" onclick="return preventBannersDelete(<?=$count?>)"><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0" /></a></td>
</tr>
<?php
    return ob_get_clean();
}
if (array_key_exists('prevent_banners', $item)) {
?>
<script type="text/javascript">
//<![CDATA[
var prevent_banners_count=<?=count($prevent_banners) + 256?>;
function setBannerData(data) {
    if (data.banner_list != null) {
        for (k in data.banner_list) setBannerData(data.banner_list[k]);
        return;
    }
    var row_id = preventBannersAddRow();
    preventBannersSetRow(row_id, data);
}
function preventBannersSort(column) {
    var rows = [];
    var vals = [];
    $("[id^='prevent_banners_rowa[']").each(function () {
        var row_id = $(this).attr("id").substr(20);
        rows.push(row_id);
        vals.push($("[name='prevent_banners" + row_id + "[" + column + "]']").text());
    });
    var reverse = true;
    var bubble = true;
    while (bubble) {
        bubble = false;
        for (var i = 0; i < (rows.length - 1); i++) for (var j = i + 1; j < rows.length; j++) if (vals[i] > vals[j]) {
            temp = rows[i]; rows[i] = rows[j]; rows[j] = temp;
            temp = vals[i]; vals[i] = vals[j]; vals[j] = temp;
            bubble = true;
            reverse = false;
        }
    }
    if (reverse) rows = rows.reverse();
    var table = $("#prevent_banners_table");
    for (var i = 0; i < rows.length; i++) {
        table.append($("[id='prevent_banners_rowa" + rows[i] + "']"));
    }
    var count = 0;
    $("[id^='prevent_banners_rowa[']").each(function () {
        $("[name$='[rank]']", this).val(count++);
    });
    $("[id^='prevent_banners_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    return false;
}
function preventBannersDelete(row_id) {
    $("[id='prevent_banners_rowa[" + row_id + "]']").remove();
    $("[id^='prevent_banners_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    return false;
}
function preventBannersAddRow() {
    var row_string = <?=json_encode(prevent_banners_row())?>;
    row_string = row_string.replace(/\[ROW_COUNT\]/g, prevent_banners_count);
    $("#prevent_banners_table").append(row_string);
    $("[id^='prevent_banners_rowa']").removeClass("alt").filter(":odd").addClass("alt");
    prevent_banners_count++;
    return prevent_banners_count - 1;
}
function preventBannersSetRow(row_id, data) {
    $("[name='prevent_banners[" + row_id + "][id]']").val(data.id);
    $("[name='prevent_banners[" + row_id + "][banner_number]']").text(data.id).unwrap().wrap('<a target="_blank" href=".?action=edit&id=' + data.id + '" />');
    $("[name='prevent_banners[" + row_id + "][text]']").text(data.name).unwrap().wrap('<a target="_blank" href=".?action=edit&id=' + data.id + '" />');
}

$(function () {
    $.getScript('/jquery/selectboxes&skipdeps.js');
    var list_storage = {};
    var list_schedule = [];
    processSchedule = function () {
        for (i = 0; i < list_schedule.length; i++) {
            var j = list_schedule[i];
            if (list_storage[j.rel] !== null) {
                var save_vals = $(j.target).val();
                $(j.target).empty();
                $(j.target).addOption('', '');
                $(j.target).addOption(list_storage[j.rel]);
                $(j.target).val(save_vals);
                list_schedule.splice(i, 1);
                i--;
            }
        }
    };
    $("select.on_demand").focus(function (event) {
        $(this).unbind("focus");
        var list_name = $(this).attr('rel');
        list_schedule.push({ target: this, rel: list_name });
        if (list_storage[list_name] == null) $.getJSON('getoptionlist.json.php', { list: escape(list_name) }, function (data) {
            list_storage[list_name] = data;
            processSchedule();
        });
        else processSchedule();
    });
    $('.prevent_banners_add_button').css('display', '').click(function () {
        var selected_list = [];
        $("[id^='prevent_banners_rowa[']").each(function () {
            var row_id = $(this).attr("id").substr(20);
            selected_list.push($("[name='prevent_banners" + row_id + "[id]']").val() + " " + $("[name='prevent_banners" + row_id + "[sub_id]']").val());
        });
        window.open(".?return_message=prevent_banners&multiple=y&selected_list=" + selected_list.join("|"));
        return false;
    });
});
//]]>
</script>

<?=formatHTMLTitle('Prevent these banners from appearing on the same page', '<a href="#" class="prevent_banners_add_button" style="display:none;color:#000;">Add Banner(s)</a>')?>

<div id="sectionPreventBanners">
  <table class="table_grid" id="prevent_banners_table">
    <tr>
      <th><a href="#" onclick="return preventBannersSort('id')">ID</a></th>
      <th><a href="#" onclick="return preventBannersSort('text')">Name of Banner</a></th>
      <th></th>
    </tr>
<?php
    $count                    = 0;
    $prevent_banners_affected = array();

    foreach ($prevent_banners as $prevent_banner) {
        $prevent_banners_affected[] = $prevent_banner;
        $prevent_banner             = bannerFromDatabase($prevent_banner);
        echo prevent_banners_row($count++, $prevent_banner);
    }
?>
  </table>
  <input type="hidden" name="prevent_banners_affected" value="<?=implode(',', $prevent_banners_affected)?>" />
  <br />
</div>

<br />
<br />

<?php
}
?>

<!-- /prevent_banners.tpl -->
