<?php

function subscriptions_conv_price($price) {
	global $studio;

	$symbol = $studio->getopt("sub-currency-symbol");
	$separator = $studio->getopt("sub-decimal-separator", ".");

	return $symbol . str_replace('.', $separator, $price);
}
