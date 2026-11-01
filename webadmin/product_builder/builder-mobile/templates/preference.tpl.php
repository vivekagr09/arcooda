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

<div style="padding: 25px 0; width: 80%; margin: auto;">
	<div class="label-wrapper align-middle">
		<h4 class="clr">VOLTAGE</h4>
	</div>

	<div class="voltage-selector selection-bar button-checkbox option-container-border" data-name="voltage" style="margin: 10px 0;">
		<table class="clr selection-bar-grid">
			<tr>
				<td>
					<span class="selection-bar-label">110V</span>
				</td>

				<td>
					<div class="option-btn-radio" data-value="110"></div>
				</td>
			</tr>
		</table>
	</div>

	<div class="voltage-selector selection-bar button-checkbox option-container-border" data-name="voltage" style="margin: 10px 0;">
		<table class="clr selection-bar-grid">
			<tr>
				<td>
					<span class="selection-bar-label">220V</span>
				</td>

				<td>
					<div class="option-btn-radio" data-value="220"></div>
				</td>
			</tr>
		</table>
	</div>

	<!-- Seperator -->
	<div style="padding-top: 25px;"></div>

	<?php if ($machines[$machineKey]['delivery_air'] > 0 || $machines[$machineKey]['delivery_pickup'] > 0 || $machines[$machineKey]['delivery_sea'] > 0) { ?>
	<div class="label-wrapper align-middle">
		<h4 class="clr">SHIPPING METHOD</h4>
	</div>

	<!-- PICK UP -->
	<?php if ($machines[$machineKey]['delivery_pickup'] > 0) { ?>
	<div class="shipping-selector selection-bar button-checkbox option-container-border" data-name="shipping" style="margin: 10px 0;">
		<table class="clr selection-bar-grid">
			<tr>
				<td>
					<span class="selection-bar-label">Pickup</span>
				</td>

				<td style='padding: 0 10px;'>
					<span class="selection-bar-label">$<?= $machines[$machineKey]['delivery_pickup_price'] ?><span style='font-size: 10px;'><?= $machines[$machineKey]['currency'] ?></span></span>
				</td>

				<td>
					<div class="option-btn-radio" data-name="shipping" data-value="pickup" data-price="<?= $machines[$machineKey]['delivery_pickup_price'] ?>" data-currency="<?= $machines[$machineKey]['currency'] ?>"></div>
				</td>
			</tr>
		</table>
	</div>
	<?php } ?>

	<!-- SEA -->
	<?php if ($machines[$machineKey]['delivery_sea'] > 0) { ?>
	<div class="shipping-selector selection-bar button-checkbox option-container-border" data-name="shipping" style="margin: 10px 0;">
		<table class="clr selection-bar-grid">
			<tr>
				<td>
					<span class="selection-bar-label">Sea Freight</span>
				</td>

				<td style='padding: 0 10px;'>
					<span class="selection-bar-label">$<?= $machines[$machineKey]['delivery_sea_price'] ?><span style='font-size: 10px;'><?= $machines[$machineKey]['currency'] ?></span></span>
				</td>

				<td>
					<div class="option-btn-radio" data-name="shipping" type="button" data-value="sea" data-currency="<?= $machines[$machineKey]['currency'] ?>" data-price="<?= $machines[$machineKey]['delivery_sea_price'] ?>"></div>
				</td>
			</tr>
		</table>
	</div>
	<?php } ?>

	<!-- AIR -->
	<?php if ($machines[$machineKey]['delivery_air'] > 0) { ?>
	<div class="shipping-selector selection-bar button-checkbox option-container-border" data-name="shipping" style="margin: 10px 0;">
		<table class="clr selection-bar-grid">
			<tr>
				<td>
					<span class="selection-bar-label">Air Freight</span>
				</td>

				<td style='padding: 0 10px;'>
					<span class="selection-bar-label">$<?= $machines[$machineKey]['delivery_air_price'] ?><span style='font-size: 10px;'><?= $machines[$machineKey]['currency'] ?></span></span>
				</td>

				<td>
					<div class="option-btn-radio" data-name="shipping" type="button" data-value="air" data-currency="<?= $machines[$machineKey]['currency'] ?>" data-price="<?= $machines[$machineKey]['delivery_air_price'] ?>"></div>
				</td>
			</tr>
		</table>
	</div>
	<?php } ?>


	<?php } ?>
</div>