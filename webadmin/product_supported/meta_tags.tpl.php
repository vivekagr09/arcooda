<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$meta_by_list = getStaffList();
// create the META tags....if not already created..
$meta_title = $data['meta_title'];
$total_title = strlen($meta_title);
// new method.....stip the product name & description for the Keywords, so have to check for all products

$meta_keys = formatMeta($data['meta_keyword'],'keyword');
$ar = explode(',',$meta_keys);
$total_keys = 0;
foreach($ar as $key=>$value) {
    if(trim($value) != '')
        $total_keys++;
}
//$total_keys = count($ar);
/*if($product['meta_by'] == 0)
    $meta_keys = $keyword;*/
$meta_desc = htmlspecialchars_decode(strip_tags($data['meta_description']));
$total_desc = strlen($meta_desc);

$productNameChunk = explode(" ", $meta_title);
?>
<script type="text/javascript">
function SEOCheckMeta(type, input, productName) {
    var productNameChunk = productName.split(" ");

    var totalTicked = 0;
    var unmatchedWords = [];
    for(var i = 0; i < productNameChunk.length; i++) {
        if (input.value.match(new RegExp(productNameChunk[i], 'i'))) {
            document.getElementById(type + '-keyword-' + i).style.color = "gray";
            totalTicked++;
        } else {
            document.getElementById(type + '-keyword-' + i).style.color = "red";
            unmatchedWords.push(productNameChunk[i]);
        }
    }
    
    // Output unmatched words to the console
    console.log("Unmatched words:", unmatchedWords);

    if (totalTicked == productNameChunk.length) {
        document.getElementById(type + '-status').innerHTML = "Keywords all good";
        document.getElementById(type + '-status').style.color = "green";
    } else {
        document.getElementById(type + '-status').innerHTML = "Keywords still not used";
        document.getElementById(type + '-status').style.color = "black";
    }
}
function updateTotals(cyp, hwm) {
    if (cyp == null) cyp = false;
    if (hwm == null) hwm = false;

    var meta = getValue('data[meta_keyword' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']');
    //now update the length of the words
    var keywords = meta.split(',');
    var len = 0;
    for (i = 0; i < keywords.length; i++) if (keywords[i] != '') len++;
    getElement('meta_title'+ ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data[meta_title' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
    getElement('meta_keys' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = len;
    getElement('meta_desc'+ ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : ''))).innerHTML = getValue('data[meta_description' + ((cyp) ? '_cyp' : ((hwm) ? '_hwm' : '')) + ']').length;
}
//]]>
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMeta&quot;);return false">Site Meta Tags</a>')?>
 <?php
        if(isset($_GET['message']) && $_GET['message'] != ''){
            echo '<p style="color:red;text-align:center;">'.$_GET['message'].'<p>';
        }
    ?>

<div id="sectionMeta">
    <table class="wide" style="width:95%;">
        <tr>
            <td class="left_title"><a href="" onclick="openWindow('help.php?action=title',400,400,true);return false">Title :</a></td>
            <td >
                <input id="meta-title-input" style="width:98%;" type="text" name="data[meta_title]" value="<?= $meta_title ?>" onkeyup="SEOCheckMeta('meta-title', this, '<?= htmlspecialchars($data['name']); ?>');" onchange="updateTotals();">

                <div style="text-align: right">
                    SEO: <span id="meta-title-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-title-keyword-<?= $i ?>"><?= $pn ?></span>
                        <?php
                        }
                        ?>
                    </p>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-title', document.getElementById('meta-title-input'), '<?= htmlspecialchars($meta_title); ?>');
                </script>
                <div  style="text-align: right;"><span name="meta_title"><?= strlen($meta_title) ?></span> (Recommended 10-70)</div>
            </td>
        </tr>
        <tr>
            <td  class="left_title"><a href="" onclick="openWindow('help.php?action=keywords',400,400,true);return false">Keywords :</a></td>
            <td>
                <textarea id="meta-keys-input" name="data[meta_keyword]" cols="75" rows="7" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-keys', this, '<?= $meta_keys; ?>');"><?= $meta_keys ?></textarea>

                <div style="text-align: right">
                    SEO: <span id="meta-keys-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-keys-keyword-<?= $i ?>"><?= $pn ?></span>
<?php
}
?>
                    </p>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-keys', document.getElementById('meta-keys-input'), '<?= htmlspecialchars($meta_keys); ?>');
                </script>
                <div  style="text-align: right;"><span name="meta_keys"><?= count(explode(',', $meta_keys)) ?></span> (Recommended 50-160)</div>
            </td>
        </tr>
        <tr>
            <td class="left_title"><a href="#" onclick="openWindow('help.php?action=desc',400,400,true);return false">Description :</a></td>
            <td>
                <textarea id="meta-desc-input" name="data[meta_description]" cols="75" rows="10" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-desc', this, <?=htmlspecialchars($meta_desc); ?>);"><?= $meta_desc ?></textarea>

                <div style="text-align: right">
                    SEO: <span id="meta-desc-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-desc-keyword-<?= $i ?>"><?= $pn ?></span>
<?php
}
?>
                    </p>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-desc', document.getElementById('meta-desc-input'), '<?= htmlspecialchars($meta_desc); ?>');
                </script>
                <div style="text-align: right;"><span  name="meta_desc"><?= strlen($meta_desc) ?></span> (Recommended 50-160)</div>
            </td>
        </tr>
        <tr>

        </tr>
    </table>
    <?php include "meta_tags_list.tpl.php"; ?>
    <center>