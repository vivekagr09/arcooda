<?php
/**
 * Project: Product Builder
 *
 * standard.tpl.php
 *
 * Standard template for product builder. The column in this template is
 * initially set up by staff. The number of items displayed in a row is the
 * number of column.
 *
 * This template need options variable from parent
 * Options data can be optain from ProductBuilder Module.
 *
 * @author Kelvin (khtat@highwaygroup.net)
 * @since  29/12/2017
 */

moduleLoad('product'); //productFromDatabase

$name = $options['name'];
$qty  = $options['qty'];
$col  = $options['column'];
$imageurl=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .$_SERVER['HTTP_HOST'].'/images/builder//';
$optionData = $pb->getOptionById($options['id']);

// Sort options
$optionList = $pb->sortOptionsByPosition($optionData['options']);

$width = 100 / intval($col); // In percentage

?>
<div style="padding: 25px 0;">
	<div class="page-title"><h2 class="clr">Choose <?php echo ucwords($name); ?></h2></div>

	<table class="clr pb-product-table" style='width: 100%;'>
<?php

$noItemRow = 0;

foreach ($optionList as $itemKey => $item) {
    $total_price = $item['price'] * $qty;

    if (0 == $noItemRow) {
        echo "<tr>";
    }

    echo "<td style='width:{$width}%;' class='align-middle'>";
    echo "<div class='option-container'>";

    switch ($item['option_key']) {
        case 0:
            $productData = productFromDatabase($item['product_id'], $item['site_id']);
            $img         = str_replace('thumb', 'large', $productData['thumb_url']);

            echo "<div class='option-image-wrapper'>";
            echo "<img src='{$img}' width='250' height='250' class='option-image' />";

            echo "<div class='option-standard-details'>";
            echo "<div class='option-standard-details-content'>";
            echo "<div class='grid-pdx10'><h3 class='clr'>{$productData['product_name']}</h3></div>";
            echo "<div class='grid-pdx10'><p class='clr'>{$productData['long_desc']}</p></div>";
            echo "</div>"; // END option-standard-details-content

            echo "<span class='font-12'>Details</span>";
            echo "</div>"; // END option-standard-details

            echo "</div>"; // END option-image-wrapper

            echo "<div class='option-btn-wrapper'>";
            echo "<button type='button' class='option-select blue-btn button-selector' data-option-key='{$optionKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$productData['thumb_url']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$productData['product_name']}' data-option-qty='{$qty}'>SELECT</button>";
            echo "</div>"; // end option-btn-wrapper

            echo "<div class='grid-pdtbx10 align-middle'>";
            echo "<span class='option-price'>{$item['currency']}$ {$item['price']}</span>";
            echo "</div>";

            break;

        case 1:
            echo "<div class='option-text-wrapper'>";
            echo "<div class='grid-pdx10'><h3 class='clr'>{$item['header']}</h3></div>";
            echo "<p class='clr' style='padding: 10px;'>{$item['text']}</p>";

            echo "</div>"; // END option-text-wrapper

            echo "<div class='option-btn-wrapper'>";
            echo "<button type='button' class='option-select blue-btn button-selector' data-option-key='{$optionKey}' data-option-optionkey='{$item['option_key']}' data-option-img='graphic/text.png' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$item['header']}' data-option-qty='{$qty}'>SELECT</button>";
            echo "</div>"; // end option-btn-wrapper

            echo "<div class='grid-pdtbx10 align-middle'>";
            echo "<span class='option-price'>{$item['currency']}$ {$item['price']}</span>";
            echo "</div>";

            break;

        case 2:
            echo "<div class='option-image-wrapper'>";
            echo "<img src='{$imageurl}{$item['img']}.{$item['ext']}' width='250' height='250' class='option-image' />";

            echo "<div class='option-standard-details'>";
            echo "<div class='option-standard-details-content'>";
            echo "<div class='grid-pdx10'><h3 class='clr'>{$item['header']}</h3></div>";
            echo "<div class='grid-pdx10'><p class='clr'>{$item['description']}</p></div>";
            echo "</div>"; // END option-standard-details-content

            echo "<span class='font-12'>Details</span>";
            echo "</div>"; // END option-standard-details

            echo "</div>"; // END option-image-wrapper

            echo "<div class='option-btn-wrapper'>";
            echo "<button type='button' class='option-select blue-btn button-selector' data-option-key='{$optionKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$imageurl}{$item['img']}.{$item['ext']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$item['header']}' data-option-qty='{$qty}'>SELECT</button>";
            echo "</div>"; // end option-btn-wrapper

            echo "<div class='grid-pdtbx10 align-middle'>";
            echo "<span class='option-price'>{$item['currency']}$ {$item['price']}</span>";
            echo "</div>";

            break;
    }

    echo "</div>";
    echo "</td>";

    $noItemRow++;

    if ($noItemRow == $col) {
        echo "</tr>";
    }
}

?>
	</table>
</div>

<div id="option-details-popup-bg">
	<div id="option-details-popup">
		<div id="option-details-content-loader"></div>
		<div id="option-details-popup-cancel">X</div>
	</div>
</div>