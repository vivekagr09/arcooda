<?php
/**
 * Project: Product Builder
 *
 * standard_v2.tpl.php
 *
 * Standard template for product builder.
 *
 * This template need options variable from parent
 * Options data can be optain from ProductBuilder Module.
 *
 * @author Kelvin (khtat@highwaygroup.net)
 * @since  03 JAN 2018
 */

moduleLoad('product'); //productFromDatabase

$name = $options['name'];
$qty  = $options['qty'];
$col  = $options['column'];
$imageurl=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .$_SERVER['HTTP_HOST'].'/images/builder//';
$optionData = $pb->getOptionById($options['id']);

// Sort options
$optionList = $pb->sortOptionsByPosition($optionData['options']);

?>
<div style="padding: 25px 0; width: 75%; margin: auto; border-left: 1px solid #f0f0f0; border-right: 1px solid #f0f0f0;" class="align-middle" id="options-slider" data-col="<?=$col?>">
	<!--div class="page-title"><h2 class="clr">Choose <?php echo ucwords($name); ?></h2></div-->
<?php

foreach ($optionList as $itemKey => $item) {
    $total_price = $item['price'] * $qty;

    $ea = ($item['price'] > 0) ? "(x{$qty} units)" : "";

    //echo "<td style='width:252px;' class='align-middle'>";
    echo "<div class='grid-inline-top align-middle option-items' style='width: 300px; margin: 10px; outline: none;'>";
    echo "<div class='option-select button-selector option-container-border'>";
    echo "<div class='option-container'>";

    switch ($item['option_key']) {
        case 0:
            $productData = productFromDatabase($item['product_id'], $item['site_id']);

            $img = str_replace('thumb', 'large', $productData['thumb_url']);
            echo "<div class='option-name-wrapper'>";
            echo "<h4>{$productData['product_name']}</h4>";
            echo "</div>";

            echo "<div class='option-image-wrapper'>";
            echo "<img src='{$img}' width='250' height='250' class='option-image' />";

            echo "</div>"; // END option-image-wrapper

            echo "<div class='align-middle'>";
            echo "<span class='option-price'>$" . $item['price'] . "</span><span class='option-price' style='font-size: 10PX;'>{$item['currency']}</span><span class='option-units'> {$ea}</span>";
            echo "</div>";

            echo "<div class='desc-wrapper' style='display: none;'>";
            echo "<p class='clr option-desc'>{$productData['long_desc']}</p>";
            echo "</div>";

            echo "<div class='option-btn-wrapper'>";
            echo "<div class='option-btn-radio' data-option-key='{$itemKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$productData['thumb_url']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$productData['product_name']}' data-option-qty='{$qty}'></div>";
            //echo "<button type='button' class='option-select blue-btn button-selector' data-option-key='{$optionKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$productData['thumb_url']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$productData['product_name']}' data-option-qty='{$qty}'>SELECT</button>";
            echo "</div>"; // end option-btn-wrapper

            break;

        case 1:
            echo "<div class='option-name-wrapper'><h4>{$item['header']}</h4></div>";

            echo "<div class='option-image-wrapper'>";
            echo "<p class='clr' style='padding: 10px;'>{$item['text']}</p>";
            echo "</div>";

            echo "<div class='align-middle'>";
            echo "<span class='option-price'>$" . $item['price'] . "</span><span class='option-price' style='font-size: 10PX;'>{$item['currency']}</span><span class='option-units'> {$ea}</span>";
            echo "</div>";

            echo "<div class='option-btn-wrapper'>";
            echo "<div class='option-btn-radio' data-option-key='{$itemKey}' data-option-optionkey='{$item['option_key']}' data-option-img='graphic/text.png' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$item['header']}' data-option-qty='{$qty}'></div>";
            //echo "<button type='button' class='option-select blue-btn button-selector' data-option-key='{$optionKey}' data-option-optionkey='{$item['option_key']}' data-option-img='graphic/text.png' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$item['header']}' data-option-qty='{$qty}'>SELECT</button>";
            echo "</div>"; // end option-btn-wrapper

            break;

        case 2:
            echo "<div class='option-name-wrapper'>";
            echo "<h4>{$item['header']}</h4>";
            echo "</div>";

            echo "<div class='option-image-wrapper'>";
            echo "<img src='{$imageurl}{$item['img']}.{$item['ext']}' width='250' height='250' class='option-image' />";

            echo "</div>"; // END option-image-wrapper

            echo "<div class='align-middle'>";
            echo "<span class='option-price'>$" . $item['price'] . "</span><span class='option-price' style='font-size: 10PX;'>{$item['currency']}</span><span class='option-units'> {$ea}</span>";
            echo "</div>";

            echo "<div class='desc-wrapper' style='display: none;'>";
            echo "<p class='clr option-desc'>{$item['description']}</p>";
            echo "</div>";

            echo "<div class='option-btn-wrapper'>";
            echo "<div class='option-btn-radio' data-option-key='{$itemKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$imageurl}{$item['img']}.{$item['ext']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$item['header']}' data-option-qty='{$qty}'></div>";
            //echo "<button type='button' class='option-select blue-btn button-selector' data-option-key='{$optionKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$productData['thumb_url']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$productData['product_name']}' data-option-qty='{$qty}'>SELECT</button>";
            echo "</div>"; // end option-btn-wrapper

            break;
    }

    echo "</div>";
    echo "</div>";
    echo "</div>";
}

?>
</div>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function () {
    $('#options-slider').slick({
        centerMode: false,
        infinite: false,
        centerPadding: '60px',
        slidesToShow: 4,
        arrows: true
    });
});
//]]>
</script>