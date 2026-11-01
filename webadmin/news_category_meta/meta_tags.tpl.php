<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
if (!isset($data1) || !is_array($data1)) {
    return;
}
$meta_by_list = getStaffList();;
// create the META tags....if not already created..
$meta_title = $data1['meta_title'];
$total_title = strlen($meta_title);
// new method.....stip the product name & description for the Keywords, so have to check for all products
$keyword = formatMETA($data1['meta_title'].' '.$data1['meta_desc'],'keyword');
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

$productNameChunk = explode(" ", $data1['meta_title']);
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMeta&quot;);return false">'.$data1['name'].' META Tags</a>')?>

<div id="sectionMeta">
    <table class="wide" style="width:95%;">
        <tr>
            <td class="left_title"><a href="" onclick="openWindow('help.php?action=title',400,400,true);return false">Title :</a></td>
            <td >
                <input id="meta-title-<?=$data1['id']?>-input" style="width:98%;" type="text" name="data[<?=$data1['id']?>][meta_title]" value="<?= $meta_title ?>" onkeyup="SEOCheckMeta('meta-title-<?=$data1['id']?>', this, '<?= htmlspecialchars($data1['meta_title']); ?>');" onchange="updateTotals(<?=$data1['id']?>);">

                <div style="text-align: right">
                    SEO: <span id="meta-title-<?=$data1['id']?>-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-title-<?=$data1['id']?>-keyword-<?= $i ?>"><?= $pn ?></span>
                        <?php
                        }
                        ?>
                    </p>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-title-<?=$data1['id']?>', document.getElementById('meta-title-<?=$data1['id']?>-input'), '<?= htmlspecialchars($data1['meta_title']); ?>');
                </script>
            </td>
            <td name="meta_title_<?=$data1['id']?>"><?= strlen($meta_title) ?></td>
        </tr>
        <tr>
            <td  class="left_title"><a href="" onclick="openWindow('help.php?action=keywords',400,400,true);return false">Keywords :</a></td>
            <td>
                <?php //echo formatHTMLTextarea('products[meta_keys]', $meta_keys, true, ' onchange="updateTotals();"', 5) ?>
                <textarea id="meta-keys-<?=$data1['id']?>-input" name="data[<?=$data1['id']?>][meta_key]" cols="75" rows="7" onchange="updateTotals(<?=$data1['id']?>);" onkeyup="SEOCheckMeta('meta-keys-<?=$data1['id']?>', this, '<?= htmlspecialchars($data1['meta_key']); ?>');"><?= $data1["meta_key"] ?></textarea>

                <div style="text-align: right">
                    SEO: <span id="meta-keys-<?=$data1['id']?>-status">Keywords still not used</span>

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
                    SEOCheckMeta('meta-keys-<?=$data1['id']?>', document.getElementById('meta-keys-<?=$data1['id']?>-input'), '<?= htmlspecialchars($data1['meta_key']); ?>');
                </script>
            </td>
            <td name="meta_keys_<?=$data1['id']?>"><?= count(explode(',', $meta_keys)) ?></td>
        </tr>
        <tr>
            <td class="left_title"><a href="#" onclick="openWindow('help.php?action=desc',400,400,true);return false">Description :</a></td>
            <td>
                <textarea id="meta-desc-<?=$data1['id']?>-input" name="data[<?=$data1['id']?>][meta_desc]" cols="75" rows="10" onchange="updateTotals(<?=$data1['id']?>);" onkeyup="SEOCheckMeta('meta-desc-<?=$data1['id']?>', this, '<?= htmlspecialchars($data1['meta_desc']); ?>');"><?= $data1["meta_desc"] ?></textarea>

                <div style="text-align: right">
                    SEO: <span id="meta-desc-<?=$data1['id']?>-status">Keywords still not used</span>

                    <p style="color: red; margin: 0;">
                        <?php foreach($productNameChunk as $i => $pn) { ?>
                            <span id="meta-desc-<?=$data1['id']?>-keyword-<?= $i ?>"><?= $pn ?></span>
<?php
}
?>
                    </p>
                </div>

                <script>
                    // Check SEO
                    SEOCheckMeta('meta-desc-<?=$data1['id']?>', document.getElementById('meta-desc-input'), '<?= htmlspecialchars($data1['meta_desc']); ?>');
                </script>
            </td>
            <td name="meta_desc_<?=$data1['id']?>"><?= strlen($meta_desc) ?></td>
        </tr>
        <tr>

        </tr>
    </table>
    <?php include "meta_tags_list.tpl.php"; ?>
    <center>