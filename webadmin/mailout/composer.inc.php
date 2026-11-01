<?php
/**
 * @var bool $usePostVars
 */
?><?php

global $DB;

if (isset($_POST['preview'])) {
    $usePostVars = true;
}
?>
<script type="text/javascript">
//<![CDATA[
function doTopImagePath() {
	// if the top section's image path has not been set then set it.
	if (document.forms[0].elements['top[image]']) {
		document.forms[0].elements['imagePath'].value = document.forms[0].elements['top[image]'].value;
	}
}
function doSend() {
	if (confirm('Are you sure you wish to send the mailing list?')) {
		document.forms[0].action = 'sender.php';
		document.forms[0].elements['action2'].value = 'send';
		document.forms[0].submit();
	}
}
//]]>
</script>

<table cellpadding="5" cellspacing="0" style="margin:auto;border-style:none">
	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Email Subject :</strong></td>
		<td class="right"><input type="text" name="subject" value="<?=htmlspecialchars($_POST['subject'])?>" style="width:400px" /></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Mailout Title :</strong></td>
		<td colspan="2" nowrap="nowrap" class="right"><table cellpadding="3" style="border-style:none">
			<tr><td><select name="title" style="width:200px">
				<option value="Weekly Sales Report" <?=$_POST['title'] == "Weekly Sales Report" ? 'selected' : ''?> >Weekly Sales Report</option>
				<option value="Special Offer" <?=$_POST['title'] == "Special Offer" ? 'selected' : ''?> >Special Offer</option>
				<option value="Special Member Offer" <?=$_POST['title'] == "Special Member Offer" ? 'selected' : ''?> >Special Member Offer</option>
			</select></td></tr>
			<tr><td><b>OR</b></td></tr>
			<tr><td><input type="text" name="newTitle" value="<?=htmlspecialchars($_POST['newTitle'])?>" style="width:200px" /></td></tr>
		</table></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Top Title :</strong></td>
		<td class="right"><input type="text" name="top[title]" value="<?=htmlspecialchars($_POST['top']['title'])?>" style="width:300px" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Top Description :</strong></td>
		<td class="right"><textarea name="top[description]" rows="7" cols="40"><?=htmlspecialchars($_POST['top']['description'])?></textarea></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Image :</strong></td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<?=("" != $_POST['imagePath']) ? $_POST['imagePath'] : '<input type="file" name="top[image]" style="width:300px" />'?>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Image Size:</strong></td>
		<td class="right"><table width="100%" border=0 align="center"><tr>
			<td><input type="radio" name="top[imageSize]" value="s"<?=($_POST['top']['imageSize'] == "s") ? ' checked="checked"' : ''?> />Small (96x96)</td>
			<td><input type="radio" name="top[imageSize]" value="m"<?=($_POST['top']['imageSize'] == "m") ? ' checked="checked"' : ''?> />Medium (140x140)</td>
			<td><input type="radio" name="top[imageSize]" value="l"<?=(!isset($_POST['top']['imageSize']) || "l" == $_POST['top']['imageSize']) ? ' checked="checked"' : '' ?> />Large (200x200)</td>
		</tr></table></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
<?php
$orderCounter = 2;
?>
<!-- featured products -->
	<tr>
		<td nowrap="nowrap" class="left"><strong>Featured Products :</strong></td>
		<td valign="top" class="right"><table width="80%" cellspacing="1" cellpadding="2" style="margin:auto;background:#f0f4ff;border-style:none">
			<tr style="background:#09e;text-align:center">
				<td width="15%"><b>Order</b></td>
				<td width="15%"><b>Summary</b></td>
				<td width="15%"><b>Detailed</b></td>
				<td width="55%"><b>Product ID</b></td>
			</tr>
<?php
$max_featured_products = mailout_options('feature products');
if ($max_featured_products <= 0) {
    $max_featured_products = 4;
}

for ($f = 0; $f < $max_featured_products; $f++) {
?>
			<tr style="text-align:center">
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][order]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['order'] : $orderCounter?>" style="width:22px" /></td>
				<td width="15%"><input type="radio" name="section[<?=$orderCounter?>][display]" value="SUMMARY" <?=($usePostVars && $_POST['section'][$orderCounter]['display'] == "SUMMARY") ? "CHECKED" : ""?> /></td>
				<td width="15%"><input type="radio" name="section[<?=$orderCounter?>][display]" value="DETAILED"<?php
    if ($usePostVars) {
        echo $_POST['section'][$orderCounter]['display'] == "DETAILED" ? ' checked="checked"' : "";
    } else {
        echo ' checked="checked"';
    }
?> /></td>
				<td width="40%">
					<input type="text" name="section[<?=$orderCounter?>][id]" value="<?=htmlspecialchars($_POST['section'][$orderCounter]['id'])?>" />
					<input type="hidden" name="section[<?=$orderCounter?>][type]" value="featured" />
				</td>
			</tr>
<?php
    $orderCounter++;
}
?>
		</table></td>
	</tr>
<!-- //featured products -->
<!-- latest Products -->
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left">
			<strong>Latest Products :</strong><br />
			<br />
			<a href="/webadmin/mailout/options_admin.php" target="_blank">Change default values</a>
		</td>
		<td valign="top" class="right"><table width="100%" cellspacing="1" cellpadding="2" style="border-style:none;margin:auto;background:#f0f4ff">
			<tr style="text-align:center;background:#09e">
				<td width="15%"><b>Order</b></td>
				<td width="15%"><b>Number</b></td>
				<td width="15%"><b>Summary</b></td>
				<td width="15%"><b>Detailed</b></td>
				<td width="40%"><b>Type</b></td>
			</tr>
<?php
if ($query = $DB->query(sprintf(
    "SELECT `id`, `description` FROM `product_types` WHERE `parent` = '0' ORDER BY `ordering`"
))) {
    while ($row = $DB->fetch_assoc($query)) {
?>
			<tr style="text-align:center">
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][order]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['order'] : $orderCounter ?>" style="width:22px" /></td>
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][number]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['number'] : mailout_options('latest sections')?>" style="width:22px" /></td>
				<td width="15%"><input type="radio" name="section[<?=$orderCounter?>][display]" value="summary"
<?php
        if ($usePostVars) {
            echo $_POST['section'][$orderCounter]['display'] == "summary" ? "CHECKED" : "";
        } else {
            echo "CHECKED";
        }
?> /></td>
				<td width="15%">
					<input type="radio" name="section[<?=$orderCounter?>][display]" value="detailed"<?=($usePostVars && $_POST['section'][$orderCounter]['display'] == "detailed") ? ' checked="checked"' : ""?> />
					<input type="hidden" name="section[<?=$orderCounter?>][category_id]" value="<?=$row['id']?>" />
					<input type="hidden" name="section[<?=$orderCounter?>][type]" value="latest_products" />
				</td>
				<td width="40%"><?=htmlspecialchars($row['description'])?></td>
			</tr>
<?php
        $orderCounter++;
	}
	$DB->free_result($query);
}
?>
		</table></td>
	</tr>
<!-- //latest products -->

	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left">
			<strong>Auto Entries :</strong><br />
			<br />
			<a href="/webadmin/mailout/options_admin.php" target="_blank">Change default values</a>
		</td>
		<td valign="top" class="right"><table width="100%" cellspacing="1" cellpadding="2" style="border-style:none;margin:auto;background:#f0f4ff">
			<tr style="text-align:center">
				<td width="15%"><input type="text" name="section[1][order]" value="<?=($usePostVars) ? $_POST['section'][1]['order'] : "1"?>" style="width:22px" /></td>
				<td width="15%"><input type="text" name="section[1][number]" value="<?=($usePostVars) ? $_POST['section'][1]['number'] : mailout_options('news')?>" style="width:22px" /></td>
				<td width="15%"><input type="radio" name="section[1][display]" value="SUMMARY" checked="checked" /></td>
				<td width="15%">
					<input type="hidden" name="section[1][category]" value="news" />
					<input type="hidden" name="section[1][type]" value="auto" />
				</td>
				<td width="40%">News</td>
			</tr>
			<tr style="text-align:center">
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][order]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['order'] :  $orderCounter?>" style="width:22px" /></td>
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][number]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['number'] : mailout_options('products on sale') ?>" style="width:22px" /></td>
				<td width="15%"><input type="radio" name="section[<?=$orderCounter?>][display]" value="SUMMARY" checked="checked" /></td>
				<td width="15%">
					<input type="hidden" name="section[<?=$orderCounter?>][category]" value="sales" />
					<input type="hidden" name="section[<?=$orderCounter++?>][type]" value="auto" />
				</td>
				<td width="40%">Products on Sale</td>
			</tr>
			<tr style="text-align:center">
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][order]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['order'] : $orderCounter?>" style="width:22px" /></td>
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][number]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['number'] : mailout_options('wanted')?>" style="width:22px" /></td>
				<td width="15%"><input type="radio" name="section[<?=$orderCounter?>][display]" value="SUMMARY" checked="checked" /></td>
				<td width="15%">
					<input type="hidden" name="section[<?=$orderCounter?>][category]" value="wanted" />
					<input type="hidden" name="section[<?=$orderCounter++;?>][type]" value="auto" />
				</td>
				<td width="40%">Wanted</td>
			</tr>
			<tr style="text-align:center">
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][order]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['order'] : $orderCounter?>" style="width:22px" /></td>
				<td width="15%"><input type="text" name="section[<?=$orderCounter?>][number]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['number'] : mailout_options('latest products')?>" style="width:22px" /></td>
				<td width="15%"><input type="radio" name="section[<?=$orderCounter?>][display]" value="SUMMARY" checked="checked" /></td>
				<td width="15%">
					<input type="hidden" name="section[<?=$orderCounter?>][category]" value="latest" />
					<input type="hidden" name="section[<?=$orderCounter++;?>][type]" value="auto" />
				</td>
				<td width="40%">Latest Products</td>
			</tr>
			<tr style="text-align:center">
				<td width="15%" height="49"><input type="text" name="section[<?=$orderCounter?>][order]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['order'] : $orderCounter ?>" style="width:22px" /></td>
				<td width="15%" height="49"><input type="text" name="section[<?=$orderCounter?>][number]" value="<?=($usePostVars) ? $_POST['section'][$orderCounter]['number'] : mailout_options('coming soon')?>" style="width:22px" /></td>
				<td width="15%" height="49"><input type="radio" name="section[<?=$orderCounter?>][display]" value="SUMMARY" checked="checked" /></td>
				<td width="15%" height="49">
					<input type="hidden" name="section[<?=$orderCounter?>][category]" value="coming_soon" />
					<input type="hidden" name="section[<?=$orderCounter++?>][type]" value="auto" />
				</td>
				<td width="40%" height="49">Coming Soon</td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="top">&nbsp;</td>
		<td class="top">&nbsp;</td>
	</tr>
	<tr>
		<td nowrap="nowrap">&nbsp;</td>
		<td>
			<input type="hidden" name="action2" value="preview" />
			<input type="hidden" name="imagePath" value="<?=htmlspecialchars($_POST['imagePath'])?>" />
			<input type="button" name="Button" value="&lt;&lt; Back" onClick="javascript:history.back()" />
			<input type="button" name="attach" value="Add Attachments" onClick="MM_openBrWindow('/webadmin/mailout/attachment.php','addatt','scrollbars=yes,resizable=yes,width=400,height=300')" />
			<input type="submit" name="preview" value="Preview" onClick="doTopImagePath();" />
			<input type="button" name="send" value="Send &gt;&gt;" onClick="doSend();" />
		</td>
	</tr>
</table>
