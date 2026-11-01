<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
moduleLoad('freight');
moduleLoad('member'); //memberCouriersFromDatabase
?>

<script type="text/javascript">
//<![CDATA[
function courierFreightSelected(type)
{
	var other=(type=='local') ? 'International' : 'Local';
	type=type.charAt(0).toUpperCase()+type.substr(1);
	getElement('section'+type+'Freight').style.display='';
	getElement('section'+other+'Freight').style.display='none';
}
function updateFreight(sea_air,type)
{
	setValue('ship_product_name',type.toUpperCase()+' '+sea_air.charAt(0).toUpperCase()+sea_air.substr(1)+' Freight');
	var ins=(getValue('ship_insurance')=='n') ? 'N' : 'I';
	setValue('ship_product_id','F00'+ins);
}
function updateCourierAccount(type)
{
	var other=(type=='our') ? 'cus' : 'our';
	getElement(type+'_couriers').style.display='';
	getElement(other+'_couriers').style.display='none';
}
function updateCourierFreight(id, name, insurance, price, account_num, tail_gate)
{
	getElement('product_ship_rowa').style.display='';
	if(account_num==null) account_num='';
	if(tail_gate==null) tail_gate='n';
	if(insurance==null) insurance=getValue('ship_insurance');
	if(price == null) price='0.00';
	if(id>100) name+= '(Customer Account)';
	setValue('ship_product_name',name);
	setValue('ship_courier_name',name);
	setValue('ship_insurance',insurance);
	setValue('ship_tail_gate',tail_gate);
	setValue('ship_product_id','F'+id+((insurance=='n') ? 'N' : 'I'));
	setValue('ship_id',id);
	setValue('ship_offer',price);
	setValue('ship_courier_price','$'+price+' '+getValue('invoice_currency'));
	setValue('ship_offerau',convertCurrency(price,getValue('invoice_currency'),'AUD',true));
	setValue('ship_account_num',account_num);
	if(window.productUpdateTotals) productUpdateTotals();
}
function selectQuotation(type)
{
	getElement('other_couriers').style.display='none';
	var allowed=Array('SeaFreight','AirFreight','Couriers');
	for(i=0;i<allowed.length;i++)
		getElement('section'+allowed[i]).style.display= (allowed[i]==type) ? '' : 'none';
}
function freightCourierSearch(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="/webadmin/documentinvoice/courier_search.php";
	//theFrm.target='_blank';
	theFrm.target="freight_search";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
function clearFreightCouriers()
{
	if(true)
	{
		var table=getElement('freight_couriers');
		var length=table.rows.length;
		for(i=0;i<length;i++)
			table.deleteRow(0);
	}
}
function setCourierData(data)
{
	clearFreightCouriers();
	for(var key in data)
		addFreightCourier(data[key]);
}
function addFreightCourier(data)
{
	var table=getElement('freight_couriers');
	if(table==null) return false;
	var newRow=addTableRow(table,'');
	if(table.rows.length%2==0) newRow.className='alt';
	addTableCell(newRow,'<input type="radio" name="ship_courier" value="'+data['id']+'" onclick="updateCourierFreight(\''+data['id']+'\', \''+data['name']+'\', \''+data['insurance']+'\', \''+Number(data['sell_price']).toFixed(2)+'\', \'\', \''+data['tail_gate']+'\')"/>');
	addTableCell(newRow,data['name']);
	var ins=(data['insurance']=='n') ? 'No':'Yes';
	addTableCell(newRow,ins);
	var tail=(data['tail_gate']=='n') ? 'No':'Yes';
	addTableCell(newRow,tail);
	addTableCell(newRow,'$'+Number(data['sell_price']).toFixed(2));
}
//]]>
</script>
<?php

$sea_match = array('20FT Sea Freight' => '20ft', '40FT Sea Freight' => '40ft', '40HQ Sea Freight' => '40hq', 'LCL Sea Freight' => 'lcl');
$air_match = array('DIRECT Air Freight' => 'direct', 'INDIRECT Air Freight' => 'indirect');
$ship_type = $sea_match[$sinvoice['ship_product_name']];
if ('' == $ship_type) {
    $ship_type = $air_match[$sinvoice['ship_product_name']];
}

if ('' == $ship_type) {
    $ship_type = ('AU' == $sinvoice['ship_country'] && 20 != $sinvoice['site_id']) ? 'local' : 'international';
}

if ('' != $sinvoice['ship_type']) {
    $ship_type = $sinvoice['ship_type'];
}

function searchCouriers($country, $code, $state, $destination, $weight, $goods_value, $currency, $site_id)
{
    $state = str_replace('.', '', $state);

    $courier_list = array();

	if (((20 == $site_id && 'HK' == $country) || (20 != $site_id && 'AU' == $country)) && function_exists('freightDomesticCalculate') && function_exists('freightCalculatePrices')) {
        $code = substr('0000' . intval($code), -4);
        if (0 == $code) {
            $code = '----';
        }

        $search_order = array($destination . ' , ' . $code . ' , ' . $state, $destination . ' , , ' . $state, ' , ' . $code . ' , ' . $state, $destination . ' , ' . $code . ' , ', ' , ' . $code . ' , ', $destination . ' , , ');
        foreach ($search_order as $search_for) {
            if (count($results = postcodeSearch($search_for))) {
                break;
            }
        }

        if (count($results)) {
            $result      = reset($results);
            $destination = $result['Locality'];
            $state       = $result['State'];
            $code        = $result['Pcode'];
        }
        $courier_list = freightDomesticCalculate($destination, $state, $code, $weight);
        $courier_list = freightCalculatePrices($courier_list, $weight, $goods_value);
        foreach ($courier_list as $key => $value) {
            if ('' == $value['currency']) {
                $courier_list[$key]['currency'] = (20 != $site_id) ? 'AUD' : 'HKD';
            }

            $courier_list[$key]['Locality']      = $destination;
            $courier_list[$key]['State']         = $state;
            $courier_list[$key]['Pcode']         = $code;
            $courier_list[$key]['Country']       = (20 != $site_id) ? 'AU' : 'HK';
            $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
        }
    }

    if (count($courier_list) == 0 && function_exists('freightInternationalCalculate') && function_exists('freightCalculateIntPrices')) {
        $courier_list = freightInternationalCalculate($country, $currency, $weight);
        $courier_list = freightCalculateIntPrices($courier_list, $weight, $currency, $goods_value);
        foreach ($courier_list as $key => $value) {
            if ('' == $value['currency']) {
                $courier_list[$key]['currency'] = $currency;
            }

            $courier_list[$key]['Country']       = $country;
            $courier_list[$key]['supplier_name'] = getFreightForwarderName($value['supplier']);
        }
    }

	// need to add the insurance & tail gate stuff to the couriers now..
    $add_list = array();
    foreach ($courier_list as $key => $value) {
        //print_array($value);
        if ('y' == $value['insurance'] && $value['insurance_cost'] > 0) {
            $value['sell_price'] += $value['insurance_cost'];
            $value['cost_price'] += $value['insurance_cost'];
            $value['retail_price'] += $value['insurance_cost'];
            $value['insurance'] = 'y';
            $add_list[]         = $value;
        }
        if (0.0 != $value['insurance_cost']) {
            $courier_list[$key]['insurance'] = 'n';
            if ($value['insurance_cost'] < 1.0) {
                unset($courier_list[$key]);
            }
        }
    }
    foreach ($add_list as $value) {
        $courier_list[] = $value;
    }

    $add_list = array();
    foreach ($courier_list as $key => $value) {
        if (0.0 != $value['tail_gate_charge'] && $weight > 25) {
            $value['sell_price'] += $value['tail_gate_charge'];
            $value['cost_price'] += $value['tail_gate_charge'];
            $value['retail_price'] += $value['tail_gate_charge'];
            $value['tail_gate'] = 'y';
            $value['notes']     = trim($value['notes'] . "\n" . 'Tail Gate Included');
            $add_list[]         = $value;
        }
        if (0.0 != $value['tail_gate_charge']) {
            $courier_list[$key]['tail_gate'] = 'n';
            if ($value['tail_gate_charge'] < 1.0) {
                unset($courier_list[$key]);
            }
        }
    }
    foreach ($add_list as $value) {
        $courier_list[] = $value;
    }

	if (function_exists('freightSortArray')) {
		$courier_list = freightSortArray($courier_list);
	}

    if ('AU' == $country) {
        $pick_up      = array(array('id' => 0, 'name' => 'Pick-Up', 'tail_gate' => 'n', 'insurance' => 'n', 'sell_price' => 0));
        $courier_list = $pick_up + $courier_list;
    }
    return $courier_list;
}
$courier_list = searchCouriers(
	$sinvoice['ship_country'],
	$sinvoice['ship_postcode'],
	$sinvoice['ship_state'],
	$sinvoice['ship_city'],
	$sinvoice['ship_weight'],
	$sinvoice['products_offerau'],
	((20 != $sinvoice['site_id']) ? 'AUD' : 'HKD'),
	$sinvoice['site_id']
);
//print_array($courier_list);
foreach ($courier_list as $id => $array) {
    $courier_list[$id]['sell_price'] = ceil($array['sell_price']);
}

$member_couriers  = memberCouriersFromDatabase($sinvoice['member_id']);
$member_couriers2 = getFreightServices('personal', '', true);
if (count($member_couriers)) {
    foreach ($member_couriers as $array) {
        $member_couriers2[$array['courier_id']] = array('name' => $array['courier_name'], 'account_num' => $array['account_num']);
    }
}

$member_couriers = $member_couriers2;
?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionFreight\');">Freight Details</a>')?>
<div id="sectionFreight">
	<center>
		<input type="button" value="Courier Quotes" onclick="selectQuotation('Couriers');" />
		<input type="button" value="Sea Freight Quotes" onclick="selectQuotation('SeaFreight');" />
		<input type="button" value="Air Freight Quotes" onclick="selectQuotation('AirFreight');" />
	</center>
	<br />
	<table class="wide">
		<tbody id="sectionCouriers" style="<?=('local' != $ship_type && 'international' != $ship_type) ? 'display:none' : ''?>">
		<tr>
			<td class="left_title" style="width:25%">Courier</td>
			<?php $courier_name = $sinvoice['ship_product_name']?>
			<td><input style="width:98%" type="text" name="ship_courier_name" value="<?=htmlspecialchars($courier_name)?>" readonly="readonly"/></td>
		</tr>
		<tr>
			<td class="left_title">Sale Price :</td>
			<td><input style="width:98%" name="ship_courier_price" value="$<?=htmlspecialchars($sinvoice['ship_offer'] . ' ' . $sinvoice['invoice_currency'])?>" readonly="readonly"/></td>
		</tr>
		<tr><td class="left_title" colspan="2" style="text-align:center"><input type="button" value=" View Other Courier Methods " onclick="toggleSection('other_couriers')"></td></tr>
		<tbody id="other_couriers" style="<?=($sinvoice['ship_id'] > 0 || 'Pick-Up' == $sinvoice['ship_product_name']) ? 'display:none' : ''?>">
		<tr><td class="left_title">&nbsp;</td><td><input type="button" value=" Search Couriers " onclick="freightCourierSearch(this.form);"/></tr>
		<tr>
			<td class="left_title"><iframe name="freight_search" style="width:16px;height:16px;visibility:hidden;"></iframe></td>
			<td colspan="2"><table name="freight_courier_table" class="spacetable" style="width:100%;text-align:center;">
				<tr><th></th><th>Service</th><th>Insurance</th><th>Tail Gate</th><th>Sell Price (<span name="product_currency"><?=$sinvoice['invoice_currency']?></span>)</th></tr>
				<tbody id="freight_couriers">
			<?php
$count = 0;
foreach ($courier_list as $id => $array) {
    $checked = ($sinvoice['ship_id'] == $array['id'] && $sinvoice['ship_insurance'] == $array['insurance'] && $sinvoice['ship_tail_gate'] == $array['tail_gate']) ? 'checked="checked"' : '';
?>
				<tr class="<?=($count % 2 == 1) ? 'alt' : ''?>">
					<td><input type="radio" name="ship_courier" onclick="updateCourierFreight('<?=$array['id']?>', '<?=$array['name']?>', '<?=$array['insurance']?>', '<?=sprintf('%.2f', $array['sell_price'])?>','','<?=$array['tail_gate']?>')" <?=$checked?> /></td><td><?=$array['name']?></td>
					<td><?=$array['insurance'] == 'n' ? 'No' : 'Yes'?></td><td><?=$array['tail_gate'] == 'n' ? 'No' : 'Yes'?></td><td>$<?=sprintf('%.2f', $array['sell_price'])?></td>
				</tr>
			<?php
$count++;
}
?>
				</tbody>
				<tr><td colspan="3" style="text-align:left;padding-left:5px;"><b>Personal Couriers :</b></td></tr>
				<tbody id="personal_couriers">
			<?php
$count = 0;
foreach ($member_couriers as $id => $name) {
    $account_num = '';
    if (is_array($name)) {
        $account_num = $name['account_num'];
        $name        = $name['name'];
    }
    $checked = ($sinvoice['ship_id'] == $id) ? 'checked="checked"' : '';
?>
				<tr class="<?=($count % 2 == 1) ? 'alt' : ''?>">
					<td><input type="radio" name="ship_courier" onclick="updateCourierFreight('<?=$id?>', '<?=$name?>',null,null,'<?=$account_num?>')" <?=$checked?> /></td>
					<td><?=$name . (('' != $account_num) ? ' (Account Num : ' . $account_num . ')' : '')?></td><td>N/A</td><td>N/A</td><td>$0.00</td>
				</tr>
			<?php
$count++;
}
?>
				</tbody>
			</table></td>
		</tr>
		</tbody>
		</tbody>
		<tbody id="sectionAirFreight" style="<?=(!in_array($ship_type, $air_match)) ? 'display:none' : ''?>">
		<tr>
			<td class="left_title">Air Freight Type</td>
			<td>
				<input type="radio" name="ship_type" value="direct" onclick="updateFreight('air','direct');"<?=('direct' == $ship_type) ? 'checked' : ''?>/>Direct&nbsp;
				<input type="radio" name="ship_type" value="indirect" onclick="updateFreight('air','indirect');" <?=('indirect' == $ship_type) ? 'checked' : ""?>/>In-Direct
			</td>
		</tbody>
		<tbody id="sectionSeaFreight" style="<?=(!in_array($ship_type, $sea_match)) ? 'display:none' : ''?>">
		<tr>
			<td class="left_title">Sea Freight Type</td>
			<td>
				<input type="radio" name="ship_type" value="lcl" onclick="updateFreight('sea','lcl');" <?=('lcl' == $ship_type) ? 'checked="checked"' : ""?>/>LCL&nbsp;
				<input type="radio" name="ship_type" value="20ft" onclick="updateFreight('sea','20ft');" <?=('20ft' == $ship_type) ? 'checked="checked"' : ""?>/>20 Ft&nbsp;
				<input type="radio" name="ship_type" value="40ft" onclick="updateFreight('sea','40ft');" <?=('40ft' == $ship_type) ? 'checked="checked"' : ""?>/>40 Ft&nbsp;
				<input type="radio" name="ship_type" value="40hq" onclick="updateFreight('sea','40hq');" <?=('40hq' == $ship_type) ? 'checked="checked"' : ""?>/>40 Ft HQ
			</td>
		</tr>
		</tbody>
<?php
$harmonised_code_list   = documentinvoiceGetOptionList('harmonised_code');
$harmonised_code_level0 = array();
$harmonised_code_level1 = array();
$harmonised_code_level2 = array();
foreach ($harmonised_code_list as $k => $v) {
    switch (strlen($k)) {
        case 2:
            $harmonised_code_level0[$k] = $k . ' - ' . $v;
            break;
        case 4:
            $harmonised_code_level1[$k] = $k . ' - ' . $v;
            break;
        default:
            $harmonised_code_level2[substr($k, 0, 2)][substr($k, 0, 4)][$k] = $k . ' - ' . $v;
            break;
    }
}

foreach ($harmonised_code_level2 as $k0 => $v0) {
    if (isset($harmonised_code_level0[$k0])) {
        unset($harmonised_code_level2[$k0]);
        $k0                          = $harmonised_code_level0[$k0];
        $harmonised_code_level2[$k0] = $v0;
        ksort($harmonised_code_level2);
    }
    foreach ($v0 as $k1 => $v1) {
        if (isset($harmonised_code_level1[$k1])) {
            unset($harmonised_code_level2[$k0][$k1]);
            $k1                               = $harmonised_code_level1[$k1];
            $harmonised_code_level2[$k0][$k1] = $v1;
            ksort($harmonised_code_level2[$k0]);
        }
    }
}
$harmonised_code_nest = $harmonised_code_level2;
?>
		<!-- <tr>
			<td class="left_title">Harmonised Code</td>
			<td><select name="harmonised_code" style="width:80%;">
				<option value="0">Not Selected</option>
				<?=formatHTMLOptions($harmonised_code_nest, $sinvoice['harmonised_code'])?>
			</select>
			<a href="invoice_options.php?section=harmonised_code" target="_blank" class="addedit">Add / Edit</a>
			</td>
		</tr>
		<tr>
			<td class="left_title">Country Of Origin</td>
			<td><select name="origin_country" style="width:80%;">
				<option value="0">Not Selected</option>
				<?=formatHTMLOptions(documentinvoiceGetOptionList('origin_country'), $sinvoice['origin_country'])?>
			</select>
			<a href="invoice_options.php?section=origin_country" target="_blank" class="addedit">Add / Edit</a>
			</td>
		</tr> -->
		<tr>
			<td class="left_title">Freight Insurance</td>
			<td colspan="4"><select name="ship_insurance" style="width:100%;"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $sinvoice['ship_insurance'])?></select></td>
		</tr>
		<tr id="ship_tail_gate_tr" style="<?=($sinvoice['ship_weight'] < 25) ? 'display:none' : ''?>">
			<td class="left_title">Tail Gate</td>
			<td colspan="4"><select name="ship_tail_gate" style="width:100%;"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $sinvoice['ship_tail_gate'])?></select></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%">Shipping Account Number :</td>
			<td colspan="4"><input style="width:98%;" type="text" name="ship_account_num" value="<?=$sinvoice['ship_account_num']?>"/></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%">Tracking Number :</td>
			<td colspan="4"><input style="width:98%;" type="text" name="proforma_tracknumber" value="<?=$sinvoice['proforma_tracknumber']?>"/></td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%">Shipment Status :</td>
			<td colspan="4">
				<table class="table_grid" style="width: 100%;">
					<tr style=" <?= ($sinvoice['is_freight_checked'] == 'yes' && !empty($sinvoice['freight_received_date']))?'background-color:#91e9774a':''?>">
						<td>
							<input type="hidden"  name="fright_tracking_service" value="<?= $sinvoice['fright_tracking_service'] ?>">
							<input type="hidden"  name="frieght_icon_traking_url" value="<?= $sinvoice['frieght_icon_traking_url'] ?>">
							<input type="hidden"  name="invoice_action" value="<?= $sinvoice['invoice_action'] ?>">
							<input type="hidden"  name="is_delivery_order" value="1">
							<?= $invoice_frieight_tracking_service[$sinvoice['fright_tracking_service']] ?? 'No'?></td>
						<td>
					        <div style="display: flex;justify-content: center;">
							<?php if($sinvoice['frieght_traking_url']){ ?>
								<a href="<?= $sinvoice['frieght_traking_url'] ?>" target="_blank"><?= $sinvoice['proforma_tracknumber'] ?></a>

							<?php } else {  ?>
							<?= $sinvoice['proforma_tracknumber'] ?>
							<?php } 
						        if($sinvoice['frieght_icon_traking_url']){
						            echo '<a href="'.$sinvoice['frieght_icon_traking_url'].'" target="_blank"><img src ="/webadmin/freight_tracking/plane.png" style="width: 20px;padding-left: 5px;"></a>';
						        }elseif ($icontracking['url']) {
						            $trackingUrlIcon = str_replace("{tracking_id}", $sinvoice['proforma_tracknumber'],$icontracking['url']);
						             echo '<a href="'.$trackingUrlIcon.'" target="_blank"><img src ="/webadmin/freight_tracking/plane.png" style="width: 20px;padding-left: 5px;"></a>';
						        }else{
						            echo 'No';
						        }

						    ?>
						    </div>
						</td>
						<td><?= $invoice_status[$sinvoice['invoice_action']] ?></td>
						<td><?= $sinvoice['ship_country']?></td>
						<td><input type="date" size="10" maxlength="10" name="freight_ship_date" value="<?= $sinvoice['freight_ship_date'] ?>"></td>
						<td><input type="date" size="10" maxlength="10" name="freight_received_date" value="<?= $sinvoice['freight_received_date'] ?>"></td>
							<td>
							<?php
								$shipdate = new DateTime($sinvoice['freight_ship_date']);
								$receiveddate = date('Y-m-d');
								if($sinvoice['freight_received_date']){
									$receiveddate = $sinvoice['freight_received_date'];
								}
								$receiveddata = new DateTime($receiveddate);

								$diff = $shipdate->diff($receiveddata);
								if($sinvoice['freight_ship_date']){
									echo $diff->days;
								}
								
							?>
						</td>
						 <td><?= htmlspecialchars($all_staff_list[$sinvoice['handler_id']]) ?></td>
						 <td>
							<select name="is_freight_checked">
								<option value="no" <?= ($sinvoice['is_freight_checked'] == 'no' ? 'selected':'')?>>No</option>
								<option value="yes" <?= ($sinvoice['is_freight_checked'] == 'yes' ? 'selected':'')?>>Yes</option>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
