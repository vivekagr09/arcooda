<?php
/**
 * 	Project: Product Builder
 *
 *	first.tpl.php
 *
 *	This template is to display the machines choices as an index page.
 *	
 *	This template needs machines variables from the parent.
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  03 JAN 2018
 */

moduleLoad('product'); //productFromDatabase
?>

<div style="padding: 25px 0; width: 90%; margin: auto;" id="machine-slider">
	<?php
	foreach($machines as $machineKey => $machine) {
		if ($machine['category'] == $cat || $builder['category'][0] == 1) {	// Display all if category is 1 or display machines in the specified category
			$productData = productFromDatabase($machine['product_id'], $machine['sub_id']);
			//$productImage = str_replace('thumb', 'large', $productData['thumb_url']);

			$productImage = "";

			if (isset($productData['image_url'])) {
				$productImage = relative_url($productData['image_url']);
			} else if (isset($productData['thumb_url'])) {
				$productImage = str_replace('thumb', 'large', $productData['thumb_url']);
			}

			// Image issue hack
			switch($_SERVER['HTTP_HOST']) {
				case "builder.oceankingarcade.com" :
					$productImage = str_replace('http://builder.oceankingarcade.com', 'https://www.arcade-game-sales.com', $productImage);
					break;
			}

			echo "<div class='grid-inline-top align-middle machines' style='margin: 0 10px; outline: none;'>";
			echo "<div class='machine-selector button-selector option-container-border'>";
			echo "<div class='option-container'>";

			echo "<div class='option-name-wrapper'>";
			echo "<h4>{$productData['product_name']}</h4>";
			echo "</div>";

			echo "<div class='option-image-wrapper'>";
			echo "<img src='{$productImage}' width='80%' class='option-image' />";

			echo "</div>"; // END option-image-wrapper

			echo "<div class='align-middle grid-pdtbx10'>";
			echo "<span class='option-price font-1p5em'>$". $machine['price'] ."</span><span class='option-price'>{$machine['currency']}</span>";
			echo "</div>";

			echo "<div class='option-btn-wrapper grid-pdtbx10'>";
			echo "<div class='option-btn-radio' data-machine-name='{$machine['name']}' data-machine-key='{$machineKey}' data-machine-price='{$machine['price']}' data-machine-currency='{$machine['currency']}' data-machine-thumb='{$productData['thumb_url']}' data-machine-pickup='{$machine['delivery_pickup_price']}' data-machine-sea='{$machine['delivery_sea_price']}' data-machine-air='{$machine['delivery_air_price']}'></div>";
			echo "</div>"; // end option-btn-wrapper

			// Description
			$output = preg_replace("/\-+(.*)?/i","<ul style='text-align: left;'><li>$1</li></ul>",$machine['description']);
			$output = preg_replace("/(\<\/ul\>\n(.*)\<ul\>-)+/","",$output);

			echo "<div class='option-description-wrapper'>";
			echo "<div class='option-description'>{$output}</div>";
			echo "</div>";

			echo "</div>";
			echo "</div>";
			echo "</div>";
		}
	}
	?>

</div>

<script type="text/javascript">

	$(document).ready(function(){
		$('#machine-slider').slick({
		  centerMode: false,
		  infinite: false,
		  centerPadding: '60px',
		  slidesToShow: 1,
		  arrows:true
		});
	});
</script>