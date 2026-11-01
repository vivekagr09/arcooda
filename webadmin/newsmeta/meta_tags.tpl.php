<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$meta_by_list = getStaffList();
// create the META tags....if not already created..
$meta_title = $fetchdata[0]['value'];
$total_title = strlen($meta_title);
// new method.....stip the product name & description for the Keywords, so have to check for all products

$meta_keys = htmlspecialchars($fetchdata[2]['value']);
$ar = explode(',',$meta_keys);
$total_keys = 0;
foreach($ar as $key=>$value) {
    if(trim($value) != '')
        $total_keys++;
}
//$total_keys = count($ar);
/*if($product['meta_by'] == 0)
    $meta_keys = $keyword;*/
$meta_desc = htmlspecialchars($fetchdata[1]['value']);
$total_desc = strlen($meta_desc);

$productNameChunk = explode(" ", $meta_title);
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMeta&quot;);return false">News Meta Tags</a>')?>
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
                <input id="meta-title-<?=$data1['id']?>-input" style="width:98%;" type="text" name="data[NEWS_META_TITLE]" value="<?= $meta_title ?>" onkeyup="SEOCheckMeta('meta-title', this, '<?= htmlspecialchars($meta_title); ?>');" onchange="updateTotals();">

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
                <textarea id="meta-keys-input" name="data[NEWS_META_KEYS]" cols="75" rows="7" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-keys', this, '<?= $meta_keys; ?>');"><?= $meta_keys ?></textarea>

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
                <textarea id="meta-desc-input" name="data[NEWS_META_DESC]" cols="75" rows="10" onchange="updateTotals();" onkeyup="SEOCheckMeta('meta-desc', this, $meta_desc);"><?= $meta_desc ?></textarea>

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
                    SEOCheckMeta('meta-desc', document.getElementById('meta-desc-input'), '<?= $meta_desc; ?>');
                </script>
                <div style="text-align: right;"><span  name="meta_desc"><?= strlen($meta_desc) ?></span> (Recommended 50-160)</div>
            </td>
        </tr>
        <tr>

        </tr>
    </table>
    <?php include "meta_tags_list.tpl.php"; ?>
    <center>