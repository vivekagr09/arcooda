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
 *	@since  29/12/2017
 */

moduleLoad('product'); //productFromDatabase
?>
<div style="padding: 25px 0;">
	<div class="page-title"><h2 class="clr">Choose Machine</h2></div>
	<table class="clr pb-product-table">
		<?php
		foreach($machines as $machineKey => $machine) {
			$productData = productFromDatabase($machine['product_id'], $machine['site_id']);
			$productImage = str_replace('thumb', 'large', $productData['thumb_url']);

			echo "<tr>";
			
			// Image
			echo "<td style='width:250px;'>";
			echo "<div class='machine-image-wrapper'>";
			echo "<img src='{$productImage}' width='250' height='250' class='machine-image'/>";
			echo "</div>";
			echo "</td>";

			// Description
			echo "<td>";
			echo "<h3 class='clr' style='padding-left: 25px'>{$machine['name']}</h3>";

			$output = preg_replace("/\-+(.*)?/i","<ul><li>$1</li></ul>",$machine['description']);
			$output = preg_replace("/(\<\/ul\>\n(.*)\<ul\>-)+/","",$output);

			echo "<p class='clr' style='padding: 20px 0 0 25px;'>" . $output . "</p>";
			echo "</td>";

			// Select button and price
			echo "<td>";
			echo "<div class='select-btn-wrapper'>";
			echo "<button class='machine-selector blue-btn button-selector' type='button' data-machine-name='{$machine['name']}' data-machine-key='{$machineKey}' data-machine-price='{$machine['price']}' data-machine-currency='{$machine['currency']}' data-machine-thumb='{$productData['thumb_url']}'>SELECT</button>";
			echo "</div>";

			echo "<div class='select-btn-wrapper align-middle grid-pdtbx10'>";
			echo "<span class='option-price'>{$machine['currency']}$ {$machine['price']}</span>";
			echo "</div>";
			echo "</td>";

			echo "</tr>";
		}
		?>

		<!--tr>
			<td style="width:250px;">
				<div class="machine-image-wrapper">
					<img src="http://via.placeholder.com/250x250" width="250" height="250" class="machine-image" />
				</div>
			</td>

			<td>
				<h3 class="clr" style="padding-left: 25px">Machine Name</h3>
	
				<ul class="description-list">
					<li>Lorem ipsum dolor sit amet</li>
					<li>consectetur adipiscing elit</li>
					<li>Lorem ipsum dolor sit amet</li>
					<li>consectetur adipiscing elit</li>
					<li>Lorem ipsum dolor sit amet</li>
					<li>consectetur adipiscing elit</li>
					<li>Lorem ipsum dolor sit amet</li>
					<li>consectetur adipiscing elit</li>
				</ul>						
			</td>

			<td>
				<div class="select-btn-wrapper">
					<button class="blue-btn select-btn" type="button">SELECT</button>
				</div>

				<div class="select-btn-wrapper align-middle grid-pdtbx10">
					<span class="option-price">$9999</span>
				</div>
			</td>
		</tr>

		<tr>
			<td colspan="3" class="grid-pdx10"><div class="line-sep"></div></td>
		</tr -->

	</table>
</div>