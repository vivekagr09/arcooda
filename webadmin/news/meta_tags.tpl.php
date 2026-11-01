<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if (!isset($data1) || !is_array($data1)) {
    return;
}

$meta_by_list = getStaffList();;
// create the META tags....if not already created..
$meta_title = ($data1['meta_title']!='') ? htmlspecialchars($data1['meta_title']) : formatMETA($data1['news_title']);
$total_title = strlen($meta_title);
// new method.....stip the product name & description for the Keywords, so have to check for all products
$keyword = formatMETA($data1['news_title'].' '.$data1['short_description'],'keyword');
$meta_keys = ($data1['meta_key']!='') ? htmlspecialchars($data1['meta_key']) : $keyword;
$ar = explode(',',$meta_keys);
$total_keys = 0;
foreach($ar as $key=>$value) {
    if(trim($value) != '')
        $total_keys++;
}
//$total_keys = count($ar);
/*if($product['meta_by'] == 0)
    $meta_keys = $keyword;*/
$meta_desc = ($data1['meta_desc']!='') ? htmlspecialchars($data1['meta_desc']) : formatMETA($data1['short_description']);
$total_desc = strlen($meta_desc);

$productNameChunk = explode(" ", $data1['news_title']);
?>

<script type="text/javascript">
function SEOCheckMeta(type, input, productName) {
    var productNameChunk = productName.split(" ");

    var totalTicked = 0;

    for(var i = 0; i < productNameChunk.length; i++) {
        if (input.value.match(new RegExp(productNameChunk[i], 'i'))) {
            document.getElementById(type + '-keyword-' + i).style.color = "gray";
            totalTicked++;
        } else {
            document.getElementById(type + '-keyword-' + i).style.color = "red";
        }
    }

    if (totalTicked == productNameChunk.length) {
        document.getElementById(type + '-status').innerHTML = "Keywords all good";
        document.getElementById(type + '-status').style.color = "green";
    } else {
        document.getElementById(type + '-status').innerHTML = "Keywords still not used";
        document.getElementById(type + '-status').style.color = "black";
    }
}
function updateTags(){
    $("[name='data1[meta_title]']").val($("[name='data1[news_title]']").val()).change();
    $("[name='data1[meta_key]']").val(($("[name='data1[news_title]']").val() + $("[name='data1[short_desc]']").val()).replace(/[^a-z0-9\-\"]+/gi, ', ').replace(/[,\s]+$/, '')).change();
    $("[name='data1[meta_desc]']").val($("[name='data1[short_description]']").val()).change();
    $("[name='data1[slug]']").val($("[name='data1[news_title]']").val().replace(/[^a-z0-9]+/gi, '-')).change();
    return false;
}
function clearTags() {
    $("[name='data1[meta_title]']").val("").change();
    $("[name='data1[meta_key]']").val("").change();
    $("[name='data1[meta_desc]']").val("").change();
    $("[name='data1[slug]']").val("").change();
    return false;
}
function updateTotals(cyp, hwm) {
    if (cyp == null) cyp = false;
    if (hwm == null) hwm = false;
    var meta = getValue('data1[meta_key' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']');
    //now update the length of the words
    var keywords = meta.split(',');
    var len = 0;
    for (i = 0; i < keywords.length; i++) if (keywords[i] != '') len++;
    getElement('meta_title' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data1[meta_title' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
    getElement('meta_keys' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = len;
    getElement('meta_desc' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data1[meta_desc' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMeta&quot;);return false">META Tags</a>')?>

<div id="sectionMeta">
    <table class="wide" style="width:95%;">
        <tr>
            <td class="left_title"><a href="" onclick="openWindow('help.php?action=title',400,400,true);return false">Title :</a></td>
            <td >
                <input id="meta-title-input" style="width:98%;" type="text" name="data1[meta_title]" value="<?= htmlspecialchars($meta_title) ?>" onkeyup="SEOCheckMeta('meta-title', this, '<?= htmlspecialchars($data1['product_name']); ?>');" onchange="updateTotals();">

                <div style="text-align: right">
                    SEO: <span id="meta-title-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-title-keyword-<?= $i ?>"><?= $pn ?></span>
                        <?php
                        }
                        ?>
                    </p>
                    <span><?= strlen($meta_title) ?> (Recommended 10-70)</span>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-title', document.getElementById('meta-title-input'), '<?= htmlspecialchars($data1['news_title']); ?>');
                </script>
            </td>
            <td name="meta_title"></td>
        </tr>
        <tr>
            <td  class="left_title"><a href="" onclick="openWindow('help.php?action=keywords',400,400,true);return false">Keywords :</a></td>
            <td>
                <?php //echo formatHTMLTextarea('products[meta_keys]', $meta_keys, true, ' onchange="updateTotals();"', 5) ?>
                <textarea id="meta-keys-input" name="data1[meta_key]" cols="75" rows="7" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-keys', this, '<?= htmlspecialchars($data1['news_title']); ?>');"><?= $data1["meta_key"] ?></textarea>

                <div style="text-align: right">
                    SEO: <span id="meta-keys-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-keys-keyword-<?= $i ?>"><?= $pn ?></span>
<?php
}
?>
                    </p>
                    <span><?= count(explode(',', $meta_keys)) ?> (Recommended 50-160)</span>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-keys', document.getElementById('meta-keys-input'), '<?= htmlspecialchars($data1['news_title']); ?>');
                </script>
            </td>
            <td name="meta_keys"></td>
        </tr>
        <tr>
            <td class="left_title"><a href="#" onclick="openWindow('help.php?action=desc',400,400,true);return false">Description :</a></td>
            <td>
                <textarea id="meta-desc-input" name="data1[meta_desc]" cols="75" rows="10" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-desc', this, '<?= htmlspecialchars($data1['news_title']); ?>');"><?= $data1["meta_desc"] ?></textarea>

                <div style="text-align: right">
                    SEO: <span id="meta-desc-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-desc-keyword-<?= $i ?>"><?= $pn ?></span>
<?php
}
?>
                    </p>
                    <span><?= strlen($meta_desc) ?> (Recommended 50-160)</span>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-desc', document.getElementById('meta-desc-input'), '<?= htmlspecialchars($data1['news_title']); ?>');
                </script>
            </td>
            <td name="meta_desc"></td>
        </tr>
        <tr>
            <td class="left_title"><a href="#" onclick="openWindow('help.php?action=url',400,400,true);return false">URL Keywords :</a></td>
            <td><input style="width:98%;" type="text" name="data1[slug]" value="<?= htmlspecialchars($data1['slug']) ?>" /></td>
            <td></td>
        </tr>
    </table>
    <br /><br />
    <?php include __DIR__ . '/meta_tags_list.tpl.php'; ?>
    <br /><br />
    <table class="wide" align="center" style="width:95%">
        <tr style="background-color:#FFEECC">
            <td class="left_title">META Tags Last Changed :</td>
            <td>
            <?=(0 == $data1['last_change']) ? 'Never' : date("d/m/y", $data1['last_change']) . ', By: ' . $data1['change_by'];?>
            </td>
        </tr>
    </table>
    <br />
    <center>
<?php
$add = false;
$product_lock['staff'] = '';
    if(!$add)
    {
?>
        <input type="submit" value="Update" style="width:100px" <?= ($product_lock['staff']!=$_SERVER['REMOTE_USER']) ? 'disabled' : ''?>/>
<?php
    }
?>
        <input type="button" value="Auto Update Tags"  onclick="<?= htmlspecialchars("updateTags();$(" . json_encode("[name='data1[meta_title]']") . ").change();") ?>" />
        <input type="button" value="Clear" onclick="clearTags()"/>
    </center>
    <br />
    <br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
