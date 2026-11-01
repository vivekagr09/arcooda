<?php
/**
 * 	Project: Product Builder
 *
 *	videofeat.tpl.php
 *
 *	Video feature template for product builder. This template uses youtube as
 *	a video to be displayed.
 *
 *	This template need options variables from parent.
 *	Options data can be optain from ProductBuilder Module.
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */

$name = $options['name'];
$qty = $options['qty'];

$optionData = $pb->getOptionById($options['id']);

// Sort options
$optionList = $pb->sortOptionsByPrice($optionData['options']);

?>
<div style="padding: 25px 0;">
	<!--div class="page-title"><h2 class="clr">Choose <?php echo ucwords($name); ?></h2></div-->
	<table class="clr pb-product-table" style="width: 75%">
		<?php
		foreach($optionList as $itemKey => $item) {
			$total_price = $item['price'] * $qty;
			$previewImg = "";

			echo "<tr>";

			echo "<td style='width: 300px;'>";
			echo "<div class='youtube-video-wrapper'>";
			if ($item['youtube'] == "") {
				echo "<div style='width: 298px; height: 167px; text-align: center; border: 1px solid #e0e0e0;'>";
				echo "<img src='https://www.highwaygroup.net/images/builder/b0d1e258c463e29846bc34c0e2f6d9ad.png' height='169' alt='No Game Board' title='No Game Board' />";
				echo "</div>";

				$previewImg = "https://www.highwaygroup.net/images/builder/b0d1e258c463e29846bc34c0e2f6d9ad.png";
			} else {
				echo "<iframe width='300' height='169' src='https://www.youtube.com/embed/{$item['youtube']}?rel=0&amp;showinfo=0' frameborder='0' gesture='media' allow='encrypted-media' allowfullscreen></iframe>";
				$previewImg = "https://img.youtube.com/vi/{$item['youtube']}/0.jpg";
			}
			echo "</div>";
			echo "</td>";

			echo "<td>";
			echo "<h3 class='clr grid-pdlrx10'>{$item['header']}</h3>";
			echo "<div class='grid-pdx10'>";
			echo "<p class='clr description'>";
			echo $item['description'];
			echo ($item['infolink'] == "") ? "" : " <a target='_blank' href='{$item['infolink']}' style='font-size: 12px;'>More Info</a>";
			echo "</p>";
			echo "</div>";
			echo "</td>";

			echo "<td>";
			echo "<div class='select-btn-wrapper'>";
			echo "<button style='width: 170px;' class='option-select blue-btn button-selector' type='button' data-option-key='{$itemKey}' data-option-optionkey='3' data-option-name='{$item['header']}' data-option-img='{$previewImg}' data-option-price='{$total_price}' data-option-currency='{$item['currency']}' data-option-qty='{$qty}'>SELECT</button>";
			echo "</div>";

			echo "<div class='select-btn-wrapper align-middle grid-pdtbx10'>";
			echo "<span class='option-price'>$". $item['price']."</span><span style='font-size: 10px;' class='option-price'>{$item['currency']}</span>";
			echo "</div>";
			echo "</td>";

			echo "</tr>";
		}
		?>

	</table>
</div>