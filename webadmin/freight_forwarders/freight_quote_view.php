<?php
include_once 'forwarders_common.inc.php';
include_once 'templates/forwarders_header.tpl.php';

$quote_id = $posted['quote_id'];
if (!forwardersQuoteExists($quote_id)) {
    die("Quote ID passed is not valid.");
}

$quote = forwardersQuoteFromDatabase($quote_id);
if ($quote) {
    $sea         = ('Sea' == $quote['sea_air']) ? true : false;
    $sea_fields  = array('wgt' => 'Ocean Freight', 'cfs' => 'CFS Charge', 'thc' => 'THC/ORC', 'doc' => 'Document Charge', 'tel' => 'Tele-Release Document', 'awb' => 'AWB', 'ams' => 'AMS', 'baf' => 'BAF', 'clr' => 'Clearance Fee', 'exp' => 'Export Licence Fee', 'han' => 'Handling Charge', 'tru' => 'Trucking Charge', 'cra' => 'Crating Charge', 'fum' => 'Fumigation', 'com' => 'Commodity Inspection', 'cus' => 'Customs Inspection', 'ins' => 'Insurance Charge', 'ori' => 'Certificate Origin', 'oth' => 'Other');
    $air_fields  = array('wgt' => 'Air Freight', 'thc' => 'THC/ORC', 'doc' => 'Document Charge', 'awb' => 'AWB', 'exp' => 'Export Licence Fee', 'han' => 'Handling Charge', 'tru' => 'Trucking Charge', 'cra' => 'Crating Charge', 'fum' => 'Fumigation', 'com' => 'Commodity Inspection', 'cus' => 'Customs Inspection', 'ins' => 'Insurance Charge', 'ori' => 'Certificate Origin', 'oth' => 'Other');
    $cost_fields = ($sea) ? $sea_fields : $air_fields;

    $sea_fields = array('lading_number' => '', 'container_number' => '', 'vessel_name' => '', 'arrival_date' => 'ETA', 'departure_date' => 'ETD');
    $air_fields = array('master_number' => 'Master Airway Number', 'house_number' => 'House Airway Number', 'vessel_name' => 'Aircraft/Flight #', 'arrival_date' => 'ETA', 'departure_date' => 'ETD');
    $ext_fields = ($sea) ? $sea_fields : $air_fields;

    $yesno           = array('y' => 'Yes', 'n' => 'No');
    $supplier_list   = forwardersQuotesGetOptionList('product_id');
    $country_list    = getCountryList();
    $ports           = ($sea) ? getseaportslist() : getairportslist();
    $quote_currency  = strtoupper($quote['quote_currency']);
    $currency_symbol = getCurrencySymbol($quote_currency);

    $alt            = false;
    $terms          = array('fob' => 'FOB', 'exw' => 'Ex Works', 'dtd' => 'Door To Door', 'dta' => 'Door To Port');
    $container_flag = ('40hq' == $quote['container_flags']) ? '40FT HQ' : strtoupper($quote['container_flags']);
?>
<table class="wide">
	<tr><th colspan="3"><?php echo ucwords($quote['sea_air']) ?> Freight Quote : ID#<?php echo $quote_id ?></th></tr>
	<tr><td class="left_title" style="width:25%">Supplier :</td><td colspan="2"><?php echo $supplier_list[$quote['product_id']] ?></td></tr>
	<tr><td class="left_title"><?php echo ($sea) ? 'Container' : 'Service' ?> Type :</td><td colspan="2"><?php echo $container_flag ?></td></tr>
	<tr><td class="left_title">Terms :</td><td colspan="2"><?php echo $terms[$quote['term_flags']] ?></td></tr>
	<tr><td class="left_title">Ship From Country :</td><td colspan="2"><?php echo $country_list[$quote['country_from']] ?></td></tr>
	<tr><td class="left_title">Ship From Port :</td><td colspan="2"><?php echo $ports[$quote['port_from']] ?></td></tr>
	<tr><td class="left_title">Ship To Country :</td><td colspan="2"><?php echo $country_list[$quote['country_to']] ?></td></tr>
	<tr><td class="left_title">Ship To Port :</td><td colspan="2"><?php echo $ports[$quote['port_to']] ?></td></tr>
	<tr><td class="left_title">Shipping Weight (kgs) :</td><td colspan="2"><?php echo $quote['shipping_weight'] ?></td></tr>
	<tr><td class="left_title">Cubic Weight (cm<sup>3</sup>) :</td><td colspan="2"><?php echo $quote['cubic_weight'] ?></td></tr>
	<tr><td class="left_title">Transit Time :</td><td colspan="2"><?php echo $quote['transit_time'] ?></td></tr>
	<tr><td class="left_title" colspan="2">&nbsp;</td><td class="left_title" style="text-align:left">Amount (<?php echo $quote_currency ?>)</td></tr>
<?php
foreach ($cost_fields as $key => $name) {
?>
	<tr>
		<td class="left_title"><?php echo $name ?> :</td>
		<td style="width:20%;text-align:right;padding-right:15px;">&nbsp;
<?php
if ('wgt' == $key) {
            echo $currency_symbol . $quote['weight_rate'] . ' per ' . (($sea) ? 'cm<sup>3</sup>' : 'kg');
        } elseif ('ins' == $key) {
            echo $quote['insurance_rate'] . ' %';
        }

?>
		</td>
		<td><?php echo $currency_symbol . sprintf('%.2f', $quote[$key . '_amount']) ?></td>
	</tr>
<?php
}
?>
	<tr><td class="left_title">Quote Total :</td><td colspan="2"><?php echo $currency_symbol . sprintf('%.2f', $quote['cost_price']) . ' ' . $quote_currency ?></td></tr>
	<tr><td class="left_title">Sale Price :</td><td colspan="2"><?php echo $currency_symbol . sprintf('%.2f', $quote['sale_price']) . ' ' . $quote_currency ?></td></tr>
	<tr><td class="left_title">Notes :</td><td colspan="2"><?php echo nl2br($quote['general_notes']) ?></td></tr>
	<tr><td class="left_title" colspan="3">&nbsp;</td></tr>
	<tr><td class="left_title">Quote Confirmed :</td><td colspan="2"><?php echo $yesno[$quote['confirmed_flag']] ?></td></tr>
<?php
if ('y' == $quote['confirmed_flag']) {
        foreach ($ext_fields as $key => $name) {
            if ('' == $name) {
                $name = ucwords(str_replace('_', ' ', $key));
            }

?>
	<tr><td class="left_title"><?php echo $name ?> :</td><td colspan="2"><?php echo $quote[$key] ?></td></tr>
<?php
}
    }
?>
</table>
<?php
}