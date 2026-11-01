<?php
/**
 * @var int     $id
 * @var mixed[] $item
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<script type="text/javascript">
//<![CDATA[
function updateTotals()
{
    var title    = getValue("item[meta_title]");
    var meta     = getValue("item[meta_keys]");
    var desc     = getValue("item[meta_desc]");
    var url_keys = getValue("item[url_keys]");

    //now update the length of the words
    var keywords = meta.split(",");
    var len      = 0;
    for (i = 0;i < keywords.length;i++) {
        if (keywords[i] != "") {
            len++;
		}
    }

	getElement("meta_title").innerHTML = title.length;
    getElement("meta_keys").innerHTML  = len;
    getElement("meta_desc").innerHTML  = desc.length;
    getElement("url_keys").innerHTML   = url_keys.length;
}
function updateTags()
{
    setValue("item[meta_title]", getValue("item[name]"));
    setValue("item[meta_keys]", formatMETA(getValue("item[name]") + ", " + getValue("item[short_desc]"), "keyword"));
    setValue("item[meta_desc]", getValue("item[long_desc]"));
    setValue("item[url_keys]", getValue("item[name]").split(/[\x00-\x20\x80-\xff]+/).join("-"));
    updateTotals();
}
function clearTags()
{
    setValue("item[meta_title]", "");
    setValue("item[meta_keys]", "");
    setValue("item[meta_desc]", "");
    setValue("item[url_keys]", "");
    updateTotals();
}
//]]>
</script>

<?php
$total_title = strlen($item['meta_title']);
$total_desc  = strlen($item['meta_desc']);
$total_keys  = 0;
foreach (explode(',', $item['meta_keys']) as $key => $value) {
    if (trim($value) != '') {
        $total_keys++;
    }
}
?>

<?= formatHTMLTitle('META Tags') ?>

<div>
	<table class="table_fields">
		<tr>
			<th><a href="#" onclick="open_win('templates/help.php?action=title');return false" style="color:blue">Title :</a></td>
			<td><input type="text" name="item[meta_title]" style="width:99%" value="<?=htmlspecialchars($item['meta_title'])?>" onchange="updateTotals()"></td>
			<td id="meta_title" name="meta_title"><?=$total_title?></td>
		</tr>
		<tr>
			<th><a href="#" onclick="open_win('templates/help.php?action=keywords');return false" style="color:blue">Keywords :</a></td>
			<td><textarea name="item[meta_keys]" style="width:99%" rows="5" onkeypress="textareaAutoSize(this,3,3,25)" onchange="updateTotals()"><?=htmlspecialchars($item['meta_keys'])?></textarea></td>
			<td id="meta_keys" name="meta_keys"><?=$total_keys?></td>
		</tr>
		<tr>
			<th><a href="#" onclick="open_win('templates/help.php?action=desc');return false" style="color:blue">Description :</a></td>
			<td><textarea name="item[meta_desc]" style="width:99%" rows="5" onkeypress="textareaAutoSize(this,3,3,25)" onchange="updateTotals()"><?=htmlspecialchars($item['meta_desc'])?></textarea></td>
			<td id="meta_desc" name="meta_desc"><?=$total_desc?></td>
		</tr>
		<tr>
			<th><a href="#" onclick="open_win('templates/help.php?action=url_keys');return false" style="color:blue">URL Keys :</a></td>
			<td><input type="text" name="item[url_keys]" style="width:99%" value="<?=htmlspecialchars($item['url_keys'])?>" onchange="updateTotals()"></td>
			<td id="url_keys" name="url_keys"></td>
		</tr>
		<tr>
			<th>META Tags Last Changed :</td>
			<td style="padding-left:3px;background:#FEC"><?=(!is_null($item['last_update'])) ? (date("d/m/Y", strtotime($item['last_update'])) . ', By : ' . $item['staff']) : 'Never'?></td>
			<td style="padding-left:3px;background:#FEC">&nbsp;</td>
		</tr>
	</table>
	<br />
	<br />
    <div style="margin:auto;text-align:center;">
        <input type="button" value="Auto-Update Tags" onclick="updateTags()" />
        <input type="button" value="Clear Tags" onclick="clearTags()" />
    </div>
	<br />
	<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
