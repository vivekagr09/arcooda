<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$product_list=getProductFromdatabase();
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionProductSupported&quot;);return false">Products Supported</a>')?>
 <?php
        if(isset($_GET['message']) && $_GET['message'] != ''){
            echo '<p style="color:red;text-align:center;">'.$_GET['message'].'<p>';
        }
    ?>

<div id="sectionProductSupported">
    <div class="meta_content">
    <table class="wide" align="center" style="width: 90%;text-align: left;margin-left: auto;margin-right: auto;border-collapse: collapse;border: 1px #ccc solid;" >
        <tr>
            
            <td style="width:100%">
            <table style="border-collapse: collapse;border: 1px #ccc solid;">
                <?php 
                    $products=array_chunk($product_list, 3);
                    foreach ($products as $key => $chunkvalue) {
                        echo '<tr>';
                        foreach ($chunkvalue as $key => $product) {
                        ?>
                        <td>
                        <input type="checkbox" name="products[]" value="<?= $product['id']?>" <?= in_array($product['id'],$supported_product)?'checked':''?> /> <?= $product['name'] ?></td>
                    <?php

                        }
                        echo '</tr>';
                    }


                ?>
            </table>
            </td>
        </tr>
    </table>
                </div>
</div>