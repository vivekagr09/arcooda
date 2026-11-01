<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;
$created_by_list = [];
$query = $DB->query(sprintf(
        "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `active` = 'y' ORDER BY `first_name` ASC, `last_name` ASC"
    ));
    while ($row = $DB->fetch_assoc($query)) {
        $created_by_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }
 $DB->free_result($query);
// create the META tags....if not already created..
$meta_title = htmlspecialchars($item['meta_title']) ;
$total_title = strlen($meta_title);

$meta_keys = ($item['meta_keywords']!='') ? htmlspecialchars($item['meta_keywords']) : $keyword;
$ar = explode(',',$meta_keys);
$total_keys = 0;
foreach($ar as $key=>$value) {
    if(trim($value) != '')
        $total_keys++;
}
$meta_desc = ($item['meta_description']!='') ? htmlspecialchars($item['meta_desc']) : formatMETA($item['short_desc']);
$total_desc = strlen($meta_desc);

$productNameChunk = []; //explode(" ", $item['description']);
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
    $("[name='item[meta_title]']").val($("[name='item[product_name]']").val()).change();
    $("[name='item[meta_keywords]']").val(($("[name='item[product_name]']").val() + $("[name='item[short_desc]']").val()).replace(/[^a-z0-9\-\"]+/gi, ', ').replace(/[,\s]+$/, '')).change();
    $("[name='item[meta_description]']").val($("[name='item[short_desc]']").val()).change();
    $("[name='item[slug]']").val($("[name='item[product_name]']").val().replace(/[^a-z0-9]+/gi, '-')).change();
    return false;
}
function clearTags() {
    $("[name='item[meta_title]']").val("").change();
    $("[name='item[meta_keywords]']").val("").change();
    $("[name='item[meta_description]']").val("").change();
    $("[name='item[slug]']").val("").change();
    return false;
}
function updateTotals(cyp, hwm) {
    if (cyp == null) cyp = false;
    if (hwm == null) hwm = false;
    var meta = getValue('item[meta_keywords' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']');
    //now update the length of the words
    var keywords = meta.split(',');
    var len = 0;
    for (i = 0; i < keywords.length; i++) if (keywords[i] != '') len++;
    getElement('meta_title' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('item[meta_title' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
    getElement('meta_keys' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = len;
    getElement('meta_desc' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('item[meta_description' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMeta&quot;);return false">META Tags</a>')?>

<div id="sectionMeta">
    <table class="table_fields">
        <tr>
            <th><a href="" onclick="openWindow('help.php?action=title',400,400,true);return false">Title :</a></th>
            <td>
                <input id="meta-title-input" style="width:98%;" type="text" name="item[meta_title]" value="<?= htmlspecialchars($meta_title) ?>" onkeyup="SEOCheckMeta('meta-title', this, '<?= htmlspecialchars($item['product_name']); ?>');" onchange="updateTotals();">

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
                    SEOCheckMeta('meta-title', document.getElementById('meta-title-input'), '<?= htmlspecialchars($item['product_name']); ?>');
                </script>
            </td>
            <td name="meta_title"></td>
        </tr>
        <tr>
            <th ><a href="" onclick="openWindow('help.php?action=keywords',400,400,true);return false">Keywords :</a></th>
            <td>
                <?php //echo formatHTMLTextarea('products[meta_keys]', $meta_keys, true, ' onchange="updateTotals();"', 5) ?>
                <textarea id="meta-keys-input" name="item[meta_keywords]" cols="75" rows="7" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-keys', this, '<?= htmlspecialchars($item['product_name']); ?>');"><?= $item["meta_keywords"] ?></textarea>

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
                    SEOCheckMeta('meta-keys', document.getElementById('meta-keys-input'), '<?= htmlspecialchars($item['product_name']); ?>');
                </script>
            </td>
            <td name="meta_keys"></td>
        </tr>
        <tr>
            <th><a href="#" onclick="openWindow('help.php?action=desc',400,400,true);return false">Description :</a></th>
            <td>
                <textarea id="meta-desc-input" name="item[meta_description]" cols="75" rows="10" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-desc', this, '<?= htmlspecialchars($item['product_name']); ?>');"><?= $item["meta_description"] ?></textarea>

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
                    SEOCheckMeta('meta-desc', document.getElementById('meta-desc-input'), '<?= htmlspecialchars($item['product_name']); ?>');
                </script>
            </td>
            <td name="meta_desc"></td>
        </tr>
    </table>
    <br /><br />
    <?php include __DIR__ . '/_meta_tags_list.tpl.php'; ?>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
