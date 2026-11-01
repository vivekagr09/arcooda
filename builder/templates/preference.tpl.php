<?php
/**
 * 	Project: Product Builder
 *
 *	preference.tpl.php
 *
 *	This template is to choose voltages and shipping methods.
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  04 JAN 2018
 */
?>

<div style="padding: 25px 0;">

	<table class="clr" style="margin: auto;">
		<tr>
			<td style="padding: 50px 20px;">
				<div class="label-wrapper align-middle">
					<h4 class="clr">VOLTAGE</h4>
				</div>
			</td>

			<td>
				<table class="clr">
					<tr>
						<td style="padding: 0 10px;">
							<div class="voltage-selector selection-bar button-checkbox option-container-border-prefrence" data-name="voltage" id="option-voltage-110">
								<table class="clr selection-bar-grid">
									<tr>
										<td>
											<div class="option-btn-radio" data-value="110"></div>
										</td>
										<td>
											<span class="selection-bar-label">110V</span>
										</td>

										
									</tr>
								</table>
							</div>
						</td>

						<td style="padding: 0 10px;">
							<div class="voltage-selector selection-bar button-checkbox option-container-border-prefrence" data-name="voltage">
								<table class="clr selection-bar-grid" id="option-voltage-220">
									<tr>
										<td>
											<div class="option-btn-radio" data-value="220"></div>
										</td>
										<td>
											<span class="selection-bar-label">220V</span>
										</td>

										
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<?php if ($machines[$machineKey]['delivery_air'] > 0 || $machines[$machineKey]['delivery_pickup'] > 0 || $machines[$machineKey]['delivery_sea'] > 0) { ?>
		<tr>
			<td style="padding: 50px 20px;">
				<div class="label-wrapper align-middle">
					<h4 class="clr">SHIPPING METHOD</h4>
				</div>
			</td>

			<td>
				<table class="clr" style="width: 100%;">
					<tr>
						<?php if ($machines[$machineKey]['delivery_pickup'] > 0) { ?>
						<td style="padding: 0 10px;">
							<div class="shipping-selector selection-bar button-checkbox option-container-border-prefrence" data-name="shipping" id="option-shipping-pickup">
								<table class="clr selection-bar-grid">
									<tr>
										<td>
											<div class="option-btn-radio" data-name="shipping" data-value="pickup" data-price="<?= $machines[$machineKey]['delivery_pickup_price'] ?>" data-currency="<?= $machines[$machineKey]['currency'] ?>"></div>
										</td>
										<td>
											<span class="selection-bar-label">Pickup</span>
										</td>

										<td style='padding: 0 10px;'>
											<span class="selection-bar-label">$<?= $machines[$machineKey]['delivery_pickup_price'] ?><span style='font-size: 10px;'><?= $machines[$machineKey]['currency'] ?></span></span>
										</td>

										
									</tr>
								</table>
							</div>
						</td>
						<?php } ?>

						<?php if ($machines[$machineKey]['delivery_sea'] > 0) { ?>
						<td style="padding: 0 10px;">
							<div class="shipping-selector selection-bar button-checkbox option-container-border-prefrence" data-name="shipping" id="option-shipping-sea">
								<table class="clr selection-bar-grid">
									<tr>
										<td>
											<div class="option-btn-radio" data-name="shipping" type="button" data-value="sea" data-currency="<?= $machines[$machineKey]['currency'] ?>" data-price="<?= $machines[$machineKey]['delivery_sea_price'] ?>"></div>
										</td>
										<td>
											<span class="selection-bar-label">Sea Freight</span>
										</td>

										<td style='padding: 0 10px;'>
											<span class="selection-bar-label">$<?= $machines[$machineKey]['delivery_sea_price'] ?><span style='font-size: 10px;'><?= $machines[$machineKey]['currency'] ?></span></span>
										</td>

										
									</tr>
								</table>
							</div>
						</td>
						<?php } ?>

						<?php if ($machines[$machineKey]['delivery_air'] > 0) { ?>
						<td style="padding: 0 10px;">
							<div class="shipping-selector selection-bar button-checkbox option-container-border-prefrence" data-name="shipping" id="option-shipping-air">
								<table class="clr selection-bar-grid">
									<tr>
										
										<td>
											<div class="option-btn-radio" data-name="shipping" type="button" data-value="air" data-currency="<?= $machines[$machineKey]['currency'] ?>" data-price="<?= $machines[$machineKey]['delivery_air_price'] ?>"></div>
										</td>
										<td>
											<span class="selection-bar-label">Air Freight</span>
										</td>

										<td style='padding: 0 10px;'>
											<span class="selection-bar-label">$<?= $machines[$machineKey]['delivery_air_price'] ?><span style='font-size: 10px;'><?= $machines[$machineKey]['currency'] ?></span></span>
										</td>

									</tr>
								</table>
							</div>
						</td>
						<?php } ?>
					</tr>
				</table>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>