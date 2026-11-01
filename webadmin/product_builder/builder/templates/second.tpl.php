<?php
/**
 * 	Project: Product Builder
 *
 *	second.tpl.php
 *
 *	This template is to display the machines voltages after the machine is selected.
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  03 JAN 2018
 */
?>

<div style="padding: 25px 0;">

	<div class="align-middle">
		<div class="machine-image-wrapper">
			<img src="http://via.placeholder.com/350x350" width="350" height="350" id="machine-image" />
		</div>

		<div class="grid-inline-top" style="margin-right: 50px;">
			<div class="label-wrapper align-middle">
				<h4 class="clr">VOLTAGE</h4>
			</div>

			<div class="button-selector-wrapper">
				<button class="voltage-selector blue-btn button-checkbox" data-name="voltage" type="button" data-value="110" style="margin-right:10px;">
					110V
				</button><button class="voltage-selector blue-btn button-checkbox" data-name="voltage" type="button" data-value="220">
					220V
				</button>
			</div>
		</div>

		<?php if ($machines[$machineKey]['delivery_air'] > 0 || $machines[$machineKey]['delivery_pickup'] > 0 || $machines[$machineKey]['delivery_sea'] > 0) { ?>

		<div class="grid-inline-top">
			<div class="label-wrapper align-middle">
				<h4 class="clr">SHIPPING METHOD</h4>
			</div>

			<div class="button-selector-wrapper">
				<?php if ($machines[$machineKey]['delivery_pickup'] > 0) { ?>
				<button id="delivery_pickup" class="shipping-selector blue-btn button-checkbox" type="button" data-name="shipping" data-value="pickup" data-price="<?= $machines[$machineKey]['delivery_pickup_price'] ?>" data-currency="<?= $machines[$machineKey]['currency'] ?>" style="margin-right:10px;">Pickup</button>
				<?php } ?>

				<?php if ($machines[$machineKey]['delivery_sea'] > 0) { ?>
				<button id="delivery_sea" class="shipping-selector blue-btn button-checkbox" data-name="shipping" type="button" data-value="sea" data-currency="<?= $machines[$machineKey]['currency'] ?>" data-price="<?= $machines[$machineKey]['delivery_sea_price'] ?>" style="margin-right:10px;">Sea Freight</button>
				<?php } ?>

				<?php if ($machines[$machineKey]['delivery_air'] > 0) { ?>
				<button id="delivery_air" class="shipping-selector blue-btn button-checkbox" data-name="shipping" type="button" data-value="air" data-currency="<?= $machines[$machineKey]['currency'] ?>" data-price="<?= $machines[$machineKey]['delivery_air_price'] ?>">Air Freight</button>
				<?php } ?>
			</div>
		</div>

		<?php } ?>

	</div>
</div>
